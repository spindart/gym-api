<?php

// Localhost

// define('DB_HOST', 'localhost');
// define('DB_NAME', 'tecnofit');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_PORT', '3306');

//Docker

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'tecnofit');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'root');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
