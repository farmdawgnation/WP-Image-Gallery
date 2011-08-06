<?php
/*
Plugin Name: WP Image Gallery
Version: 1.4.3
Plugin URI: http://www.thepenguinmafia.com/projects/wpig
Description: Adds gallery capabilities to WordPress pages and posts.
Author: M. Foxtrot
Author URI: http://www.thepenguinmafia.com
*/

/* Copyright (c)2006 Matt Foxtrot. All rights reserved. */
/* Contributors: Pekka Gaiser */

//wpig_create_thumbnail() creates the thumbnail images for the specified file.
function wpig_create_thumbnail($original) {
	//Creating the small 100px wide thumbnail
	$srcim = @imagecreatefromjpeg(get_option('wpig_path') . 'gallery/' . $original);
	
	if(imagesx($srcim) > 100) {
		$newwidth = 100;
		$newheight = 100 * (imagesy($srcim)/imagesx($srcim));
		
		$dstim = @imagecreatetruecolor($newwidth, $newheight);
		
		imagecopyresized($dstim, $srcim, 0, 0, 0, 0, $newwidth, $newheight, imagesx($srcim), imagesy($srcim));
		
		imagejpeg($dstim, get_option('wpig_path') . 'gallery/thumbcache/' . $original);
		
		//Creating the larger 300px wide thumbnail
		$srcim = @imagecreatefromjpeg(get_option('wpig_path') . 'gallery/' . $original);
		
		if(imagesx($srcim) > 300) {
			$newwidth = 300;
			$newheight = 300 * (imagesy($srcim)/imagesx($srcim));
			
			$dstim = @imagecreatetruecolor($newwidth, $newheight);
			
			imagecopyresized($dstim, $srcim, 0, 0, 0, 0, $newwidth, $newheight, imagesx($srcim), imagesy($srcim));
			
			imagejpeg($dstim, get_option('wpig_path') . 'gallery/thumbcache/larger-' . $original);
		} else {
			//only copy larger image.
			copy(get_option('wpig_path') . 'gallery/' . $original, get_option('wpig_path') . 'gallery/thumbcache/larger-' . $original);
		}
	} else {
		//copy image
		copy(get_option('wpig_path') . 'gallery/' . $original, get_option('wpig_path') . 'gallery/thumbcache/larger-' . $original);
		copy(get_option('wpig_path') . 'gallery/' . $original, get_option('wpig_path') . 'gallery/thumbcache/' . $original);
	}
	//End thumbnail creation.
}

//wpig_add_pages() adds the admin pages to WordPress
function wpig_add_pages() {
	//Add an options page.
	add_options_page('Image Galleries', 'Image Galleries', 7, __FILE__, 'wpig_options_page');
	
	//Add a manage page.
	add_management_page('Image Galleries', 'Image Galleries', 4, __FILE__, 'wpig_management_page');
}

//wpig_options_page() displays the options page in WordPress
function wpig_options_page() {
	global $wpdb, $table_prefix;
	$siteurl = get_option('siteurl');
	
  if (isset($_POST['info_update'])) {
	$maxsize = $_POST['maxsize'];
	$types = $_POST['types'];
	$imgsperrow = $_POST['imgsperrow'];
	$container_style = $_POST['container_style'];
	$image_style = $_POST['image_style'];
	$title_style = $_POST['title_style'];
	$desc_style = $_POST['desc_style'];
	
	update_option('wpig_maximgsize', $maxsize);
	update_option('wpig_imgtypes', $types);
	update_option('wpig_imgsperrow', $imgsperrow);
	update_option('wpig_container_style', $container_style);
	update_option('wpig_image_style', $image_style);
	update_option('wpig_title_style', $title_style);
	update_option('wpig_desc_style', $desc_style);
    ?><div id="message" class="updated fade"><p><strong>Image Gallery Options updated successfully.</strong></p></div><?php
	}
	
	if(isset($_GET['act'])) {
		$wpdb->query("UPDATE " . $table_prefix . "imagegallery SET imggallery=1 WHERE imggallery=0;");
		?><div class="updated"><p><strong>Images Recovered.</strong></p></div><?php
	}
	?>
<div class=wrap>
  <form method="post">
    <h2>Image Gallery Options</h2>
    <strong>Misc Information</strong><br/>
    Below are the administrative settings. Please don't forget to create a CHMOD 665 directory under <em>wp-content/</em> that is called <em>gallery/</em> and then a directory in <em>gallery/</em> called <em>thumbcache/</em>. Without this directory, the gallery has no place to store it's images! (GASP!!) So... yeah.... create the directory! Now! Have you done it? Good. Enjoy the plugin. :O)
    <br/><br/>
	<strong>Administrative Gallery Options</strong><br/>
	Max Image Size: <input type="text" value="<?php echo get_option('wpig_maximgsize'); ?>" name="maxsize"> bytes.<br />
	Any individual file larger than <strong><?php echo get_cfg_var('upload_max_filesize'); ?>B</strong> will automatically be rejected by this server, regardless of the setting above.<br/>
	Hey! Don't yell at us! Talk to your web host about it - it's their limit!<br /><br />
	Allowed Types: <input type="text" value="<?php echo get_option('wpig_imgtypes'); ?>" name="types"><br /><br/>
	<em>wp-content/</em> Path: <?php echo get_option('wpig_path'); ?><br/>
	<em>This setting is autodetected when the plugin is activated. However, if you ever move your setup to a different server you'll have to deactivate and reactivate the plugin for this to be reset. Otherwise, the gallery won't work.</em>
	<br/><br/>
	<strong>Stylistic Gallery Options</strong><br/>
	As of 1.4.0, all of the elements in the galleries can be styled using CSS. The classes wpig-image-container, wpig-image, wpig-title, and wpig-description can be used in your style sheets to style the galleries. Alternatively, if you don't feel like adding such things to
	your style sheet, you can specify what styles you'd like applied here. Please note that these styles will over-ride any styles in your
	template's CSS, if there are any.<br/>
	Image Container Style: <input type="text" value="<?php echo get_option('wpig_container_style'); ?>" name="container_style" /><br/>
	<strong>Default:</strong> <em>padding-bottom: 50px; clear: left</em><br/>
	Image Style: <input type="text" value="<?php echo get_option('wpig_image_style'); ?>" name="image_style" /><br/>
	<strong>Default:</strong> <em>border:0; float:left; margin-right:10px</em><br/>
	Title Style: <input type="text" value="<?php echo get_option('wpig_title_style'); ?>" name="title_style" /><br/>
	<strong>Default</strong> <em>font-weight:bold</em><br/>
	Description Style: <input type="text" value="<?php echo get_option('wpig_desc_style'); ?>" name="description_style" /><br/>
     <!--<br/><br/><strong>Missing Images?</strong><br/>
     Just upgrade from 1.0.x and find all your pictures missing? They're not gone. Create a gallery for them and click the link below.<br/>
     <a href="<?php echo $siteurl; ?>/wp-admin/options-general.php?page=wpig.php&amp;act=recover">Recover Images</a> -->
<div class="submit">
  <input type="submit" name="info_update" value="Update Options" /></div>
  </form>
 </div> <?php
}

//wpig_management_page() displays the management page in WordPress
function wpig_management_page() {
	global $wpdb, $table_prefix;
	$siteurl = get_option('siteurl');
	if (isset($_POST['img_upload'])) {
		$image = $_FILES['image'];
		$title = $_POST['title'];
		$imgdesc = $_POST['imgdesc'];
		$imggallery = $_POST['imggallery'];
		
		if($image['size'] > get_option('wpig_maximgsize')) {
			?><div id="message" class="error"><p><strong>Upload Canceled. The image file is too large.</strong></p></div><?php
		} else {
			$fileparts = explode('.', $image['name']);
			$allowedtypes = explode(',', get_option('wpig_imgtypes'));
			if(array_search($fileparts[1], $allowedtypes) === FALSE) {
				?><div id="message" class="error"><p><strong>The image type <?php echo($fileparts[1]); ?> can not be uploaded.</strong></p></div><?php
			} else {
				if(file_exists(get_option('wpig_path') . 'gallery/' . $image['name'])) {
					?><div id="message" class="error"><p><strong>Image was not uploaded. Uploading that image would have caused another to have been overwritten. Please either rename the image filename or delete the other image first.</strong></p></div><?php
				} else {
					if(file_exists($image['tmp_name'])) {
						move_uploaded_file($image['tmp_name'], get_option('wpig_path') . 'gallery/' . $image['name']);
						$sql = "INSERT INTO " . $table_prefix . "imagegallery (title, imgdesc, imgpath, imggallery)
							VALUES ('$title', '$imgdesc', '" . $image['name'] . "', $imggallery);";
						$wpdb->query($sql);
						
						wpig_create_thumbnail($image['name']);
						
						?><div id="message" class="updated fade"><p><strong>Image was uploaded successfully.</strong></p></div><?php
					} else {
						?><div id="message" class="error"><p><strong>Image was rejected by server. Probably for being larger than <?php echo get_cfg_var('upload_max_filesize'); ?>B.</strong></p></div><?php
					}
				}
			}
		}
	} else if(isset($_POST['img_update'])) {
		$id = $_POST['imgid'];
		$title = $_POST['title'];
		$imgdesc = $_POST['imgdesc'];
		
		$wpdb->query("UPDATE " . $table_prefix . "imagegallery SET title='$title', imgdesc='$imgdesc' WHERE id=$id");
		
		?><div id="message" class="updated fade"><p><strong>Image Data Updated!</strong></p></div><?php
	} else if(isset($_POST['gal_create'])) {
		$name = $_POST['galname'];
		$desc = $_POST['galdesc'];
		$zip = $_FILES['galzip'];
		
		$sql = "INSERT INTO " . $table_prefix . "galleries (name, shortinfo) VALUES ('$name', '$desc');";
		$wpdb->query($sql);
		
		if($zip['name'] && function_exists('zip_open')) {
			$id_row = $wpdb->get_row("SELECT id FROM " . $table_prefix . "galleries WHERE name='$name'", ARRAY_A);
			$galleryid = $id_row['id'];
			
			$zip_handle = zip_open($zip['tmp_name']);
			
			while(($zip_entry = zip_read($zip_handle))) {
				if(zip_entry_open($zip_handle, $zip_entry)) {
					$entry_name = zip_entry_name($zip_entry);
					$entry_file = fopen(get_option('wpig_path') . 'gallery/' . zip_entry_name($zip_entry), 'w');
					
					while(($entry_data = zip_entry_read($zip_entry))) {
						$n = fwrite($entry_file, $entry_data);
					}
					
					fclose($entry_file);
					zip_entry_close($zip_entry);
					
					wpig_create_thumbnail($entry_name);
					
					$sql = "INSERT INTO " . $table_prefix . "imagegallery (title, imgdesc, imgpath, imggallery)
							VALUES ('$entry_name', '$entry_name', '$entry_name', $galleryid);";
					$wpdb->query($sql);
				}
			}
		}
		
		?><div id="message" class="updated fade"><p><strong>Gallery Created.</strong></p></div><?php
	} else if(isset($_GET['action']) && $_GET['action'] == 'gal_del') {
		$id = $_GET['id'];
		
		$wpdb->query("DELETE FROM " . $table_prefix . "galleries WHERE id=$id");
		
		$row = $wpdb->get_row("SELECT * FROM " . $table_prefix . "imagegallery WHERE imggallery=$id", ARRAY_A);
		
		while($row != $last_row) {
			if(!unlink(get_option('wpig_path') . 'gallery/' . $row['imgpath'])) {
				$delerr = 1;
			} else {
				//'echo 'deleted ' . $row['imgpath'] . ' ';
			}
			
			if(!unlink(get_option('wpig_path') . 'gallery/thumbcache/' . $row['imgpath'])) {
				$delerr = 1;
			} else {
				//echo 'deleted ' . get_option('wpig_path') . 'gallery/thumbcache/' . $row['imgpath'] . ' ';
			}
			
			if(!unlink(get_option('wpig_path') . 'gallery/thumbcache/larger-' . $row['imgpath'])) {
				$delerr = 1;
			} else {
				//echo 'deleted ' . get_option('wpig_path') . 'gallery/thumbcache/larger-' . $row['imgpath'] . ' ';
			}
			
			$last_row = $row;
			$row = $wpdb->get_row(null, ARRAY_A);
			
			/*echo('row: ');
			print_r($row);
			echo('<br />');
			
			echo('last row: ');
			print_r($last_row);
			echo('<br />');*/
		}
		
		$wpdb->query("DELETE FROM " . $table_prefix . "imagegallery WHERE imggallery=$id");
		
		if($delerr == 0) {
			?><div id="message" class="updated fade"><p><strong>Image Gallery and all Images deleted.</strong></p></div><?php
		} else {
			?><div id="message" class="error"><p><strong>Database Records for the gallery and its images were removed, however WPIG had some problems trying to delete some of the image files.</strong></p></div><?php
		}
	} else if(isset($_GET['action']) && $_GET['action'] == 'del') {
		$id = $_GET['id'];
		$row = $wpdb->get_row("SELECT * FROM " . $table_prefix . "imagegallery WHERE id=$id", ARRAY_A);
		
		$wpdb->query("DELETE FROM " . $table_prefix . "imagegallery WHERE id=$id");
		
		if(unlink(get_option('wpig_path') . 'gallery/' . $row['imgpath'])) {
			if(unlink(get_option('wpig_path') . 'gallery/thumbcache/' . $row['imgpath'])) {
				?><div id="message" class="updated fade"><p><strong>Image deleted.</strong></p></div><?php
			} else {
				?><div id="message" class="error"><p><strong>The image was removed, but image thumbnail couldn't be deleted.</strong></p></div><?php
			}
		} else {
			?><div id="message" class="error"><p><strong>The image couldn't be deleted.</strong></p></div><?php
		}
	} else if(isset($_GET['action']) && $_GET['action'] == 'edit') {
		$id = $_GET['id'];
		$row = $wpdb->get_row("SELECT * FROM " . $table_prefix . "imagegallery WHERE id=$id", ARRAY_A);
		?>
		<div class="wrap" id="wpig-updateimg">
			<form method="post">
				<h2>Edit Image Information</h2>
				<div style="float:right" id="infoboxes">
				Image Title: <input type="text" name="title" value="<?php echo $row['title']; ?>" size="40"> <br />
				Image Description: <input type="text" name="imgdesc" value="<?php echo $row['imgdesc']; ?>" size="40">
				</div>
				<img src="<?php echo $siteurl . '/wp-content/gallery/thumbcache/larger-' . $row['imgpath']; ?>" />
				<input type="hidden" name="imgid" value="<? echo $row['id']; ?>">
				<div class="submit">
					<input type="button" value="Cancel" onClick="getElementById('wpig-updateimg').style.visibility='hidden';getElementById('wpig-updateimg').style.height=0;getElementById('wpig-updateimg').style.margin=0;getElementById('wpig-updateimg').style.padding=0;getElementById('infoboxes').style.visibility='hidden';getElementById('infoboxes').style.height=0;getElementById('infoboxes').style.margin=0;getElementById('infoboxes').style.padding=0;">
					<input type="submit" name="img_update" value="Update Image">
				</div>
			</form>
		</div>
		
		<?php
	}
	
	if(isset($_GET['view'])) {
		$view = $_GET['view'];
		?>
		<div class="wrap">
			<h2>Image Gallery Management</h2>
			<table width="100%" cellpadding="3" cellspacing="3"> 
				<tr> 
				<th scope="col"></th> 
				
				<th scope="col">Title</th> 
				<th scope="col">Description</th>
				<th scope="col"></th>
				<th scope="col"></th> 
				</tr>
				<?php
				$i = 0;
				$rowstyle = 'alternate';
				while($row = $wpdb->get_row("SELECT id,title,imgdesc, imgpath FROM " . $table_prefix . "imagegallery WHERE imggallery=$view", ARRAY_A, $i)) {
				?>
				<tr class='<?php echo $rowstyle; ?>'> 
				
				<td width=100>
				 <a href='<?php echo $siteurl; ?>/wp-admin/edit.php?page=wpig.php&amp;view=<?php echo $view; ?>&amp;action=edit&amp;id=<?php echo $row['id']; ?>' class='edit'>
		          <img src="<?php echo wpig_get_thumbnail($row["imgpath"], 100, null); ?>" border="0">
		         </a>
		
				</td>
				
				<td>
				  <?php echo $row['title']; ?>
				</td>
				<td>
					<?php echo $row['imgdesc']; ?>
				</td>
				<td><a href='<?php echo $siteurl; ?>/wp-admin/edit.php?page=wpig.php&amp;view=<?php echo $view; ?>&amp;action=edit&amp;id=<?php echo $row['id']; ?>' class='edit'>Edit</a></td>
				<td><a href='<?php echo $siteurl; ?>/wp-admin/edit.php?page=wpig.php&amp;action=del&amp;id=<?php echo $row['id']; ?>' class='delete' onclick="return confirm('Are you sure you want to delete this image?')">Delete</a></td> 
				</tr>
				<?php
				$i++;
					if($rowstyle == 'alternate') {
						$rowstyle = '';
					} else {
						$rowstyle = 'alternate';
					}
				}
				?>
			 </table>
			</div>
			
		<div class="wrap">
			<form enctype="multipart/form-data" method="post">
				<h2>Upload an Image</h2>
				Image File: <input type="file" name="image"> (Max Size <?php echo get_option('wpig_maximgsize')/1000; ?> KB.)<br/>
				Image Title: <input type="text" name="title"><br/>
				Image Description: <input type="text" name="imgdesc">
				<input type="hidden" name="imggallery" value="<? echo $view; ?>">
			<div class="submit">
				<input type="submit" name="img_upload" value="Upload Image">
			</div>
			</form>
		</div>
		<?php
	} else {
		?>
		<div class="wrap">
			<h2>Image Gallery Management</h2>
			<table width="100%" cellpadding="3" cellspacing="3">
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Gallery Title</th>
					<th scope="col">Description</th>
					<th scope="col"></th>
					<th scope="col"></th>
				</tr>
				<?php
				$i = 0;
				$rowstyle = 'alternate';
				while($row = $wpdb->get_row("SELECT * FROM " . $table_prefix . "galleries", ARRAY_A, $i)) {
				?>
				<tr class="<?php echo $rowstyle; ?>">
					<td><?php echo($row['id']); ?></td>
					<td><?php echo($row['name']); ?></td>
					<td><?php echo($row['shortinfo']); ?></td>
					<td><a href='<?php echo $siteurl; ?>/wp-admin/edit.php?page=wpig.php&amp;view=<?php echo $row['id']; ?>' class='edit'>Manage</a></td>
					<td><a href='<?php echo $siteurl; ?>/wp-admin/edit.php?page=wpig.php&amp;action=gal_del&amp;id=<?php echo $row['id']; ?>' class='delete' onclick="return confirm('Are you sure you want to delete this gallery?\nALL IMAGES IN THIS GALLERY WILL BE DELETED!')">Delete</a></td>
				</tr>
				<?php
					$i++;
					if($rowstyle == 'alternate') {
						$rowstyle = '';
					} else {
						$rowstyle = 'alternate';
					}
				}
				?>
			</table>
			</div>
			
		<div class="wrap">
			<form enctype="multipart/form-data" method="post">
				<h2>Add a New Gallery</h2>
				Gallery Name: <input type="text" name="galname"><br/>
				Short Description: <input type="text" name="galdesc"><br/>
				<?php
				if(function_exists('zip_open')) {
				?>
				ZIP Containing Images: <input type="file" name="galzip"> (Max Size: <?php echo get_cfg_var('upload_max_filesize'); ?>B)<br />
				<?php
				} else {
				?>
				ZIP Containing Images: This installation of PHP does not have the ZIP functions enabled. Please contact the administrator.<br />
				<?php
				}
				?>
				<div class="submit">
					<input type="submit" name="gal_create" value="Create Gallery">
				</div>
			</form>
		</div>
		<?php
	}
}

//wpig_show_gallery() displays the gallery on the page, if the tag is set to do so.
function wpig_show_gallery($content) {
	global $wpdb, $table_prefix;
	$siteurl = get_option('siteurl');
	
	while(preg_match('|\[gallery:([0-9]+)\]|', $content, $matches) > 0) {
		$galleryid = $matches[1];
		
		$gallery = '<div id="gallery-' . $galleryid . '">';
		
		$num_results = $wpdb->query("SELECT title, imgdesc, imgpath FROM " . $table_prefix . "imagegallery WHERE imggallery=$galleryid ORDER BY id");
		
		$i = 0;
		
		while($i < $num_results) {
			$row = $wpdb->get_row("SELECT title, imgdesc, imgpath FROM " . $table_prefix . "imagegallery WHERE imggallery=$galleryid ORDER BY id", ARRAY_A, $i);
			
			$gallery .= '<div class="wpig-image-container" style="' . get_option('wpig_container_style') . '"><a href="' . $siteurl . '/wp-content/gallery/' . $row['imgpath'] . '"><img src="' . $siteurl . '/wp-content/gallery/thumbcache/' . $row['imgpath'] . '" class="wpig-image" style="' . get_option('wpig_image_style') . '" /></a><div class="wpig-title" style="' . get_option('wpig_title_style') . '">' . $row['title'] . '</div><div class="wpig-desc" style="' . get_option('wpig_desc_style') . '">' . $row['imgdesc'] . '</div></div>';
				
			$i++;
		}
		
		$gallery .= '</div>';
		
		$content = preg_replace('|\[gallery:([0-9]+)\]|', $gallery, $content, 1);
	}
	
	return $content;
}

function wpig_get_thumbnail($imgpath, $maxwidth = null, $maxheight = null) {
	$thumb_URI 	= $siteurl . '/wp-content/gallery/thumbcache/' . $imgpath;
	
	return $thumb_URI;
}

//wpig_install() is the install function for the plugin
function wpig_install () {
   global $table_prefix, $wpdb, $user_level;
   require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

   $table_name = $table_prefix . "imagegallery";

   get_currentuserinfo();
   if ($user_level < 8) { return; }
   
   $sql = "CREATE TABLE ".$table_name." (
	     id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	     title VARCHAR(200) NOT NULL,
	     imgdesc VARCHAR(200) NOT NULL,
	     imgpath VARCHAR(200) NOT NULL,
	     imggallery mediumint(9) NOT NULL
	   );";

    @dbDelta($sql);
    
    $table_name = $table_prefix . "galleries";
    
   $sql = "CREATE TABLE ".$table_name." (
	     id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	     name VARCHAR(200) NOT NULL,
	     shortinfo VARCHAR(200) NOT NULL
	   );";
    
    @dbDelta($sql);
    
    $filepath = __FILE__;
    $filepath_arr = explode("plugins/", $filepath);
    
    add_option('wpig_maximgsize', '2000000');
	add_option('wpig_imgtypes', 'jpg');
	add_option('wpig_path', $filepath_arr[0]);
	add_option('wpig_container_style', 'padding-bottom: 50px; clear: left');
	add_option('wpig_image_style', 'border:0; float:left; margin-right:10px');
	add_option('wpig_title_style', 'font-weight:bold');
	add_option('wpig_desc_style', '');
}

//add the actions
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
   add_action('init', 'wpig_install');
}

add_action('admin_menu', 'wpig_add_pages');
add_action('the_content', 'wpig_show_gallery');
?>