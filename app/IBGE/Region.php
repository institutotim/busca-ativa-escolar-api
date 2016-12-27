<?php
/**
 * busca-ativa-escolar-api
 * Region.php
 *
 * Copyright (c) LQDI Digital
 * www.lqdi.net - 2016
 *
 * @author Aryel Tupinambá <aryel.tupinamba@lqdi.net>
 *
 * Created at: 27/12/2016, 12:40
 */

namespace BuscaAtivaEscolar\IBGE;


class Region {

	private static $cached = [];

	private static $data = [
		1 => ['id' => 1, 'name' => 'NORTE', 'code' => 'NO', 'uf_ids' => [11, 12, 13, 14, 15, 16, 17]],
		2 => ['id' => 2, 'name' => 'NORDESTE', 'code' => 'NE', 'uf_ids' => [21, 22, 23, 24, 25, 26, 27, 28, 29]],
		3 => ['id' => 3, 'name' => 'SUDESTE', 'code' => 'SE', 'uf_ids' => [31, 32, 33, 35]],
		4 => ['id' => 4, 'name' => 'SUL', 'code' => 'SU', 'uf_ids' => [41, 42, 43]],
		5 => ['id' => 5, 'name' => 'CENTRO OESTE', 'code' => 'CO', 'uf_ids' => [50, 51, 52, 53]],
	];

	private static $indexByCode = [
		'NO' => 1,
		'NE' => 2,
		'SE' => 3,
		'SU' => 4,
		'CO' => 5,
	];

	const NORTE = 1;
	const NORDESTE = 2;
	const SUDESTE = 3;
	const SUL = 4;
	const CENTROOESTE = 5;


	/**
	 * Gets a region by it's unique ID
	 * @param integer $id The region ID
	 * @return Region
	 * @throws \Exception if ID is invalid
	 */
	public static function getByID($id) {
		$id = intval($id);

		if(isset(self::$cached[$id])) {
			return self::$cached[$id];
		}

		if(!isset(self::$data[$id])) {
			throw new \Exception("Invalid Region ID: {$id}");
		}

		return self::$cached[$id] = new Region(self::$data[$id]);
	}

	/**
	 * Gets a region by it's short code
	 * @param string $code The short region code (eg.: NE)
	 * @return Region
	 */
	public static function getByCode($code) {
		$id = self::$indexByCode[strtoupper(trim($code))];
		return self::getByID($id);
	}

	/**
	 * @var integer Unique numeric ID for the region
	 */
	public $id;

	/**
	 * @var string Region name (unicode)
	 */
	public $name;

	/**
	 * @var string Region short code (two characters, uppercase)
	 */
	public $code;

	/**
	 * @var UF[] List of UFs contained in region
	 */
	protected $ufs = null;

	/**
	 * @var integer[] List of UF IDs contained in region
	 */
	protected $uf_ids = [];

	/**
	 * Region constructor.
	 * @param array $data Expects the keys 'id', 'name' and 'code'
	 */
	public function __construct(array $data) {
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->code = $data['code'];
		$this->uf_ids = $data['uf_ids'];
	}

	/**
	 * Gets the list of UFs in this Region
	 * @return UF[]
	 */
	public function getUFs() {
		if($this->ufs !== null) {
			return $this->ufs;
		}

		$this->ufs = [];

		foreach($this->uf_ids as $ufid) {
			$this->ufs[$ufid] = UF::getByID($ufid);
		}

		return $this->ufs;
	}

}