<?php

require_once('../../src/tms/tag.php');

_tms_repeat_begin('{"name":"repeat","title":"test","group":"test","row":"2"}');

$text = _tms_text('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

var_dump($text);

_tms_repeat_end();