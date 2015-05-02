#!/usr/bin/env php
<?php
/**
 *  Ethna Command
 *
 */
use Ethnam\Generator\Command;
$binDir = __DIR__; // 'vendor/bin' or 'ethnam-generator/bin'

require_once  $binDir . '/../autoload.php';

$handle = new Command;
$handle->run();
