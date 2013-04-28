<?php

require_once('../../src/tms/tag.php');

$custom = _tms_custom('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2","fields":"test:测试:text"}');

var_dump($custom);