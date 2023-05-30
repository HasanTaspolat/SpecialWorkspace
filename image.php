<?php

require_once "./db.php";

if (isset($_GET['id'])) {
    // Ensure ID is an integer
    $id = intval($_GET['id']);

    // Fetch image data from the database
    $query = "SELECT profile FROM user WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Echo the profile image data to check it's correct
    var_dump($row['profile']);

    // If there's image data, send it to the browser
    if ($row !== false && $row['profile'] !== null) {
        header("Content-type: image/jpg");
        echo $row['profile'];
    }
}
?>
