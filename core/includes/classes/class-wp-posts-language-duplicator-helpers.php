<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Wp_Posts_Language_Duplicator_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		WPPOSTSLAN
 * @subpackage	Classes/Wp_Posts_Language_Duplicator_Helpers
 * @author		Omar Habeh
 * @since		1.0.0
 */
class Wp_Posts_Language_Duplicator_Helpers{
	
	/**
	 * Callback function to duplicate post and all it's details
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return  int
	 */
	public function duplicate_page_with_acf($original_page_id) {
		
		// Get the original page data
		$original_page = get_post($original_page_id);
	  
		if ($original_page) {
		  // Clone the page
		  $cloned_page = array(
			'post_title' => $original_page->post_title,
			'post_content' => htmlspecialchars($original_page->post_content),
			'post_excerpt' => $original_page->post_excerpt,
			'post_status' => $original_page->post_status,
			'post_type' => $original_page->post_type,
			'post_author' => $original_page->post_author,
			'post_parent' => 0,
			'menu_order' => $original_page->menu_order,
			'comment_status' => $original_page->comment_status,
			'ping_status' => $original_page->ping_status,
		  );
	  
		  $cloned_page_id = wp_insert_post($cloned_page);
	  
          // Success! The page has been cloned
		  if (!is_wp_error($cloned_page_id)) {
	  
			// Duplicate the ACF fields
			$original_acf_fields = get_fields($original_page_id);
			if (!empty($original_acf_fields)) {
			  foreach ($original_acf_fields as $field_key => $field_value) {
				update_field($field_key, $field_value, $cloned_page_id);
			  }
			}
	  
			// Clone the template
			$original_template = get_page_template_slug($original_page_id);
			if ($original_template) {
			  update_post_meta($cloned_page_id, '_wp_page_template', $original_template);
			}
	  
			// Clone the meta fields
			$original_meta_fields = get_post_meta($original_page_id);
			foreach ($original_meta_fields as $key => $values) {
			  foreach ($values as $value) {
				add_post_meta($cloned_page_id, $key, $value);
			  }
			}
	  
			// Clone the taxonomies (if applicable)
			$original_taxonomies = wp_get_object_terms($original_page_id, get_object_taxonomies($original_page->post_type));
			if (!empty($original_taxonomies)) {
			  wp_set_object_terms($cloned_page_id, wp_list_pluck($original_taxonomies, 'slug'), $original_taxonomies[0]->taxonomy);
			}
	  
			// Clone the featured image (if applicable)
			$original_featured_image = get_post_thumbnail_id($original_page_id);
			if (!empty($original_featured_image)) {
			  set_post_thumbnail($cloned_page_id, $original_featured_image);
			}

			// Update the parent of the cloned page to the translated post of the original parent
			$original_parent_id = $original_page->post_parent;
			$translated_parent_id = pll_get_post($original_parent_id);
			if ($translated_parent_id) {
				wp_update_post(array('ID' => $cloned_page_id, 'post_parent' => $translated_parent_id));
			}
	  
			return $cloned_page_id; // Return the ID of the cloned page
		  } else {
			// Error occurred while cloning the page
			return new WP_Error('page_clone_error', 'Error cloning the page.');
		  }
		} else {
		  // Original page not found
		  return new WP_Error('page_not_found', 'Original page not found.');
		}
	}

}