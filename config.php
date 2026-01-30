<?php
/**
 * config.php - Konfigurationsdatei für L.I.P (Live Interaction Portal)
 * 
 * Diese Datei enthält alle grundlegenden Konfigurationen für das System.
 * Änderungen hier beeinflussen das gesamte Portal.
 */

// ===== FEHLER-BERICHTERSTATTUNG =====
// In Produktion sollte error_reporting auf 0 gesetzt werden
error_reporting(E_ALL);
ini_set('display_errors', 1); // 0 = Aus, 1 = An

// ===== ZEITZONE =====
date_default_timezone_set('Europe/Berlin'); // Ändern Sie dies nach Bedarf

// ===== APPLICATION KONSTANTEN =====
define('APP_NAME', 'Live Interaction Portal');
define('APP_SHORT_NAME', 'L.I.P');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Development Team');
define('APP_YEAR', date('Y'));

// ===== URL KONFIGURATION =====
define('BASE_URL', 'http://localhost'); // Ohne Trailing Slash
define('APP_URL', BASE_URL); // Alias
define('SECURE', false); // true für HTTPS, false für HTTP

// ===== DATEIPFADE =====
define('ROOT_PATH', __DIR__); // Projektroot-Verzeichnis
define('LOGS_PATH', ROOT_PATH . '/logs'); // Logs-Verzeichnis
define('ASSETS_PATH', ROOT_PATH . '/assets'); // Assets-Verzeichnis
define('TEMP_PATH', ROOT_PATH . '/temp'); // Temporäre Dateien

// ===== DATENBANK KONFIGURATION (Optional - für zukünftige Erweiterung) =====
define('DB_TYPE', 'mysql'); // mysql, sqlite, pgsql
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'lip_database');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8mb4');

// Für SQLite:
// define('DB_FILE', ROOT_PATH . '/database/lip.db');

// ===== SESSION KONFIGURATION =====
// Session-Namen
define('SESSION_NAME', 'LIP_SESSION');
define('SESSION_TIMEOUT', 3600); // 1 Stunde in Sekunden

// Session-Cookie-Einstellungen
ini_set('session.name', SESSION_NAME);
ini_set('session.cookie_httponly', 1); // Nur HTTP-Zugriff (nicht JavaScript)
ini_set('session.use_strict_mode', 1); // Strikte Session-ID-Validierung
ini_set('session.cookie_samesite', 'Lax'); // CSRF-Schutz
ini_set('session.cookie_secure', SECURE ? 1 : 0); // HTTPS-Only bei Produktion

// Für SameSite mit älteren PHP-Versionen:
// session_set_cookie_params([
//     'httponly' => true,
//     'samesite' => 'Lax'
// ]);

// ===== SICHERHEITS-EINSTELLUNGEN =====
define('HASH_ALGORITHM', 'sha256');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_OPTIONS', ['cost' => 10]); // Bcrypt Cost Factor

// CSRF-Token Konfiguration
define('CSRF_TOKEN_NAME', '_csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600); // 1 Stunde

// ===== ROLLEN-KONFIGURATION =====
$GLOBALS['ROLE_HIERARCHY'] = [
    'Administrator' => 5,
    'Superuser' => 4,
    'Master' => 3,
    'Slave' => 2,
    'Konsument' => 1
];

$GLOBALS['ROLE_PERMISSIONS'] = [
    'Administrator' => [
        'view_dashboard' => true,
        'manage_users' => true,
        'manage_roles' => true,
        'manage_superusers' => true,
        'manage_masters' => true,
        'manage_slaves' => true,
        'manage_konsumenten' => true,
        'system_admin' => true,
        'view_logs' => true,
        'edit_config' => true
    ],
    'Superuser' => [
        'view_dashboard' => true,
        'manage_users' => true,
        'manage_roles' => false,
        'manage_superusers' => false,
        'manage_masters' => true,
        'manage_slaves' => true,
        'manage_konsumenten' => true,
        'system_admin' => false,
        'view_logs' => true,
        'edit_config' => false
    ],
    'Master' => [
        'view_dashboard' => true,
        'manage_users' => false,
        'manage_roles' => false,
        'manage_superusers' => false,
        'manage_masters' => false,
        'manage_slaves' => true,
        'manage_konsumenten' => true,
        'system_admin' => false,
        'view_logs' => false,
        'edit_config' => false
    ],
    'Slave' => [
        'view_dashboard' => true,
        'manage_users' => false,
        'manage_roles' => false,
        'manage_superusers' => false,
        'manage_masters' => false,
        'manage_slaves' => false,
        'manage_konsumenten' => true,
        'system_admin' => false,
        'view_logs' => false,
        'edit_config' => false
    ],
    'Konsument' => [
        'view_dashboard' => true,
        'manage_users' => false,
        'manage_roles' => false,
        'manage_superusers' => false,
        'manage_masters' => false,
        'manage_slaves' => false,
        'manage_konsumenten' => false,
        'system_admin' => false,
        'view_logs' => false,
        'edit_config' => false
    ]
];

// ===== BENUTZERDATENBANK (Demo-Daten) =====
// In Produktion sollte dies aus einer echten Datenbank kommen
$GLOBALS['VALID_USERS'] = [
    'Administrator' => [
        'password' => '$2y$10$U6OMkMaH7HEZTqfNzI8q.O/4dQH/AhEZJhDJqGVJyqQXN1K4v32jq', // r00t
        'role' => 'Administrator',
        'email' => 'administrator@lip.local',
        'created' => '2026-01-29',
        'active' => true
    ],
    'SuperUser01' => [
        'password' => '$2y$10$U6OMkMaH7HEZTqfNzI8q.O/4dQH/AhEZJhDJqGVJyqQXN1K4v32jq', // r00t
        'role' => 'Superuser',
        'email' => 'superuser01@lip.local',
        'created' => '2026-01-29',
        'active' => true
    ],
    'Master01' => [
        'password' => '$2y$10$U6OMkMaH7HEZTqfNzI8q.O/4dQH/AhEZJhDJqGVJyqQXN1K4v32jq', // r00t
        'role' => 'Master',
        'email' => 'master01@lip.local',
        'created' => '2026-01-29',
        'active' => true
    ],
    'Slave01' => [
        'password' => '$2y$10$U6OMkMaH7HEZTqfNzI8q.O/4dQH/AhEZJhDJqGVJyqQXN1K4v32jq', // r00t
        'role' => 'Slave',
        'email' => 'slave01@lip.local',
        'created' => '2026-01-29',
        'active' => true
    ],
    'Konsument01' => [
        'password' => '$2y$10$U6OMkMaH7HEZTqfNzI8q.O/4dQH/AhEZJhDJqGVJyqQXN1K4v32jq', // r00t
        'role' => 'Konsument',
        'email' => 'konsument01@lip.local',
        'created' => '2026-01-29',
        'active' => true
    ]
];

// ===== ROLLEN-BESCHREIBUNGEN =====
$GLOBALS['ROLE_DESCRIPTIONS'] = [
    'Administrator' => 'Sie haben vollständige Kontrolle über das gesamte System. Alle Funktionen sind verfügbar.',
    'Superuser' => 'Sie haben erweiterte Administrative Rechte und können Benutzer verwalten.',
    'Master' => 'Sie können Slave-Accounts erstellen, verwalten und alle Funktionen eines Slaves nutzen.',
    'Slave' => 'Sie können Konsumenten-Accounts erstellen und verwalten.',
    'Konsument' => 'Sie haben Zugriff auf die Standardfunktionen: Editor, Notizen und persönliche Einstellungen.'
];

// ===== ANWENDUNGEN =====
$GLOBALS['APPLICATIONS'] = [
    'Editor' => [
        'name' => 'Editor',
        'icon' => '📄',
        'description' => 'Textbearbeitung',
        'min_role_level' => 1
    ],
    'Notizen' => [
        'name' => 'Notizen',
        'icon' => '📝',
        'description' => 'Notizen verwalten',
        'min_role_level' => 1
    ],
    'Konsumenten verwalten' => [
        'name' => 'Konsumenten verwalten',
        'icon' => '👥',
        'description' => 'Konsumenten-Verwaltung',
        'min_role_level' => 2
    ],
    'Slaves verwalten' => [
        'name' => 'Slaves verwalten',
        'icon' => '🔗',
        'description' => 'Slave-Verwaltung',
        'min_role_level' => 3
    ],
    'Benutzer verwalten' => [
        'name' => 'Benutzer verwalten',
        'icon' => '👔',
        'description' => 'Benutzerverwaltung',
        'min_role_level' => 4
    ],
    'Systemverwaltung' => [
        'name' => 'Systemverwaltung',
        'icon' => '⚙️',
        'description' => 'System-Admin',
        'min_role_level' => 5
    ],
    'Einstellungen' => [
        'name' => 'Einstellungen',
        'icon' => '⚙️',
        'description' => 'Persönliche Einstellungen',
        'min_role_level' => 1
    ]
];

// ===== EMAIL KONFIGURATION (Optional - für zukünftige Erweiterung) =====
define('MAIL_FROM', 'noreply@lip.local');
define('MAIL_FROM_NAME', 'L.I.P System');
define('MAIL_SMTP_HOST', 'localhost');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_USER', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_SECURE', 'tls'); // tls oder ssl

// ===== LOGGING KONFIGURATION =====
define('LOG_ENABLED', true);
define('LOG_LEVEL', 'DEBUG'); // DEBUG, INFO, WARNING, ERROR
define('LOG_FORMAT', '[{DATE}] {LEVEL} - {MESSAGE}');

// ===== API RATE LIMITING =====
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 100); // Requests
define('RATE_LIMIT_WINDOW', 3600); // Sekunden (1 Stunde)

// ===== DATEI-UPLOADS =====
define('UPLOAD_ENABLED', false); // false für Test, true für Produktion
define('UPLOAD_DIR', ROOT_PATH . '/uploads');
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in Bytes
define('UPLOAD_ALLOWED_TYPES', ['pdf', 'txt', 'docx', 'xlsx']); // Erlaubte Dateitypen

// ===== UI KONFIGURATION =====
define('THEME', 'light'); // light oder dark
define('LANGUAGE', 'de'); // de oder en
define('ITEMS_PER_PAGE', 25); // Pagination

// ===== MAINTENANCE MODE =====
define('MAINTENANCE_MODE', false); // true = System wird gewartet
define('MAINTENANCE_MESSAGE', 'Das System wird derzeit gewartet. Bitte versuchen Sie es später erneut.');

// ===== HELPER FUNKTIONEN =====

/**
 * Gibt einen vollständigen URL zurück
 */
function get_url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Prüft, ob die aktuelle Anfrage über HTTPS erfolgt
 */
function is_https() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
           $_SERVER['SERVER_PORT'] == 443;
}

/**
 * Gibt die aktuelle Rolle des Benutzers zurück
 */
function get_user_role() {
    return $_SESSION['role'] ?? 'Konsument';
}

/**
 * Gibt das Rollen-Level zurück
 */
function get_role_level($role = null) {
    if($role === null) {
        $role = get_user_role();
    }
    return $GLOBALS['ROLE_HIERARCHY'][$role] ?? 0;
}

/**
 * Prüft, ob der Benutzer eine Berechtigung hat
 */
function has_permission($permission) {
    $role = get_user_role();
    $permissions = $GLOBALS['ROLE_PERMISSIONS'][$role] ?? [];
    return $permissions[$permission] ?? false;
}

/**
 * Prüft, ob das Rollen-Level ausreichend ist
 */
function has_role_level($required_level) {
    return get_role_level() >= $required_level;
}

/**
 * Protokolliert eine Nachricht
 */
function log_message($level, $message) {
    if(!LOG_ENABLED) return;
    
    if(!is_dir(LOGS_PATH)) {
        mkdir(LOGS_PATH, 0755, true);
    }
    
    $log_file = LOGS_PATH . '/app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] [{$level}] {$message}\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * Sanitizes user input
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generiert einen CSRF-Token
 */
function generate_csrf_token() {
    if(!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validiert einen CSRF-Token
 */
function validate_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ===== VERZEICHNISSE ERSTELLEN =====
// Erstelle notwendige Verzeichnisse, falls sie nicht existieren
foreach([LOGS_PATH, ASSETS_PATH, TEMP_PATH] as $dir) {
    if(!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

?>