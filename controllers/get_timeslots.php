<?php
require_once '../config/Database.php';

if (!isset($_GET['date']) || !isset($_GET['service'])) {
    echo json_encode([]);
    exit;
}

$date = $_GET['date'];
$service = $_GET['service']; // 'midi' ou 'soir'

// Récupérer le jour de la semaine (ex : 'Lundi')
$timestamp = strtotime($date);
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
$day = ucfirst(strftime('%A', $timestamp)); // 'Lundi', 'Mardi', etc.

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT * FROM info WHERE day = :day LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':day', $day);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$times = [];

if ($row) {
    if ($service === 'midi') {
        $start = $row['opening_morning'];
        $end = $row['closure_morning'];
    } else {
        $start = $row['opening_night'];
        $end = $row['closure_night'];
    }

    // Générer les créneaux de 30min
    $current = strtotime($start);
    $endTime = strtotime($end);

    while ($current < $endTime) {
        $times[] = date('H:i', $current);
        $current = strtotime('+15 minutes', $current);
    }
}

echo json_encode($times);
exit;