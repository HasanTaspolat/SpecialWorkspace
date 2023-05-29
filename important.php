<?php
   require "./db.php" ;
   $id =strval($_POST["task_n"]);
   $rs = $db->prepare("update tasktable set important='yes' where id = ?");
   $rs->execute([$id]);
?>