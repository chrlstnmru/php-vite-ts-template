<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

$env = Dotenv::createImmutable(__DIR__ . '/../../');
$env->safeLoad();

$viteHost = 'http://';
$viteHost .= $_ENV['VITE_HOSTNAME'] ?? 'localhost';
$viteHost .= ':' . ($_ENV['VITE_PORT'] ?? '5173');

define('VITE_HOSTNAME', $viteHost);
define('BUILD_DIR', '/dist');

unset($viteHost);
