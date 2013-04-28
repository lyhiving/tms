<?php

require_once('../../src/tms/tag.php');

$autoExtract = _tms_autoExtract('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2","fields":"test:测试:text"}');

var_dump($autoExtract);