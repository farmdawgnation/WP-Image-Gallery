<?php
/*
Plugin Name: WP Image Gallery Reloaded
Version: 2.0.0
Plugin URI: http://www.farmdawgnation.com/projects/wpig
Description: A rewrite of the original WP Image Gallery plugin that allows users to maintain a fully functional image gallery within wordpress.
Author: Matt Farmer
Author URI: http://www.farmdawgnation.com
License: Apache License
*/

/*  Copyright 2006-2011 Matt Farmer  (email : matt[a t]frmr[d o t]me)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/***
 * The ORIGINAL WP Image Gallery v2.0
 * Back and better than ever with all of the tasty object-oriented MVC goodness
 * that your momma couldn't even dream of in 2006.
 *
 * @author Matt Farmer
 * @version 2.0.0
***/
class wpig {
	/**
	 * Some useful static variables from a programmy sense.
	**/
	protected static $wpig2_version = '2.0.0';
	protected static $wpig2_revision = '1';
	protected static $wpig2_dbversion = '1';

	protected static $image_table = '';
	protected static $gallery_table = '';

	/**
	 * Generate the table names and store them
	 * in the static context.
	 * @author Matt Farmer
	**/
	public static function generateTableNames() {
		global $wpdb;

		//Generate table names
		self::$image_table = $wpdb->prefix . "wpig2images";
		self::$gallery_table = $wpdb->prefix . "wpig2galleries";
	}

	/**
	 * Install/Upgrade the WPIG plugin in to the WordPress system.
	 * @author Matt Farmer
	**/
	public static function install() {
		//We need access to dbDelta and the $wpdb global object.
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;

		//Generate the table names
		$images_table = $wpdb->prefix . "wpig2images";
		$gallery_table = $wpdb->prefix . "wpig2galleries";

		//SQL statements
		$images_sql = "CREATE TABLE ".$images_table." (
			id int(11) NOT NULL AUTO_INCREMENT PRIMARY  KEY,
			galleries_id int(11) NOT NULL,
			title VARCHAR(200) NOT NULL DEFAULT '',
			description VARCHAR(200) NOT NULL DEFAULT '',
			path VARCHAR(200) NOT NULL DEFAULT '',
			order int(11) NOT NULL
		);";

		$galleries_sql = "CREATE TABLE ".$gallery_table." (
			id int(11) NOT NULL AUTO_INCREMENT PRIMARY  KEY,
			title VARCHAR(200) NOT NULL,
			slug VARCHAR(200) NOT NULL
		);";

		//P.A.R.T.Y? BECAUSE I GOTTA
		//Run those sql statements.
		dbDelta($images_sql);
		dbDelta($galleries_sql);

		//And miscellenious options to the WP info store.
		add_option('wpig2_db_version', '1');
		add_option('wpig2_imgtypes', 'jpg');
		add_option('wpig2_container_css', 'padding-bottom: 50px; clear: left');
		add_option('wpig2_image_css', 'border:0; float:left; margin-right:10px');
		add_option('wpig2_title_css', 'font-weight:bold');
		add_option('wpig2_desc_css', '');
		add_option('wpig2_thumb_width', '200');
	}

	/**
	 * Register the admin menus.
	 * @author Matt Farmer
	**/
	public static function registerAdminMenus() {
		//Register the settings submenu
		add_submenu_page('options-general.php', 'WPIG Settings', 'WPIG Settings', 'manage_options', 'wpig', array('wpig', 'settingsPage'));

		//Register the galleries submenu.
		add_submenu_page('upload.php', 'Manage Galleries', 'Manage Galleries', 'upload_files', 'wpig', array('wpig', 'managePage'));
	}

	/**
	 * Register the options that shall appear on the setting menu.
	 * @author Matt Farmer
	**/
	public static function registerSettingMenuOptions() {
		//Whitelist the following settings to appear on the settings page.
		register_setting('wpig2-options', 'wpig2_imgtypes');
		register_setting('wpig2-options', 'wpig2_container_css');
		register_setting('wpig2-options', 'wpig2_image_css');
		register_setting('wpig2-options', 'wpig2_title_css');
		register_setting('wpig2-options', 'wpig2_desc_css');
		register_setting('wpig2-options', 'wpig2_thumb_width');
	}

	/**
	 * Render the settings page.
	 * @author Matt Farmer
	**/
	public static function settingsPage() {
		//Load view.
		require(__DIR__ . "/views/settings.tpl.php");
	}

	/**
	 * Render the galleries page.
	 * @author Matt Farmer
	**/
	public static function managePage() {
		//Database
		global $wpdb;

		//Generate table names
		self::generateTableNames();

		//Incoming post data?
		if(count($_POST) > 0 || count($_FILES) > 0) {
			//We're adding a new gallery. Let's make the magic happen.
			if($_GET['action'] == 'add') {
				$wpdb->insert( self::$gallery_table, array(
					'title' => $_POST['title'],
					'slug' => sanitize_title($_POST['slug'], sanitize_title($_POST['title']))
				));
			} else if($_GET['action'] == 'edit') { //I really wish MP was a true MVC....
				$wpdb->update( self::$gallery_table, array(
						'title' => $_POST['title'],
						'slug' => sanitize_title($_POST['slug'], sanitize_title($_POST['title']))
					),
					array(
						'id' => $_GET['id']
					)
				);
			} else if($_GET['action'] == 'view') {
				//incoming post data from the photo manager.

				//loop over the incoming attribute updates
				//if they were passed
				if(count($_POST) > 0) {
					//Process the attribute updates
					foreach($_POST['image'] as $k => $data) {
						//Update the associated data for each image
						$wpdb->update(
							self::$image_table,
							array(
								'title' => $data['title'],
								'description' => $data['description']
							),
							array(
								'id' => $k
							)
						);
					}
				}

				//loop over the incoming upload data
				//if it was passed
				if(count($_FILES) > 0) {
					$success = true;
					$errors = array();

					//Insert the record for the image
					$wpdb->insert(self::$image_table, array(
						'galleries_id' => $_GET['id']
					));
					$imageId = $wpdb->insert_id;

					//Hey, we've got a file upload.
					if($_FILES['upload']['error'] != UPLOAD_ERR_OK) {
						//Unuccessful upload.
						$success = false;
						$errors[] = "File upload error " . $_FILES['upload']['error'];
					}

					//Extension validation....
					if($success) {
						//Successful upload.
						//Figure out our file extension....
						$fileParts = explode(".", $_FILES['upload']['name']);
						$ext = strtolower($fileParts[count($fileParts)-1]);

						//Is it a valid extension?
						if($ext != 'zip' && !in_array($ext, explode("|", get_option('wpig2_imgtypes')))) {
							//Invalid upload extensions
							$success = false;
							$errors[] = "Invalid upload extension. Please check your settings.";
						}
					}

					//The file is on the server and the upload is valid.
					//Do we have the galleries directory?
					if($success) {
						//This is a valid file format. Continue with the image upload process.
						chdir("../wp-content");

						//Check the existance of the galleries directory
						if(!is_dir('galleries')) {
							if(!mkdir('galleries')) {
								$success = false;
								$errors[] = "Cloud not create galleries directory.";
							}
						}
					}

					//If we have the galleries directory, can we create the drop location for the photo?
					if($success) {
						//CHDIR to galleries
						chdir('galleries');

						$imageFolder = getcwd() . '/' . $_GET['id'] . "/" . $imageId;

						//Try to create the drop location, adding an error if we fail.
						if(!mkdir($imageFolder)) {
							$success = false;
							$errors[] = "Could not create image directory. Please check your permissions.";
						}
					}

					//If we made it this far, we're home free.
					if($success) {
						$tmpName = $_FILES['upload']['tmp_name'];
						$name = $_FILES['upload']['name'];

						if(move_uploaded_file($tmpName, "$imageFolder/$name")) {
							//Success!
						} else {
							$success = false;
							$errors[] = "Could not move image into image folder.";
						}
					}
				}
			}

			//Rudamentary Redirect
			unset($_GET['action']);
		} else if(isset($_GET['action']) && $_GET['action'] == 'delete') {
			//We are deleting a post. First verify that the image id is numeric
			if(is_numeric($_GET['id'])) {
				$wpdb->query($wpdb->prepare('DELETE FROM ' . self::$gallery_table . ' WHERE id = %d', $_GET['id']));
			}

			//Unset to trigger manage page.
			unset($_GET['action']);
		}

		//Check the action
		if(!isset($_GET['action'])) {
			//No action. Display manage page.
			//Retrieve galleries from DB.
			$galleries = $wpdb->get_results('SELECT * FROM ' . self::$gallery_table);

			//Load view
			require(__DIR__ . '/views/manage.tpl.php');
		} else if($_GET['action'] == 'add') {
			//We're adding a new gallery. Display the form.
			require(__DIR__ . '/views/form.tpl.php');
		} else if($_GET['action'] == 'edit') {
			//We're retrieving an individual record.
			if(!is_numeric($_GET['id'])) {
				die(); //protect ourselves from bad queries.
			}

			//Generate the query
			$id = $_GET['id'];
			$gallery = $wpdb->get_row('SELECT * FROM ' . self::$gallery_table . " WHERE id=$id");

			//Load view
			require(__DIR__ . '/views/form.tpl.php');
		} else if($_GET['action'] == 'view') {
			//We're displaying pictures that are already in a gallery
			//so, make sure the ID is valid, then retrieve the gallery and images.
			if(!is_numeric($_GET['id'])) {
				die(); //protect ourselves from bad queries.
			}

			//Generate query
			$id = $_GET['id'];
			$gallery = $wpdb->get_row('SELECT * FROM ' . self::$gallery_table . " WHERE id=$id");
			$photos = $wpdb->get_results('SELECT * FROM ' . self::$image_table . " WHERE galleries_id=$id");

			//Load view
			require(__DIR__ . '/views/images/manage.tpl.php');
		}
	}
}

/****
	HOOKS, HOOKS, HOOKS, OH MY!
	BELOWETH GO THE WORDPRESS HOOKS
****/
register_activation_hook(__FILE__, array('wpig', 'install'));
add_action('admin_menu', array('wpig', 'registerAdminMenus'));
add_action('admin_init', array('wpig', 'registerSettingMenuOptions'));
?>
