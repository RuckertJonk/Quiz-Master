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

    $titel = $_POST["title"];
    $beschrijving = $_POST["description"];
    $image = $_POST["image"];

    $stmt = $conn->prepare("INSERT INTO quizzes (titel, beschrijving, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $titel, $beschrijving, $image);
    $stmt->execute();

    $quiz_id = $stmt->insert_id;

    foreach ($_POST["questions"] as $q) {

        $vraagtekst = $q["text"];
        $vraag_type = $q["type"];

        $stmtQ = $conn->prepare("INSERT INTO vragen (quiz_id, vraagtekst, vraag_type) VALUES (?, ?, ?)");
        $stmtQ->bind_param("iss", $quiz_id, $vraagtekst, $vraag_type);
        $stmtQ->execute();

        $vraag_id = $stmtQ->insert_id;

        foreach ($q["answers"] as $index => $answer) {

            if ($vraag_type === "multiple") {
                $is_correct = in_array($index, $q["correct"]) ? 1 : 0;
            } else {
                $is_correct = ($index == $q["correct"]) ? 1 : 0;
            }

            $stmtA = $conn->prepare("INSERT INTO antwoorden (vraag_id, antwoordtekst, is_correct) VALUES (?, ?, ?)");
            $stmtA->bind_param("isi", $vraag_id, $answer, $is_correct);
            $stmtA->execute();
        }
    }

    echo "<script>alert('Quiz succesvol aangemaakt!'); window.location='quiz.overzicht.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Maken</title>

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
            text-align: center;
            font-size: 26px;
            font-weight: bold;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        label {
            font-weight: bold;
            color: #1A3A5F;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        .btn {
            background: #1A3A5F;
            color: white;
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: 0.25s;
            width: 100%;
        }

        .btn:hover {
            background: #162F4D;
        }

        .add-btn {
            background: #2E7D32;
        }

        .add-btn:hover {
            background: #256628;
        }

        .remove-btn {
            background: #C62828;
        }

        .remove-btn:hover {
            background: #A61E1E;
        }

        .question-box {
            background: #EEF3F8;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 6px solid #1A3A5F;
        }

        .btn-cancel-small {
            background: #1A3A5F;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 15px;
            display: block;
            width: fit-content;
            margin: 15px auto 0 auto;
            text-align: center;
            transition: 0.25s;
        }

        .btn-cancel-small:hover {
            background: #11263d;
            transform: scale(1.03);
        }
    </style>

    <script>
        let questionCount = 0;

        function addQuestion() {
            questionCount++;

            const container = document.getElementById("questions");

            const box = document.createElement("div");
            box.className = "question-box";
            box.innerHTML = `
                <h3>Vraag ${questionCount}</h3>

                <label>Vraagtekst:</label>
                <input type="text" name="questions[${questionCount}][text]" required>

                <label>Vraagtype:</label>
                <select name="questions[${questionCount}][type]" onchange="toggleCorrectInputs(${questionCount}, this.value)">
                    <option value="single">Single choice</option>
                    <option value="multiple">Multiple choice</option>
                </select>

                <label>Antwoord 1:</label>
                <input type="text" name="questions[${questionCount}][answers][]" required>

                <label>Antwoord 2:</label>
                <input type="text" name="questions[${questionCount}][answers][]" required>

                <label>Antwoord 3:</label>
                <input type="text" name="questions[${questionCount}][answers][]" required>

                <label>Antwoord 4:</label>
                <input type="text" name="questions[${questionCount}][answers][]" required>

                <div id="correct-${questionCount}">
                    <label>Correct antwoord:</label>
                    <select name="questions[${questionCount}][correct]" required>
                        <option value="0">Antwoord 1</option>
                        <option value="1">Antwoord 2</option>
                        <option value="2">Antwoord 3</option>
                        <option value="3">Antwoord 4</option>
                    </select>
                </div>

                <button type="button" class="btn remove-btn" onclick="this.parentElement.remove()">Verwijder vraag</button>
            `;

            container.appendChild(box);
        }

        function toggleCorrectInputs(q, type) {
            const correctDiv = document.getElementById(`correct-${q}`);

            if (type === "single") {
                correctDiv.innerHTML = `
                    <label>Correct antwoord:</label>
                    <select name="questions[${q}][correct]" required>
                        <option value="0">Antwoord 1</option>
                        <option value="1">Antwoord 2</option>
                        <option value="2">Antwoord 3</option>
                        <option value="3">Antwoord 4</option>
                    </select>
                `;
            } else {
                correctDiv.innerHTML = `
                    <label>Correcte antwoorden:</label>
                    <label><input type="checkbox" name="questions[${q}][correct][]" value="0"> Antwoord 1</label>
                    <label><input type="checkbox" name="questions[${q}][correct][]" value="1"> Antwoord 2</label>
                    <label><input type="checkbox" name="questions[${q}][correct][]" value="2"> Antwoord 3</label>
                    <label><input type="checkbox" name="questions[${q}][correct][]" value="3"> Antwoord 4</label>
                `;
            }
        }
    </script>
</head>

<body>

    <header>Maak je eigen Quiz</header>

    <div class="container">

        <form method="POST">

            <label>Titel van de quiz:</label>
            <input type="text" name="title" required>

            <label>Beschrijving:</label>
            <textarea name="description" rows="3" required></textarea>

            <label>Afbeelding URL:</label>
            <input type="text" name="image" required>

            <h2 style="color:#1A3A5F;">Vragen</h2>

            <div id="questions"></div>

            <button type="button" class="btn add-btn" onclick="addQuestion()">+ Voeg vraag toe</button>

            <button type="submit" class="btn">Quiz Opslaan</button>

            <a href="quiz.overzicht.php" class="btn-cancel-small">Annuleren</a>


        </form>

    </div>

</body>

</html>