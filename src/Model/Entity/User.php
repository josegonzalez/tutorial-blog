<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

class User extends Entity {

	// Make all fields mass assignable for now.
	protected $_accessible = ['*' => true];

	// ...

	protected function _setPassword($password) {
		return (new DefaultPasswordHasher)->hash($password);
	}

	// ...
}
