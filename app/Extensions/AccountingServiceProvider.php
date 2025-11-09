<?php

namespace App\Extensions;

use Illuminate\Support\ServiceProvider;
use App\Services\Accounting\JournalService;

class AccountingServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton(JournalService::class, function () {
			return new JournalService();
		});
	}

	public function boot(): void
	{
		$this->publishes([
			__DIR__ . '/../../config/accounting.php' => config_path('accounting.php'),
		], 'config');
	}
}
