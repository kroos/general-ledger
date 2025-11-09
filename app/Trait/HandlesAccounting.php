<?php

namespace App\Traits;

use App\Services\Accounting\JournalService;
use Illuminate\Support\Facades\Log;

trait HandlesAccounting
{
	protected JournalService $journalService;

	public function initializeHandlesAccounting()
	{
		$this->journalService = app(JournalService::class);
	}

	protected function safeRecord(callable $callback, string $context)
	{
		try {
			return $callback();
		} catch (\Throwable $e) {
			Log::error("Accounting error in {$context}: " . $e->getMessage());
			report($e);
		}
	}
}
