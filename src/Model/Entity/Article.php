<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Article extends Entity {

	use \App\Model\Entity\DatatableTrait;

	// Make all fields mass assignable for now.
	protected $_accessible = ['*' => true];

}
