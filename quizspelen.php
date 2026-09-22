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

if (!isset($_GET['id'])) {
    die("Geen quiz geselecteerd.");
}

$quiz_id = intval($_GET['id']);

$quiz = $conn->query("SELECT * FROM quizzes WHERE quiz_id = $quiz_id")->fetch_assoc();
if (!$quiz) {
    die("Quiz niet gevonden.");
}

$vragen = $conn->query("SELECT * FROM vragen WHERE quiz_id = $quiz_id ORDER BY vraag_id ASC");
$vragen_lijst = $vragen->fetch_all(MYSQLI_ASSOC);

$index = isset($_GET['q']) ? intval($_GET['q']) : 0;

if (!isset($_GET['q'])) {
    setcookie("quiz_time", 0, time() + 3600);
}

if (!isset($_COOKIE["quiz_time"])) {
    setcookie("quiz_time", 0, time() + 3600);
}

if (!isset($_COOKIE["quiz_score"])) {
    setcookie("quiz_score", 0, time() + 3600);
}

if ($index >= count($vragen_lijst)) {

    $score = floatval($_COOKIE["quiz_score"] ?? 0);
    $total = count($vragen_lijst);
    $totalTime = intval($_COOKIE["quiz_time"] ?? 0);

    setcookie("quiz_score", "", time() - 3600);
    setcookie("quiz_time", "", time() - 3600);

    $user_id = $_SESSION["user_id"] ?? null;

    include "badge_checker.php";

    if ($user_id !== null) {

        give_badge($conn, $user_id, "Eerste Quiz");

        if ($score == $total) {
            give_badge($conn, $user_id, "Perfecte Score");
        }

        if ($totalTime < 30) {
            give_badge($conn, $user_id, "Snelheidsduivel");
        }

        $played = $conn->query("SELECT COUNT(*) AS t FROM leaderboard WHERE user_id=$user_id")->fetch_assoc()['t'];
        if ($played >= 10) {
            give_badge($conn, $user_id, "10 Quizzes");
        }

        $stmt = $conn->prepare("INSERT INTO leaderboard (quiz_id, name, score, total) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdi", $quiz_id, $username, $score, $total);
        $stmt->execute();
    }




?>
    <!DOCTYPE html>
    <html lang="nl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultaat</title>
        <style>
            body {
                background-color: #e9eff6;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            .box {
                background: white;
                padding: 30px;
                max-width: 420px;
                width: 100%;
                border-radius: 16px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                text-align: center;
            }

            h1,
            h2 {
                color: #1A3A5F;
            }

            input {
                padding: 12px;
                width: 100%;
                border-radius: 10px;
                border: 1px solid #ccc;
                margin-top: 10px;
                font-size: 16px;
                box-sizing: border-box;
            }

            button {
                padding: 14px;
                width: 100%;
                background: #1A3A5F;
                color: white;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                font-size: 16px;
                margin-top: 15px;
            }

            button:hover {
                background: #162F4D;
            }
        </style>
    </head>

    <body>
        <div class="box">
            <h1>Je bent klaar!</h1>
            <p>Je score:</p>
            <h2><?= round($score, 2) ?> / <?= $total ?></h2>
            <p>Tijd bezig:</p>
            <h2>
                <?php
                $min = floor($totalTime / 60);
                $sec = $totalTime % 60;
                echo $min . ":" . str_pad($sec, 2, "0", STR_PAD_LEFT);
                ?>
            </h2>
            <p>Naam voor leaderboard:</p>
            <input type="text" id="playerName" placeholder="Jouw naam">
            <button onclick="saveScore()">Opslaan</button>
            <button onclick="window.location.href='quiz.overzicht.php'">Terug naar menu</button>
        </div>
        <script>
            function saveScore() {
                const name = document.getElementById("playerName").value.trim();
                if (!name) return;

                const form = document.createElement("form");
                form.method = "POST";
                form.action = "leaderboard.php";

                form.innerHTML = `
                <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
                <input type="hidden" name="name" value="${name}">
                <input type="hidden" name="score" value="<?= $score ?>">
                <input type="hidden" name="total" value="<?= $total ?>">
                <input type="hidden" name="time_spent" value="<?= $totalTime ?>">
            `;

                document.body.appendChild(form);
                form.submit();
            }
        </script>
    </body>

    </html>
<?php
    exit;
}

$vraag = $vragen_lijst[$index];
$vraag_id = $vraag['vraag_id'];
$vraag_type = $vraag['vraag_type'];

$antwoorden = $conn->query("SELECT * FROM antwoorden WHERE vraag_id = $vraag_id")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $huidige_score = floatval($_COOKIE["quiz_score"] ?? 0);

    if ($vraag_type === "multiple") {
        $gekozen = $_POST["antwoord"] ?? [];
        if (!is_array($gekozen)) $gekozen = [$gekozen];

        $correcte = array_filter($antwoorden, fn($a) => $a["is_correct"] == 1);
        $correct_ids = array_column($correcte, "antwoord_id");

        $goed_gekozen = count(array_intersect($gekozen, $correct_ids));
        $totaal_correct = count($correct_ids);

        if ($totaal_correct > 0) {
            $punten = $goed_gekozen / $totaal_correct;
            $huidige_score += $punten;
        }
    } else {
        $gekozen = intval($_POST["antwoord"]);
        $correct = $conn->query("SELECT is_correct FROM antwoorden WHERE antwoord_id = $gekozen")->fetch_assoc()['is_correct'];

        if ($correct == 1) {
            $huidige_score += 1;
        }
    }

    setcookie("quiz_score", $huidige_score, time() + 3600);

    header("Location: quizspelen.php?id=$quiz_id&q=" . ($index + 1));
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['titel']) ?></title>
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
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .time-spent {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #1A3A5F, #2D5B8C);
            color: white;
            padding: 12px 18px;
            border-radius: 50px;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 6px 15px rgba(26, 58, 95, 0.3);
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .progress-wrapper {
            width: 100%;
            background-color: #dce4ef;
            border-radius: 10px;
            height: 16px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: #1A3A5F;
            transition: width 0.4s ease;
        }

        h2,
        h3 {
            color: #1A3A5F;
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

        input[type="checkbox"],
        input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button {
            padding: 14px;
            width: 100%;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 17px;
            margin-top: 20px;
        }

        button:hover {
            background: #162F4D;
        }
    </style>
</head>

<body>
    <header><?= htmlspecialchars($quiz['titel']) ?></header>
    <div class="container">
        <div class="time-spent">
            Tijd bezig: <span id="timeSpent">0:00</span>
        </div>
        <div class="progress-wrapper">
            <div id="progressBar" class="progress-bar"></div>
        </div>
        <h2>Vraag <?= $index + 1 ?> van <?= count($vragen_lijst) ?></h2>
        <h3><?= htmlspecialchars($vraag['vraagtekst']) ?></h3>
        <form method="POST">
            <?php if ($vraag_type === "multiple"): ?>
                <?php foreach ($antwoorden as $antwoord): ?>
                    <label>
                        <input type="checkbox" name="antwoord[]" value="<?= $antwoord['antwoord_id'] ?>">
                        <?= htmlspecialchars($antwoord['antwoordtekst']) ?>
                    </label>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($antwoorden as $antwoord): ?>
                    <label>
                        <input type="radio" name="antwoord" value="<?= $antwoord['antwoord_id'] ?>" required>
                        <?= htmlspecialchars($antwoord['antwoordtekst']) ?>
                    </label>
                <?php endforeach; ?>
            <?php endif; ?>
            <button type="submit">Volgende</button>
        </form>
    </div>
    <script>
        let current = <?= $index + 1 ?>;
        let total = <?= count($vragen_lijst) ?>;
        document.getElementById("progressBar").style.width = (current / total * 100) + "%";

        let timeSpent = <?= intval($_COOKIE["quiz_time"] ?? 0) ?>;

        setInterval(() => {
            timeSpent++;
            document.cookie = "quiz_time=" + timeSpent + "; path=/";
            document.getElementById("timeSpent").textContent =
                Math.floor(timeSpent / 60) + ":" + String(timeSpent % 60).padStart(2, "0");
        }, 1000);
        window.addEventListener("beforeunload", function () {
            if (!quizBezig) {
                document.cookie = "quiz_time=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "quiz_score=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            }
        });
    </script>
</body>

</html>