<?php
/**
 * Application Configuration
 *
 * Global constants and session settings for the
 * Aqua B Water Refilling Station system.
 */

// ----- Application Info -----
define('APP_NAME',    'Aqua B Water Refilling Station');
define('APP_VERSION', '1.0.0');

// ----- Base URL (auto-detect) -----
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($script, '/\\');

define('BASE_URL', "{$protocol}://{$host}{$basePath}");

// ----- Session Configuration -----
ini_set('session.cookie_httponly', 1);   // Prevent JavaScript access to session cookie
ini_set('session.use_only_cookies', 1);  // Only use cookies for session ID
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_secure', $isHttps ? 1 : 0);
ini_set('session.gc_maxlifetime', 1800); // 30-minute session lifetime
