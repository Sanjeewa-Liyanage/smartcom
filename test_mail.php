<?php
require 'vendor/autoload.php';
require 'config/app.php';
require 'app/Core/Mailer.php';

try {
    \App\Core\Mailer::send('test@example.com', 'Test User', 'Test Subject', '<p>Test</p>');
    echo 'Success';
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
