<?php
/**
 * router.php — PHP built-in dev-server router
 *
 * Mimics the Apache .htaccess rewrite rule:
 *   RewriteRule ^(.*)$ public/index.php [QSA,L]
 *
 * Usage:
 *   php -S localhost:8000 router.php
 *
 * Any real file that exists under the project root is served directly
 * (e.g. public/css, public/js). Everything else is handed to
 * public/index.php (the front controller).
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly (from the public/ sub-folder)
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    return false; // Let the built-in server handle it
}

// Everything else → front controller
require __DIR__ . '/public/index.php';
