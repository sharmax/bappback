<?php
	include("../config.php");
	$bulletins['err_message'] = '';
	if(isset($_GET['username'],$_GET['password'])){
		$user = $_GET['username'];
		$password = $_GET['password'];
		if($userinfo = mysql_fetch_array(mysql_query("select * from users where bitsid='".$user."'"))){
			if(sha1($password) == $userinfo['password']){
				date_default_timezone_set("Asia/Kolkata");
				$bulletins['data'] = '';
				$categories = array('CHEMICAL','CIVIL','EEE','CS','MECH','PHARMA','BIO','CHEM','ECO','MATHS','PHY','HUM','MAN');
				$types = array('one','two','three','four');
				$datem= date('F');
				$dateY= date('Y');
				$res = mysql_query("select * from bulletin where Month = '".$datem."' and Year = '".$dateY."' ORDER BY code");
				while($result = mysql_fetch_array($res)){
					$bulletins['data'][$result['code']]['books'] = array();
					$bulletins['data'][$result['code']]['journals'] = array();
          $bulletins['issue'] = $result['issue_number'];
					$bulletins['volume'] = $result['volume_number'];
					for($i = 1; $i <= 4; $i++){
						$bulletins['data'][$result['code']]['books']['book'.$i] =  array();
						$bulletins['data'][$result['code']]['journals']['journal'.$i] =  array();
						$bulletins['data'][$result['code']]['books']['book'.$i]['pic'] = $result['pic'.$i];
						$bulletins['data'][$result['code']]['books']['book'.$i]['url'] = $result['url'.$i];
						$bulletins['data'][$result['code']]['books']['book'.$i]['type'] = $result['book_type_'.$types[$i-1]];
						$bulletins['data'][$result['code']]['journals']['journal'.$i]['pic'] = $result['tc'.$i];
						$bulletins['data'][$result['code']]['journals']['journal'.$i]['url'] = $result['jurl'.$i];
						$bulletins['data'][$result['code']]['journals']['journal'.$i]['type'] = $result['Journal_type_'.$types[$i-1]];
					}
				}
        if($bulletins['data'] == ''){
          $bulletins['err_message'] = 'No bulletins found for '.$datem;
        }
			}
			else{
				$bulletins['err_message'] = 'Invalid Password!';
			}
		}
		else{
			$bulletins['err_message'] = "Invalid Username!";	
		}
	}
	else{
		$bulletins['err_message'] = "Incomplete Parameters!";
	}
	echo json_encode($bulletins);
?>
