<?php

require_once('../../src/tms/tag.php');

_tms_module_begin('{"name":"module"}');

$text = _tms_text('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

_tms_module_end();

var_dump($text);