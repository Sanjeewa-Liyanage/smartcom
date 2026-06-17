<?php
/**
 * Application Configuration
 * ─────────────────────────────────────────────
 * BASE_PATH: the URL sub-directory where the app lives.
 *   • '/comclz'  → http://localhost/comclz/
 *   • ''         → http://localhost/  (virtual host root)
 */

// ── URL ───────────────────────────────────────────────────────────────────────
define('BASE_PATH', '/comclz');
define('BASE_URL',  'http://localhost' . BASE_PATH);

// ── JWT ───────────────────────────────────────────────────────────────────────
// IMPORTANT: Change JWT_SECRET to a long, random string before deploying!
define('JWT_SECRET', 'SCC_2025_@#SuperSecret!ChangeThisInProduction#@_SmartCommerceCore');
define('JWT_EXPIRY', 60 * 60 * 8);   // 8 hours in seconds

// ── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'scc_session');
define('SESSION_LIFETIME', 60 * 60 * 8);  // 8 hours in seconds

// ── Application ───────────────────────────────────────────────────────────────
define('APP_NAME',    'Smart Commerce Core');
define('APP_VERSION', '1.0.0');
define('APP_ENV',     'development');  // 'development' | 'production'
