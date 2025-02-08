<?php

require '../vendor/autoload.php';

ob_start();
$openapi = \OpenApi\Generator::scan([__DIR__ . '/../src']);
ob_end_clean();

header('Content-Type: application/json');
echo $openapi->toJson(); 