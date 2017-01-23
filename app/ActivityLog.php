<?php
/**
 * busca-ativa-escolar-api
 * ActivityLog.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2017
 *
 * @author Aryel Tupinambá <aryel.tupinamba@lqdi.net>
 *
 * Created at: 19/01/2017, 14:10
 */

namespace BuscaAtivaEscolar;


use BuscaAtivaEscolar\Traits\Data\IndexedByUUID;
use BuscaAtivaEscolar\Traits\Data\TenantScopedModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model {

	use SoftDeletes;
	use IndexedByUUID;
	use TenantScopedModel;

	protected $table = "activity_log";
	protected $fillable = [
		'tenant_id',
		'user_id',
		'content_type',
		'content_id',
		'action',
		'parameters',
		'metadata',
	];

	protected $casts = [
		'parameters' => 'object',
		'metadata' => 'object',
	];

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * The tenant that owns this log entry. May be null if the content object is not tenant-scoped.
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function tenant() {
		return $this->hasOne('BuscaAtivaEscolar\Tenant', 'id', 'tenant_id');
	}

	/**
	 * The user who performed the action this entry log refers to.
	 * May be null if the activity was performed by a command/bot/etc.
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function user() {
		return $this->hasOne('BuscaAtivaEscolar\User', 'id', 'user_id');
	}

	/**
	 * The content this log entry is attached to. Is an instance of Eloquent's Model. May or may not be tenant-scoped.
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
	 */
	public function content() {
		return $this->morphTo('content');
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Scope: orders by date, descending
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeOrdered($query) {
		return $query->orderBy('created_at', 'DESC');
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * Registers an activity in the activity log
	 * @param Model $content The target content the activity was performed on
	 * @param string $action The code of the activity that was performed. All similar actions must share the action code.
	 * @param array $parameters The parameters of the action, such as in what context it happened, with what other entities, etc.
	 * @param array $metadata Any relevant action metadata (current environment, ip address, etc)
	 * @return ActivityLog The created activity log entry
	 */
	public static function writeEntry(Model $content, $action, $parameters = [], $metadata = []) {
		$entry = new ActivityLog();
		$entry->tenant_id = $content->tenant_id ?? null;
		$entry->content_type= get_class($content);
		$entry->content_id = $content->id;
		$entry->action = $action;
		$entry->parameters = $parameters;
		$entry->metadata = $metadata;
		$entry->save();

		// TODO: register on ElasticSearch?

		return $entry;
	}

}