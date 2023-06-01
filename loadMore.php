<?php
require_once 'db.php'; // adjust this to your actual db connection file
session_start();

$last_timestamp = isset($_POST['lastPost']) ? $_POST['lastPost'] : 0;

// This assumes you have the friend IDs stored in the session
$friendIds = isset($_SESSION['friendIds']) ? $_SESSION['friendIds'] : [];

if (!empty($friendIds)) {
    $inQuery = implode(',', array_fill(0, count($friendIds), '?'));

    // Adjust this SQL query to fit your schema
    $postsStmt = $db->prepare("
        SELECT * 
        FROM posts 
        WHERE user_id IN ($inQuery) AND timestamp < ? 
        ORDER BY timestamp DESC 
        LIMIT 10
    ");

    // Merge the last_timestamp to the parameters
    $params = array_merge($friendIds, [$last_timestamp]);
    $postsStmt->execute($params);

    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch comments for each post and add it to the posts array
    foreach ($posts as &$post) {
        $commentsStmt = $db->prepare("
            SELECT * 
            FROM comments 
            WHERE post_id = ? 
            ORDER BY timestamp DESC
        ");
        $commentsStmt->execute([$post['id']]);
        $post['comments'] = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            SELECT * FROM user 
            WHERE id = ?
        ");
        $stmt->execute([$post['user_id']]);
        $post['owner'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($posts);
} else {
    echo json_encode([]);
}
