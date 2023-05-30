<!DOCTYPE html>
<html>

<head>
    <title>Timeline</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>

<body>
    <!-- Add navigation bar, header, etc. here -->

    <!-- Timeline posts -->
    <div id="timeline" class="container">
    <a href="logout.php">LOGOUT FROM APP<i id="icon" class="material-icons">exit_to_app</i></a>
        <?php if (isset($posts)) foreach ($posts as $post) : ?>
            <div class="post card">
                <div class="card-content">
                    <span class="card-title"><?php echo $post['title']; ?></span>
                    <p><?php echo $post['content']; ?></p>
                    <!-- Add post image here if exists -->
                </div>
                <div class="card-action">
                    <!-- Add like and unlike buttons here -->
                    <a href="#" class="like">Like</a>
                    <a href="#" class="unlike">Unlike</a>
                </div>
                <!-- Add comments here -->
            </div>
        <?php endforeach; ?>

        <div id="search" class="input-field">
            <input id="search-input" type="text" class="validate" style="width: 300px; border: 2px solid #D1C4E9; border-radius: 7px;">
            <label for="search-input" class="purple-text text-lighten-3">Search for friends...</label>
            <button id="search-button" class="btn waves-effect waves-light purple lighten-3"><i class="material-icons">search</i></button>
        </div>
        <div id="search-results" class="row" style="border: 2px solid #D1C4E9; border-radius: 7px;"></div>


        <!-- Include jQuery, Materialize, and your own script file -->
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

                $('.add-friend').on('click', function() {
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