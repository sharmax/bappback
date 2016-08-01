<?php
  require_once __DIR__ . '/db_connect.php';
  $db = new DB_CONNECT();
  $response['err_message'] = '';
  $response['message'] = '';
  $response["success"] = 0;
  if(isset($_GET['username'],$_GET['password'])){
    $user = $_GET['username'];
    $password = $_GET['password'];
    if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
      if(sha1($password) == $userinfo['password']){
        $result = mysql_query("SELECT * FROM lfms where status <> 2 ORDER BY sno DESC");
        if($result && mysql_num_rows($result) > 0){
          $response["data"] = array();
          while($row = mysql_fetch_array($result)){
              $response["data"][$row["sno"]] = array();
              $response["data"][$row["sno"]]["particulars"] = $row["particulars"];
              $response["data"][$row["sno"]]["brand"] = $row["Brand_Name"];
              $response["data"][$row["sno"]]["date"] = $row["date"];
              $response["data"][$row["sno"]]["time"] = $row["time"];
              $response["data"][$row["sno"]]["status"] = $row["status"];
          }
          $response["success"] = 1;
        } 
        else {
          $response["message"] = "No Items found";
        }
      }
      else{
        $response['err_message'] = 'Invalid Password!';
      }
    }
    else{
      $response['err_message'] = "Invalid Username!";	
    }
  }
  else{
    $response['err_message'] = "Incomplete Parameters!";
  }
  echo json_encode($response);
?>
