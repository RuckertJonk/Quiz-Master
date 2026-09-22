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

    if (strlen($username) < 3) {
        $error = "Gebruikersnaam moet minimaal 3 tekens zijn.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, is_admin) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $username, $hash);

        if ($stmt->execute()) {
            echo "<script>alert('Account aangemaakt!'); window.location='login.php';</script>";
            exit;
        } else {
            $error = "Gebruikersnaam bestaat al.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>

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

        /* MOBIEL */
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
    <h2>Account aanmaken</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Gebruikersnaam" required>
        <input type="password" name="password" placeholder="Wachtwoord" required>

        <button type="submit">Registreren</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <p>Heb je al een account? <a href="login.php">Inloggen</a></p>
</div>



</body>
</html>
