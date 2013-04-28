<?php

require_once('../../src/tms/tag.php');

$articleList = _tms_articleList('{"name":"test","title":"test","group":"test","row":"2","defaultRow":"2"}');

var_dump($articleList);