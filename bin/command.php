#!/usr/bin/env php
<?php
/**
 *  Ethna Command
 *
 */
use Ethnam\Generator\Command;
$binDir = __DIR__; // 'ethnam-generator/bin'


//require_once  $binDir . '/../autoload.php';

// 'vendor/autoload.php';
require_once  $binDir . '/../../../autoload.php';

$handle = new Command;
$handle->run();
