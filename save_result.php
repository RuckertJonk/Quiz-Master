<?php
session_start();

$host = 'localhost';
$dbname = 'quizmaker';
$username = 'bit_academy';
$password = 'bit_academy';

$conn = new mysqli($host, $username, $password, $dbname);

$user_id = $_SESSION["user_id"];
$quiz_id = intval($_POST["quiz_id"]);
$score = floatval($_POST["score"]);
$total_questions = intval($_POST["total"]);
$time = intval($_POST["time"]);

$stmt = $conn->prepare("INSERT INTO user_quiz_scores (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iidi", $user_id, $quiz_id, $score, $total_questions);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO leaderboard (user_id, quiz_id, score) VALUES (?, ?, ?)");
$stmt->bind_param("iid", $user_id, $quiz_id, $score);
$stmt->execute();

include "badge_checker.php";

header("Location: leaderboard.php?quiz_id=" . $quiz_id);
exit;
?>
