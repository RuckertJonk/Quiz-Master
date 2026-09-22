<?php

function give_badge($conn, $user_id, $badge_name) {

    $stmt = $conn->prepare("SELECT badge_id FROM badges WHERE naam = ?");
    $stmt->bind_param("s", $badge_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $badge = $result->fetch_assoc();

    if (!$badge) {
        return;
    }

    $badge_id = $badge["badge_id"];

    $check = $conn->prepare("SELECT badge_id FROM user_badges WHERE user_id=? AND badge_id=?");
    $check->bind_param("ii", $user_id, $badge_id);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $badge_id);
        $insert->execute();
    }
}

