<?php
require_once "./db.php";
require_once "./Upload.php";
session_start(); // 
// add the fetch in here
if (!isset($_SESSION['user'])) {
    echo 'You must be logged in to perform this action.';
    exit;
}

$currentUser = $_SESSION['user']['id'];
$friendsStmt = $db->prepare("SELECT sender_id, receiver_id FROM friend_requests WHERE (sender_id = :currentUser OR receiver_id = :currentUser) AND status = 2");
$friendsStmt->execute(['currentUser' => $currentUser]);
$friendRows = $friendsStmt->fetchAll(PDO::FETCH_ASSOC);

$friendIds = [];
foreach ($friendRows as $row) {
    $friendId = $row['sender_id'] == $currentUser ? $row['receiver_id'] : $row['sender_id'];
    $friendIds[] = $friendId;
}

// Fetch posts of friends
if (!empty($friendIds)) {
    $inQuery = implode(',', array_fill(0, count($friendIds), '?'));
    $postsStmt = $db->prepare("SELECT * FROM posts WHERE user_id IN ($inQuery) ORDER BY timestamp DESC LIMIT 10");
    $postsStmt->execute($friendIds);
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $posts = [];
}
$stmt = $db->prepare("SELECT * FROM friend_requests WHERE receiver_id = :user_id AND status = 0");
$stmt->execute([':user_id' => $_SESSION['user']['id']]);
$friendRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Timeline</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<style>
.post-image {
    max-width: 100%;
    height: auto;
}
</style>
<body>
    <!-- Add navigation bar, header, etc. here -->

    <!-- Timeline posts -->
    <div id="timeline" class="container">
        <a href="logout.php">LOGOUT FROM APP<i id="icon" class="material-icons">exit_to_app</i></a>
        <a href="#modal-friend-requests" class="btn-floating btn-large waves-effect waves-light modal-trigger"><i class="material-icons">notifications</i></a>

<!-- Modal Structure -->
<div id="modal-friend-requests" class="modal">
    <div class="modal-content">
        <h4>Friend Requests</h4>
        <?php if (!empty($friendRequests)) : ?>
            <ul class="collection">
                <?php foreach ($friendRequests as $request) : ?>
                    <li class="collection-item"><?= htmlspecialchars($request['sender_id'], ENT_QUOTES, 'UTF-8') // Replace this with sender's name ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No new friend requests.</p>
        <?php endif; ?>
    </div>
    <div class="modal-footer">
        <a href="#" class="modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
</div>
        <?php if (!empty($posts)) foreach ($posts as $post) : ?>
            <div class="col s12 m4">
                <div class="card" style="border-radius: 7px; overflow: hidden;">
                    <div class="card-image waves-effect waves-block waves-light">
                        <?php if ($post['image']) : ?>
                            <img class="activator post-image" src="images/<?= htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                    </div>
                    <div class="card-content grey lighten-3 black-text">
                        <span class="card-title activator black-text">
                            <?= htmlspecialchars($post['text'], ENT_QUOTES, 'UTF-8') ?>
                            <i class="material-icons right">more_vert</i>
                        </span>
                    </div>
                    <div class="card-action grey lighten-3">
                        <a href="#" class="like">Like</a>
                        <a href="#" class="unlike">Unlike</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div id="search" class="input-field">
            <input id="search-input" type="text" class="validate" style="width: 300px; border: 2px solid #D1C4E9; border-radius: 7px;">
            <label for="search-input" class="purple-text text-lighten-3">Search for friends...</label>
            <button id="search-button" class="btn waves-effect waves-light purple lighten-3"><i class="material-icons">search</i></button>
        </div>
        <div id="search-results" class="row" style="border: 2px solid #D1C4E9; border-radius: 7px;"></div>
        <div class="container">
            <form id="post-form" action="handlePost.php" method="POST" enctype="multipart/form-data">
                <div class="input-field">
                    <textarea id="post-text" class="materialize-textarea" name="text"></textarea>
                    <label for="post-text">Share something...</label>
                </div>

                <div class="file-field input-field">
                    <div class="btn purple lighten-3">
                        <span>Upload</span>
                        <input type="file" name="imagename">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Upload your image">
                    </div>
                </div>

                <button type="submit" class="btn waves-effect waves-light purple lighten-3">Post</button>
            </form>
        </div>


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#search-button').on('click', function() {
                    $.ajax({
                        url: 'handleSearch.php', // You need to create this PHP script
                        type: 'GET',
                        data: {
                            query: $('#search-input').val()
                        },
                        success: function(response) {
                            // Display the search results
                            $('#search-results').html(response);
                        }
                    });
                });
                $('#post-form').on('submit', function(e) {
                    e.preventDefault();

                    var formData = new FormData();
                    formData.append('text', $('#post-text').val());
                    formData.append('imagename', $('input[type=file]')[0].files[0]);

                    $.ajax({
                        url: 'handlePost.php', // Server script to process the post
                        type: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            // Parse the response (assuming it's in JSON format)
                            var post = JSON.parse(response);

                            // Create a new post element
                            var newPostHtml = `
                            <div class="col s8 m2">
                    <div class="card" style="border-radius: 7px; overflow: hidden;">
                        <div class="card-image waves-effect waves-block waves-light">
                            ${post.image ? '<img class="activator" src="images/' + post.image + '">' : ''}
                        </div>
                        <div class="card-content black lighten-3">
                            <span class="card-title activator grey-text text-darken-4">Your Post: ${post.content}<i class="material-icons right">more_vert</i></span>
                        </div>
                        <div class="card-action black lighten-3">
                            <a href="#" class="like">Like</a>
                            <a href="#" class="unlike">Unlike</a>
                        </div>
                    </div>
                </div>`;

                            // Prepend the new post to the timeline
                            $('#timeline').prepend(newPostHtml);
                        },

                        error: function(xhr, status, error) {
                            // Handle any errors
                            console.error(error);
                        }
                    });
                });
                $('.modal').modal();
                $(document).on('click', '.add-friend', function() {
                    console.log("buraya girdi");
                    var userId = $(this).data('user-id'); // Get user ID from data attribute

                    // AJAX request
                    $.ajax({
                        url: 'handleRequest.php', // URL of the PHP script
                        type: 'post', // Request method
                        data: {
                            receiver_id: userId
                        }, // Data to be sent to the server
                        success: function(response) { // A function to be called if request succeeds
                            alert(response); // Display the result
                        },
                        error: function(jqXHR, textStatus, errorThrown) { // A function to be called if request fails
                            console.error('Error: ' + textStatus, errorThrown);
                        }
                    });
                });

                $('#loadMore').on('click', function() {
                    $.ajax({
                        url: 'load_more_posts.php', // You need to create this file
                        type: 'POST',
                        data: {
                            last_id: lastPostId
                        },
                        success: function(response) {
                            // Parse response (which should be in JSON format) and append posts to timeline
                            let posts = JSON.parse(response);
                            posts.forEach(post => {
                                let postHtml = `
                                <div class="post card">
                                    <div class="card-content">
                                        <span class="card-title">${post.title}</span>
                                        <p>${post.content}</p>
                                    </div>
                                    <div class="card-action">
                                        <a href="#" class="like">Like</a>
                                        <a href="#" class="unlike">Unlike</a>
                                    </div>
                                </div>`;
                                $('#timeline').append(postHtml);
                            });

                            if (posts.length > 0) {
                                lastPostId = posts[posts.length - 1].id;
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });
                });
            });
        </script>
</body>

</html>