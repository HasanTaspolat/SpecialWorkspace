<?php
    // Login.php know the authenctication but this auth info has to be sent to 'main.php'

    session_start();

    require_once "./db.php";

    extract($_POST);
    var_dump($_FILES);
    $rs = $db->prepare("select * from user where email = ?");
    $rs->execute([$email]);

    if( $rs->rowCount() === 1){ 
        // valid email address
        // checks if there is a user as for that email address?
        $user = $rs->fetch(PDO::FETCH_ASSOC);
        var_dump($user);
        if ( password_verify($password, $user["password"])){
            //echo "user authenticated"; 
            $_SESSION["user"] = $user; // login.php puts user data in global persistent data area 
            header("Location: main.php");
            exit;
        }
    }else{
        echo "no user with that email address";
    }

    $_SESSION["message"] = "Login Failed!";
    //header("Location : index.php?err=login failed");
    header("Location: index.php");

?>