<?php
	include("../config.php");
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				if(isset($_GET['title'], $_GET['author'])){
					$title = $_GET['title'];
					$author = $_GET['author'];
					$reserve = mysql_query("select * from book_of_the_month where bitsid = '".$user."' and book_title = '".$title."' and book_author = '".$author."'");
					if(mysql_fetch_array($reserve)){
						$conversations['message'] = "Book Already Reserved!";
					}
					else{
						if(mysql_query("insert into book_of_the_month (name, bitsid, book_title, book_author, date, time, status) values ('".$userinfo['name']."', '".$user."', '".$title."',  '".$author."',  '".date('d/m/Y')."',  '".date('H:i:s')."', '0')")){
							$conversations['message'] = "Book Reserved!";
						}
						else{
							$conversations['message'] = "Book Failed to Reserve!";
						}
					}
				}
				else{
					$conversations['err_message'] = "Incomplete Parameters!";
				}
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