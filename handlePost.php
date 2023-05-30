<?php
    require_once "./db.php";
    require_once "./Upload.php";

    session_start(); // Start session to use $_SESSION

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $text = $_POST['text'];

        $upload = new Upload('imagename', 'images');  // 'imagename' is the name of the input file field

        // Image upload handling
        $imageName = $upload->file();
        if ($upload->error()) {
            die('Error uploading image file: ' . $upload->error());
        }

        $sender_id = $_SESSION['user']['id']; // Fetch user ID from session
        $stmt = $db->prepare("INSERT INTO posts (user_id, text, image) VALUES (?, ?, ?)");
        $stmt->execute([$sender_id, $text, $imageName]);

        echo json_encode([
            'content' => $text,
            'image' => $imageName
        ]);
    }
?>
