<?php
	include("../config.php");
	$categories = array('breco','ill','ao','grieve','breview','feedback');
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password)==$userinfo['password']){
				if(isset($_GET['start'],$_GET['cat'])){
					$cat = $categories[intval($_GET['cat'])-1];
					$start = intval($_GET['start']);
					$conversations = array();
					$comms = "select * from communications where cat='".$cat."' ";
					if($userinfo['category']!="Admin"){
						$comms .= "and bitsid='".$user."' ";
						$isAdmin = 0;
					}
					else{
						$isAdmin = 1;
					}
					$comms .= "and status not like '%inactive%' ORDER BY id DESC LIMIT ".$start.",5";
					$p = 0;
					$conversation = NULL;
					if($comm = mysql_query($comms)){
						$conversation = mysql_fetch_array($comm);
					}
					while($p < 5 && $conversation){
						if($p < 4){
							$conversations[$conversation['id']] = array();
							if($isAdmin==1){
								$conversations[$conversation['id']]['bitsid'] = $conversation['bitsid'];
								$user = mysql_fetch_array(mysql_query("select * from users where bitsid='".$conversation['bitsid']."'"));
								$conversations[$conversation['id']]['category'] = $user['category'];
								$conversations[$conversation['id']]['name'] = $user['name'];
							}
							$conversations[$conversation['id']]['topic'] = $conversation['topic'];
							$conversations[$conversation['id']]['date'] = $conversation['date'];
							$conversations[$conversation['id']]['time'] = $conversation['time'];
							$conversations[$conversation['id']]['admins'] = $conversation['admins'];
							$conversations[$conversation['id']]['talk'] = $conversation['comms'];
							$conversations[$conversation['id']]['status'] = $conversation['comms'];
							$conversation = mysql_fetch_array($comm);
						}
						$p++;
					}
					if($p == 0){
						$conversations['message'] = "No conversations found";
					}
					$conversations['start'] = $start;
				}
				else if(isset($_GET['id'],$_GET['action'],$_GET['cat'])){
					$action = $_GET['action'];
					if($action == "update"){
						$id = $_GET['id'];
						$cat = $categories[intval($_GET['cat'])-1];
						$sql = "select id, admins, comms, status from communications where cat='".$cat."' and id >= ".$id." ";
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
							while($conversation){
								$conversations[$conversation['id']] = array();
								$conversations[$conversation['id']]['admins'] = $conversation['admins'];
								$conversations[$conversation['id']]['talk'] = $conversation['comms'];
								$conversations[$conversation['id']]['status'] = $conversation['status'];
								$conversation = mysql_fetch_array($comm);
							}
						}
						else{
							$conversations['message'] = "No conversations found";
						}
					}
					else if($action == "delete"){
						$id = $_GET['id'];
						if($comm = mysql_query("update communications set status = 'inactive' where id = '".$id."'")){
							$conversations['message'] = "Conversation Deleted Successfully!";
						}
						else{
							$conversations['message'] = "Error occurred while Deletion!";
						}
					}
					else if($action == "reply"){
						if(isset($_GET['reply'])){
							$id = $_GET['id'];
							$conv = mysql_fetch_array(mysql_query("select id,comms,admins from communications where id = '".$id."'"));
							$comms = $_GET['reply'];
							$comms = str_replace(array("\r\n", "\n", "\r"),"<br />",str_replace("(href=","<a href=",str_replace('")','">',str_replace('//','</a>',$comms))));
							if($userinfo['category']=="Admin"){
								$admins = array();
								$na = substr_count($conv['admins'],";");
								$p = 0;
								$x = 0;
								for($i=1;$i<=$na;$i++){
									$pos = strpos($conv['admins'],";",$p);
									$admins[$i-1] = substr($conv['admins'],$p,$pos-$p);
									if($admins[$i-1]==$userinfo['name']){
										$x=1;
										$i=$na;
									}
									$p = $pos+1;
								}
								if($x == 1){
									$admin = $conv['admins'].$userinfo['name'].";";
									mysql_query("UPDATE communications SET admins='".$admin."' WHERE id ='".$id."'");
								}
							}
							$comm = $conv['comms']." ".$userinfo['name']."(Date-".date("d/m/Y").",Time-".date('h:i:s').")| ".$comms." //";
							if(mysql_query("UPDATE communications SET comms='".$comm."' WHERE id ='".$id."'")){
								$conversations['message'] = "Conversation Updated Successfully!";
							}
							else{
								$conversations['err_message'] = "Error occurred while Replying!";
							}
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