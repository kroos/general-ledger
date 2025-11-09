<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Services\JournalService;
use App\Models\{
	Journal, JournalEntry, PurchaseBill, SalesInvoice, Payment, Account
};
use DomainException;

class JournalServiceTest extends TestCase
{
	use RefreshDatabase;

	protected JournalService $service;

	protected function setUp(): void
	{
		parent::setUp();

		$this->service = app(JournalService::class);

		// Seed accounts based on config/accounting.php
		foreach (config('accounting.defaults') as $key => $id) {
			Account::factory()->create(['id' => $id]);
		}
	}

	/** @test */
	public function it_creates_a_balanced_journal_for_purchase_bill()
	{
		$bill = PurchaseBill::factory()->create(['total_amount' => 200]);

		$journal = $this->service->recordBill($bill);

		$this->assertInstanceOf(Journal::class, $journal);
		$this->assertEquals($bill->id, $journal->journalable_id);
		$this->assertEquals(PurchaseBill::class, $journal->journalable_type);

		$entries = $journal->entries;
		$this->assertCount(2, $entries);

		$this->assertEquals(
			$entries->sum('debit'),
			$entries->sum('credit'),
			'Journal entries should be balanced.'
		);
	}

	/** @test */
	public function it_creates_a_balanced_journal_for_sales_invoice()
	{
		$invoice = SalesInvoice::factory()->create(['total_amount' => 300]);

		$journal = $this->service->recordInvoice($invoice);

		$entries = $journal->entries;

		$this->assertTrue($entries->sum('debit') === $entries->sum('credit'));
		$this->assertEquals($invoice->id, $journal->journalable_id);
	}

	/** @test */
	public function it_throws_domain_exception_when_unbalanced()
	{
		$this->expectException(DomainException::class);
		$this->expectExceptionMessage('Journal entries are not balanced.');

		DB::beginTransaction();

		// Intentionally imbalance the data
		$entries = [
			['account_id' => 1, 'debit' => 100, 'credit' => 0],
			['account_id' => 2, 'debit' => 0, 'credit' => 50],
		];

		$this->service->createJournal(
			PurchaseBill::class,
			1,
			$entries,
			'posted'
		);
	}

	/** @test */
	public function it_rolls_back_transaction_on_exception()
	{
		DB::shouldReceive('rollBack')->once();
		DB::shouldReceive('beginTransaction')->once();

		try {
			$this->service->createJournal(
				PurchaseBill::class,
				1,
				[['account_id' => 1, 'debit' => 100, 'credit' => 0]],
				'posted'
			);
		} catch (DomainException $e) {
			$this->assertStringContainsString('not balanced', $e->getMessage());
		}
	}

	/** @test */
	public function it_uses_configured_default_accounts()
	{
		$defaults = config('accounting.defaults');
		$this->assertIsArray($defaults);

		$this->assertArrayHasKey('cash_account_id', $defaults);
		$this->assertArrayHasKey('sales_revenue_id', $defaults);
		$this->assertArrayHasKey('accounts_receivable_id', $defaults);
		$this->assertArrayHasKey('accounts_payable_id', $defaults);
	}

	/** @test */
	public function it_creates_entries_with_correct_relationships()
	{
		$bill = PurchaseBill::factory()->create(['total_amount' => 500]);

		$journal = $this->service->recordBill($bill);

		$entry = $journal->entries()->first();

		$this->assertNotNull($entry->account);
		$this->assertEquals($journal->id, $entry->journal_id);
	}
}
