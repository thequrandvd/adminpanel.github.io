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
			
			
			if( !empty($video_title) && 
				!empty($cid) && 
				!empty($video_duration) && 
				!empty($video_description)) {
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_gallery (video_title, cat_id, video_duration, video_url, video_id,  video_description, video_type)
						VALUES(?, ?, ?, ?, ?, ?, ?)";
						
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('sssssss', 
								$video_title, 
								$cid, 
								$video_duration, 
								$video_url, 
								$video_id, 
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
		
		</div>

		<div class="col-md-8">
		<label>Youtube Video URL : <a data-toggle="modal" href="#myModal"> ? </a></label> 
		<input type="text" class="form-control" name="video_url" placeholder="https://www.youtube.com/watch?v=7PCkvCPvDXk" required/>
		<br/>

		<label>Youtube Video ID : <a data-toggle="modal" href="#myModal2"> ? </a></label>
		<input type="text" class="form-control" name="video_id" placeholder="7PCkvCPvDXk" required/>
		<br/>

		<input type="hidden" class="form-control" name="video_type" value="youtube" required/>

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

<!-- Modal 1 -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">How to set Youtube Video URL?</h4>
        </div>
        <div class="modal-body">
          	<label>Please as per our Application, Video Link Must be Same as That</label>
		  	<pre>https://www.youtube.com/watch?v=7PCkvCPvDXk</pre>
		  	<br>
			<label>True :</label>
			<br>
			<div class="alert alert-success">
				https://www.youtube.com/watch?v=7PCkvCPvDXk
			</div> 
			<br>
			<label>False :</label>
			<br>
			<div class="alert alert-danger">
				https://www.youtube.com/watch?v=7PCkvCPvDXkg&hd=1 thats not support
			</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- Modal 1 -->
  <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">How to Get Youtube Video ID?</h4>
	        </div>
	        <div class="modal-body">
	          	<label>As Example youtube link below :</label>
			  	<pre>https://www.youtube.com/watch?v=7PCkvCPvDXk</pre>
			  	<br>
				<label>Copy the Characters after " = " : </label>
				<br>
				<div class="alert alert-info">
					https://www.youtube.com/watch?v=<label>7PCkvCPvDXk</label>
				</div> 
				<br>
				<label>Youtube Video ID like this :</label>
				<br>
				<div class="alert alert-success">
					7PCkvCPvDXk
			</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
			

<?php 
	$stmt_category->close();
	include_once('includes/close_database.php'); ?>