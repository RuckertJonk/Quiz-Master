<?php
$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Geen quiz geselecteerd.");
}

$quiz_id = intval($_GET['id']);

$quiz = $conn->query("SELECT * FROM quizzes WHERE quiz_id = $quiz_id")->fetch_assoc();
$vragen = $conn->query("SELECT * FROM vragen WHERE quiz_id = $quiz_id")->fetch_all(MYSQLI_ASSOC);

$data = [
    "titel" => $quiz["titel"],
    "beschrijving" => $quiz["beschrijving"],
    "image_url" => $quiz["image_url"],
    "vragen" => []
];

foreach ($vragen as $vraag) {
    $vraag_id = $vraag["vraag_id"];
    $antwoorden = $conn->query("SELECT * FROM antwoorden WHERE vraag_id = $vraag_id")->fetch_all(MYSQLI_ASSOC);

    $data["vragen"][] = [
        "vraagtekst" => $vraag["vraagtekst"],
        "antwoorden" => array_map(function($a) {
            return [
                "antwoordtekst" => $a["antwoordtekst"],
                "is_correct" => $a["is_correct"]
            ];
        }, $antwoorden)
    ];
}

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="quiz_export.json"');

echo json_encode($data, JSON_PRETTY_PRINT);
exit;
?>
