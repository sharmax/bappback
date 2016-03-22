<?php
	include("../config.php");
	$categories = array('breco','ill','ao','grieve','breview','feedback');
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				if(isset($_GET['id'],$_GET['action'],$_GET['cat'])){
					$conversations['data'] = array();
					$conversations['action'] = $_GET['action'];
					if($conversations['action'] == "update"){
						$id = $_GET['id'];
						$conversations['cat'] = $categories[intval($_GET['cat'])-1];
						$sql = "select * from communications where cat='".$conversations['cat']."' and id >= ".$id." ";
						if($userinfo['category']!="Admin"){
							$sql .= "and bitsid='".$user."' ";
							$isAdmin = 0;
						}
						else{
							$isAdmin = 1;
						}
						$sql .= "ORDER BY id ASC";
						if($comm = mysql_query($sql)){
							$conversation = mysql_fetch_array($comm);
							if($conversation){
								while($conversation){
									$conversations['data'][$conversation['id']] = array();
									$conversations['data'][$conversation['id']]['bitsid'] = $conversation['bitsid'];
									$user = mysql_fetch_array(mysql_query("select * from users where bitsid='".$conversation['bitsid']."'"));
									$conversations['data'][$conversation['id']]['category'] = $user['category'];
									$conversations['data'][$conversation['id']]['name'] = $user['name'];
									$conversations['data'][$conversation['id']]['topic'] = $conversation['topic'];
									$conversations['data'][$conversation['id']]['date'] = $conversation['date'];
									$conversations['data'][$conversation['id']]['time'] = $conversation['time'];
									$conversations['data'][$conversation['id']]['admins'] = $conversation['admins'];
									$conversations['data'][$conversation['id']]['talk'] = $conversation['comms'];
									$conversations['data'][$conversation['id']]['status'] = $conversation['status'];
									$conversation = mysql_fetch_array($comm);
								}
							}
							else{
								$conversations['message'] = "No conversations found";
							}
						}
						else{
							$conversations['message'] = "No conversations found";
						}
					}
					else if($conversations['action'] == "delete"){
						$id = $_GET['id'];
						if($comm = mysql_query("update communications set status = 'inactive' where id = '".$id."'")){
							$conversations['message'] = "Conversation Deleted Successfully!";
						}
						else{
							$conversations['err_message'] = "Error occurred while Deletion!";
						}
					}
					else if($conversations['action'] == "reply"){
						$conversations['data'] = array();
						if(isset($_GET['reply'])){
							$id = $_GET['id'];
							$conv = mysql_fetch_array(mysql_query("select * from communications where id = '".$id."'"));
							$comms = $_GET['reply'];
							$comms = str_replace(array("\r\n", "\n", "\r"),"<br />",str_replace("(href=","<a href=",str_replace('")','">',str_replace('//','</a>',$comms))));
							$x = 0;
							if($userinfo['category']=="Admin"){
								$x = 1;
								$admins = array();
								$na = substr_count($conv['admins'],";");
								$p = 0;
								for($i=1;$i<=$na;$i++){
									$pos = strpos($conv['admins'],";",$p);
									$admins[$i-1] = substr($conv['admins'],$p,$pos-$p);
									if($admins[$i-1]==$userinfo['name']){
										$x=0;
										$i=$na;
									}
									$p = $pos+1;
								}
							}
							$comm = $conv['comms']." ".$userinfo['name']."(Date-".date("d/m/Y").",Time-".date('h:i A').")| ".$comms." //";
							$conversations['data'][$id] = array();
							if(mysql_query("UPDATE communications SET comms='".$comm."' WHERE id ='".$id."' AND status like '%open%'")){
								$conversations['data'][$id]['admins'] = $conv['admins'];
								$conversations['data'][$id]['talk'] = $conv['comms'];
								if($x == 1){
									$admin = $conv['admins'].$userinfo['name'].";";
									if(mysql_query("UPDATE communications SET admins='".$admin."' WHERE id ='".$id."'")){
										$conversations['message'] = "Conversation Updated Successfully!";
										$conversations['data'][$id]['admins'] = $admin;
									}
									else{
										$conversations['err_message'] = "Error occurred while Replying!";	
										$conversations['data'][$id]['admins'] = $conv['admins'];
									}
									$conversations['data'][$id]['talk'] = $comm;
								}
								
							}
							else{
								$conversations['err_message'] = "Error occurred while Replying!";
							}
							$conversations['data'][$id]['bitsid'] = $conv['bitsid'];
							$user = mysql_fetch_array(mysql_query("select * from users where bitsid='".$conv['bitsid']."'"));
							$conversations['data'][$id]['category'] = $user['category'];
							$conversations['data'][$id]['name'] = $user['name'];
							$conversations['data'][$id]['topic'] = $conv['topic'];
							$conversations['data'][$id]['date'] = $conv['date'];
							$conversations['data'][$id]['time'] = $conv['time'];
							$conversations['data'][$id]['status'] = $conv['status'];
						}
						else{
							$conversations['err_message'] = "Incomplete Parameters!";
						}
					}
					else{
						$conversations['err_message'] = "Incorrect Action Parameter!";	
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