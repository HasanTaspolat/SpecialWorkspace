<?php
require_once "./db.php";
require_once "./Upload.php";
session_start(); // 
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

$stmt = $db->prepare("SELECT CASE WHEN sender_id = :user_id THEN receiver_id ELSE sender_id END AS user_id FROM friend_requests WHERE status = 2 AND (sender_id = :user_id OR receiver_id = :user_id)");
$stmt->execute([':user_id' => $_SESSION['user']['id']]);
$friendIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

$friends = [];
if (!empty($friendIDs)) {
    $stmt = $db->prepare("SELECT id, name, email, profile FROM user WHERE id IN (" . implode(',', $friendIDs) . ")");
    $stmt->execute();
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommentsForPost($postId, $db)
{
    $stmt = $db->prepare("
        SELECT * FROM comments 
        WHERE post_id = ?
        ORDER BY timestamp DESC
    ");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>FREEEND - Timeline</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body class="all-body">
    <nav class="all-nav">
        <div class="header-all">
            <div class="project-name">FREE-END</div>
        </div>
    </nav>
    <a class="log-text" href="logout.php">
        <div class="logout-text"> LOGOUT </div>
        <div><i id="icon" class="material-icons exit-icon">exit_to_app</i> </div>
    </a>
    <div id="timeline" class="cont-timeline">
        <div id="search" class="input-field all-sections">
            <div class="search-section">
                <input id="search-input" type="text" class="validate" style="  border-bottom: 1px solid #ffffff; width: 300px; " placeholder="  Search for friends...">
                <button id="search-button" class="btn waves-effect waves-light purple lighten-3 button-search"><i class="material-icons">search</i></button>
            </div>
            <div class="top-search-section">
                <div class="logout-section">
                    <a href="#modal-friend-requests" class="btn-floating btn-large waves-effect waves-light modal-trigger friend-modal button-image-profile"><i class="material-icons">notifications</i></a>
                    <a href="#modal-friends" class="btn-floating btn-large waves-effect waves-light modal-trigger button-image-profile"><i class="material-icons">group</i></a>
                </div>
                <div id="modal-friends" class="modal friend-req-modal">
                    <div class="modal-content">
                        <h4 class="modal-text">Friends</h4>
                        <?php if (!empty($friends)) : ?>
                            <ul class="collection">
                                <?php foreach ($friends as $friend) : ?>
                                    <li class="collection-item avatar">
                                        <img src="images/<?= htmlspecialchars($friend['profile'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="circle">
                                        <span class="title modal-text"><?= htmlspecialchars($friend['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <p class="modal-text"><?= htmlspecialchars($friend['email'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p class="modal-text">No friends to show.</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="modal-close waves-effect waves-green btn-flat modal-text">Close</a>
                    </div>
                </div>
                <div id="modal-friend-requests" class="modal friend-req-modal">
                    <div class="modal-content">
                        <h4 class="modal-text">Friend Requests</h4>
                        <?php if (!empty($friendRequests)) : ?>
                            <ul class="collection modal-text">
                                <?php foreach ($friendRequests as $request) : ?>
                                    <li class="collection-item"><?= htmlspecialchars($request['sender_id'], ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p class="modal-text">No new friend requests.</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="modal-close waves-effect waves-green btn-flat modal-text">Close</a>
                    </div>
                </div>

            </div>
        </div>
        <div class="cont-timeline-second">
            <form class="form-section" id="post-form" action="handlePost.php" method="POST" enctype="multipart/form-data">
                <div class="input-field input-share">
                    <textarea class="text-area" id="post-text" class="materialize-textarea" name="text" placeholder="Share something.."></textarea>
                </div>

                <div class="file-field input-field">
                    <div class="btn button-image-profile purple lighten-3 btn-width">
                        <span>Upload</span>
                        <input type="file" name="imagename">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate image-input" type="text" placeholder="Upload your image">
                    </div>
                </div>
                <button type="submit" class="btn waves-effect waves-light purple lighten-3 button-image-profile btn-width">Post</button>
            </form>
        </div>
        <div class="all-cards row">
            <?php if (!empty($posts)) foreach ($posts as $post) : ?>
                <div class="col s4 card-width">
                    <div class="card">
                        <div class="card-image waves-effect waves-block waves-light">
                            <?php if ($post['image']) : ?>
                                <img class="activator post-image image-post-card" src="images/<?= htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php endif; ?>
                        </div>
                        <div class="card-content grey lighten-3 black-text card-content-beg">
                            <span class="card-title activator black-text">
                                <?= htmlspecialchars($post['text'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <div class="thumbs">
                                <span><i class="material-icons icon-white button-image-profile thumb btn-width-2">thumb_up</i></span>
                                <span><i class="material-icons icon-white button-image-profile thumb btn-width-2">thumb_down</i></span>
                            </div>
                        </div>
                        <div class="card-action grey lighten-3">
                            <a href="#" class="like button-image-profile  btn-width like-unlike" data-post-id="<?= $post['id'] ?>"> Like</a>
                            <a href="#" class="unlike button-image-profile  btn-width  like-unlike" data-post-id="<?= $post['id'] ?>"> Unlike</a>
                        </div>
                        <div class="card-content grey lighten-3">
                            <h5>Comments</h5>
                            <ul id="comments-list-<?= $post['id'] ?>">
                                <?php
                                $comments = getCommentsForPost($post['id'], $db);
                                foreach ($comments as $comment) :
                                ?>
                                    <li>
                                        <span><strong><?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8') ?>:</strong></span>
                                        <?= htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') ?>
                                        <span class="timestamp" style="font-size: 6px;"> <?= $comment['timestamp'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="input-field">
                                <textarea id="comment-<?= $post['id'] ?>" class="materialize-textarea"></textarea>
                                <label for="comment-<?= $post['id'] ?>">Add a comment...</label>
                                <button class="add-comment btn waves-effect waves-light button-image-profile " data-post-id="<?= $post['id'] ?>">Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.add-comment').click(function(e) {
                    e.preventDefault();

                    var postId = $(this).data('post-id');
                    var commentText = $('#comment-' + postId).val();

                    $.ajax({
                        url: 'handleComment.php',
                        method: 'post',
                        data: {
                            post_id: postId,
                            comment: commentText
                        },
                        success: function(response) {
                            var comment = JSON.parse(response);
                            var addcomment = `<li> <span> ${comment.username}: </span>
                            ${comment.comment}
                            <span style="font-size: 9px"> Just now. <span>
                            `;
                            $('.comments-list-' + postId).prepend(addcomment);
                        },
                        error: function() {
                            alert('An error occurred while adding the comment.');
                        }
                    });
                });
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