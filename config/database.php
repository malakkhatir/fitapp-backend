<?php
// ============================================
// Configuration de la base de données
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'fitapp');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP : par défaut, pas de mot de passe

// ============================================
// Connexion PDO (singleton)
// ============================================
function getPDO() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ============================================
// Réponses JSON
// ============================================
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message, $code = 400) {
    jsonResponse(['erreur' => $message], $code);
}

// ============================================
// Lire le corps JSON d'une requête POST/PUT
// ============================================
function readJsonBody() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// ============================================
// Authentification par token Bearer
// ============================================
function getTokenFromHeader() {
    $headers = getallheaders();
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s+(.+)/', $auth, $m)) {
        return trim($m[1]);
    }
    return null;
}

function requireAuth() {
    $token = getTokenFromHeader();
    if (!$token) jsonError('Token manquant', 401);

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, prenom, email, objectif_hebdo, photo_url, poids, taille, age, sexe FROM users WHERE token = ?');
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) jsonError('Token invalide ou expiré', 401);
    return $user;
}

function generateToken() {
    return bin2hex(random_bytes(32)); // 64 caractères hex
}
