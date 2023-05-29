<?php

// Protection code from unauthorized users
  if( !isset($_SESSION["user"])){
    $_SESSION["message"] = "Unauthorized User";
    header("Location : index.php");
    exit; // if doesnt exist, user was not authenticated!
  }
  // if ( isset($_SESSION["email"])){
  //   header("Location : register.php");
  //   exit;
  // }

