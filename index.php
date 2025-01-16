<?php

use Utils\DataView;

require_once 'Utils/DataView.php';

$arr = ['data'=>1, "ad"=>5];

$arr = array_merge($arr, ['test'=>4, 'a'=>2]);
// $arr = [...$arr, ...["test"=>4, "a"=>2]];
var_dump($arr);