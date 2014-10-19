<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Component\PaginatorComponent;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;

class DatatablePaginatorComponent extends PaginatorComponent {
	public $components = ['Paginator'];

	public function implementedEvents() {
		return [
			'Controller.beforeRender' => 'beforeRender',
		];
	}

	public function beforeRender() {
		$Controller = $this->_registry->getController();
		$request = $Controller->request;
		if (!$request->is('ajax')) {
			return;
		}

		$_serialize = Hash::get($Controller->viewVars, '_serialize', []);
		$_serialize = array_merge($_serialize, ['draw', 'recordsFiltered', 'recordsTotal']);
		$Controller->set(['_serialize' => $_serialize]);
	}

	public function paginate($object, array $settings = []) {
		$request = $this->_registry->getController()->request;
		if (!$request->is('ajax')) {
			return parent::paginate($object, $settings);
		}

		$_object = $object;
		if ($_object instanceof Query) {
			$query = $_object;
			$_object = $_query->repository();
		}

		$alias = $_object->alias();
		$options = $this->mergeOptions($alias, $settings);
		$options = $this->validateSort($_object, $options);
		$options = $this->checkLimit($options);

		$options += ['page' => 1];
		$options['page'] = (int)$options['page'] < 1 ? 1 : (int)$options['page'];
		list($finder, $options) = $this->_extractFinder($options);

		$columns = Hash::get($options, 'columns', null);
		if (empty($columns)) {
			$columns = $_object->schema()->columns();
		}

		$settings = $this->processDatatableData(
			$request->data,
			$columns,
			$options['limit']
		);

		if (empty($query)) {
			$query = $object->find($finder, $options);
		}

		$recordsTotal = $query->count();

		if (isset($settings['search']) && $settings['search'] !== null) {
			$searchField = Hash::get($options, 'searchField', 'id');
			$query->where([
				sprintf('%s LIKE', $searchField) => '%' . $settings['search'] . '%'
			]);
		}

		$results = parent::paginate($query, $settings);
		$this->_registry->getController()->set([
			'columns' => $columns,
			'draw' => (int)$request->data('draw'),
			'recordsFiltered' => $request['paging'][$alias]['count'],
			'recordsTotal' => $recordsTotal
		]);

		return $this->toDatatable($results, $columns);
	}

	public function processDatatableData(array $data, array $columns, $maxLimit) {
		$limit = Hash::get($data, 'length', $maxLimit);
		$offset = Hash::get($data, 'start', 0);
		$order = $this->processDatatableOrder($data, $columns);

		if ($limit > $maxLimit) {
			$limit = $maxLimit;
		}

		$page = 1;
		if ($offset > 0) {
			$page = ($offset / $limit) + 1;
		}

		return [
			'limit' => $limit,
			'page' => $page,
			'order' => $order,
			'search' => Hash::get($data, 'search.value', null),
		];
	}

	public function processDatatableOrder(array $data, array $columns) {
		$sortColumns = Hash::extract($data, 'order.{n}.column');
		$sortOrders = Hash::extract($data, 'order.{n}.dir');
		if (empty($sortColumns)) {
			$sortColumns = [0];
			$sortOrders = ['asc'];
		}

		$_sortColumns = [];
		foreach ($sortColumns as $sortColumn) {
			$_sortColumns[] = $columns[$sortColumn];
		}

		return array_combine($_sortColumns, $sortOrders);
	}

	public function toDatatable(ResultSet $entities, array $columns) {
		$iterator = $entities->map(function($entity) use ($columns) {
			if (method_exists($entity, 'toDatatable')) {
				return $entity->toDatatable($columns);
			}
			return $entity->toArray();
		});
		return iterator_to_array($iterator);
	}

}
