<?php
$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

$quizzes = $conn->query("SELECT quiz_id, titel, image_url FROM quizzes ORDER BY quiz_id DESC");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kies een Leaderboard</title>

<style>
    body {
        background-color: #F4F7FA;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    header {
        background: #1A3A5F;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 26px;
        font-weight: bold;
        border-bottom: 4px solid #162F4D;
        border-radius: 0 0 12px 12px;
    }

    .container {
        max-width: 900px;
        margin: 30px auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        overflow: hidden;
        transition: 0.25s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .card:hover {
        transform: scale(1.03);
    }

    .card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .card h3 {
        padding: 15px;
        margin: 0;
        color: #1A3A5F;
        font-size: 20px;
        text-align: center;
    }
</style>
</head>

<body>

<header>Kies een Leaderboard</header>

<div class="container">
    <?php while ($q = $quizzes->fetch_assoc()): ?>
        <a class="card" href="leaderboard.php?quiz_id=<?= $q['quiz_id'] ?>">
            <img src="<?= htmlspecialchars($q['image_url']) ?>" alt="Quiz afbeelding">
            <h3><?= htmlspecialchars($q['titel']) ?></h3>
        </a>
    <?php endwhile; ?>
</div>

</body>
</html>
