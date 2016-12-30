<?php
/**
 * busca-ativa-escolar-api
 * CaseStep.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2016
 *
 * @author Aryel Tupinambá <aryel.tupinamba@lqdi.net>
 *
 * Created at: 29/12/2016, 13:22
 */

namespace BuscaAtivaEscolar\CaseSteps;

use BuscaAtivaEscolar\ChildCase;
use BuscaAtivaEscolar\Tenant;
use BuscaAtivaEscolar\Traits\Data\IndexedByUUID;
use BuscaAtivaEscolar\Traits\Data\TenantScopedModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class CaseStep extends Model {

	use SoftDeletes;
	use IndexedByUUID;
	use TenantScopedModel;

	protected $baseFillable = [
		'child_id',
		'case_id',
		'step_type',
		'is_completed',
	];

	public $stepFields = [];

	public function __construct(array $attributes = []) {
		$this->fillable = array_merge($this->baseFillable, $this->stepFields);
		$this->incrementing = false; // This is already defined by IndexedByUUID, but we're overriding __construct here

		parent::__construct($attributes);
	}

	public function getRouteKeyName() {
		return 'id';
	}

	public function child() {
		return $this->hasOne('BuscaAtivaEscolar\Child', 'id', 'child_id');
	}

	public function childCase() {
		return $this->hasOne('BuscaAtivaEscolar\ChildCase', 'id', 'case_id');
	}

	public static function generate(Tenant $tenant, ChildCase $case, string $class, array $data) {
		$data['tenant_id'] = $tenant->id;
		$data['case_id'] = $case->id;
		$data['child_id'] = $case->child_id;

		$data['step_type'] = "BuscaAtivaEscolar\\CaseSteps\\{$class}";

		$data['is_completed'] = false;

		return ($data['step_type'])::create($data);
	}

}