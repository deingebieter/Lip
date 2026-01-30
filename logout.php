<?php
/**
 * logout.php - Logout-Funktion für L.I.P
 * 
 * Diese Datei:
 * - Beendet die aktuelle Session
 * - Löscht alle Session-Daten
 * - Leitet zurück zur Login-Seite
 */

session_start();

// Session-Informationen für Logging (optional)
$logout_user = $_SESSION['username'] ?? 'Unbekannt';
$logout_role = $_SESSION['role'] ?? 'Unbekannt';
$logout_time = date('Y-m-d H:i:s');

// Optional: Logout-Event in Log-Datei speichern
// Diese Zeile können Sie aktivieren, wenn Sie ein Audit-Log möchten
// file_put_contents('logs/logout.log', "[{$logout_time}] Benutzer '{$logout_user}' ({$logout_role}) hat sich abgemeldet\n", FILE_APPEND);

// ===== SESSION BEENDEN =====

// Alle Session-Variablen löschen
$_SESSION = array();

// Session-Cookie löschen (falls vorhanden)
if(isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Session zerstören
session_destroy();

// ===== WEITERLEITUNG ZUR LOGIN-SEITE =====
header('Location: index.php');
exit;
?>