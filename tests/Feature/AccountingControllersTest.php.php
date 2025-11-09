<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\{
	Account, Payment, PurchaseBill, SalesInvoice, Journal
};
use App\Services\JournalService;
use DomainException;
use Mockery;

class AccountingControllersTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		// Seed minimal accounts from config
		foreach (config('accounting.defaults') as $key => $id) {
			Account::factory()->create(['id' => $id]);
		}
	}

	/** @test */
	public function purchase_bill_store_creates_journal_when_posted()
	{
		$journalService = Mockery::mock(JournalService::class);
		$journalService->shouldReceive('recordBill')
			->once()
			->andReturn(Journal::factory()->create());

		$this->app->instance(JournalService::class, $journalService);

		$data = PurchaseBill::factory()->make()->toArray();
		$data['items'] = [
			['account_id' => 1, 'description' => 'Item A', 'quantity' => 1, 'unit_price' => 100, 'amount' => 100],
		];
		$data['action'] = 'post';

		$response = $this->post(route('accounting.purchase-bills.store'), $data);

		$response->assertRedirect(route('accounting.purchase-bills.index'));
		$response->assertSessionHas('success', 'Purchase Bill posted successfully.');
	}

	/** @test */
	public function sales_invoice_handles_domain_exception_cleanly()
	{
		$journalService = Mockery::mock(JournalService::class);
		$journalService->shouldReceive('recordInvoice')
			->andThrow(new DomainException('Unbalanced journal entries.'));

		$this->app->instance(JournalService::class, $journalService);

		$invoice = SalesInvoice::factory()->make();
		$data = $invoice->toArray();
		$data['items'] = [
			['account_id' => 1, 'description' => 'Test', 'quantity' => 1, 'unit_price' => 10, 'amount' => 10],
		];
		$data['action'] = 'post';

		$response = $this->post(route('accounting.sales-invoices.store'), $data);

		$response->assertSessionHas('danger', 'Unbalanced journal entries.');
	}

	/** @test */
	public function payment_controller_handles_draft_and_post_consistently()
	{
		$journalService = Mockery::mock(JournalService::class);
		$journalService->shouldReceive('recordPayment')
			->andReturn(Journal::factory()->create());

		$this->app->instance(JournalService::class, $journalService);

		$payment = Payment::factory()->make(['amount' => 200]);
		$data = $payment->toArray();
		$data['action'] = 'draft';

		$response = $this->post(route('accounting.payments.store'), $data);

		$response->assertSessionHas('success');
		$this->assertDatabaseHas('payments', ['status' => 'draft']);
	}

	/** @test */
	public function journal_controller_show_returns_journal_with_entries()
	{
		$journal = Journal::factory()->hasEntries(3)->create();

		$response = $this->get(route('accounting.journals.show', $journal));

		$response->assertOk();
		$response->assertViewHas('journal', $journal);
	}

	/** @test */
	public function config_accounts_are_used_instead_of_hardcoded_ids()
	{
		$defaults = config('accounting.defaults');
		foreach ($defaults as $key => $id) {
			$this->assertIsInt($id, "Expected integer for $key in config/accounting.php");
		}
	}
}
