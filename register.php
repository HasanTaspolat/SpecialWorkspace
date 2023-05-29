<?php

    session_start();

    require_once "./auth.php";

if (!empty($_POST)) {
    extract($_POST);

    require_once "./db.php";
    require_once "./Upload.php";

    // Parse email addresses in free text.
    $re = '/(\w+)@((?:\w+\.){1,3}(?:com|tr))/iu' ;
    if ( preg_match($re, $email) === 0) {
        $_SESSION["message"] = "Please enter valid email!"; 
        $_SESSION["email"] = $email;
      }

    $upload = new Upload("profile", "images");

    $rs = $db->prepare("insert into user (name, email, password, profile) values (?,?,?,?)");
    $rs->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $upload->file()]); // generate hashing value


    // redirect browser to index.php
    header("Location: index.php");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Title of the document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
          .container {
            margin-top: 100px;
        }

        .input-field {
            width: 60%;
            margin: 30px auto;
        }

        #me {
            color: #ffa726;
        }

        #forg {
            color: cyan;
        }

        #FileButton {
            background-color: #4caf50;
            color: #FFC900;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }

        #FileButton:hover {
            border: solid 2px #FFC900;
            transition: border-width 0.6s linear;
        }

        #RegisterButton:hover {
            border: solid 2px #FFC900;
            transition: border-width 0.6s linear;
        }

        #RegisterButton {
            background-color: #4caf50;
            color: #FFC900;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }
        #icon{
            color: greenyellow;
        }
    </style>
</head>

<body>
    <nav>
    <div class="nav-wrapper green">
            <a href="index.php" class="brand-logo center"><span id="forg"> Forget</span><span id="me" class="class=" orange lighten-1>Me? </span></a>

        </div>
    </nav>

    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
                <input name="username" id="username" type="text" class="validate" value=" <?= isset($username) ? filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>  "  >
                <label for="username">Name Lastname</label>
            </div>

            <div class="input-field">
                <input name="email" id="email" type="text" class="validate" value=" <?= isset($email) ? filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>  " >
                <label for="email">Email</label>
            </div>


            <div class="input-field">
                <input name="password" id="password" type="text" class="validate" >
                <label for="password">Password</label>
            </div>

            <div class="file-field input-field">
                <div id="FileButton"  class="btn">
                    <span>File</span>
                    <input type="file" name="profile">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>

            <div  class="center">
                <button id="RegisterButton"  class="btn waves-effect waves-light" type="submit" name="action">Register
                    <i class="material-icons right">send</i>
                </button>
            </div>


        </form>
    </div>

    <?php
  if (isset($_SESSION["message"])) {
    $err = $_SESSION["message"]; // I've consuemd that message! dont forget to unset it
    echo "<script> M.toast({html: '$err'}); </script>";
    // delete key value pair, in case delete the message from the session
    unset($_SESSION["message"]);
  }
  ?>

    <script>
        $(function() {

        })
    </script>
</body>

</html>