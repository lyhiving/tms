<?php

require_once('../../src/tms/tag.php');

$imageLink = _tms_imageLink('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

var_dump($imageLink);