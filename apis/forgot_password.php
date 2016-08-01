<?php
  include("../config.php");
  $response['err_message'] = '';
  $response['message'] = '';
  if(isset($_GET['username'])){
    $user = $_GET['username'];
    if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
      $email = $userinfo["email"];
      if(strrpos($email,".bits-") && strrpos($email,".ac.in"))
			{
				if(preg_match('#^(([a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+\.?)*[a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+)@(([a-z0-9-_]+\.?)*[a-z0-9-_]+)\.[a-z]{2,}$#i',$email))
				{
					$pass = uniqid();
					$passmd5 = sha1($pass);
					if(mysql_query("update users set password='".$passmd5."' where bitsid='".$user."'"))
					{
            require("../account/PHPMailerAutoload.php");
            $mail = new PHPMailer();
            $mail->IsSMTP(); // send via SMTP
            //IsSMTP(); // send via SMTP
            $mail->SMTPAuth = true; // turn on SMTP authentication
            $mail->Username = "helpdesk.library@pilani.bits-pilani.ac.in"; // SMTP username
            $mail->Password = "circulation"; // SMTP password
            $webmaster_email = "helpdesk.library@pilani.bits-pilani.ac.in"; //Reply to this email ID
            $mail->From = $webmaster_email;
            $mail->FromName = "Library - Web-master";
            $mail->AddAddress($email);
            $mail->AddReplyTo($webmaster_email,"Web-master");
            $mail->IsHTML(true); // send as HTML
            $mail->Subject = "New Password for BITS Pilani Library Portal";
            $mail->Body = "Hey ".$userinfo["name"].", Your new password is '".$pass."'."; //HTML Body
  					if($mail->Send()){
  						$response['message'] = 'Password has been sent to your BITS Email.';
  					}
            else{
              $response['message'] = "Password couldn't be been sent. Please contact administrator.";
            }
          }
          else{
            $response['err_message'] = "Invalid BITS Email!";
          }
        }
        else{
          $response['err_message'] = "Invalid BITS Email!";
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
