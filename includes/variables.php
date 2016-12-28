<?php
	//database configuration
	$host 		= "localhost";
	$user 		= "root";
	$pass 		= "";
	$database	= "your_videos_channel";
	$connect 	= new mysqli($host, $user, $pass,$database) or die("Error : ".mysql_error());


	//set path url for your video uploaded
	$video_base_url = "http://your_domain_name/your_videos_channel/upload/"

?>