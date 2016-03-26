<?php
	include("../config.php");
	$conversations['err_message'] = '';
	$conversations['message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				if(isset($_GET["action"])){
					date_default_timezone_set("Asia/Kolkata");
					$action = $_GET["action"];
					if($action == "update"){
						$conversations['data'] = array();
						$today = date("Y-m-d");
						$news = mysql_query("select * from newsfeed where DATEDIFF('".$today."',date) <= 7 ORDER by date DESC");
						while($headlines = mysql_fetch_array($news)){
							$conversations['data'][$headlines['id']] = array();
							$conversations['data'][$headlines['id']]["news_type"] = $headlines['news_type'];
							$conversations['data'][$headlines['id']]["title"] = $headlines['title'];
							$conversations['data'][$headlines['id']]["url"] = $headlines['url'];
							if($headlines['url'] == "" && $headlines['image_path'] != ""){
								$conversations['data'][$headlines['id']]["url"] = $headlines['image_path'];
							}
							$conversations['data'][$headlines['id']]["date"] = $headlines['date'];
							$conversations['data'][$headlines['id']]["added_by"] = $headlines['added_by'];
							$conversations['data'][$headlines['id']]["newspaper"] = $headlines['newspaper_name'];
							$conversations['data'][$headlines['id']]["keywords"] = $headlines['keywords'];
							$conversations['data'][$headlines['id']]["pages"] = $headlines['page'];
						}
						if(count($conversations['data']) == 0){
							$conversations['err_message'] = "No headlines found";		
						}
					}
					else if($action == "search"){
						$conversations['data'] = array();
						if(isset($_GET['startDate'], $_GET['endDate'])){
							$startDate = $_GET['startDate'];
							$endDate = $_GET['endDate'];
							$sql = array();
							echo $startDate."<br />";
							$keyword = "";
							if(isset($_GET['keyword']) && $_GET['keyword'] != ""){
								$keyword = $_GET['keyword'];
								$n = substr_count($keyword," ");
								$s = 0;
								$e = strpos($keyword," ",$s);
								for($i = 0; $i < $n; $i++){
									$sql[] = 'keywords LIKE "%'.substr($keyword, $s, $e).'%"';
									$s = $e + 1;
								}
								$sql = 'SELECT * FROM newsfeed WHERE DATEDIFF(date,"'.$startDate.'") >= 0 and DATEDIFF(date,"'.$endDate.'") <= 0 and '.implode(" OR ", $sql).'';
							}
							else{
								$sql = 'SELECT * FROM newsfeed WHERE DATEDIFF(date,"'.$startDate.'") >= 0 && DATEDIFF(date,"'.$endDate.'") <= 0';
							}
							echo $sql."<br />";
							$news = mysql_query($sql);
							while($headlines = mysql_fetch_array($news)){
								$conversations['data'][$headlines['id']] = array();
								$conversations['data'][$headlines['id']]["news_type"] = $headlines['news_type'];
								$conversations['data'][$headlines['id']]["title"] = $headlines['title'];
								$conversations['data'][$headlines['id']]["url"] = $headlines['url'];
								if($headlines['url'] == "" && $headlines['image_path'] != ""){
									$conversations['data'][$headlines['id']]["url"] = $headlines['image_path'];
								}
								$conversations['data'][$headlines['id']]["date"] = $headlines['date'];
								$conversations['data'][$headlines['id']]["added_by"] = $headlines['added_by'];
								$conversations['data'][$headlines['id']]["newspaper"] = $headlines['newspaper_name'];
								$conversations['data'][$headlines['id']]["keywords"] = $headlines['keywords'];
								$conversations['data'][$headlines['id']]["pages"] = $headlines['page'];
							}
							$conversations['message'] = count($conversations['data'])." headline(s) found from '".$startDate."' to '".$endDate."' for keywords '".$keyword."'";
						}
						else{
							$conversations['err_message'] = "Incomplete Parameters!";
						}
					}
					else{
						$conversations['err_message'] = "Incomplete Parameters!";
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