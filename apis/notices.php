<?php
	include("../config.php");
	if($images = mysql_query("select * from noticeboard ORDER BY id ASC")){
		$image = mysql_fetch_array($images);
		$notices['data'] = array();
		while($image){
			$notices['data'][$image['id']] = array();
			$notices['data'][$image['id']]['image'] = $image['image'];
			$notices['data'][$image['id']]['link'] = $image['link'];
			$image = mysql_fetch_array($images);
		}
	}
	echo json_encode($notices);
?>