<?php
require_once __DIR__ . '/../config/database.php';

$user = requireAuth();
$pdo = getPDO();

// ============================================
// STATS
// ============================================
$stmt = $pdo->prepare(
    'SELECT COUNT(*) as total, COALESCE(SUM(minutes), 0) as minutes 
     FROM sessions WHERE user_id = ?'
);
$stmt->execute([$user['id']]);
$stats = $stmt->fetch();

// ============================================
// STREAK (jours consécutifs)
// ============================================
$stmt = $pdo->prepare(
    'SELECT DISTINCT DATE(date) as jour FROM sessions 
     WHERE user_id = ? ORDER BY jour DESC'
);
$stmt->execute([$user['id']]);
$jours = $stmt->fetchAll(PDO::FETCH_COLUMN);

$streak = 0;
if (count($jours) > 0) {
    $today = new DateTime();
    $today->setTime(0, 0, 0);
    $hier = clone $today;
    $hier->modify('-1 day');

    $premier = new DateTime($jours[0]);
    $premier->setTime(0, 0, 0);

    if ($premier >= $hier) {
        $streak = 1;
        $current = clone $premier;
        for ($i = 1; $i < count($jours); $i++) {
            $suivant = new DateTime($jours[$i]);
            $suivant->setTime(0, 0, 0);
            if ($current->diff($suivant)->days == 1) {
                $streak++;
                $current = $suivant;
            } else {
                break;
            }
        }
    }
}

// ============================================
// FAVORIS
// ============================================
$stmt = $pdo->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
$stmt->execute([$user['id']]);
$nbFavoris = (int)$stmt->fetchColumn();

// ============================================
// DÉFINITION DES BADGES
// ============================================
$definitions = [
    [
        'id' => 'premier_pas', 'nom' => 'Premier pas',
        'description' => 'Terminer ta première séance',
        'icone' => 'footsteps', 'couleur' => '#22C55E',
        'valeur' => (int)$stats['total'], 'cible' => 1,
    ],
    [
        'id' => 'habitue', 'nom' => 'Habitué',
        'description' => '5 séances terminées',
        'icone' => 'barbell', 'couleur' => '#3B82F6',
        'valeur' => (int)$stats['total'], 'cible' => 5,
    ],
    [
        'id' => 'determine', 'nom' => 'Déterminé',
        'description' => '10 séances terminées',
        'icone' => 'flame', 'couleur' => '#F59E0B',
        'valeur' => (int)$stats['total'], 'cible' => 10,
    ],
    [
        'id' => 'champion', 'nom' => 'Champion',
        'description' => '25 séances terminées',
        'icone' => 'trophy', 'couleur' => '#F59E0B',
        'valeur' => (int)$stats['total'], 'cible' => 25,
    ],
    [
        'id' => 'legende', 'nom' => 'Légende',
        'description' => '50 séances terminées',
        'icone' => 'medal', 'couleur' => '#8B5CF6',
        'valeur' => (int)$stats['total'], 'cible' => 50,
    ],
    [
        'id' => 'marathonien', 'nom' => 'Marathonien',
        'description' => '500 minutes cumulées',
        'icone' => 'timer', 'couleur' => '#EF4444',
        'valeur' => (int)$stats['minutes'], 'cible' => 500,
    ],
    [
        'id' => 'regulier', 'nom' => 'Régulier',
        'description' => '3 jours consécutifs',
        'icone' => 'calendar', 'couleur' => '#10B981',
        'valeur' => $streak, 'cible' => 3,
    ],
    [
        'id' => 'semaine', 'nom' => 'Semaine parfaite',
        'description' => '7 jours consécutifs',
        'icone' => 'star', 'couleur' => '#F59E0B',
        'valeur' => $streak, 'cible' => 7,
    ],
    [
        'id' => 'mois', 'nom' => 'Mois parfait',
        'description' => '30 jours consécutifs',
        'icone' => 'ribbon', 'couleur' => '#EC4899',
        'valeur' => $streak, 'cible' => 30,
    ],
    [
        'id' => 'collectionneur', 'nom' => 'Collectionneur',
        'description' => '5 exercices favoris',
        'icone' => 'heart', 'couleur' => '#EF4444',
        'valeur' => $nbFavoris, 'cible' => 5,
    ],
];

$badges = array_map(function ($b) {
    return [
        'id' => $b['id'],
        'nom' => $b['nom'],
        'description' => $b['description'],
        'icone' => $b['icone'],
        'couleur' => $b['couleur'],
        'debloque' => $b['valeur'] >= $b['cible'],
        'valeur' => min($b['valeur'], $b['cible']),
        'cible' => $b['cible'],
        'progression' => $b['cible'] > 0
            ? round(($b['valeur'] / $b['cible']) * 100)
            : 0,
    ];
}, $definitions);

$nombreDebloques = count(array_filter($badges, fn($b) => $b['debloque']));

jsonResponse([
    'badges' => $badges,
    'stats' => [
        'totalSeances'     => (int)$stats['total'],
        'minutesTotales'   => (int)$stats['minutes'],
        'streak'           => $streak,
        'favoris'          => $nbFavoris,
        'nombreDebloques'  => $nombreDebloques,
        'totalBadges'      => count($badges),
    ],
]);