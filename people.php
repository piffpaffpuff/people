<?php

/*
 * Plugin Name: People
 * Plugin URI: https://github.com/piffpaffpuff
 * Description: A plugin to display the people of an agency, group or any other team.
 * Version: 1.0
 * Author: piffpaffpuff
 * Author URI: https://github.com/piffpaffpuff
 * License: GPL3
 *
 * Copyright (C) 2011 Triggvy Gunderson
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
/**
 * Main class
 */
if (!class_exists('People')) {
class People {

	public static $plugin_file_path;
	public static $plugin_directory_url;
	public static $plugin_directory_path;
	public static $plugin_basename;
	public static $post_type;
	public static $slug;
	
	/**
	 * Constructor
	 */
	public function __construct() {
		self::$plugin_file_path = __FILE__;
		self::$post_type = 'person';
		self::$plugin_directory_url = plugin_dir_url(self::$plugin_file_path);
		self::$plugin_directory_path = plugin_dir_path(self::$plugin_file_path);
		self::$plugin_basename = plugin_basename(self::$plugin_file_path);
		
		$this->slug = self::$post_type;
	}
	
	/**
	 * Include the classes
	 */
	public function includes() {
	}
	
	/**
	 * Load the code
	 */
	public function load() {
		// include the classes
		$this->includes();
				
		// load hooks
		add_action('plugins_loaded', array($this, 'load_translation'));
		add_action('init', array($this, 'hooks_init'));
		add_action('admin_init', array($this, 'hooks_admin'));
	}

	/**
	 * Load the translations
	 */
	public function load_translation() {
   		load_plugin_textdomain('people', false, dirname(self::$plugin_basename) . '/languages/');
	}
	
	/**
	 * Load the main hooks
	 */
	public function hooks_init() {
 		add_theme_support('post-thumbnails');
   		$this->add_types();
   		$this->add_taxonomies();
	}
	
	/**
	 * Load the main hooks
	 */
	public function hooks_admin() {
		add_action('admin_print_styles', array($this, 'add_styles'));
		add_action('admin_print_scripts-post.php', array($this, 'add_scripts'));
		add_action('admin_print_scripts-post-new.php', array($this, 'add_scripts'));
				
		add_action('add_meta_boxes', array($this, 'add_boxes'));
		add_action('save_post', array($this, 'save_box_data'));
	}
	
	/**
	 * Add the styles
	 */
	public function add_styles() {
		wp_enqueue_style('people', self::$plugin_directory_url . 'css/style.css');
	}
	
	/**
	 * Add the scripts
	 */
	public function add_scripts() {
		wp_enqueue_script('people', self::$plugin_directory_url . 'js/script.js', array('jquery'));
	}
	
	/**
	 * Create custom post types
	 */
	public function add_types() {	
		$labels = array(
			'name' => __('People', 'people'),
			'singular_name' => __('Person', 'people'),
			'add_new' => __('Add New', 'people'),
			'add_new_item' => __('Add New Person', 'people'),
			'edit_item' => __('Edit Person', 'people'),
			'new_item' => __('New Person', 'people'),
			'all_items' => __('All People', 'people'),
			'view_item' => __('View Person', 'people'),
			'search_items' => __('Search People', 'people'),
			'not_found' => __('No People found', 'people'),
			'not_found_in_trash' => __('No People found in Trash', 'people'),
			'parent_item_colon' => '',
			'menu_name' => __('People', 'people')
		);
	
		$args = array(
	    	'labels' => $labels,
	    	'public' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'post-formats'),
			'capability_type' => 'post',
			'rewrite' => array('slug' => apply_filters('people_rewrite_slug', $this->slug)),
			'menu_position' => 9,
			'has_archive' => true
		); 
	
		register_post_type(self::$post_type, $args);
	}
	
	/**
	 * Create custom taxonomies
	 */
	function add_taxonomies() {	
		// Profession
		$taxonomy_name = self::$post_type . '_profession';
		
		$labels = array(
		    'name' => __('Professions', 'people'),
		    'singular_name' => __('Profession', 'people'),
		    'search_items' => __('Search Professions', 'people'),
		    'all_items' => __('All Professions', 'people'),
		    'parent_item' => __( 'Parent Professions', 'people'),
    		'parent_item_colon' => __( 'Parent Professions:', 'people'),
		    'edit_item' => __('Edit Profession', 'people'),
		    'update_item' => __('Update Profession', 'people'),
		    'add_new_item' => __('Add New Profession', 'people'),
		    'new_item_name' => __('New Profession Name', 'people'),
		    'separate_items_with_commas' => __('Separate Professions with commas', 'people'),
		    'add_or_remove_items' => __('Add or remove Professions', 'people'),
		    'choose_from_most_used' => __('Choose from the most used Professions', 'people'),
		    'menu_name' => __('Professions', 'people')
		);
		
		$args = array(
			'labels' => $labels,
	    	'rewrite' => array('slug' => $this->slug . '/' . self::$post_type . '-profession', 'with_front' => true),
	    	'hierarchical' => true,
			'show_ui' => true
		);
				
		register_taxonomy($taxonomy_name, self::$post_type, $args);
		
		// Occupation
		$taxonomy_name = self::$post_type . '_occupation';
		
		$labels = array(
		    'name' => __('Occupations', 'people'),
		    'singular_name' => __('Occupation', 'people'),
		    'search_items' => __('Search Occupations', 'people'),
		    'all_items' => __('All Occupations', 'people'),
		    'parent_item' => __( 'Parent Occupations', 'people'),
    		'parent_item_colon' => __( 'Parent Occupations:', 'people'),
		    'edit_item' => __('Edit Occupation', 'people'),
		    'update_item' => __('Update Occupation', 'people'),
		    'add_new_item' => __('Add New Occupation', 'people'),
		    'new_item_name' => __('New Occupation Name', 'people'),
		    'separate_items_with_commas' => __('Separate Occupations with commas', 'people'),
		    'add_or_remove_items' => __('Add or remove Occupations', 'people'),
		    'choose_from_most_used' => __('Choose from the most used Occupations', 'people'),
		    'menu_name' => __('Occupations', 'people')
		);
		
		$args = array(
			'labels' => $labels,
	    	'rewrite' => array('slug' => $this->slug . '/' . self::$post_type . '-occupation', 'with_front' => true),
	    	'hierarchical' => true,
			'show_ui' => true
		);
				
		register_taxonomy($taxonomy_name, self::$post_type, $args);
	}
	
	/**
	 * Add the meta boxes
	 */
	public function add_boxes() {			
		add_meta_box('people-information-box', __('Information', 'people'), array($this, 'create_box_information'), self::$post_type, 'side', 'default');
	}
	
	/**
	 * Create the box information
	 */
	public function create_box_information($post, $metabox) {
		// Use nonce for verification
		wp_nonce_field(self::$plugin_basename, 'people_nonce');
		?>
		<p class="form-fieldset"><label><span><?php _e('Telephone', 'people'); ?></span></label><input type="text" class="regular-text" name="people[telephone]" value="<?php echo $this->get_person_meta($post->ID, 'telephone'); ?>" title="<?php _e('Telephone', 'people'); ?>"></p>
		<p class="form-fieldset"><label><span><?php _e('E-Mail', 'people'); ?></span></label><input type="text" class="regular-text" name="people[email]" value="<?php echo $this->get_person_meta($post->ID, 'email'); ?>" title="<?php _e('E-Mail', 'people'); ?>"></p>
		<p class="form-fieldset"><label><span><?php _e('Facebook', 'people'); ?></span></label><input type="text" class="regular-text code" name="people[facebook]" value="<?php echo $this->get_person_meta($post->ID, 'facebook'); ?>" title="<?php _e('Facebook', 'people'); ?>" placeholder="http://"></p>
		<p class="form-fieldset"><label><span><?php _e('Twitter', 'people'); ?></span></label><input type="text" class="regular-text code" name="people[twitter]" value="<?php echo $this->get_person_meta($post->ID, 'twitter'); ?>" title="<?php _e('Twitter', 'people'); ?>" placeholder="http://"></p>
		<p class="form-fieldset"><label><span><?php _e('Website', 'people'); ?></span></label><input type="text" class="regular-text code" name="people[website]" value="<?php echo $this->get_person_meta($post->ID, 'website'); ?>" title="<?php _e('Website', 'people'); ?>" placeholder="http://"></p>
		<?php
	}
	
	/**
	 * Save the box data
	 */
	public function save_box_data($post_id) {
		// Verify this came from the our screen and with 
		// proper authorization, because save_post can be 
		// triggered at other times. 
		if(empty($_POST['people_nonce']) || !wp_verify_nonce( $_POST['people_nonce'], self::$plugin_basename)) {
			return $post_id;
		}
		
		// Verify if this is an auto save routine. If it 
		// is our form has not been submitted, so we dont 
		// want to do anything. 
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// Check permissions
		if($_POST['post_type'] ==  'page') {
			if(!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} else {
			if(!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}
		}
		
		// We're authenticated: Now we need to find 
		// and save the data.

		// Save, update or delete the custom field of the post.
		// split all array keys and save them as unique meta to 
		// make them queryable by wordpress.
		if(isset($_POST['people'])) {
			// format the telephone
			if(!empty($_POST['people']['telephone'])) {
				$_POST['people']['telephone'] = preg_replace('[\D]', '', $_POST['people']['telephone']);
			}
			
			// format the email
			if(!is_email($_POST['people']['email'])) {
				$_POST['people']['email'] = null;
			}
			
			// Save the meta
			foreach($_POST['people'] as $key => $value) {
				update_post_meta($post_id, '_people_' . $key, $value);
			}
		}
	}
	
	/**
	 * Get the meta value from a key
	 */
	public function get_person_meta($post_id, $key) {
		return get_post_meta($post_id, '_people_' . $key, true);
	}
	
	/**
	 * Get the taxonomy terms from a key
	 */
	public function get_person_taxonomy($post_id, $key, $hierarchical = true, $args = null) {	
		$terms = wp_get_object_terms($post_id, $key, $args); 
		
		if(!is_wp_error($terms) && sizeof($terms) > 0) {
			// return the flat tree
			if(!$hierarchical) {
				return $terms;
			}
			
			// return the hierarchical tree		
			$childs = array();
		
			// find all childs
			foreach($terms as $term) {
				$childs[$term->parent][] = $term;
			}
		
			// cascade all childs
			foreach($terms as $term) {
				if (isset($childs[$term->term_id])) {
					$term->childs = $childs[$term->term_id];
				}
			}
		
			// flat the childs tree by its base node
			$tree = $childs[0];
			
			return $tree;
		}
	
		return;
	}
}
}

/*
 * Instance
 */
$people = new People();
$people->load();

/*
 * Template functions
 */
 
/**
 * Get terms
 */
if(!function_exists('get_person_taxonomy')) {
function get_person_taxonomy($key, $hierarchical = true, $args = null) {
	global $people, $post;
	$terms = $people->get_person_taxonomy($post->ID, $key, $hierarchical, $args);
	return $terms;
}
}

/**
 * Show terms
 */
if(!function_exists('person_taxonomy')) {
function person_taxonomy($key, $args = null) {
	global $post;
	$args = is_array($args) ? $args : array();
	$args['taxonomy'] = $key;
	return wp_list_categories($args);
}
}

/**
 * Get the telephone number
 */
if(!function_exists('get_person_telephone')) {
function get_person_telephone() {
	global $people, $post;
	return $people->get_person_meta($post->ID, 'telephone');
}
}

/**
 * Get the email address
 */
if(!function_exists('get_person_email')) {
function get_person_email() {
	global $people, $post;
	return $people->get_person_meta($post->ID, 'email');
}
}

/**
 * Get the twitter url
 */
if(!function_exists('get_person_twitter')) {
function get_person_twitter() {
	global $people, $post;
	return $people->get_person_meta($post->ID, 'twitter');
}
}

/**
 * Get the facebook url
 */
if(!function_exists('get_person_facebook')) {
function get_person_facebook() {
	global $people, $post;
	return $people->get_person_meta($post->ID, 'facebook');
}
}

/**
 * Get the website url
 */
if(!function_exists('get_person_website')) {
function get_person_website() {
	global $people, $post;
	return $people->get_person_meta($post->ID, 'website');
}
}

?>