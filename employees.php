<?php

/*
 * Plugin Name: Employees
 * Plugin URI: https://github.com/piffpaffpuff/employees
 * Description: A plugin to display the employees of an agency, group or any other team.
 * Version: 1.0
 * Author: piffpaffpuff
 * Author URI: https://github.com/piffpaffpuff
 * License: GPL3
 *
 * Copyright (C) 2011 Iwan Negro
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
if (!class_exists('Employees')) {
class Employees {

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
		self::$post_type = 'employee';
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
   		load_plugin_textdomain('employees', false, dirname(self::$plugin_basename) . '/languages/');
	}
	
	/**
	 * Load the main hooks
	 */
	public function hooks_init() {
 		add_theme_support('post-thumbnails');
   		$this->add_type();
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
		wp_enqueue_style('employees', self::$plugin_directory_url . 'css/style.css');
	}
	
	/**
	 * Add the scripts
	 */
	public function add_scripts() {
		wp_enqueue_script('employees', self::$plugin_directory_url . 'js/script.js', array('jquery'));
	}
	
	/**
	 * Create custom post type
	 */
	public function add_type() {	
		$labels = array(
			'name' => __('Employees', 'employees'),
			'singular_name' => __('Employee', 'employees'),
			'add_new' => __('Add New', 'employees'),
			'add_new_item' => __('Add New Employee', 'employees'),
			'edit_item' => __('Edit Employee', 'employees'),
			'new_item' => __('New Employee', 'employees'),
			'all_items' => __('All Employees', 'employees'),
			'view_item' => __('View Employee', 'employees'),
			'search_items' => __('Search Employees', 'employees'),
			'not_found' => __('No Employees found', 'employees'),
			'not_found_in_trash' => __('No Employees found in Trash', 'employees'),
			'parent_item_colon' => '',
			'menu_name' => __('Employees', 'employees')
		);
	
		$args = array(
	    	'labels' => $labels,
	    	'public' => true,
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'post-formats'),
			'capability_type' => 'post',
			'rewrite' => array('slug' => apply_filters('employees_rewrite_slug', $this->slug)),
			'menu_position' => 15,
			'has_archive' => true
		); 
	
		register_post_type(self::$post_type, $args);
	}
	
	/**
	 * Add the meta boxes
	 */
	public function add_boxes() {			
		add_meta_box('employees-information-box', __('Information', 'employees'), array($this, 'create_box_information'), self::$post_type, 'side', 'default');
	}
	
	/**
	 * Create the box information
	 */
	public function create_box_information($post, $metabox) {
		// Use nonce for verification
		wp_nonce_field(self::$plugin_basename, 'employees_nonce');
		?>
		<p class="form-fieldset"><label><span><?php _e('Profession', 'employees'); ?></span><input type="text" class="regular-text" name="employees[profession]" value="<?php echo $this->get_employees_meta($post->ID, 'profession'); ?>" title="<?php _e('Profession', 'employees'); ?>"></label></p>
		<p class="form-fieldset"><label><span><?php _e('Occupation', 'employees'); ?></span><input type="text" class="regular-text" name="employees[occupation]" value="<?php echo $this->get_employees_meta($post->ID, 'occupation'); ?>" title="<?php _e('Occupation', 'employees'); ?>"></label></p>
		<p class="form-fieldset"><label><span><?php _e('Telephone', 'employees'); ?></span><input type="text" class="regular-text" name="employees[telephone]" value="<?php echo $this->get_employees_meta($post->ID, 'telephone'); ?>" title="<?php _e('Telephone', 'employees'); ?>"></label></p>
		<p class="form-fieldset"><label><span><?php _e('E-Mail', 'employees'); ?></span><input type="text" class="regular-text" name="employees[email]" value="<?php echo $this->get_employees_meta($post->ID, 'email'); ?>" title="<?php _e('E-Mail', 'employees'); ?>"></label></p>
		<p class="form-fieldset"><label><span><?php _e('Facebook', 'employees'); ?></span><input type="text" class="code" name="employees[facebook]" value="<?php echo $this->get_employees_meta($post->ID, 'facebook'); ?>" title="<?php _e('Facebook', 'employees'); ?>" placeholder="http://"></label></p>
		<p class="form-fieldset"><label><span><?php _e('Twitter', 'employees'); ?></span><input type="text" class="code" name="employees[twitter]" value="<?php echo $this->get_employees_meta($post->ID, 'twitter'); ?>" title="<?php _e('Twitter', 'employees'); ?>" placeholder="http://"></label></p>
		<?php
	}
	
	/**
	 * Save the box data
	 */
	public function save_box_data($post_id) {
		// Verify this came from the our screen and with 
		// proper authorization, because save_post can be 
		// triggered at other times. 
		if(empty($_POST['employees_nonce']) || !wp_verify_nonce( $_POST['employees_nonce'], self::$plugin_basename)) {
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
		if(isset($_POST['employees'])) {
			// format the telephone
			if(!empty($_POST['employees']['telephone'])) {
				$_POST['employees']['telephone'] = preg_replace('[\D]', '', $_POST['employees']['telephone']);
			}
			
			// format the email
			if(!is_email($_POST['employees']['email'])) {
				$_POST['employees']['email'] = null;
			}
			
			// Save the meta
			foreach($_POST['employees'] as $key => $value) {
				update_post_meta($post_id, '_employees_' . $key, $value);
			}
		}
	}
	
	/**
	 * Get the meta value from a key
	 */
	public function get_employees_meta($post_id, $key) {
		return get_post_meta($post_id, '_employees_' . $key, true);
	}	
}
}

/*
 * Instance
 */
$employees = new Employees();
$employees->load();

/*
 * Template functions
 */
 
/**
 * Get the profession
 */
if(!function_exists('get_employee_profession')) {
function get_employee_profession() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'profession');
}
}

/**
 * Get the occupation
 */
if(!function_exists('get_employee_occupation')) {
function get_employee_occupation() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'occupation');
}
}

/**
 * Get the telephone number
 */
if(!function_exists('get_employee_telephone')) {
function get_employee_telephone() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'telephone');
}
}

/**
 * Get the email address
 */
if(!function_exists('get_employee_email')) {
function get_employee_email() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'email');
}
}

/**
 * Get the twitter url
 */
if(!function_exists('get_employee_twitter')) {
function get_employee_twitter() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'twitter');
}
}

/**
 * Get the facebook url
 */
if(!function_exists('get_employee_facebook')) {
function get_employee_facebook() {
	global $employees, $post;
	return $employees->get_employees_meta($post->ID, 'facebook');
}
}

?>