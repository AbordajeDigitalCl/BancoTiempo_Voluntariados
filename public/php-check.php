<?php

header('Content-Type: text/plain');

echo 'PHP SAPI: ' . PHP_SAPI . PHP_EOL;
echo 'Loaded php.ini: ' . (php_ini_loaded_file() ?: 'none') . PHP_EOL;
echo 'pdo_mysql loaded: ' . (extension_loaded('pdo_mysql') ? 'yes' : 'no') . PHP_EOL;
echo 'PHP extension directory: ' . ini_get('extension_dir') . PHP_EOL;