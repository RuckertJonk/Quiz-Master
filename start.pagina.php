<?php
session_start();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaker Startpagina</title>

    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #1A3A5F, #274b7a);
            font-family: Arial, sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            text-align: center;
            padding: 28px 0;
            font-size: 34px;
            font-weight: bold;
            letter-spacing: 1px;
            text-shadow: 0 3px 8px rgba(0,0,0,0.35);
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .card {
            background: white;
            color: #1A3A5F;
            width: 90%;
            max-width: 650px;
            padding: 45px;
            border-radius: 22px;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            animation: fadeIn 0.6s ease;
            border: 3px solid rgba(255,255,255,0.15);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            margin-bottom: 12px;
            font-size: 30px;
        }

        p {
            font-size: 18px;
            margin-bottom: 35px;
            color: #3b4f6b;
        }

        .button-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: block;
            width: 100%;
            max-width: 300px;
            padding: 16px;
            background: #1A3A5F;
            color: white;
            text-decoration: none;
            border-radius: 14px;
            font-size: 19px;
            font-weight: bold;
            transition: 0.25s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            text-align: center;
        }

        .btn:hover {
            background: #162F4D;
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.25);
        }

        footer {
            text-align: center;
            padding: 18px;
            font-size: 14px;
            opacity: 0.85;
        }

        @media (max-width: 480px) {
            .card {
                padding: 28px;
            }

            h1 {
                font-size: 26px;
            }

            .btn {
                font-size: 17px;
            }
        }
    </style>
</head>

<body>

<header>Quiz Master</header>

<div class="container">
    <div class="card">
        <h1>Welkom bij QuizMaster</h1>
        <p>Kies een optie hieronder om te beginnen.</p>

        <div class="button-wrapper">
            <a href="quiz.overzicht.php" class="btn">Quiz spelen</a>
            <a href="quizmaken.php" class="btn">Quiz maken</a>
                <a href="login.php" class="btn">Inloggen</a>
        </div>
    </div>
</div>

<footer>© 2026 QuizMaster — Gemaakt door The Master Debaters</footer>

</body>
</html>
