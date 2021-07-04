<?php

namespace ACA\ACF\Sorting;

use ACP;

class Repeater extends ACP\Sorting\AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		$ids = $this->strategy->get_results( [
			'meta_key' => $this->meta_key,
			'orderby'  => 'meta_value_num',
		] );

		return [
			'ids' => $ids,
		];
	}

}