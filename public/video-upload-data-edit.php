<?php
	include_once('includes/connect_database.php');
	include_once('functions.php'); 
	require_once("thumbnail_images.class.php");
?>
<div id="content" class="container col-md-12">
	<?php 
	
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
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
			
		$sql_query = "SELECT video_thumbnail, video_url FROM tbl_gallery WHERE id = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_video_thumbnail, $previous_video_url);
			$stmt->fetch();
			$stmt->close();
		}
		
		
		if(isset($_POST['btnEdit'])){
			
			$video_title = $_POST['video_title'];
			$cid = $_POST['cid'];
			$video_duration = $_POST['video_duration'];
			$video_id = $_POST['video_id'];
			$video_description = $_POST['video_description'];

			$video_url = $_FILES['video_url']['name'];
			$video_error = $_FILES['video_url']['error'];
			$video_type = $_FILES['video_url']['type'];
			
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

			$allowedExts2 = array("mp4", "3gp");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["video_thumbnail"]["name"]));

			error_reporting(E_ERROR | E_PARSE);
			$extension2 = end(explode(".", $_FILES["video_url"]["name"]));
			
			if(!empty($video_thumbnail)){
				if(!(($image_type == "image/gif") || 
					($image_type == "image/jpeg") || 
					($image_type == "image/jpg") || 
					($image_type == "image/x-png") ||
					($image_type == "image/png") || 
					($image_type == "image/pjpeg")) &&
					!(in_array($extension, $allowedExts))){
					
					$error['video_thumbnail'] = "*<span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
				}
			}

			if(!empty($video_url)){
				if(!(($video_type == "video/3gp") || 
					($video_type == "video/mp4")) &&
					!(in_array($extension2, $allowedExts2))){
					
					$error['video_url'] = "*<span class='label label-danger'>Video type must 3gp or mp4!</span>";
				}
			}
			
					
			if( !empty($video_title) && 
				!empty($cid) && 
				!empty($video_duration) && 
				!empty($video_description) &&
				empty($error['video_url']) && 
				empty($error['video_thumbnail'])) {
				
				if(!empty($video_thumbnail) && !empty($video_url)) {
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['video_thumbnail']['name']);
					$function = new functions;
					$video_thumbnail = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
				
					// delete previous image
					$delete = unlink('upload/'."$previous_video_thumbnail");
					$delete = unlink('upload/thumbs/'."$previous_video_thumbnail");
					
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

					// create random video file name
					$string2 = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['video_url']['name']);
					$function2 = new functions;
					$video_url = $function2->get_random_string($string2, 4)."-".date("Y-m-d").".".$extension2;
					$delete = unlink("$previous_video_url");
					$unggah2 = 'upload/'.$video_url;
					$upload2 = move_uploaded_file($_FILES['video_url']['tmp_name'], $unggah2);	 
	  
					// updating all data
					$sql_query = "UPDATE tbl_gallery 
							SET video_title = ? , cat_id = ?, video_duration = ?, 
							video_url = ?, video_id = ?, video_thumbnail = ?, video_description = ? 
							WHERE id = ?";
					
					$upload_video = $video_base_url  . $video_url;
					$upload_image = $video_thumbnail;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssssssss', 
									$video_title, 
									$cid, 
									$video_duration, 
									$upload_video, 
									$video_id, 
									$upload_image,
									$video_description,
									$ID);
						
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				} 

				if(!empty($video_thumbnail)) {
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['video_thumbnail']['name']);
					$function = new functions;
					$video_thumbnail = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
				
					// delete previous image
					$delete = unlink('upload/'."$previous_video_thumbnail");
					$delete = unlink('upload/thumbs/'."$previous_video_thumbnail");
					
					// upload new image
					$unggah = 'upload/'.$video_thumbnail;
					$upload = move_uploaded_file($_FILES['video_thumbnail']['tmp_name'], $unggah);

					error_reporting(E_ERROR | E_PARSE);
					copy($video_thumbnail, $unggah);
									 
											$thumbpath= 'upload/thumbs/'.$video_thumbnail;
											$obj_img = new thumbnail_images();
											$obj_img->PathImgOld = $unggah;
											$obj_img->PathImgNew =$thumbpath;
											$obj_img->NewWidth = 72;
											$obj_img->NewHeight = 72;
											if (!$obj_img->create_thumbnail_images()) 
												{
												echo "Thumbnail not created... please upload image again";
													exit;
												}	 	 
	  
					// updating all data
					$sql_query = "UPDATE tbl_gallery 
							SET video_title = ? , cat_id = ?, video_duration = ?, video_id = ?, video_thumbnail = ?, video_description = ? 
							WHERE id = ?";
					
					$upload_video = $video_base_url  . $video_url;
					$upload_image = $video_thumbnail;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssssss', 
									$video_title, 
									$cid, 
									$video_duration,
									$video_id, 
									$upload_image,
									$video_description,
									$ID);
						
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}

				if(!empty($video_url)) {
					
					// create random video file name
					$string2 = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['video_url']['name']);
					$function2 = new functions;
					$video_url = $function2->get_random_string($string2, 4)."-".date("Y-m-d").".".$extension2;
					$delete = unlink("$previous_video_url");
					$unggah2 = 'upload/'.$video_url;
					$upload2 = move_uploaded_file($_FILES['video_url']['tmp_name'], $unggah2);	 
	  
					// updating all data
					$sql_query = "UPDATE tbl_gallery 
							SET video_title = ? , cat_id = ?, video_duration = ?, 
							video_url = ?, video_id = ?, video_description = ? 
							WHERE id = ?";
					
					$upload_video = $video_base_url  . $video_url;
					$upload_image = $video_thumbnail;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssssss', 
									$video_title, 
									$cid, 
									$video_duration, 
									$upload_video, 
									$video_id, 
									$video_description,
									$ID);
						
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}

				else {
					
					// updating all data except image and video file
					$sql_query = "UPDATE tbl_gallery SET video_title = ?, cat_id = ?, video_duration = ?, video_id = ?, video_description = ? WHERE id = ?";
							
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssssss', 
									$video_title, 
									$cid,
									$video_duration, 
									$video_id, 
									$video_description,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}
					
				// check update result
				if($update_result){
					$error['update_data'] = " <span class='label label-primary'>Success update video.</span>";
				}else{
					$error['update_data'] = " <span class='label label-danger'>Failed to update video.</span>";
				}
			}
			
		}
		
		// create array variable to store previous data
		$data = array();
			
		$sql_query = "SELECT * FROM tbl_gallery WHERE id = ?";
			
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['id'],
					$data['cid'],  
					$data['video_title'], 
					$data['video_url'], 
					$data['video_id'],
					$data['video_thumbnail'],
					$data['video_duration'], 
					$data['video_description'],
					$data['video_type']
					);
			$stmt->fetch();
			$stmt->close();
		}
		
			
	?>
	<div class="col-md-12">
	<h1>Edit Video <?php echo isset($error['update_data']) ? $error['update_data'] : '';?></h1>
	<hr />
	</div>
	<form method="post" enctype="multipart/form-data">
	<div class="col-md-9">
		<div class="col-md-4">
			<label>Video Title :</label>
			<input type="text" name="video_title" class="form-control" value="<?php echo $data['video_title']; ?>" required />
		<br/>

	    <label>Video Duration :</label>
		<input type="text" name="video_duration" id="video_duration" value="<?php echo $data['video_duration']; ?>" class="form-control" required>
		<br/>

	    <label>Category :</label><?php echo isset($error['cid']) ? $error['cid'] : '';?>
		<select name="cid" class="form-control">
			<?php while($stmt_category->fetch()){ 
				if($category_data['cid'] == $data['cid']){?>
					<option value="<?php echo $category_data['cid']; ?>" selected="<?php echo $data['cid']; ?>" ><?php echo $category_data['category_name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $category_data['cid']; ?>" ><?php echo $category_data['category_name']; ?></option>
				<?php }} ?>
		</select>
		
	    <br/>
		<label>Image :</label><?php echo isset($error['video_thumbnail']) ? $error['video_thumbnail'] : '';?>
		<input type="file" name="video_thumbnail" id="video_thumbnail"/><br />
		<img src="upload/<?php echo $data['video_thumbnail']; ?>" width="210" height="160"/>
		</div>

		<div class="col-md-8">
		<label>Upload New Video :</label> (max : 8 Mb) <?php echo isset($error['video_url']) ? $error['video_url'] : '';?>
		<input type="file" name="video_url" id="video_url" />
		<p>Current video path : <?php echo $data['video_url']; ?></p>
		<br/>

		<input type="hidden" class="form-control" name="video_id" value="000q1w2" required/>

		<label>Video Description :</label><?php echo isset($error['video_description']) ? $error['video_description'] : '';?>
		<textarea name="video_description" id="video_description" class="form-control" rows="16"><?php echo $data['video_description']; ?></textarea>
		<script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">                        
        	CKEDITOR.replace( 'video_description' );
    	</script>
		</div>
	</div>
		
	<div class="col-md-3">
	<br/>
		<div class="panel panel-default">
			<div class="panel-heading">Add</div>
				<div class="panel-body">
					<input type="submit" class="btn-primary btn" value="Update" name="btnEdit" />
				</div>
		</div>
	</div>
	</form>
	<div class="separator"> </div>
</div>

<?php 
	$stmt_category->close();
	include_once('includes/close_database.php'); ?>