<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée', 405);
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    jsonError('Aucune image reçue');
}

$file = $_FILES['avatar'];

// ⚠️ FORCING: Validate by extension and real image content, NOT the client MIME type
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
    jsonError('Format non supporté (JPG, PNG ou WEBP uniquement)');
}

$infosImage = @getimagesize($file['tmp_name']);
if ($infosImage === false) {
    jsonError('Le fichier n\'est pas une image valide');
}

// Verifi l'taille (max 3MB)
if ($file['size'] > 3 * 1024 * 1024) {
    jsonError('Image trop lourde (max 3MB)');
}

// Smiya dial l'fichier (unique)
$nomFichier = 'user_' . $user['id'] . '_' . time() . '.' . $extension;
$cheminDossier = __DIR__ . '/../uploads/avatars/';
$cheminComplet = $cheminDossier . $nomFichier;

// 7yed l'ancienne image ila kayna
$stmt = $pdo->prepare('SELECT photo_url FROM users WHERE id = ?');
$stmt->execute([$user['id']]);
$ancienneImage = $stmt->fetchColumn();

if ($ancienneImage) {
    $ancienChemin = __DIR__ . '/../' . $ancienneImage;
    if (file_exists($ancienChemin) && is_file($ancienChemin)) {
        unlink($ancienChemin);
    }
}

// Deplaci l'fichier l'jdid
if (!move_uploaded_file($file['tmp_name'], $cheminComplet)) {
    jsonError('Erreur lors de la sauvegarde', 500);
}

// Sauvegarder f l'base de données
$photoUrl = 'uploads/avatars/' . $nomFichier;
$stmt = $pdo->prepare('UPDATE users SET photo_url = ? WHERE id = ?');
$stmt->execute([$photoUrl, $user['id']]);

jsonResponse([
    'success' => true,
    'photoUrl' => $photoUrl,
]);