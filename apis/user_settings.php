<?php
	include("../config.php");
	$settings['Message'] = '';
	$settings['status'] = 0;
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid ='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				if(isset($_GET['change_type'])){
					$change = $_GET['change_type'];
					if($change == "2" || isset($_GET['new_value'])){
						if($change == '0'){
              $value = $_GET['new_value'];
  						$new_password = sha1($value);
  						if(mysql_query("update users set password = '".$new_password."' where bitsid = '".$user."'")){
								$settings['status'] = 1;
							}
							else{
								$settings['Message'] = "Something Went Wrong";
							}
						}
						else if($change == '1'){
              $value = $_GET['new_value'];
							if(mysql_query("update users set mobile = '".$value."' where bitsid = '".$user."'")){
								$settings['status'] = 1;
							}
							else{
								$settings['Message'] = "Something Went Wrong";
							}
						}
						else if($change == '2'){
              if(isset($_POST['new_value'])){
                $image = $_POST['new_value'];
                $path = "../uploads/profilepics/".$userinfo['id'].".jpg";
                if(file_put_contents($path, base64_decode($image))){
                  $settings['status'] = 1;
                  $settings['file_name'] = $userinfo['id'].".jpg";
                }
                else{
                  $settings['Message'] = "Something Went Wrong";
                }
              }
              else{
                $settings = "Image not provided!";
              }
						}
					}
					else{
						$settings['Message'] = "Incomplete Parameters!";
					}
				}
				else{
					$settings['name'] = $userinfo['name'];
					$settings['mobile_number'] = $userinfo['mobile'];
					$settings['email_id'] = $userinfo['email'];
					$settings['Image'] = "http://172.21.1.15/uploads/profilepics/".$userinfo['avatar'];
					$settings['status'] = 0;
				}
			}
			else{
				$settings['Message'] = 'Invalid Password!';
			}
		}
		else{
			$settings['Message'] = "Invalid Username!";	
		}
	}
	else{
		$settings['Message'] = "Incomplete Parameters!";
	}
	echo json_encode($settings);
?>
