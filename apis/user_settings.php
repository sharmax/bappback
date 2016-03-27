<?php
	include("../config.php");
	$conversations['Message'] = '';
	$conversations['status'] = 1;
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid ='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				if(isset($_GET['change_type'])){
					$change = $_GET['change_type'];
					if(isset($_GET['new_value'])){
						$value = $_GET['new_value'];
						if($change == '0'){
						$new_password = sha1($value);
						if(mysql_query("update users set password = '".$new_password."' where bitsid = '".$user."'")){
								$conversations['status'] = 0;
							}
							else{
								$conversations['Message'] = "Something Went Wrong";
							}
						}
						else if($change == '1'){
							if(mysql_query("update users set mobile = '".$value."' where bitsid = '".$user."'")){
								$conversations['status'] = 0;
							}
							else{
								$conversations['Message'] = "Something Went Wrong";
							}
						}
						else if($change == '2'){
						}
					}
					else{
						$conversations['Message'] = "Incomplete Parameters!";
					}
				}
				else{
					$conversations['mobile_number'] = $userinfo['mobile'];
					$conversations['email_id'] = $userinfo['email'];
					$conversations['Image'] = "http://172.21.1.15/uploads/profilepics/".$userinfo['avatar'];
					$conversations['status'] = 0;
				}
			}
			else{
				$conversations['Message'] = 'Invalid Password!';
			}
		}
		else{
			$conversations['Message'] = "Invalid Username!";	
		}
	}
	else{
		$conversations['Message'] = "Incomplete Parameters!";
	}
	echo json_encode($conversations);
?>