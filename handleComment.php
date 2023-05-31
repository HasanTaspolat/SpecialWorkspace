<?php
// This assumes you have session_start() somewhere before this in order to use $_SESSION
session_start();

// Get the PDO object (Database connection object)
require_once 'db.php'; // Adjust this according to your project

// Extract post data
extract($_POST);

// Get the user ID and username from the session
$userId =  $_SESSION['user']['id'];
$username =  $_SESSION['user']['name']; // Adjust this according to where you stored the username

// Prepare a statement to insert the new comment into the database
$stmt = $db->prepare("
    INSERT INTO comments (post_id, user_id, username, comment) 
    VALUES (?, ?, ?, ?)
");

// Execute the statement with the post ID, user ID, username, and comment
if ($stmt->execute([$post_id, $userId, $username, $comment])) {
    
} else {
    
}

echo json_encode([
    'comment' => $comment,
    'name' => $username
]);

?>
