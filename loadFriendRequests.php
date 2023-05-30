<?php
session_start();
require_once "./db.php";

if (!isset($_SESSION['user'])) {
    echo 'You must be logged in to perform this action.';
    exit;
}

$user = $_SESSION['user'];

// Query to fetch friend requests for the current user
$stmt = $db->prepare("SELECT friend_requests.id, friend_requests.sender_id, user.name, user.email
                     FROM friend_requests
                     INNER JOIN user ON friend_requests.sender_id = user.id
                     WHERE friend_requests.receiver_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($requests) > 0) {
    foreach ($requests as $request) {
        echo '<li class="collection-item">';
        echo '<div>';
        echo '<span class="title">' . htmlspecialchars($request['name']) . '</span>';
        echo '<p>' . htmlspecialchars($request['email']) . '</p>';
        echo '</div>';
        echo '<div class="secondary-content">';
        echo '<button class="accept-request waves-effect waves-light btn" data-request-id="' . htmlspecialchars($request['id']) . '">Accept</button>';
        echo '<button class="reject-request waves-effect waves-light btn" data-request-id="' . htmlspecialchars($request['id']) . '">Reject</button>';
        echo '</div>';
        echo '</li>';
    }
} else {
    echo '<li class="collection-item">No friend requests.</li>';
}
