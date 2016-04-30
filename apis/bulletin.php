<?php
	include("../config.php");
	$conversations['err_message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				date_default_timezone_set("Asia/Kolkata");
				$conversations['data'] = '';
				$categories = array('CHEMICAL','CIVIL','EEE','CS','MECH','PHARMA','BIO','CHEM','ECO','MATHS','PHY','HUM','MAN');
				$types = array('one','two','three','four');
				$datem= date('F');
				$dateY= date('Y');
				$res = mysql_query("select * from bulletin where Month = '".$datem."' and Year = '".$dateY."' ORDER BY code");
				while($result = mysql_fetch_array($res)){
					$conversations['data'][$result['code']]['books'] = array();
					$conversations['data'][$result['code']]['journals'] = array();
					$conversations['data']['issue'] = $result['issue_number'];
					$conversations['data']['volume'] = $result['volume_number'];
					for($i = 1; $i <= 4; $i++){
						$conversations['data'][$result['code']]['books']['book'.$i] =  array();
						$conversations['data'][$result['code']]['journals']['journal'.$i] =  array();
						$conversations['data'][$result['code']]['books']['book'.$i]['title'] = $result['title'.$i];
						$conversations['data'][$result['code']]['books']['book'.$i]['author'] = $result['auth'.$i];
						$conversations['data'][$result['code']]['books']['book'.$i]['pic'] = $result['pic'.$i];
						$conversations['data'][$result['code']]['books']['book'.$i]['url'] = $result['url'.$i];
						$conversations['data'][$result['code']]['books']['book'.$i]['type'] = $result['book_type_'.$types[$i-1]];
						$conversations['data'][$result['code']]['journals']['journal'.$i]['title'] = $result['j'.$i];
						$conversations['data'][$result['code']]['journals']['journal'.$i]['pic'] = $result['tc'.$i];
						$conversations['data'][$result['code']]['journals']['journal'.$i]['url'] = $result['jurl'.$i];
						$conversations['data'][$result['code']]['journals']['journal'.$i]['type'] = $result['Journal_type_'.$types[$i-1]];
					}
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