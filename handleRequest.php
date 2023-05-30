<?php
session_start();
require_once "./db.php";

$user = $_SESSION['user'];

// Check if a friend request from the current user to the target user already exists
$checkStmt = $db->prepare("SELECT COUNT(*) FROM friend_requests WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
$checkStmt->execute(['sender_id' => $user['id'], 'receiver_id' => $_POST['receiver_id']]);
$requestCount = $checkStmt->fetchColumn();

if ($requestCount > 0) {
    echo 'Friend request already sent.';
    exit;
}

// Insert the friend request into the friend_requests table
$stmt = $db->prepare("INSERT INTO friend_requests (sender_id, receiver_id) VALUES (:sender_id, :receiver_id)");
$stmt->execute(['sender_id' => $user['id'], 'receiver_id' => $_POST['receiver_id']]);

if ($stmt->rowCount() > 0) {
    echo 'Friend request sent!';
} else {
    echo 'Failed to send friend request.';
}
