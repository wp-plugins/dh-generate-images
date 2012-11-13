<?php
/*
Plugin Name: D Hill's Generate Custom Image Sizes
Plugin URI: http://www.davidhill.ie/2012/11/wordpress-plugin-dh-generate-images/
Description: Generates or Re-Generates Images for Custom Image sizes sizes after upload.
Version: 1.0
Author: David Hill
Author URI: http://www.davidhill.ie
License: GPL2
*/


/*  Copyright 2011  David Hill  (email : david@davidhill.ie)

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

add_action('admin_menu', 'my_plugin_menu');

/*--------------------------------------------*
* Core Functions
*---------------------------------------------*/

function my_plugin_menu() {

		add_media_page('DH Generate Images', 'DH Generate Images', 'edit_posts', basename(__FILE__), 'regen_images_page');

	}

/**
* Adds settings/options page
*/

function regen_images_page() { 


				$img_sizes = get_intermediate_image_sizes();
				$default_sizes = array_slice($img_sizes, 0, 3);
				
				$default_size_details = array();
				
				foreach ($default_sizes as $default_size) {
				
					$default_size_details[$default_size] = array();
					$default_size_details[$default_size]['width'] = get_option( "{$default_size}_size_w" );
					$default_size_details[$default_size]['height'] = get_option( "{$default_size}_size_h" );

					if(get_option( "{$default_size}_crop" )) {
						
							$default_size_details[$default_size]['crop'] = 1; 
					
						} else {
						
							$default_size_details[$default_size]['crop'] = 0;
						
						}
				
				}

				global $_wp_additional_image_sizes;
				global $img_size_details;
				
				$img_size_details = $default_size_details + $_wp_additional_image_sizes;
				



			if(isset($_POST['dh_d_hill_plugin_template_save'])){
				if (! wp_verify_nonce($_POST['_wpnonce'], 'dh_d_hill_plugin_template-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 

				$choice = $_POST['image-size'];

				if( $choice !== 'none' ) {
					
					echo '<div class="updated"><p>Success! Your images were successfully generated!</p><ul><li>'.do_resizing($choice).'</li></ul></div>';
				
				} else {
				
				
						echo '<div class="error"><p>Please select and Image size to Generate</p></div>';
				
				}

				
			}
?>								   
			<div class="wrap">
			<h2>DH Generate Images</h2>
			<form method="post" id="dh_d_hill_plugin_template_options">
			
			<p>Select the Image Size you want to Generate/Regenerate Images for.</p>
			<?php wp_nonce_field('dh_d_hill_plugin_template-update-options'); 

				echo '<select name="image-size">';
				echo '<option value="none">Select Image Size</option>';
										
				foreach ($img_sizes as $img_size) {
						
						echo '<option value="'.$img_size.'">'.$img_size.': ';
						echo $img_size_details[$img_size]['width'].'px x ';
						echo $img_size_details[$img_size]['height'].'px';
						echo '</option>';
				}

				echo '</select>';

?>

				<p class="submit"> 
					<input type="submit" name="dh_d_hill_plugin_template_save" class="button-primary" value="Generate Images" />
				</p>
			</form>	
			</div><!-- close wrap -->			
			<?php
		}

function resize_image ($img_to_resize, $img_size) {

	$src_file = get_attached_file($img_to_resize);

	global $_wp_additional_image_sizes;
	global $img_size_details;
	
	$img_dimensions = $img_size_details[$img_size];
		 
	$newmeta = image_make_intermediate_size ( $src_file, $img_dimensions['width'], $img_dimensions['height'], $img_dimensions['crop']); 
  	
  	if($newmeta) { // If a new image was created : Only then update meta

		$metadata = wp_get_attachment_metadata( $img_to_resize ); // Current metadata

 		$metadata['sizes'][$img_size] = $newmeta; // Add new image size to current metadata array
	
		wp_update_attachment_metadata( $img_to_resize, $metadata ); // // Update metadata
		
		$result = 'Generated: '.$newmeta['file'];
	
  	} else {
		$result = 'File for Image-id: '.$img_to_resize.' was not generated';
	}
  	
  	return $result;

} // end resize_image()

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


function get_all_images() {


	// Directly querying the database is normally frowned upon, but all
	// of the API functions will return the full post objects which will
	// suck up lots of memory. This is best, just not as future proof.
	
global $wpdb;
	
if ( ! $images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" ) ) {
					echo '	<p>' . sprintf( __( "Unable to find any images. Are you sure <a href='%s'>some exist</a>?", 'regenerate-thumbnails' ), admin_url( 'upload.php?post_mime_type=image' ) ) . "</p></div>";
					return;
				}

	// Generate the list of IDs
	$image_ids = array();
	
	foreach ( $images as $image ) {	$image_ids[] = $image->ID; }
	
	return $image_ids;

} // end get_all_images()



// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

function do_resizing($choice) {
	
	$all_images = get_all_images();
	
	// echo $choice;

	$result = '';

	foreach ( $all_images as $img ) {
	
		$result .= '<li>'. resize_image ( $img, $choice ). '</li>';
	
	}
	
	return $result;

} // end do_resizing()


 // HELP TAB

add_filter( 'contextual_help', 'wptuts_contextual_help', 10, 3 );

function wptuts_contextual_help( $contextual_help, $screen_id, $screen ) {
    // Only add to certain screen(s). The add_help_tab function for screen was introduced in WordPress 3.3.
    if ( $screen_id != 'media_page_dh-gen-images' || ! method_exists( $screen, 'add_help_tab' ) )
        return $contextual_help;
    $screen->add_help_tab( array(
        'id'      => 'wptuts-overview-tab',
        'title'   => __( 'Overview', 'plugin_domain' ),
        'content' => '<p>' . __( 'The most common reason for an image generation failure is a source image being smaller than the image to be generated.. in that case the plugin will not generate an image.', 'plugin_domain' ) . '</p>',
    ));
    return $contextual_help;
}


?>