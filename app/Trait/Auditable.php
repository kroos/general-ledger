<?php
namespace App\Trait;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
	public static function bootAuditable()
	{
		static::created(fn(Model $m) => $m->logActivity('created'));

		static::updating(fn(Model $m) => $m->auditOldAttributes = $m->getOriginal());

		static::updated(function (Model $m) {
    $m->logActivity('updated', $m->auditOldAttributes ?? []);
    unset($m->auditOldAttributes);
});

		static::deleted(function (Model $m) {
			$event = $m->isForceDeleting() ? 'force_deleted' : 'deleted';
			$m->logActivity($event);
		});

		static::restored(fn(Model $m) => $m->logActivity('restored'));
	}

	protected function getAuditExclude(): array
	{
		return property_exists(static::class, 'auditExclude')
		? static::$auditExclude
		: ['password', 'remember_token'];
	}

	protected function filteredAttributes(array $attrs): array
	{
		foreach ($this->getAuditExclude() as $key) unset($attrs[$key]);
		return $attrs;
	}

	protected function computeDiff(array $before, array $after): array
	{
		$diff = [];
		foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $k) {
			$b = $before[$k] ?? null;
			$a = $after[$k] ?? null;
			if ($b !== $a) $diff[$k] = ['before' => $b, 'after' => $a];
		}
		return $diff;
	}

	protected function shouldIncludeSnapshot(): bool
	{
		return property_exists(static::class, 'auditIncludeSnapshot')
		&& static::$auditIncludeSnapshot === true;
	}

	protected function isEventCritical(string $event): bool
	{
		$critical = property_exists(static::class, 'auditCriticalEvents')
		? static::$auditCriticalEvents
		: ['deleted','force_deleted','posted','voided'];
		return in_array($event, $critical);
	}

	public function logActivity(string $event, array $before = [])
	{
		try {
			$after = $this->filteredAttributes($this->getAttributes());
			$before = $this->filteredAttributes($before ?: $this->getOriginal());
			$diff = $this->computeDiff($before, $after);

			$req = null;
			try { $req = Request::instance(); } catch (\Throwable) {}

			ActivityLog::create([
				'user_id'     => Auth::id(),
				'event'       => $event,
				'model_type'  => static::class,
				'model_id'    => $this->getKey(),
				'route_name'  => $req?->route()?->getName(),
				'method'      => $req?->getMethod(),
				'url'         => $req?->fullUrl(),
				'ip_address'  => $req?->ip(),
				'user_agent'  => $req?->header('User-Agent'),
				'guard'       => auth()->getDefaultDriver() ?? null,
				'is_critical' => $this->isEventCritical($event),
				'description' => sprintf('%s %s (%s)',
				                         class_basename(static::class),
				                         $event,
				                         $this->getKey() ?? '-'
				                       ),
				'changes'     => $diff ?: null,
				'snapshot'    => $this->shouldIncludeSnapshot() ? $after : null,
			]);
		} catch (\Throwable $e) {
			Log::error('Audit log failed: '.$e->getMessage(), [
				'model'=>static::class,'id'=>$this->getKey(),'event'=>$event,
			]);
		}
	}

	public function __set($key, $value)
	{
    // prevent accidental persistence of temp audit properties
		if (in_array($key, ['oldAttributesForAudit', 'auditOldAttributes'])) {
			$this->$key = $value;
			return;
		}

		parent::__set($key, $value);
	}

	public function recordActivity(string $event, array $before = [])
	{
		return $this->logActivity($event, $before);
	}


}
