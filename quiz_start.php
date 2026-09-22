<?php
require 'includes/db.php';

$quiz_id = intval($_GET['id']);

$question = $conn->query("
    SELECT * FROM questions 
    WHERE quiz_id = $quiz_id 
    ORDER BY id ASC 
    LIMIT 1
")->fetch_assoc();

if (!$question) {
    die("Geen vragen gevonden voor deze quiz.");
}

$answers = $conn->query("
    SELECT * FROM answers 
    WHERE question_id = {$question['id']}
");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Start</title>

    <style>
        body {
            background-color: #F4F7FA;
            margin: 0;
            font-family: Arial, sans-serif;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 700px;
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }

        h2 {
            color: #1A3A5F;
            font-size: 24px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        label {
            display: block;
            background: #eef3fa;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.25s;
            font-size: 16px;
        }

        label:hover {
            background: #dce6f3;
        }

        input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button {
            padding: 14px 25px;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        button:hover {
            background: #162F4D;
        }

        @media (max-width: 600px) {
            .container {
                padding: 18px;
            }

            h2 {
                font-size: 20px;
            }

            label {
                font-size: 15px;
                padding: 12px;
            }

            button {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <h2><?= htmlspecialchars($question['question_text']) ?></h2>

    <form action="check_answer.php" method="POST">
        <?php while ($a = $answers->fetch_assoc()): ?>
            <label>
                <input type="radio" name="answer" value="<?= $a['id'] ?>" required>
                <?= htmlspecialchars($a['answer_text']) ?>
            </label>
        <?php endwhile; ?>

        <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
        <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

        <button type="submit">Controleer</button>
    </form>
</div>

</body>
</html>
