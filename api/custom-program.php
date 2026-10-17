<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

// POST : Créer un programme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = readJsonBody();
    $titre = trim($body['titre'] ?? '');
    $exercicesIds = $body['exercicesIds'] ?? [];

    if ($titre === '' || count($exercicesIds) === 0) {
        jsonError('Titre et exercices obligatoires');
    }

    // 7seb l'duree total (somme des exercices)
    $inQuery = implode(',', array_fill(0, count($exercicesIds), '?'));
    $stmt = $pdo->prepare("SELECT SUM(duree) as total FROM exercises WHERE id IN ($inQuery)");
    $stmt->execute($exercicesIds);
    $duree = (int)$stmt->fetchColumn() ?: 15;

    // Zid l'programme f table programs
    $stmt = $pdo->prepare(
        'INSERT INTO programs (user_id, is_custom, titre, niveau, public, objectif, duree, description, icone) 
         VALUES (?, 1, ?, "Personnalisé", "Tous", "Personnalisé", ?, "Mon programme personnalisé", "barbell")'
    );
    $stmt->execute([$user['id'], $titre, $duree]);
    $programId = (int)$pdo->lastInsertId();

    // Zid les exercices f table program_exercises
    $stmtEx = $pdo->prepare('INSERT INTO program_exercises (program_id, exercise_id, ordre) VALUES (?, ?, ?)');
    foreach ($exercicesIds as $index => $exId) {
        $stmtEx->execute([$programId, $exId, $index]);
    }

    jsonResponse(['success' => true, 'id' => $programId]);
}

// DELETE : Supprimer un programme
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('ID manquant');

    // T'akked belli l'programme dial l'user (machi dial chi wa7ed akhor)
    $stmt = $pdo->prepare('DELETE FROM programs WHERE id = ? AND user_id = ? AND is_custom = 1');
    $stmt->execute([$id, $user['id']]);

    if ($stmt->rowCount() === 0) jsonError('Programme introuvable', 404);

    jsonResponse(['success' => true]);
}
// PUT : Modifier un programme
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) jsonError('ID manquant');

    $body = readJsonBody();
    $titre = trim($body['titre'] ?? '');
    $exercicesIds = $body['exercicesIds'] ?? [];

    if ($titre === '' || count($exercicesIds) === 0) {
        jsonError('Titre et exercices obligatoires');
    }

    // T'akked belli l'programme dial l'user
    $stmt = $pdo->prepare('SELECT id FROM programs WHERE id = ? AND user_id = ? AND is_custom = 1');
    $stmt->execute([$id, $user['id']]);
    if (!$stmt->fetch()) jsonError('Programme introuvable', 404);

    // 7seb l'duree l'jdida
    $inQuery = implode(',', array_fill(0, count($exercicesIds), '?'));
    $stmt = $pdo->prepare("SELECT SUM(duree) as total FROM exercises WHERE id IN ($inQuery)");
    $stmt->execute($exercicesIds);
    $duree = (int)$stmt->fetchColumn() ?: 15;

    // Update programme
    $stmt = $pdo->prepare('UPDATE programs SET titre = ?, duree = ? WHERE id = ?');
    $stmt->execute([$titre, $duree, $id]);

    // Mse7 les anciens exercices
    $pdo->prepare('DELETE FROM program_exercises WHERE program_id = ?')->execute([$id]);

    // Zid les nouveaux
    $stmtEx = $pdo->prepare('INSERT INTO program_exercises (program_id, exercise_id, ordre) VALUES (?, ?, ?)');
    foreach ($exercicesIds as $index => $exId) {
        $stmtEx->execute([$id, $exId, $index]);
    }

    jsonResponse(['success' => true]);
}

jsonError('Méthode non autorisée', 405);

