<?= $this->element('Datatable/css') ?>
<?= $this->element('Datatable/scripts') ?>
<?= $this->element('Datatable/js', [
	'url' => ['action' => $this->request->action],
	'columns' => $columns
]) ?>

<?= $this->element('Datatable/table') ?>
