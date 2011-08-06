<div class="wrap">
	<?php if($_GET['action'] == 'add') { ?>
	<h2>Add Gallery</h2>
	<?php } else { ?>
	<h2>Edit Gallery</h2>
	<?php } ?>
	
	<form method="post">
		<h3 class="title">General Settings</h3>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Gallery Title</th>
			<td>
				<input type="text" class="regular-text" name="title" value="<?php if(isset($gallery)) echo $gallery->title; ?>" />
			</td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Gallery Slug</th>
			<td>
				<input type="text" class="regular-text" name="slug" value="<?php if(isset($gallery)) echo $gallery->slug ?>" />
			</td>
			</tr>
		</table>
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>