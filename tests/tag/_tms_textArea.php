<?php

require_once('../../src/tms/tag.php');

$textArea = _tms_textArea('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

var_dump($textArea);