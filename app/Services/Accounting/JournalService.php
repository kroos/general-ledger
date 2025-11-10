<?php
namespace App\Services\Accounting;

use App\Models\Accounting\{
	Account, Journal, JournalEntry, Payment, SalesInvoice, PurchaseBill, TransactionRule
};

// load db facade
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Validation\ValidationException;

use DomainException;
use Session;
use Throwable;
use Exception;
use Log;

class JournalService
{
	/**
	 * Automatically create and post a balanced journal
	 * based on source type (sale, purchase, etc.)
	 */
	public static function post(string $sourceType, int $sourceId, float $amount, string $description = '')
	{
		DB::beginTransaction();

		try {
			$rule = TransactionRule::where('source_type', $sourceType)->firstOrFail();

			$ledgerId = match($sourceType) {
				'sale' => 2,      // Sales Ledger ID (seeded)
				'purchase' => 3,  // Purchase Ledger ID (seeded)
				default => 1,     // General Ledger ID
			};

			$journal = Journal::create([
				'date' => now(),
				'reference_no' => strtoupper($sourceType) . '-' . str_pad($sourceId, 5, '0', STR_PAD_LEFT),
				'ledger_type_id' => $ledgerId,
				'source_type' => $sourceType,
				'source_id' => $sourceId,
				'description' => $description,
				'status' => 'posted',
			]);

			$journal->entries()->createMany([
				[
					'account_id' => $rule->debit_account_id,
					'debit' => $amount,
					'credit' => 0,
					'description' => 'Auto debit from ' . ucfirst($sourceType),
				],
				[
					'account_id' => $rule->credit_account_id,
					'debit' => 0,
					'credit' => $amount,
					'description' => 'Auto credit from ' . ucfirst($sourceType),
				],
			]);

			DB::commit();

			return $journal;
		} catch (\Throwable $e) {
			DB::rollBack();
			throw $e;
		}
	}

	/**
	 * Save draft journal (unbalanced allowed)
	 */
	public static function saveDraft(array $data)
	{
		return Journal::create([
			'date' => $data['date'] ?? now(),
			'ledger_type_id' => $data['ledger_type_id'] ?? 1,
			'description' => $data['description'] ?? null,
			'status' => 'draft',
		]);
	}

	/** Post a draft journal if valid */
	public static function postDraft(Journal $journal): void
	{
		if ($journal->status !== 'draft') {
			throw new DomainException('Only draft journals can be posted.');
		}

		if (! $journal->isBalanced()) {
			throw new DomainException('Journal is not balanced.');
		}

		DB::transaction(function () use ($journal) {
			$journal->update([
				'status' => 'posted',
				'posted_at' => now(),
			]);

						// optional: lock entries if needed
			foreach ($journal->entries as $entry) {
				$entry->touch();
			}

						// audit log
			$journal->recordActivity('posted', [
				'status' => 'posted',
				'posted_at' => $journal->posted_at,
			]);
		});
	}

	public static function unpost(Journal $journal): void
	{
		if ($journal->status !== 'posted') {
			throw new \DomainException('Only posted journals can be unposted.');
		}

		// In future, add checks here (e.g., period locked, reconciled, etc.)
		\DB::transaction(function () use ($journal) {
			$journal->update([
				'status' => 'draft',
				'posted_at' => null,
			]);

				// Audit
			$journal->recordActivity('unposted', [
				'status' => 'draft',
				'posted_at' => null,
			]);
		});
	}

	public function postSalesInvoice($invoice)
	{
		if ($invoice->status === 'posted') return;

		// Debit AR, Credit Revenue
		$entries = [];
		$total = 0;
		foreach ($invoice->items as $item) {
			$entries[] = ['account_id' => $item->account_id, 'credit' => $item->amount, 'debit' => 0, 'memo' => $item->description];
			$total += $item->amount;
		}

		$accountsReceivable = Account::where('code', '1100')->first(); // or use configured AR account
		$entries[] = ['account_id' => $accountsReceivable->id, 'debit' => $total, 'credit' => 0, 'memo' => 'Accounts Receivable'];

		$journal = $this->createJournal('SalesInvoice', $invoice->id, $invoice->date, $entries);

		$invoice->update(['status' => 'posted', 'posted_at' => now()]);
		$invoice->logActivity('posted');
		return $journal;
	}

	public function postPurchaseBill($bill)
	{
		if ($bill->status === 'posted') return;

		// Debit Expense, Credit AP
		$entries = [];
		$total = 0;
		foreach ($bill->items as $item) {
			$entries[] = ['account_id' => $item->account_id, 'debit' => $item->amount, 'credit' => 0, 'memo' => $item->description];
			$total += $item->amount;
		}

		$accountsPayable = Account::where('code', '2100')->first(); // or use configured AP account
		$entries[] = ['account_id' => $accountsPayable->id, 'credit' => $total, 'debit' => 0, 'memo' => 'Accounts Payable'];

		$journal = $this->createJournal('PurchaseBill', $bill->id, $bill->date, $entries);

		$bill->update(['status' => 'posted', 'posted_at' => now()]);
		$bill->logActivity('posted');
		return $journal;
	}

	public function postReceivePayment($payment)
	{
		if ($payment->status === 'posted') return;

		$entries = [];
		$bankAccountId = $payment->account_id;
		$arAccount = Account::where('code', '1100')->first(); // Accounts Receivable

		$entries[] = [
			'account_id' => $bankAccountId,
			'debit' => $payment->amount,
			'credit' => 0,
			'memo' => 'Cash/Bank Received'
		];

		$entries[] = [
			'account_id' => $arAccount->id,
			'credit' => $payment->amount,
			'debit' => 0,
			'memo' => 'Customer Payment'
		];

		$journal = $this->createJournal('Payment', $payment->id, $payment->date, $entries);

		$payment->update(['status' => 'posted', 'posted_at' => now()]);
		$payment->logActivity('posted');

		// Optionally mark invoice as paid if full settlement
		if ($payment->source && $payment->source->total <= $payment->amount)
			$payment->source->update(['status' => 'paid']);

		return $journal;
	}

	public function postMakePayment($payment)
	{
		if ($payment->status === 'posted') return;

		$entries = [];
		$bankAccountId = $payment->account_id;
		$apAccount = Account::where('code', '2100')->first(); // Accounts Payable

		$entries[] = [
			'account_id' => $apAccount->id,
			'debit' => $payment->amount,
			'credit' => 0,
			'memo' => 'Supplier Payment'
		];

		$entries[] = [
			'account_id' => $bankAccountId,
			'credit' => $payment->amount,
			'debit' => 0,
			'memo' => 'Cash/Bank Outflow'
		];

		$journal = $this->createJournal('Payment', $payment->id, $payment->date, $entries);

		$payment->update(['status' => 'posted', 'posted_at' => now()]);
		$payment->logActivity('posted');

		if ($payment->source && $payment->source->total <= $payment->amount)
			$payment->source->update(['status' => 'paid']);

		return $journal;
	}

	/**
	 * Dummy offset logic — later replaced with proper mapping.
	 * For now, returns the first non-bank account for simplicity.
	 */
	protected function guessOffsetAccount(Payment $payment): int
	{
		return \App\Models\Accounting\Account::where('id', '<>', $payment->account_id)
		->whereNull('deleted_at')
		->value('id') ?? $payment->account_id;
	}

	/**
	 * Rebuild journal when payment is edited.
	 */
	public function rebuildPaymentJournal(Payment $payment): void
	{
		DB::transaction(function () use ($payment) {
			// If no journal yet, just create one
			if (!$payment->journal) {
				$this->recordPayment($payment);
				return;
			}

			// Delete old entries (soft delete if model uses SoftDeletes)
			$payment->journal->entries()->delete();

			// Re-create the journal entries for the current payment values
			$this->recordPayment($payment);
		});
	}

	/**
	 * Create a journal for any source (invoice, bill, payment, etc.)
	 */
	public function createJournal(string $sourceType, int $sourceId, array $entries, string $status = 'draft'): Journal
	{
		return DB::transaction(function () use ($sourceType, $sourceId, $entries, $status) {
			$totalDebit  = collect($entries)->sum('debit');
			$totalCredit = collect($entries)->sum('credit');

			$isBalanced = bccomp($totalDebit, $totalCredit, 2) === 0;

			if ($status === 'posted' && !$isBalanced) {
				throw new DomainException("Journal not balanced — cannot post.");
			}

			$journal = Journal::create([
				'date'         => now(),
				'reference_no' => strtoupper(substr(md5(uniqid('', true)), 0, 10)),
				'ledger_type_id' => 1,
				'source_type'  => $sourceType,
				'source_id'    => $sourceId,
				'status'       => $status,
				'posted_at'    => $status === 'posted' ? now() : null,
			]);

			foreach ($entries as $entry) {
				$journal->entries()->create($entry);
			}

			return $journal;
		});
	}

	/**
	 * Rebuild a journal — deletes and recreates entries safely.
	 */
	public function rebuildJournal(Journal $journal, array $entries, bool $post = true): void
	{
		DB::transaction(function () use ($journal, $entries, $post) {
			$journal->entries()->delete();

			$totalDebit  = collect($entries)->sum('debit');
			$totalCredit = collect($entries)->sum('credit');

			if ($post && bccomp($totalDebit, $totalCredit, 2) !== 0) {
				throw new DomainException('Rebuild failed: journal not balanced.');
			}

			foreach ($entries as $entry) {
				$journal->entries()->create($entry);
			}

			$journal->update([
				'status' => $post ? 'posted' : 'draft',
				'posted_at' => $post ? now() : null,
			]);
		});
	}

	/**
	 * Handle Sales Invoice journal posting.
	 */
	public function recordInvoice(SalesInvoice $invoice): Journal
	{
		$entries = [];

		foreach ($invoice->items as $item) {
			$entries[] = [
				'account_id' => $item->account_id,
				'debit'      => $item->amount,
				'credit'     => 0,
				'memo'       => $item->description ?? 'Sales Item',
			];
		}

		$entries[] = [
			'account_id' => config('accounting.accounts.sales_revenue'),
			'debit'      => 0,
			'credit'     => $invoice->subtotal,
			'memo'       => 'Sales Revenue',
		];

		return $this->createJournal(SalesInvoice::class, $invoice->id, $entries, 'posted');
	}

	/**
	 * Handle Purchase Bill journal posting.
	 */
	public function recordBill(PurchaseBill $bill): Journal
	{
		$entries = [];

		foreach ($bill->items as $item) {
			$entries[] = [
				'account_id' => $item->account_id,
				'debit'      => $item->amount,
				'credit'     => 0,
				'memo'       => $item->description ?? 'Purchase Item',
			];
		}

		$entries[] = [
			'account_id' => config('accounting.accounts.accounts_payable'),
			'debit'      => 0,
			'credit'     => $bill->total,
			'memo'       => 'Accounts Payable',
		];

		return $this->createJournal(PurchaseBill::class, $bill->id, $entries, 'posted');
	}

	/**
	 * Handle Payment journal posting.
	 */
	public function recordPayment(Payment $payment): Journal
	{
		$entries = [];

		$isReceive = $payment->type === 'receive';
		$isPay = $payment->type === 'pay';

		if ($isReceive) {
			$entries[] = [
				'account_id' => $payment->account_id,
				'debit'      => $payment->amount,
				'credit'     => 0,
				// 'memo'       => 'Cash/Bank Received',
				'description'       => 'Cash/Bank Received',
			];
			$entries[] = [
				'account_id' => config('accounting.accounts.accounts_receivable'),
				'debit'      => 0,
				'credit'     => $payment->amount,
				// 'memo'       => 'Accounts Receivable',
				'description'       => 'Accounts Receivable',
			];
		} elseif ($isPay) {
			$entries[] = [
				'account_id' => config('accounting.accounts.accounts_payable'),
				'debit'      => $payment->amount,
				'credit'     => 0,
				// 'memo'       => 'Accounts Payable',
				'description'       => 'Accounts Payable',
			];
			$entries[] = [
				'account_id' => $payment->account_id,
				'debit'      => 0,
				'credit'     => $payment->amount,
				// 'memo'       => 'Cash/Bank Paid',
				'description'       => 'Cash/Bank Paid',
			];
		}

		return $this->createJournal(Payment::class, $payment->id, $entries, 'posted');
	}

	/**
	 * Safety check to ensure journal is balanced.
	 */
	public function isBalanced(Journal $journal): bool
	{
		$debit  = $journal->entries->sum('debit');
		$credit = $journal->entries->sum('credit');
		return bccomp($debit, $credit, 2) === 0;
	}

}
