<?php
	include_once('includes/connect_database.php');
	include_once('functions.php'); 
?>

<?php

	//Total category count
	$sql_category   = "SELECT COUNT(*) as num FROM tbl_category";
	$total_category = mysqli_query($connect, $sql_category);
	$total_category = mysqli_fetch_array($total_category);
	$total_category = $total_category['num'];

	$sql_youtube   = "SELECT COUNT(*) as num FROM tbl_gallery WHERE video_type = 'youtube'";
	$total_youtube = mysqli_query($connect, $sql_youtube);
	$total_youtube = mysqli_fetch_array($total_youtube);
	$total_youtube = $total_youtube['num'];

	$sql_url   = "SELECT COUNT(*) as num FROM tbl_gallery WHERE video_type = 'server'";
	$total_url = mysqli_query($connect, $sql_url);
	$total_url = mysqli_fetch_array($total_url);
	$total_url = $total_url['num'];

	$sql_upload   = "SELECT COUNT(*) as num FROM tbl_gallery WHERE video_type != 'server' AND video_type != 'youtube'";
	$total_upload = mysqli_query($connect, $sql_upload);
	$total_upload = mysqli_fetch_array($total_upload);
	$total_upload = $total_upload['num'];

?>
<div id="content" class="container col-md-12">

<div class="col-md-12">
		<h1>Dashboard</h1>
		<hr/>
	</div>

 		<a href="category.php">
			<div class="col-sm-6 col-md-2">
	            <div class="thumbnail">    
	              <div class="caption">
	              <center>
	              <img src="images/ic_category.png" width="100" height="100">
	                <h3><?php echo $total_category;?></h3>
	                <p class="detail">Category</p>  
	                </center>
	              </div>
	            </div>
	         </div>
	    </a>

	    <a href="video-youtube.php">
	          <div class="col-sm-6 col-md-2">
	            <div class="thumbnail">    
	              <div class="caption">
	              <center>
	              	<div class="btn-group">
				    	<img src="images/ic_youtube.png" width="100" height="100">
			                <h3><?php echo $total_youtube;?></h3>
			                <p class="detail">YouTube Source</p>  
	                </center>
	              </div>
	            </div>
	          </div>
	    </a>

	    <a href="video-server.php">
	          <div class="col-sm-6 col-md-2">
	            <div class="thumbnail">    
	              <div class="caption">
	              <center>
	              	<div class="btn-group">
				    	<img src="images/ic_url.png" width="100" height="100">
			                <h3><?php echo $total_url;?></h3>
			                <p class="detail">Url Video Source</p>  
	                </center>
	              </div>
	            </div>
	          </div>
	    </a>

	    <a href="video-upload.php">
	          <div class="col-sm-6 col-md-2">
	            <div class="thumbnail">    
	              <div class="caption">
	              <center>
	              	<div class="btn-group">
				    	<img src="images/ic_upload.png" width="100" height="100">
			                <h3><?php echo $total_upload;?></h3>
			                <p class="detail">Upload Video</p>  
	                </center>
	              </div>
	            </div>
	          </div>
	    </a>

        <a href="admin.php">
          <div class="col-sm-6 col-md-2">
            <div class="thumbnail"> 
              <div class="caption">
              <center>
              <img src="images/ic_setting.png" width="100" height="100">
                <h3><br></h3>
                <p class="detail">Setting</p>     
                </center>
              </div>
            </div>
          </div>
        </a>
</div>

<?php include_once('includes/close_database.php'); ?>