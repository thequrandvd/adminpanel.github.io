<?php

	include 'includes/variables.php';

	DEFINE ('DB_HOST', $host);
	DEFINE ('DB_USER', $user);	 
	DEFINE ('DB_PASSWORD', $pass);
	DEFINE ('DB_NAME', $database);
	 
	$mysqli = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MySQL');
	@mysql_select_db (DB_NAME) OR die ('Could not select the database');
 
 ?>
<?php
 
 	mysql_query("SET NAMES 'utf8'"); 
	//mysql_query('SET CHARACTER SET utf8');
	
	if(isset($_GET['cat_id']))
	{
			//$query="SELECT * FROM tbl_category WHERE cid='".$_GET['cat_id']."' ORDER BY tbl_category.cid DESC";		
			//$resouter = mysql_query($query);
			
			$query="SELECT * FROM tbl_category c,tbl_gallery n WHERE c.cid=n.cat_id and c.cid='".$_GET['cat_id']."' ORDER BY n.id DESC";			
			$resouter = mysql_query($query);
			
	}
	else if(isset($_GET['id']))
	{		
			$id = $_GET['id'];

			$query="SELECT * FROM tbl_category c, tbl_gallery n WHERE c.cid = n.cat_id && n.id = '$id'";					
			$resouter = mysql_query($query);
			
	}
	else if(isset($_GET['latest']))
	{
			$limit=$_GET['latest'];	 	
			
			$query="SELECT * FROM tbl_category c,tbl_gallery n WHERE c.cid=n.cat_id ORDER BY n.id DESC LIMIT $limit";			
			$resouter = mysql_query($query);
	}
	else if(isset($_GET['apps_details']))
	{ 
			$query="SELECT * FROM tbl_settings WHERE id='1'";		
			$resouter = mysql_query($query);
	}
	else
	{	
			$query="SELECT * FROM tbl_category ORDER BY cid DESC";			
			$resouter = mysql_query($query);
	}
     
    $set = array();
     
    $total_records = mysql_num_rows($resouter);
    if($total_records >= 1){
     
      while ($link = mysql_fetch_array($resouter, MYSQL_ASSOC)){
	   
        $set['YourVideosChannel'][] = $link;
      }
    }
     
     echo $val= str_replace('\\/', '/', json_encode($set));
	 	 
	 
?>