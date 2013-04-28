<?php

require_once('../../src/tms/tag.php');

// 导入数据文件
_tms_import('./import.json');

$test = _tms_text('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

echo file_get_contents('./import.json');