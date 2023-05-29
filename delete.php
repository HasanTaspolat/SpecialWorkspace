<?php
    require "./db.php" ;
    $tsk =strval($_POST["task_n"]);
    // deletion from database according to id
    $rs = $db->prepare("delete from tasktable where id = ?");
    $rs->execute([$tsk]);
?>