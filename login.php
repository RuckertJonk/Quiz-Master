<?php
session_start();

$conn = new mysqli("localhost", "bit_academy", "bit_academy", "quizmaker");

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {

        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["is_admin"] = $user["is_admin"];

        header("Location: quiz.overzicht.php");
        exit;

    } else {
        $error = "Onjuiste login gegevens.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>

    <style>
        body {
            background-color: #F4F7FA;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .box {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
            text-align: center;
        }

        h2 {
            color: #1A3A5F;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-top: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background: #1A3A5F;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
        }

        button:hover {
            background: #162F4D;
        }

        .error {
            color: red;
            margin-top: 12px;
            font-size: 15px;
        }

        p {
            margin-top: 18px;
            font-size: 15px;
        }

        a {
            color: #1A3A5F;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* MOBIEL OPTIMALISATIE */
        @media (max-width: 450px) {
            .box {
                padding: 22px;
            }

            h2 {
                font-size: 22px;
            }

            button {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

<div class="box">
    <h2>Inloggen</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Gebruikersnaam" required>
        <input type="password" name="password" placeholder="Wachtwoord" required>

        <button type="submit">Inloggen</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <p>Geen account? <a href="register.php">Registreren</a></p>
</div>

</body>
</html>
