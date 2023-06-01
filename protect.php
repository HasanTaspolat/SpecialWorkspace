<?php
  if( !isset($_SESSION["user"])){
    $_SESSION["message"] = "Unauthorized User";
    header("Location: index.php");
    exit;
  }


