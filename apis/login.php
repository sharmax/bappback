<?php
	include("../config.php");
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	$conversations['success'] = 0;
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				$conversations['message'] = 'Correct Credentials';
				$conversations['data'] = array();
				$conversations['data']['userid'] = $user;
				$conversations['data']['name'] = $userinfo['name'];
				$conversations['data']['email'] = $userinfo['email'];
				$conversations['data']['avatar'] = $userinfo['avatar'];
				$conversations['data']['category'] = $userinfo['category'];
				$conversations['success'] = 1;
			}
			else{
				$conversations['err_message'] = 'Invalid Password!';
			}
		}
		else{
			$conversations['err_message'] = "Invalid Username!";	
		}
	}
	else{
		$conversations['err_message'] = "Incomplete Parameters!";
	}
	echo json_encode($conversations);
?>