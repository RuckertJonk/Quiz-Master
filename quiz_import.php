<?php
$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["jsonfile"])) {

    $json = file_get_contents($_FILES["jsonfile"]["tmp_name"]);
    $data = json_decode($json, true);

    if (!$data) {
        die("Ongeldig JSON-bestand.");
    }

    // Quiz opslaan
    $stmt = $conn->prepare("INSERT INTO quizzes (titel, beschrijving, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data["titel"], $data["beschrijving"], $data["image_url"]);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    // Vragen importeren
    foreach ($data["vragen"] as $vraag) {

        $vraagtekst = $vraag["vraagtekst"];
        $vraag_type = $vraag["vraag_type"] ?? "single"; // fallback

        $stmt = $conn->prepare("INSERT INTO vragen (quiz_id, vraagtekst, vraag_type) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $quiz_id, $vraagtekst, $vraag_type);
        $stmt->execute();
        $vraag_id = $stmt->insert_id;

        // Antwoorden importeren
        foreach ($vraag["antwoorden"] as $antwoord) {

            $antwoordtekst = $antwoord["antwoordtekst"];
            $is_correct = $antwoord["is_correct"];

            $stmt = $conn->prepare("INSERT INTO antwoorden (vraag_id, antwoordtekst, is_correct) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $vraag_id, $antwoordtekst, $is_correct);
            $stmt->execute();
        }
    }

    echo "<div class='done'><h2>Quiz succesvol geïmporteerd!</h2><a href='admin.php'>Terug naar admin</a></div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz importeren</title>

    <style>
        body {
            margin: 0;
            background-color: #F4F7FA;
            font-family: Arial, sans-serif;
        }

        header {
            background: #1A3A5F;
            color: white;
            padding: 22px 0;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            border-bottom: 4px solid #162F4D;
        }

        .container {
            max-width: 600px;
            width: 90%;
            margin: 40px auto;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        h1 {
            color: #1A3A5F;
            margin-bottom: 25px;
            font-size: 24px;
        }

        input[type="file"] {
            padding: 14px;
            background: #eef3fa;
            border-radius: 12px;
            width: 100%;
            max-width: 350px;
            border: 2px dashed #1A3A5F;
            cursor: pointer;
            margin-bottom: 25px;
            font-size: 15px;
        }

        button {
            padding: 14px 20px;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 17px;
            width: 100%;
            max-width: 350px;
        }

        button:hover {
            background: #162F4D;
        }
    </style>
</head>

<body>

<header>Quiz importeren</header>

<div class="container">
    <h1>Upload een quizbestand</h1>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="jsonfile" accept=".json" required>
        <button type="submit">Importeren</button>
    </form>
</div>

</body>
</html>
