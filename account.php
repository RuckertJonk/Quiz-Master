<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

$user_id = intval($_SESSION['user_id']);


$user_stmt = $conn->prepare("
    SELECT username, aangemaakt_op
    FROM users 
    WHERE user_id = ?
");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Gebruiker niet gevonden.");
}



$last_stmt = $conn->prepare("
    SELECT q.titel, uqs.gespeeld_op 
    FROM user_quiz_scores uqs
    JOIN quizzes q ON q.quiz_id = uqs.quiz_id
    WHERE uqs.user_id = ?
    ORDER BY uqs.gespeeld_op DESC
    LIMIT 1
");
$last_stmt->bind_param("i", $user_id);
$last_stmt->execute();
$last_quiz = $last_stmt->get_result()->fetch_assoc();


$badges_stmt = $conn->prepare("
    SELECT b.naam, b.icoon
    FROM user_badges ub
    JOIN badges b ON b.badge_id = ub.badge_id
    WHERE ub.user_id = ?
");
$badges_stmt->bind_param("i", $user_id);
$badges_stmt->execute();
$badges = $badges_stmt->get_result();



$history_stmt = $conn->prepare("
    SELECT q.titel, uqs.score, uqs.total_questions, uqs.gespeeld_op
    FROM user_quiz_scores uqs
    JOIN quizzes q ON q.quiz_id = uqs.quiz_id
    WHERE uqs.user_id = ?
    ORDER BY uqs.gespeeld_op DESC
    LIMIT 10
");
$history_stmt->bind_param("i", $user_id);
$history_stmt->execute();
$history = $history_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn account</title>

    <style>
        body {
            margin: 0;
            background-color: #F4F7FA;
            font-family: Arial, sans-serif;
            padding: 12px;
        }

        header {
            background: #1A3A5F;
            color: white;
            padding: 16px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            border-bottom: 4px solid #162F4D;
            border-radius: 0 0 12px 12px;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 18px;
            border-radius: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .profile-info h2 {
            margin: 0;
            color: #1A3A5F;
        }

        .profile-info p {
            margin: 4px 0;
            color: #555;
        }

        .section {
            margin-top: 20px;
        }

        .section h3 {
            margin-bottom: 10px;
            color: #1A3A5F;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            padding: 10px;
            background: #EEF3F8;
            border-radius: 12px;
        }

        .badge-square {
            width: 100px;
            background: white;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .badge-square-img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .badge-square-text {
            margin-top: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #1A3A5F;
        }


        .btn-row {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }

        .btn:hover {
            background: #162F4D;
        }

        .btn-secondary {
            background: #777;
        }

        .btn-secondary:hover {
            background: #555;
        }
    </style>
</head>

<body>

    <header>Mijn account</header>

    <div class="container">

        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p>Lid sinds: <?= date('d-m-Y', strtotime($user['aangemaakt_op'])) ?></p>
        </div>

        <div class="badges">
            <?php if ($badges->num_rows > 0): ?>
                <?php while ($badge = $badges->fetch_assoc()): ?>
                    <div class="badge-square">
                        <img src="<?= $badge['icoon'] ?>" alt="<?= $badge['naam'] ?>" class="badge-square-img">
                        <p class="badge-square-text"><?= htmlspecialchars($badge['naam']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Je hebt nog geen badges.</p>
            <?php endif; ?>
        </div>




        <div class="section">
            <h3>Quizgeschiedenis</h3>

            <?php if ($history->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $history->fetch_assoc()): ?>
                        <li>
                            <?= htmlspecialchars($row['titel']) ?> —
                            Score: <?= $row['score'] ?>/<?= $row['total_questions'] ?> —
                            <?= date("d-m-Y H:i", strtotime($row['gespeeld_op'])) ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Je hebt nog geen quizgeschiedenis.</p>
            <?php endif; ?>
        </div>

        <div class="btn-row">
            <a href="quiz.overzicht.php" class="btn">Naar quiz overzicht</a>
            <a href="logout.php" class="btn btn-secondary">Uitloggen</a>
        </div>

    </div>

</body>

</html>