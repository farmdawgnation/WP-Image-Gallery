<div class="wrap">
	<h2>The WP Image Gallery (WPIG) Plugin: Reloaded</h2>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'wpig2-options' ); ?>
		<h3 class="title">General Options</h3>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Valid Image Types</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_imgtypes" value="<?php echo get_option('wpig2_imgtypes'); ?>" />
				<br/><span class="description">Enter the file extensions I should allow for uploads, separated by vertical bars.</span>
			</td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Thumbnail Width</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_thumb_width" value="<?php echo get_option('wpig2_thumb_width'); ?>" />
				<br/><span class="description">Enter the width in pixels for thumbnails. (leave off the "px" suffix)</span>
			</td>
			</tr>
		</table>
		
		<h3 class="title">Styling Options</h3>
		<table class="form-table">
			<tr valign="top">
			<th scope="row">Container CSS</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_container_css" value="<?php echo get_option('wpig2_container_css'); ?>" />
				<br/><span class="description">Enter any styling you would like to use for the gallery container.</span>
			</td>
			</tr>
			 
			<tr valign="top">
			<th scope="row">Image CSS</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_image_css" value="<?php echo get_option('wpig2_image_css'); ?>" />
				<br/><span class="description">Enter any styling you would like applied directly to the images.</span>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Title CSS</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_title_css" value="<?php echo get_option('wpig2_title_css'); ?>" />
				<br/><span class="description">Enter any styling you would like applied to the title of the image.</span>
			</td>
			</tr>
			
			<tr valign="top">
			<th scope="row">Description CSS</th>
			<td>
				<input type="text" class="regular-text" name="wpig2_desc_css" value="<?php echo get_option('wpig2_desc_css'); ?>" />
				<br/><span class="description">Enter any styling you would like applied to the description of the image.</span>
			</td>
			</tr>
		</table>
		
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>