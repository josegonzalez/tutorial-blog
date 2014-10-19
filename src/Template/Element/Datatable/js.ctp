<?php
// Requires `$url` and `$columns` variables. If unspecified, `$datatableColumns` will be generated from `$columns`

if (empty($url)) {
	$url = ['action' => $this->request->action];
}
if (is_array($url)) {
	$url = $this->Url->build($url);
}

$url = json_encode($url);
if (empty($datatableColumns)) {
	$datatableColumns = json_encode(array_map(function($column) {
		return ['data' => $column];
	}, $columns));
}

$script = <<<EOD
$(document).ready(function() {
$(".dataTable").dataTable( {
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url": ${url},
		"type": "POST"
	},
	"columns": ${datatableColumns}
});
});
EOD;
$this->Html->scriptBlock($script, ['block' => true, 'safe' => false]);
