#!/usr/bin/env php
<?php
/**
 *  Ethna Command
 *
 */

$binDir = __DIR__; // 'vendor/bin' or 'ethnam-generator/bin'

require_once  $binDir . '/../autoload.php';

$handle = new Ethna_Command();
$handle->run();
