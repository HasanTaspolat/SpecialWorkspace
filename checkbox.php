<?php
   
    require "./db.php" ;
    $id =strval($_POST["id"]);
    $state = $_POST["state"];
    $rs = $db->prepare("update tasktable set completed=? where id =?");
    $rs->execute([$state, $id]);
?>