<?php
require_once '../config/Database.php';

if (!isset($_GET['date']) || !isset($_GET['service'])) {
    echo json_encode([]);
    exit;
}

$date = $_GET['date'];
$service = $_GET['service']; // 'midi' ou 'soir'
$couvertsDemandes = isset($_GET['couverts']) ? max(1, (int)$_GET['couverts']) : 1; // mini 1

// Jour de la semaine en français SANS setlocale
$jours = [
    'Sunday'    => 'Dimanche',
    'Monday'    => 'Lundi',
    'Tuesday'   => 'Mardi',
    'Wednesday' => 'Mercredi',
    'Thursday'  => 'Jeudi',
    'Friday'    => 'Vendredi',
    'Saturday'  => 'Samedi'
];
$phpDay = date('l', strtotime($date)); // donne 'Monday', 'Tuesday', etc.
$day = $jours[$phpDay]; // 'Lundi', 'Mardi', etc.

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT * FROM info WHERE day = :day LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':day', $day);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode([]);
    exit;
}

if ($service === 'midi') {
    $start = $row['opening_morning'];
    $end = $row['closure_morning'];
} else {
    $start = $row['opening_night'];
    $end = $row['closure_night'];
}
$maxConvives = (int)$row['nb_max_persons'];

if (!$start || !$end) {
    echo json_encode([]);
    exit;
}

// Durée d'occupation (en secondes)
$dureeOcc = 75 * 60;

// 1. Récupère toutes les réservations sur ce service/date
$sqlResa = "SELECT reservation_heure, couvert 
            FROM reservations 
            WHERE reservation_date = :date
              AND reservation_heure >= :start
              AND reservation_heure < :end";
$stmtResa = $conn->prepare($sqlResa);
$stmtResa->bindValue(':date', $date);
$stmtResa->bindValue(':start', $start);
$stmtResa->bindValue(':end', $end);
$stmtResa->execute();
$reservations = $stmtResa->fetchAll(PDO::FETCH_ASSOC);

// 2. On génère une timeline du service : occupation minute par minute
$timeline = []; // ['12:00'=>15, '12:01'=>15, ...]
foreach ($reservations as $resa) {
    $resaTime = strtotime($resa['reservation_heure']);
    for ($t = 0; $t < $dureeOcc / 60; $t++) {
        $minute = date('H:i', strtotime("+$t minutes", $resaTime));
        // On ne stocke que les minutes dans la fenêtre du service
        if ($minute >= $start && $minute < $end) {
            if (!isset($timeline[$minute])) $timeline[$minute] = 0;
            $timeline[$minute] += (int)$resa['couvert'];
        }
    }
}

// 3. Génère les créneaux de 15min, et vérifie pour chacun
$current = strtotime($start);
$endTime = strtotime($end);
$times = [];

while ($current < $endTime) {
    $slotTime = date('H:i', $current);
    $possible = true;
    // Simule l'ajout minute par minute pendant la durée d'occupation
    for ($t = 0; $t < $dureeOcc / 60; $t++) {
        $minute = date('H:i', strtotime("+$t minutes", $current));
        // Stop si la minute sort du service
        if (strtotime($minute) >= $endTime) break;
        $occupes = isset($timeline[$minute]) ? $timeline[$minute] : 0;
        if (($occupes + $couvertsDemandes) > $maxConvives) {
            $possible = false;
            break;
        }
    }
    if ($possible) $times[] = $slotTime;
    $current = strtotime('+15 minutes', $current);
}

echo json_encode($times);
exit;
