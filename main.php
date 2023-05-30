<?php

// TODO: sanitizse, sql injection prevention, xss attack 

session_start(); // available to reading from file

require_once "./db.php";
require_once "./protect.php";

// TODO: protection add later

$user = $_SESSION["user"];



  try {
    $list_ = "select * from listTable where owner = ?";
    $list_l = $db->prepare($list_);
    $list_l->execute([$user["id"]]);
    $list_table = $list_l->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $ex) {
    echo "<p>", $ex->getMessage(), "</p>";
  }

  if(isset($_POST["task"])){
  try {
    $tb = "select * from tasktable where listowner = ?";
    $tb_ = $db->prepare($tb);
    $tb_->execute([$user["id"]]);
    $tb__ = $tb_->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $ex) {
    echo "<p>", $ex->getMessage(), "</p>";
  }

}


  if(isset($_GET["important"])){
    try {
      $important = "yes";
      $comp = "no";
      $tb = "select * from tasktable where important = ? and completed = ? and listowner = ?";
      $tb_important = $db->prepare($tb);
      $tb_important->execute([$important, $comp, $user["id"]]);
      $task_important = $tb_important->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
      echo "<p>", $ex->getMessage(), "</p>";
    }
  }

// id of list
if(isset($_POST["list"])){
    extract($_POST);
    //$list_array = explode$list_temp
    try {
      
      $owner = $user["id"]; // userin listesi oluyor 
      //var_dump($list_table[0]["id"]);
      $insert_list = "insert into listTable (list, owner) values (?, ?)";
      $insertion_l = $db->prepare($insert_list);
      $insertion_l->execute([$list, $owner]);
      //var_dump($insertion_l);
    } catch (PDOException $ex) {
      $errMsg = "Insertion Failled!";
    }
    
  try {
    $list_ = "select * from listTable where owner = ?";
    $list_l = $db->prepare($list_);
    $list_l->execute([$user["id"]]);
    $list_table = $list_l->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $ex) {
    echo "<p>", $ex->getMessage(), "</p>";
  }
}

// id of task
if (isset($_POST["task"])) {
  extract($_POST);
  extract($_GET);
  try {
    $important = "no";
    $listowner = $user["id"];
    $listoftask= $list;
    $insert_task = "insert into tasktable (task, important, listowner, list) values (?, ?, ?, ?)";
    $insertion = $db->prepare($insert_task);
    $insertion->execute([$task, $important, $listowner,$listoftask]);
  
  } catch (PDOException $ex) {
    $errMsg = "Insertion Failled!";
  }
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <style>
    .container center {
      float: inline-end;
    }

     #TASKA{
       color: white;
     }


    #listR {
    background-color: white;
  display: flex;
  justify-content: space-between;
  width: 700px;
  margin-left: 30px;
  border-radius: 3px;
  border: black;
  padding-top: 10px;
  height: 44px;
  padding-left: 5px;

    }
    #ac{
      width: 1200px;
    }

     #AddTask
     {
       float: left;
       margin-right: 280px;
       background-color:#27B7FF;
       border-radius: 12px;
       border: white;
       width: 700px;
     }

    .listnames {
      text-align: left;

      font-size: 28px;

    }

    span {
      size: 35px;
      color: black;
    }

    #idList {
      float: left;
    }

    nav {
      display: flex;
      justify-content: space-evenly;
      width: 400px;
    }

    #list {
      text-decoration: none;

    }
     #taskbar{
       float: left;
     }

    #icon {
      color: #51C73A;
    }

    body {
      width: 2000px;
    }

    #imp{
      cursor: pointer;
    }

    #dele{
      cursor: pointer;
    }

    #leftDiv {
      float: left;

      width: 620px;
      background-color: white;
      border-radius: 2px;

    }

    #rightDiv {
      float: right;
      width: 1400px;
      background-color: #E8A115;
      margin-right: 100px;
     border-left:solid 3px #27B7FF ;

    }

    #Textures {
      color: black;
      font-weight: initial;
      font-family: Arial, Helvetica, sans-serif;
    }

    .adj {
      float: right;
      margin-right: 110px;
    }

    .adj2 {
      float: left;
    }

    .listValue {
      color: black;
    }

    #nl:hover {
      background-color: #FFC900;
    }

    #addI {

      float: left;
    }
  </style>

</head>
<body>

  <!-- Div on the Left to show Lists, Importants and Additions to list options -->
  <div id="leftDiv" class="container">
    <table>
      <tr>
        <td>
        <?php
          $profile = $user["profile"] ?? "default_image.jpeg";   // Save it on session

          echo "<img   class=adj2 src='Images/{$profile}' width='40' class='circle'>
          <span class=adj id=Textures>  {$user["name"]} <span>
          <br>
          <span class=adj id=Textures>  {$user["email"]} <span>";

          ?>

        </td>
        <td>
          <ul>
            <li>
              <a href="logout.php"><i id="icon" class="material-icons">exit_to_app</i></a>

            </li>
          </ul>
        </td>
      </tr>
      
      <tr>
        <td><a href="main.php?important=yes"><i class="material-icons">star_border</i>Important</a></td>
      </tr>
      <?php
      if (isset($_POST["list"]) || isset($_GET["list"]) || !empty($list_table)) {
        extract($_POST);
        extract($_GET);

        
        foreach($list_table as $list_value){
        $number_t = $db->prepare("select count(*) from tasktable where completed = 'no' and list = ?"); // gets count of table by calling count into assos array
        $number_t->execute([$list_value["list"]]);
        $number = $number_t->fetchAll(PDO::FETCH_ASSOC);
          $url_holder = array("owner" => $list_value["owner"],"list" => $list_value["list"]);
          $querystring = http_build_query($url_holder);
          echo "
            <tr> 
              <td>
              <i id='idList' class='material-icons'> list </i>
              <a href='?$querystring'> 
              {$list_value["list"]} </a> <span>"?><?php echo $number[0]["count(*)"]?> <?php "</span>
              </td>
            </tr>
          ";
        }
      }
      ?>
      

      <tr>
        <td>
        <!-- Prompt Message / List Addition -->
          <button data-target="modal1" class="btn modal-trigger">New List</button>
          <div id="modal1" class="modal">
            <div class="modal-content">
              <form action="main.php " method="POST">          
                  <div class="input-field">
                    <input name="list" id="list" type="text" class="validate">
                    <label for="list"> </label>
                </div>
              </form>
            </div>
          </div>
        </td>
      </tr>
    </table>
    </div>
    
  <div id="rightDiv" class="container">
  
    <table>
    <?php 
      if ((isset($querystring) && !isset($_GET["important"]))  &&  (isset($_GET["list"]) || isset($_POST["list"])) ){
     $url_holder = array("owner" => $list_value["owner"],"list" => $list_value["list"]);
     $querystring = http_build_query($url_holder);
     echo"<th class='listnames'>$list  </th>";
     $task_list = "select * from taskTable where list = ?";
     $task_l = $db->prepare($task_list);
     $task_l->execute([$list]);
     $tasklist = $task_l->fetchAll(PDO::FETCH_ASSOC);
     foreach($tasklist as $tk){
       $a=strval( $tk["task"] ) ;
       $ind = $tk["id"];
       echo""?> <tr id="<?php echo $ind; ?>"><td>
       <div id="listR">
         <div> 
           <label>
             <input type="checkbox" id="ckb" class="filled-in" value="<?php echo $ind; ?>" />
             <span><?php echo $a; ?></span>
           </label>
           </div>

       <div id="rightStart">
       <td> 
       <button id="#bt<?php echo $ind; ?>" onclick="importantAjax('<?php echo $ind; ?>')" id="imp" class="btn btn-danger"><i class='material-icons' >star_border</i></button></td>
       <a onclick="deleteAjax('<?php echo $ind; ?>')" class=""><i  id="dele" class='material-icons'>delete</i> </a>
       </div>
       </div>
       </td>

     
     </div><?php     
       }?>
       
 <?php }
    ?> 
    <?php if(!isset($_GET["important"])){ ?>
   <form action="" method="POST">
   <tr>  <div id="aj" class="container">
          <div id="AddTask" class="input-field">
            <i class="material-icons prefix">control_point</i>
            <input name="task" id="task" type="text" class="validate">
            <label id="TASKA" for="task"> Add a Task</label>
          </div>
        </div> </tr>
    </table>
    <?php } ?>
      
    <script type="text/javascript">
   function importantAjax(id){   
         $.ajax({
              type:'post',
              url:'important.php',
              data:{task_n:id},
              success:function(data){
                $('bt'+id).append("<i class='material-icons' >star</i>");
              }
         });
        
  }
	 function deleteAjax(id){     
         $.ajax({
              type:'post',
              url:'delete.php',
              data:{task_n:id},
              success:function(data){
                   $('#'+ id).hide();  // we hide the id which is belongs task. Then post to delete.php and delete from tasktable 
              }
         });
	 
  }

  $('input:checkbox').on('change', function (e){    
    e.preventDefault();
    var input = $(this).next('span');
     
    if (this.checked) {
      var state = "yes";
           $.ajax({
             type : "post",
             url : "checkbox.php",
             data : {id: $(this).val(),state: state},
             success: function(data){   
            $(input).css('textDecoration', 'line-through');
            } 
      });
      } else{
        var state = "no";
        $.ajax({
             type : "post",
             url : "checkbox.php",
             data : {id: $(this).val(),state: state},
             success: function(data){   
              $(input).css('textDecoration', 'none');
            } 
      });
        
      }
    })



  </script> 
  
   <?php if(isset($_GET["important"]) && !empty($task_important)): 
      echo "<div id='rightDiv' class='container'>";
      echo "<table><tr><th> Important </th> </tr> ";
      foreach($task_important as $ti){
          echo "
            <tr><th>
            " .  $ti["task"]   . "</th><td>". $ti["list"]  ."</td></tr></table>" ;
      }
      echo "</div>";
    ?>
    <?php endif; ?>

  </div>
  </form>

<script>
  $(function() {

  })
  $(document).ready(function() {
    $('.modal').modal();
  });
</script>


</body>

</html>