<?php
$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $quiz_id = intval($_POST["quiz_id"]);
    $name = $_POST["name"];
    $score = floatval($_POST["score"]);
    $total = intval($_POST["total"]);

    $stmt = $conn->prepare("INSERT INTO leaderboard (quiz_id, name, score, total) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdi", $quiz_id, $name, $score, $total);
    $stmt->execute();

    header("Location: leaderboard.php?quiz_id=" . $quiz_id);
    exit;
}

if (!isset($_GET["quiz_id"])) {
    header("Location: leaderboard_select.php");
    exit;
}



$quiz_id = intval($_GET["quiz_id"]);

$result = $conn->query("SELECT * FROM leaderboard WHERE quiz_id = $quiz_id ORDER BY score DESC, date ASC");
$scores = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leaderboard</title>

<style>
    body {
        background-color: #e9eff6;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0;
        padding: 20px;
        font-family: Arial, sans-serif;
        color: #1A3A5F;
    }

    header {
        width: 100%;
        max-width: 800px;
        background-color: #1A3A5F;
        color: white;
        padding: 20px;
        text-align: center;
        border-bottom: 4px solid #162F4D;
        border-radius: 12px 12px 0 0;
        font-size: 26px;
        font-weight: bold;
    }

    .container {
        width: 100%;
        max-width: 800px;
        background-color: #F4F7FA;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        padding: 30px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #d9e2ef;
        text-align: left;
    }

    th {
        background-color: #dde7f1;
        font-weight: 700;
    }

    .actions {
        margin-top: 25px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    button {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        background-color: #1A3A5F;
        color: white;
        font-size: 16px;
    }

    button:hover {
        background-color: #162F4D;
    }
</style>
</head>

<body>

<header>Leaderboard</header>

<div class="container">

    <?php if (count($scores) === 0): ?>
        <p>Geen scores gevonden.</p>
    <?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Positie</th>
                <th>Naam</th>
                <th>Score</th>
                <th>Datum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scores as $i => $row): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= round($row["score"], 2) ?> / <?= $row["total"] ?></td>
                    <td><?= $row["date"] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>

    <div class="actions">
        <button onclick="window.location.href='quiz.overzicht.php'">Terug naar menu</button>
    </div>

</div>

</body>
</html>
