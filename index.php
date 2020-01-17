<?php
ini_set('display_errors', 'on');
require __DIR__ . '/vendor/autoload.php';
include "config.php";

header('Content-Type: text/html; charset=utf-8');
$my = new \YourResult\Crud($db);