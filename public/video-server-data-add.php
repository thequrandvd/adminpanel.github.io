<?php
	include_once('includes/connect_database.php');
	include_once('functions.php'); 
	require_once("thumbnail_images.class.php");
?>
<div id="content" class="container col-md-12">
	<?php 
		$sql_query = "SELECT cid, category_name 
			FROM tbl_category 
			ORDER BY cid ASC";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($category_data['cid'], 
				$category_data['category_name']
				);		
		}	
			
		//$max_serve = 10;			
		if(isset($_POST['btnAdd'])){
			$video_title = $_POST['video_title'];
			$cid = $_POST['cid'];
			$video_duration = $_POST['video_duration'];
			$video_url = $_POST['video_url'];
			$video_id = $_POST['video_id'];
			$video_description = $_POST['video_description'];
			$video_type = $_POST['video_type'];
				
			// get image info
			$video_thumbnail = $_FILES['video_thumbnail']['name'];
			$image_error = $_FILES['video_thumbnail']['error'];
			$image_type = $_FILES['video_thumbnail']['type'];
			
				
			// create array variable to handle error
			$error = array();
			
			if(empty($video_title)){
				$error['video_title'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}
				
			if(empty($cid)){
				$error['cid'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}				
				
			if(empty($video_duration)){
				$error['video_duration'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}			

			if(empty($video_description)){
				$error['video_description'] = " <span class='label label-danger'>Required, please fill out this field!!</span>";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["video_thumbnail"]["name"]));
					
			if($image_error > 0){
				$error['video_thumbnail'] = " <span class='label label-danger'>Image Not Uploaded!!</span>";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['video_thumbnail'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
			}
				
			if( !empty($video_title) && 
				!empty($cid) && 
				!empty($video_duration) && 
				empty($error['video_thumbnail']) && 
				!empty($video_description)) {
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['video_thumbnail']['name']);
				$function = new functions;
				$video_thumbnail = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					
				// upload new image
				$unggah = 'upload/'.$video_thumbnail;
				$upload = move_uploaded_file($_FILES['video_thumbnail']['tmp_name'], $unggah);

				error_reporting(E_ERROR | E_PARSE);
				copy($video_thumbnail, $unggah);
									 
											$thumbpath= 'upload/thumbs/'.$video_thumbnail;
											$obj_img = new thumbnail_images();
											$obj_img->PathImgOld = $unggah;
											$obj_img->PathImgNew =$thumbpath;
											$obj_img->NewWidth = 200;
											$obj_img->NewHeight = 200;
											if (!$obj_img->create_thumbnail_images()) 
												{
												echo "Thumbnail not created... please upload image again";
													exit;
												}	 
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_gallery (video_title, cat_id, video_duration, video_url, video_id, video_thumbnail, video_description, video_type)
						VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
						
				$upload_image = $video_thumbnail;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('ssssssss', 
								$video_title, 
								$cid, 
								$video_duration, 
								$video_url, 
								$video_id, 
								$upload_image,
								$video_description,
								$video_type
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_menu'] = " <span class='label label-primary'>Success added</span>";
				}else {
					$error['add_menu'] = " <span class='label label-danger'>Failed</span>";
				}
			}
				
			}
	?>
	<div class="col-md-12">
	<h1>Add Video <?php echo isset($error['add_menu']) ? $error['add_menu'] : '';?></h1>
	<hr />
	</div>

	<div class="col-md-12">
	<form method="post" enctype="multipart/form-data">

	<div class="col-md-9">
		<div class="col-md-4">
		<label>Video Title :</label>
		<input type="text" class="form-control" name="video_title" required/>
		<br/>
	    
	    <label>Video Duration :</label>
		<input type="text" class="form-control" name="video_duration" placeholder="5:59" required/>
		<br/>

	    <label>Category :</label><?php echo isset($error['cid']) ? $error['cid'] : '';?>
		<select name="cid" class="form-control">
			<?php while($stmt_category->fetch()){ ?>
				<option value="<?php echo $category_data['cid']; ?>"><?php echo $category_data['category_name']; ?></option>
			<?php } ?>
		</select>
		
		<br/>
		<label>Video Thumbnail :</label><?php echo isset($error['video_thumbnail']) ? $error['video_thumbnail'] : '';?>
		<input type="file" name="video_thumbnail" id="video_thumbnail"/>
		</div>

		<div class="col-md-8">
		<label>Server Video Path :</label>
		<input type="text" class="form-control" name="video_url" placeholder="http://xyz.com/videoplayback1.mp4" required/>
		<br/>

		<input type="hidden" class="form-control" name="video_id" value="000q1w2" required/>
		<input type="hidden" class="form-control" name="video_type" value="server" required/>

		<label>Video Description :</label><?php echo isset($error['video_description']) ? $error['video_description'] : '';?>
		<textarea name="video_description" id="video_description" class="form-control" rows="10"></textarea>
		<script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">                        
            CKEDITOR.replace( 'video_description' );
        </script>
		</div>
	</div>
	
	<br/>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading">Add</div>
				<div class="panel-body">
					<input type="submit" class="btn-primary btn" value="Add" name="btnAdd" />&nbsp;
					<input type="reset" class="btn-danger btn" value="Clear"/>
				</div>
		</div>
	</div>
	</form>
	</div>	
	<div class="separator"> </div>
</div>
			

<?php 
	$stmt_category->close();
	include_once('includes/close_database.php'); ?>