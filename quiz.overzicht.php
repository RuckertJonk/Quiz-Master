<?php

session_start();

$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM quizzes ORDER BY aangemaakt_op DESC");
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Master</title>

    <style>
        body {
            margin: 0;
            background-color: #F4F7FA;
            font-family: Arial, sans-serif;
        }

        header {
            background: #1A3A5F;
            color: white;
            padding: 20px 0;
            border-bottom: 4px solid #162F4D;
        }

        .header-container {
            max-width: 1100px;
            margin: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 0 20px;
        }

        .header-title {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .header-buttons {
            position: absolute;
            right: 20px;
            display: flex;
            gap: 12px;
        }

        .header-btn {
            background: white;
            color: #1A3A5F;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            transition: 0.25s;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        .header-btn:hover {
            background: #dce6f3;
            transform: translateY(-2px);
        }

        .header-btn.admin {
            background: #FFD166;
        }

        .header-btn.admin:hover {
            background: #FFCA3A;
        }

        @media (max-width: 600px) {
            .header-container {
                flex-direction: column;
                gap: 10px;
            }

            .header-buttons {
                position: static;
                justify-content: center;
            }

            .header-btn {
                padding: 8px 14px;
                font-size: 13px;
            }

            .header-title {
                font-size: 22px;
            }
        }

        .quiz-list {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 0 20px 40px;
        }

        .quiz-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
            transition: 0.25s;
        }

        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
        }

        .quiz-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .quiz-title {
            font-size: 24px;
            font-weight: bold;
            color: #1A3A5F;
            margin-bottom: 8px;
        }

        .quiz-desc {
            color: #4A4A4A;
            font-size: 16px;
            margin-bottom: 18px;
        }

        .quiz-start {
            display: inline-block;
            padding: 12px 22px;
            background: #1A3A5F;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: 0.25s;
        }

        .quiz-start:hover {
            background: #162F4D;
        }

        .btn-maken {
            display: block;
            width: fit-content;
            margin: 40px auto;
            padding: 15px 35px;
            background: #1A3A5F;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 20px;
            font-weight: bold;
            transition: 0.25s;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .btn-maken:hover {
            background: #162F4D;
            transform: scale(1.05);
        }

        @media (max-width: 900px) {
            .quiz-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .quiz-list {
                grid-template-columns: 1fr;
            }

            .quiz-img {
                height: 160px;
            }

            .btn-maken {
                width: 90%;
                text-align: center;
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

<header>
    <div class="header-container">
        <div class="header-title">Kies een Quiz</div>

        <div class="header-buttons">
            <?php if (isset($_SESSION["user_id"])): ?>
                <a href="account.php" class="header-btn">Account</a>
                <a href="admin.php" class="header-btn admin">Eigen Quizzes</a>
                <a href="logout.php" class="header-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="header-btn">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="quiz-list">
    <?php
    if ($result->num_rows > 0) {
        while ($quiz = $result->fetch_assoc()) {

            $qid = $quiz['quiz_id'];
            $qCount = $conn->query("SELECT COUNT(*) AS total FROM vragen WHERE quiz_id = $qid")->fetch_assoc()['total'];

            echo "
            <div class='quiz-card'>
                <img src='" . htmlspecialchars($quiz['image_url']) . "' class='quiz-img'>
                <div class='quiz-title'>" . htmlspecialchars($quiz['titel']) . "</div>
                <div class='quiz-desc'>" . htmlspecialchars($quiz['beschrijving']) . "</div>
                <div class='quiz-desc'><b>$qCount vragen</b></div>
                <a href='quizspelen.php?id={$quiz['quiz_id']}' class='quiz-start'>Start Quiz</a>
            </div>
            ";
        }
    } else {
        echo "<p style='text-align:center; font-size:20px;'>Nog geen quizzes aangemaakt.</p>";
    }
    ?>
</div>

<a href="quizmaken.php" class="btn-maken">Zelf een quiz maken</a>
<a href="leaderboard.php" class="btn-maken">Leaderboard</a>


</body>
</html>
