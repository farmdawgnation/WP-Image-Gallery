<div class="wrap">
	<h2>
		Manage Images
		<a href="?page=wpig&action=addImage&galleryId=<?php echo $gallery->id ?>" class="button add-new-h2">Add New</a>
	</h2>
	
	<table class="widefat fixed">
		<thead>
		<tr>
			<th scope="col" class="manage-column" style="width: 200px">Image</th>
			<th scope="col" class="manage-column">Data</th>
		</tr>
		</thead>
		<?php if(count($photos) == 0) { ?>
		<tr>
			<td colspan="2">
				<em>You have no images in this gallery.</em>
			</td>
		</tr>
		<?php } else { ?>
			<?php foreach($photos as $photo) { ?>
			<tr>
				<td>IMAGE HERE</td>
				<td>
					<div>
						<label>Image Title:</label>
						<input type="text" name="image[<?php echo $photo->id ?>][title]" value="<?php echo $photo->title ?>" />
					</div>
					<div>
						<label>Image Description:</label>
						<textarea name="image[<?php echo $photo->id ?>][description]"><?php echo $photo->description ?></textarea>
					</div>
				</td>
			</tr>
			<?php } ?>
		<?php } ?>
	</table>
	
	<form method="post" enctype="multipart/form-data">
		<h3 class="title">Image Upload</h3>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Valid Image Types</th>
			<td>
				<input type="file" class="regular-text" name="upload" />
				<br/><span class="description">You may upload single images, or a ZIP file of images.</span>
				<br/><span class="description">Max upload size: <?php echo (ini_get('upload_max_filesize')) ?></span>
				<br/><span class="description">Valid formats: <?php echo str_replace("|", ", ", get_option('wpig2_imgtypes')) ?></span>
			</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Upload Image(s)') ?>" />
		</p>
	</form>
</div>