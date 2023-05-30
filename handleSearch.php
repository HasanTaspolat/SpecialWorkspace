<?php
session_start();
require_once "./db.php";

if (!isset($_SESSION['user'])) {
    echo 'You must be logged in to perform this action.';
    exit;
}


$searchQuery = $_GET['query'] ?? '';

// Prepared statement to prevent SQL injection
$stmt = $db->prepare("SELECT * FROM user WHERE email LIKE :query");

// Bind the search query and execute the statement
$stmt->execute([
    'query' => '%' . $searchQuery . '%',
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($results) > 0) {
    foreach ($results as $user) {
        echo '<div class="col s12 m4">';
        echo '<div class="card" style="border-radius: 7px; overflow: hidden;">';
        echo '<div class="card-image waves-effect waves-block waves-light">';
        echo '<img class="activator" src="?id=' . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . '">'; // This needs to point to the current script
        echo '</div>';
        echo '<div class="card-content purple lighten-3">';
        echo '<span class="card-title activator grey-text text-darken-4">Name: ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . '<i class="material-icons right">more_vert</i></span>';
        echo '<p>Email: ' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '</div>';
        echo '<div class="card-action purple lighten-3">';
        echo '<button class="add-friend waves-effect waves-light btn" data-user-id="' . $user['id'] . '">Add Friend</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p class="center-align">No results found.</p>';
}
