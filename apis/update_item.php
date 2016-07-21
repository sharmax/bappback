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
        if (isset($_GET['sno']) && isset($_GET['name'])) {
          $sno = $_GET['sno'];
          $name = $_GET['name'];
          $result = mysql_query("UPDATE lfms SET mobile = '$name', status='0' WHERE sno = $sno");
          if ($result) {
              $response["success"] = 1;
              $response["message"] = "Item successfully updated.";
          }
        } else {
            $response["message"] = "Required field(s) is missing";;
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
