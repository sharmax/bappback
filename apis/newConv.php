<?php
	include('../config.php');
	date_default_timezone_set("Etc/UTC");
	$categories = array('breco','ill','ao','grieve','breview','feedback');
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($u = mysql_query("select * from users where bitsid='".$user."'")){
			$userinfo = mysql_fetch_array($u);
			if(sha1($password)==$userinfo['password']){
				if($userinfo["category"]!="Admin"){
					if(isset($_GET['cat'],$_GET['inputArray'])){
						$cat = $categories[intval($_GET['cat'])-1];
						$id = mysql_num_rows(mysql_query("select * from communications"));
						$id = $id + 1;
						$conversations["data"] = array();
						$conversations["data"][$id] = array();
						$conversations["data"][$id]['bitsid'] = $user;
						$conversations["data"][$id]['category'] = $userinfo['category'];
						$conversations["data"][$id]['cat'] = $cat;
						$conversations["data"][$id]['name'] = $userinfo['name'];
						$conversations["data"][$id]['date'] = date("d/m/Y");
						$conversations["data"][$id]['time'] = date('h:i A');
						$conversations["data"][$id]['admins'] = "";
						$conversations["data"][$id]['status'] = "open";
						if($cat == "breview"){
							$title = $_GET['inputArray'][0];
							$author = $_GET['inputArray'][1];
							if(mysql_query("select id from bookreview where bitsid = '".$user."' and title like '%".$title."%' and author like '%".$author."%' and status not like '%inactive%'")){
								$conversations['message'] = "Book Review has already been submitted.";
							}
							else{
								$conversations["data"][$id]['topic'] = "I Just Read: ".$title;
								$comms = "Title: ".$title."<br />"."Author: ".$author."<br />"."Review: ".$_GET['inputArray'][2];
							}
						}
						else if($cat == "grieve"){
							if($userinfo['category']!="Admin"){
								$conversations["data"][$id]['topic'] = "Not Happy with: ".$_GET['inputArray'][0];
								$comms = $_GET['inputArray'][1];
							}
						}
						else if($cat == "ill"){
							$nature = $_GET['inputArray'][0];
							if($nature=="Books"){
								$conversations["data"][$id]['topic'] = "Book Not Found: ".$_GET['inputArray'][1];
								$comms = "Title: ".$_GET['inputArray'][1]."<br />"."Author: ".$_GET['inputArray'][2]."<br />"."Accession No.: ".$_GET['inputArray'][3];
							}
							else{
								$month = $_GET['inputArray'][2];
								$year = $_GET['inputArray'][3];
								$conversations["data"][$id]['topic'] = "Journal Not Found: ".$title;
								$comms = "Journal/Magazine Name: ".$title."<br />"."Month: ".$month."<br />"."Year.: ".$year;
							}
						}
						else if($cat == "breco"){
							$number = count($_GET)/5;
							$comms = "<table><tr><th>S. No.</th><th>Title</th><th>Author</th><th>Edition</th><th>Publisher</th><th>Year</th></tr>";
							for($i=0;$i<$number;$i++){
								$comms .= "<tr><td>".($i+1)."</td><td>".$_GET['inputArray'][5*$i]."</td><td>".$_GET['inputArray'][5*$i + 1]."</td><td>".$_GET['inputArray'][5*$i + 2]."</td><td>".$_GET['inputArray'][5*$i + 3]."</td><td>".$_GET['inputArray'][5*$i + 4]."</td></tr>";
							}
							$comms .= "</table>";
							$conversations["data"][$id]['topic'] = "Books Recommended";
						}
						else if($cat == "feedback"){
							$conversations["data"][$id]['topic'] = "".$_GET['inputArray'][0];
							$comms = $_GET['inputArray'][1];
						}
						else{
							$conversations["data"][$id]['topic'] = "Database Not Accessible! : ".$_GET['inputArray'][0];
							$comms = "Name of Database: ".$_GET['inputArray'][0]."<br />"."Title of Journal: ".$_GET['inputArray'][1]."<br />"."Location where access of denied: ".$_GET['inputArray'][2];
						}
						if($x = mysql_query("select * from automessage  where type='".$cat."' and status='first'")){
							$auto = mysql_fetch_array($x);
							$comm = $userinfo['name']."(Date-".$conversations["data"][$id]['date'].",Time-".$conversations["data"][$id]['time'].")| ".$comms." // From Library(Date-".$conversations["data"][$id]['date'].",Time-".$conversations["data"][$id]['time'].")| ".$auto['text']." //";
						}
						else{
							$comm = $userinfo['name']."(Date-".$conversations["data"][$id]['date'].",Time-".$conversations["data"][$id]['time'].")| ".$comms." //";
						}
						$conversations["data"][$id]['talk'] = $comm;
						if(mysql_query("insert into communications (bitsid,topic,comms,status,admins,id,date,cat,time) values ('".$user."','".$conversations["data"][$id]['topic']."','".$comm."','open','','".$id."','".$conversations["data"][$id]['date']."','".$cat."','".$conversations["data"][$id]['time']."')")){
							$conversations['message'] = 'New conversation created!';
						}
						else{
							$conversations['err_message'] = 'Something went Wrong!';
						}
					}
					else{
						$conversations['err_message'] = 'Incomplete Parameters';
					}
				}
				else{
					$conversations['err_message'] = 'Administrator can\'t submit a new communication';
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