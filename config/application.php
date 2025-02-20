<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

// USE_ENV_ARRAY + CONVERT_* + STRIP_QUOTES
Env\Env::$options = 31;

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname(__DIR__);

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
if (file_exists($root_dir . '/.env')) {
    $env_files = file_exists($root_dir . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];

    $dotenv = Dotenv\Dotenv::createImmutable($root_dir, $env_files, false);

    $dotenv->load();

    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

/**
 * Infer WP_ENVIRONMENT_TYPE based on WP_ENV
 */
if (!env('WP_ENVIRONMENT_TYPE') && in_array(WP_ENV, ['production', 'staging', 'development', 'local'])) {
    Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
}

/**
 * URLs
 */
if (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
$_server_http_host_scheme = array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$_server_http_host_name   = array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : (env('WP_DEFAULT_HTTP_HOST') ?: 'localhost:8080');
$_server_http_url         = "$_server_http_host_scheme://$_server_http_host_name";
Config::define('WP_HOME', env('WP_HOME') ?: "$_server_http_url");
Config::define('WP_SITEURL', env('WP_SITEURL') ?: "$_server_http_url/wp");

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

/**
 * DB settings
 */
if (env('DB_SSL')) {
    Config::define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

Config::define('DB_NAME', env('DB_NAME'));
Config::define('DB_USER', env('DB_USER'));
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

if (env('DATABASE_URL')) {
    $dsn = (object) parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
    Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

/**
 * Redis settings
 */
Config::define('WP_REDIS_DISABLED', env('WP_REDIS_DISABLED'));
Config::define('WP_REDIS_HOST', env('WP_REDIS_HOST') ?: '127.0.0.1');
Config::define('WP_REDIS_PORT', env('WP_REDIS_PORT') ?: '6379');
Config::define('WP_REDIS_DATABASE', env('WP_REDIS_DATABASE') ?: '0');
Config::define( 'WP_REDIS_RETRY_INTERVAL', env( 'WP_REDIS_RETRY_INTERVAL' ) ?: null );
Config::define( 'WP_REDIS_TIMEOUT', env( 'WP_REDIS_TIMEOUT' ) ?: 1 );
Config::define( 'WP_REDIS_READ_TIMEOUT', env( 'WP_REDIS_READ_TIMEOUT' ) ?: 1 );

/**
 * Query Monitor settings
 */
Config::define('QM_DISABLED', env('QM_DISABLED'));

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);

// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);

// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

// Limit the number of post revisions
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?? true);

/**
 * Debugging Settings
 */
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', false);
Config::define('SCRIPT_DEBUG', false);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');

/**
 * Multisite settings.
 */
Config::define( 'WP_ALLOW_MULTISITE', true );
Config::define( 'MULTISITE', true );
Config::define( 'SUBDOMAIN_INSTALL', true );
$base = '/';
Config::define( 'DOMAIN_CURRENT_SITE', $_server_http_host_name );
Config::define( 'PATH_CURRENT_SITE', '/' );
Config::define( 'SITE_ID_CURRENT_SITE', 1 );
Config::define( 'BLOG_ID_CURRENT_SITE', 1 );

Config::define( 'ADMIN_COOKIE_PATH', '/' );
Config::define( 'COOKIE_DOMAIN', '' );
Config::define( 'COOKIEPATH', '' );
Config::define( 'SITECOOKIEPATH', '' );

Config::define( 'AUTOMATIC_UPDATER_DISABLED', true );
Config::define( 'DISABLE_WP_CRON', true );
Config::define( 'DISALLOW_FILE_EDIT', true );
Config::define( 'FS_METHOD', 'direct' );

// Kuva unified api url used in LocationSearch plugin.
define( 'KUVA_UNIFIED_API', env( 'KUVA_UNIFIED_API' ) );

// Revalidate tokens.
define( 'REVALIDATE_TOKEN_TESTING', env( 'REVALIDATE_TOKEN_TESTING' ) );
define( 'REVALIDATE_TOKEN_PRODUCTION_LIIKUNTA', env( 'REVALIDATE_TOKEN_PRODUCTION_LIIKUNTA' ) );
define( 'REVALIDATE_TOKEN_PRODUCTION_TAPAHTUMAT', env( 'REVALIDATE_TOKEN_PRODUCTION_TAPAHTUMAT' ) );
define( 'REVALIDATE_TOKEN_PRODUCTION_HARRASTUKSET', env( 'REVALIDATE_TOKEN_PRODUCTION_HARRASTUKSET' ) );

/**
 * WPO365 options
 */
define( 'WPO_MU_USE_SUBSITE_OPTIONS', env( 'WPO_MU_USE_SUBSITE_OPTIONS' ) ?? true );

// GRAPHQL JWT AUTH SECRET KEY.
define( 'GRAPHQL_JWT_AUTH_SECRET_KEY', env( 'GRAPHQL_JWT_AUTH_SECRET_KEY' ) );

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
