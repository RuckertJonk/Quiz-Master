<?php
session_start();
session_destroy();
header("Location: quiz.overzicht.php");
exit;
