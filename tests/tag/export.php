<?php

require_once('../../src/tms/tag.php');

$test = _tms_text('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

// 导出数据文件
_tms_export('./export.json');

echo file_get_contents('./export.json');