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
$vragen = $conn->query("SELECT * FROM vragen WHERE quiz_id = $quiz_id ORDER BY vraag_id ASC");

if (isset($_POST["save_quiz"])) {
    $stmt = $conn->prepare("UPDATE quizzes SET titel = ?, beschrijving = ?, image_url = ? WHERE quiz_id = ?");
    $stmt->bind_param("sssi", $_POST["title"], $_POST["description"], $_POST["image"], $quiz_id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

if (isset($_POST["save_question"])) {
    $vraag_id = intval($_POST["vraag_id"]);

    $stmt = $conn->prepare("UPDATE vragen SET vraagtekst = ? WHERE vraag_id = ?");
    $stmt->bind_param("si", $_POST["vraagtekst"], $vraag_id);
    $stmt->execute();

    foreach ($_POST["answers"] as $antwoord_id => $tekst) {
        $is_correct = ($_POST["correct"] == $antwoord_id) ? 1 : 0;
        $stmtA = $conn->prepare("UPDATE antwoorden SET antwoordtekst = ?, is_correct = ? WHERE antwoord_id = ?");
        $stmtA->bind_param("sii", $tekst, $is_correct, $antwoord_id);
        $stmtA->execute();
    }

    header("Location: admin.php");
    exit;
}

if (isset($_GET["delete_question"])) {
    $qid = intval($_GET["delete_question"]);
    $conn->query("DELETE FROM antwoorden WHERE vraag_id = $qid");
    $conn->query("DELETE FROM vragen WHERE vraag_id = $qid");
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Bewerken</title>

    <style>
        body {
            background-color: #F4F7FA;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
        }

        header {
            background: #1A3A5F;
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 4px solid #162F4D;
            border-radius: 0 0 12px 12px;
        }

        .container {
            max-width: 900px;
            margin: 25px auto;
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }

        h2 {
            color: #1A3A5F;
            margin-bottom: 10px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 17px;
            margin-top: 10px;
        }

        button:hover {
            background: #162F4D;
        }

        .question-box {
            background: #EEF3F8;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 6px solid #1A3A5F;
        }

        .question-delete {
            display: block;
            width: 100%;
            text-align: center;
            background: #C62828;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            text-decoration: none;
            transition: 0.25s;
        }

        .question-delete:hover {
            background: #A61E1E;
            transform: scale(1.02);
        }

        @media (max-width: 600px) {
            header {
                font-size: 20px;
                padding: 15px;
            }

            .container {
                padding: 15px;
            }

            input, textarea {
                font-size: 15px;
                padding: 10px;
            }

            button {
                font-size: 16px;
                padding: 12px;
            }

            .question-box {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<header>Quiz bewerken</header>

<div class="container">

    <h2>Quiz gegevens</h2>

    <form method="POST">
        <input type="hidden" name="save_quiz" value="1">

        <label>Titel:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($quiz['titel']) ?>">

        <label>Beschrijving:</label>
        <textarea name="description"><?= htmlspecialchars($quiz['beschrijving']) ?></textarea>

        <label>Afbeelding URL:</label>
        <input type="text" name="image" value="<?= htmlspecialchars($quiz['image_url']) ?>">

        <button type="submit">Quiz opslaan</button>
    </form>

    <hr><br>

    <h2>Vragen bewerken</h2>

    <?php while ($vraag = $vragen->fetch_assoc()): ?>
        <?php
        $vraag_id = $vraag['vraag_id'];
        $antwoorden = $conn->query("SELECT * FROM antwoorden WHERE vraag_id = $vraag_id");
        ?>

        <div class="question-box">

            <form method="POST">
                <input type="hidden" name="save_question" value="1">
                <input type="hidden" name="vraag_id" value="<?= $vraag_id ?>">

                <label>Vraagtekst:</label>
                <input type="text" name="vraagtekst" value="<?= htmlspecialchars($vraag['vraagtekst']) ?>">

                <label>Antwoorden:</label>

                <?php while ($a = $antwoorden->fetch_assoc()): ?>
                    <input type="text" name="answers[<?= $a['antwoord_id'] ?>]" value="<?= htmlspecialchars($a['antwoordtekst']) ?>">

                    <label>
                        <input type="radio" name="correct" value="<?= $a['antwoord_id'] ?>" <?= $a['is_correct'] ? 'checked' : '' ?>>
                        Correct antwoord
                    </label>
                <?php endwhile; ?>

                <button type="submit">Vraag opslaan</button>
            </form>

            <a 
                href="edit_quiz.php?id=<?= $quiz_id ?>&delete_question=<?= $vraag_id ?>" 
                class="question-delete"
                onclick="return confirm('Weet je zeker dat je deze vraag wilt verwijderen?')"
            >
                Vraag verwijderen
            </a>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>
