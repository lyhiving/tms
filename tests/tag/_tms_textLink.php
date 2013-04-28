<?php

require_once('../../src/tms/tag.php');

$textLink = _tms_textLink('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

var_dump($textLink);