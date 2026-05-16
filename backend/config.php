<?php
/**
 * Finesse — Application Configuration
 * Place this file at:  Aatif/backend/config.php
 *
 * ⚠️  NEVER commit real API keys to version control.
 *     In production, load secrets from environment variables.
 */

function env_str(string $key, string $default = ''): string
{
    $v = getenv($key);
    if ($v === false) {
        return $default;
    }
    $v = trim((string)$v);
    return $v !== '' ? $v : $default;
}

/* ── Database ─────────────────────────────────────────────────── */
define('DB_HOST', env_str('DB_HOST', 'localhost'));
define('DB_NAME', env_str('DB_NAME', 'finesse_db'));
define('DB_USER', env_str('DB_USER', 'root'));
define('DB_PASS', env_str('DB_PASS', ''));

// Optional TLS/SSL settings for MySQL (recommended in production).
define('DB_SSL_CA',   env_str('DB_SSL_CA', ''));     // e.g. C:\path\to\ca.pem
define('DB_SSL_CERT', env_str('DB_SSL_CERT', ''));   // e.g. C:\path\to\client-cert.pem
define('DB_SSL_KEY',  env_str('DB_SSL_KEY', ''));    // e.g. C:\path\to\client-key.pem
define('DB_SSL_VERIFY', env_str('DB_SSL_VERIFY', '1')); // '1' verify, '0' don't (avoid disabling)

/* ── Site / Upload paths ──────────────────────────────────────── */
define('SITE_URL',    env_str('SITE_URL', 'http://localhost/finesse'));
define('UPLOAD_DIR',  __DIR__ . '/../frontend/assets/uploads/');
define('UPLOAD_URL',  SITE_URL . '/frontend/assets/uploads/');

if (!file_exists(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}

/* ── External APIs ────────────────────────────────────────────── */

/**
 * OpenWeatherMap API key (optional).
 * Get yours free at: https://openweathermap.org/api
 * Leave empty to use the built-in mock weather data.
 */
define('WEATHER_API_KEY', env_str('WEATHER_API_KEY', ''));   // e.g. 'abc123def456...'

/* ── Google reCAPTCHA (optional) ─────────────────────────────── */
// To enable, create keys at https://www.google.com/recaptcha/admin/create
// Use reCAPTCHA v2 "I'm not a robot" (recommended for this project).
define('RECAPTCHA_SITE_KEY', env_str('RECAPTCHA_SITE_KEY', ''));    // e.g. '6Lc...'
define('RECAPTCHA_SECRET_KEY', env_str('RECAPTCHA_SECRET_KEY', ''));  // e.g. '6Lc...'