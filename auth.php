<?php

// If the user has already authenticated, go to main.php 
if( isset($_SESSION["user"])){
    $_SESSION["message"] = "Unauthorized User";
    header("Location: main.php");
    exit; // if doesnt exist, user was not authenticated!
  }