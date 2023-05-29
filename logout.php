<?php
    
    setcookie("PHPSESSID", "", 1, "/"); // deleting the sessionID cookie
    session_destroy(); // delete session file

    header("Location: index.php");

