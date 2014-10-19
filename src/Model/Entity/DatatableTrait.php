<?php

namespace App\Model\Entity;

/**
 * Class DatatableTrait
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
trait DatatableTrait {

	public function toDatatable($columns = null) {
		$data = $this->toArray();
		if ($columns === null) {
			return $data;
		}

		$_data = [];
		foreach ($data as $key => $value) {
			if (in_array($key, $columns)) {
				$_data[$key] = $value;
			}
		}

		return $_data;
	}

}
