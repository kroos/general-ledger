<?php
namespace App\Services\Accounting;

use App\Models\Accounting\{
Account, Journal, JournalEntry, Payment, SalesInvoice, PurchaseBill
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
	 * Create a general journal entry
	 */
	public function createJournal(array $data)
	{
		return DB::transaction(function () use ($data) {
			return Journal::create($data);
		});
	}

	/**
	 * Record a Purchase Bill
	 */
	public function recordBill($bill)
	{
		try {
			DB::transaction(function () use ($bill) {
				$journal = $this->createJournal([
					'ledger_type_id' => 3,
					'date' => $bill->date,
					'reference_no' => $bill->reference_no,
					'description' => "Purchase Bill #{$bill->reference_no}",
				]);

							// Debit expense, credit cash or accounts payable
				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.expense_account'),
					'debit' => $bill->subtotal,
					'credit' => 0,
				]);

				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.cash_account'),
					'debit' => 0,
					'credit' => $bill->total,
				]);
			});

		} catch (Exception $e) {
			Log::error("Failed to record purchase bill journal: " . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Record a Sales Invoice
	 */
	public function recordInvoice($invoice)
	{
		try {
			DB::transaction(function () use ($invoice) {
				$journal = $this->createJournal([
					'ledger_type_id' => 2,
					'date' => $invoice->date,
					'reference_no' => $invoice->reference_no,
					'description' => "Sales Invoice #{$invoice->reference_no}",
				]);

				// Debit cash, credit revenue
				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.cash_account'),
					'debit' => $invoice->amount,
					'credit' => 0,
				]);

				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.revenue_account'),
					'debit' => 0,
					'credit' => $invoice->subtotal,
				]);
			});

		} catch (Exception $e) {
			Log::error("Failed to record invoice journal: " . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Record a Payment (Cash Out)
	 */
	public function recordPayment($payment)
	{
		try {
			DB::transaction(function () use ($payment) {
				$journal = $this->createJournal([
					'ledger_type_id' => ($payment->type == 'make')?3:2,
					'date' => $payment->date,
					'reference_no' => $payment->reference_no,
					'description' => "Payment #{$payment->reference_no}",
				]);

				// Debit liability, credit cash
				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => $payment->account_id,
					'debit' => $payment->amount,
					'credit' => 0,
				]);

				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.cash_account'),
					'debit' => 0,
					'credit' => $payment->amount,
				]);
			});

		} catch (Exception $e) {
			Log::error("Failed to record payment journal: " . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Record a Receipt (Cash In)
	 */
	public function recordReceipt($receipt)
	{
		try {
			DB::transaction(function () use ($receipt) {
				$journal = $this->createJournal([
					'date' => $receipt->date,
					'reference_no' => $receipt->reference_no,
					'description' => "Receipt #{$receipt->reference_no}",
				]);

							// Debit cash, credit accounts receivable
				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.cash_account'),
					'debit' => $receipt->amount,
					'credit' => 0,
				]);

				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => $receipt->account_id,
					'debit' => 0,
					'credit' => $receipt->amount,
				]);
			});

		} catch (Exception $e) {
			Log::error("Failed to record receipt journal: " . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Record a Tax Transaction
	 */
	public function recordTax($model, $amount)
	{
		try {
			DB::transaction(function () use ($model, $amount) {
				$journal = $this->createJournal([
					'date' => $model->date,
					'reference_no' => $model->reference_no,
					'description' => "Tax entry for #{$model->reference_no}",
				]);

				JournalEntry::create([
					'journal_id' => $journal->id,
					'account_id' => config('accounting.defaults.journal.tax_account'),
					'debit' => $amount,
					'credit' => 0,
				]);
			});

		} catch (Exception $e) {
			Log::error("Failed to record tax journal: " . $e->getMessage());
			throw $e;
		}
	}
}
