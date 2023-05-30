<?php
session_start();

require_once "./auth.php";
//require_once "./protect.php";

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
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    .input-field {
      width: 50%;
      margin: 30px auto;
    }

    .container {
      margin-top: 150px;
    }

    #reg {
      margin-right: 50px;

    }

    #reg:hover {
      border: solid 2px #FFC900;
      transition: border-width 0.6s linear;
      color: #FFC900;
      background-color:#4caf50;

    }
    #me {
      color: #ffa726;
    }

    #forg {
      color: cyan;
    }

    #logo {
      font-family: Arial, Helvetica, sans-serif;
    }
    #icon{
      margin-right: 3px;
    }
    #navv{
      height: 70px;
    }
    #buttonLogin:hover{
      border: solid 2px #FFC900;
      transition: border-width 0.6s linear;
    }
    #buttonLogin{
     background-color:#4caf50;
     color: #FFC900;
     font-weight: bold;
     font-family: Arial, Helvetica, sans-serif;
    }
    #Email{
      color: #4caf50;
    }
    #pass{
      color: #4caf50;
    }
    #Email:hover{
      color: #FFC900;
    }
    #pass:hover{
      color: #FFC900;
    }
    .input-field{
      border: orange;
    }

  </style>
</head>

<body>

  <nav> 
    <div id="navv" class="nav-wrapper green ">
      <a href="#" id="logo" class="brand-logo center "> <span id="forg"> Forget</span><span id="me" class="class=" orange lighten-1>Me? </span></a>
      <ul id="nav-mobile" class="right">
        <li id="icon">
          <i id="icon" class="material-icons">person_add</i>
        </li>
        <li>

          <a id="reg" href="register.php">Register</a>

        </li>

      </ul>
    </div>
  </nav>

  <div class="container">
    <form action="login.php" method="POST">
      <div class="input-field">
              <i class="material-icons prefix">account_circle</i>
              <input name="email" id="email" type="text" class="validate" value=" <?= isset($email) ? filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>  "  >
              <label for="email">Email</label>
      </div>

      <div id="pass" class="input-field">
        <i class="material-icons prefix">lock</i>
        <input name="password" id="password" type="password" class="validate">
        <label for="password">Password</label>
      </div>

      <div class="center">
        <button id="buttonLogin" class=" btn waves-effect waves-light" type="submit" name="action">Login</button>
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