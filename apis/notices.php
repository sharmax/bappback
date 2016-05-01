<?php
	include("../config.php");
	$notices['data'] = array();
	if($images = mysql_query("select * from noticeboard ORDER BY id ASC")){
		$image = mysql_fetch_array($images);		
		while($image){
			$notices['data'][$image['id']] = array();
			$notices['data'][$image['id']]['image'] = $image['image'];
			$notices['data'][$image['id']]['link'] = $image['link'];
			$notices['data'][$image['id']]['type'] = "notice";
			$image = mysql_fetch_array($images);
		}
	}
	$notices['data']['botw'] = array();
	if($bo = mysql_query("select * from book_of_the_month_main ORDER BY id DESC LIMIT 0,1")){
		$botw = mysql_fetch_array($bo);
		if($botw){
			$notices['data']['botw']['title'] = $botw['book_title'];
			$notices['data']['botw']['author'] = $botw['book_author'];
			$notices['data']['botw']['image'] = $botw['book_image_path'];
			$notices['data']['botw']['type'] = "botw";
		}
	echo json_encode($notices);
?>