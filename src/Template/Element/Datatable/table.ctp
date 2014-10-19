<?php
// Requires a `$columns`
?>
<table class="dataTable display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<?php foreach ($columns as $column) : ?>
				<th><?= $column ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<?php foreach ($columns as $column) : ?>
				<th><?= $column ?></th>
			<?php endforeach; ?>
		</tr>
	</tfoot>
</table>
