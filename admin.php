<?php
$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM quizzes WHERE quiz_id = $id");
    header("Location: admin.php");
    exit;
}

$result = $conn->query("SELECT * FROM quizzes ORDER BY aangemaakt_op DESC");
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

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
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            border-bottom: 4px solid #162F4D;
        }

        .container {
            max-width: 1000px;
            width: 95%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .top-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn-main {
            background: #1A3A5F;
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-main:hover {
            background: #162F4D;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            word-break: break-word;
        }

        th {
            background: #1A3A5F;
            color: white;
            font-size: 16px;
        }

        .btn-delete,
        .btn-edit,
        .btn-export {
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }

        .btn-delete {
            background: #C62828;
            color: white;
        }

        .btn-delete:hover {
            background: #A61E1E;
        }

        .btn-edit {
            background: #2E7D32;
            color: white;
        }

        .btn-edit:hover {
            background: #256628;
        }

        .btn-export {
            background: #1565C0;
            color: white;
        }

        .btn-export:hover {
            background: #0D47A1;
        }

        .btn-back {
            display: block;
            width: fit-content;
            margin: 25px auto;
            padding: 12px 25px;
            background: #1A3A5F;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
        }

        .btn-back:hover {
            background: #162F4D;
        }

        @media (max-width: 800px) {
            header {
                font-size: 22px;
            }

            .container {
                padding: 15px;
            }

            th,
            td {
                padding: 10px;
                font-size: 14px;
            }

            .btn-delete,
            .btn-edit,
            .btn-export {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 550px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                background: #f8f8f8;
                padding: 12px;
                border-radius: 10px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 8px 5px;
                font-size: 14px;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #1A3A5F;
                margin-right: 10px;
            }

            .btn-edit,
            .btn-delete,
            .btn-export {
                width: 48%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <header>Eigen Quizzes</header>

    <div class="container">

        <div class="top-buttons">
            <a href="quiz_toevoegen.php" class="btn-main">Nieuwe quiz</a>
            <a href="quiz_import.php" class="btn-main">Quiz importeren</a>
        </div>

        <h2>Alle Quizzes</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Aangemaakt op</th>
                    <th>Acties</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($quiz = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($quiz['quiz_id']) ?></td>
                        <td data-label="Titel"><?= htmlspecialchars($quiz['titel']) ?></td>
                        <td data-label="Aangemaakt op"><?= htmlspecialchars($quiz['aangemaakt_op']) ?></td>
                        <td data-label="Acties">
                            <a class="btn-edit" href="edit_quiz.php?id=<?= $quiz['quiz_id'] ?>">Bewerken</a>
                            <a class="btn-edit" href="quizspelen.php?id=<?= $quiz['quiz_id'] ?>">Bekijken</a>
                            <a class="btn-export" href="quiz_export.php?id=<?= $quiz['quiz_id'] ?>">Exporteren</a>
                            <a class="btn-delete" href="admin.php?delete=<?= $quiz['quiz_id'] ?>" onclick="return confirm('Weet je zeker dat je deze quiz wilt verwijderen?')">Verwijderen</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>

    </div>

    <a href="quiz.overzicht.php" class="btn-back">Terug naar overzicht</a>

</body>

</html>
