<?php
/*
Plugin Name: Search Engine Visibility Warning
Plugin URI: http://blogestudio.com/
Description: Show a warning message in admin header area when your blog visibility is blocked for search engines. Also, when your blog is visible for everyone, hides the menu link to privacy options to prevent accidental changes.
Author: Pau Iglesias, Blogestudio
Version: 1.1
Text Domain: visibility-warning
License: GPLv2 or later
*/

// Avoid direct script calls via plugin URL
if (!function_exists('add_action'))
	die;

// This version only works in admin area
if (!is_admin())
	return;

/**
 * Visibility Warning plugin class
 *
 * @package WordPress
 * @subpackage Visibility Warning
 */

// Avoid declaration plugin class conflicts
if (!class_exists('BE_Visibility_Warning')) {

	// Create object plugin
	add_action('init', array('BE_Visibility_Warning', 'instance'));

	// Main and unique class
	class BE_Visibility_Warning {



		// Initialization
		// ---------------------------------------------------------------------------------------------------



		/**
		 * Creates a new object instance
		 */
		public static function instance() {
			return new BE_Visibility_Warning;
		}



		/**
		 * Initialize plugin
		 */
		private function __construct() {
			
			// Check blog public option
			$blog_public = get_option('blog_public');
			if (false !== $blog_public) {
				
				// Visible for search engines
				if ($blog_public != 0) {
					
					// For old WP versions, hide submenu Options Privacy
					add_action('admin_init', array($this, 'remove_submenu_page'), 100);
					
					// For recent WP versions, hide Visibility options in Settings > reading
					add_action('current_screen', array($this, 'check_current_screen'));
				
				// Blocked for search engines
				} elseif ($blog_public == 0) {
					
					// Show warning messages in header area
					add_action('admin_notices', array($this, 'show_warning'));
				}
			}
		}



		/**
		 *  Load translation file
		 */
		private function load_plugin_textdomain() {
			
			// Check load
			static $loaded;
			if (isset($loaded))
				return;

			// Mark as loaded
			$loaded = true;
			
			// Check if this plugin is placed in wp-content/mu-plugins directory
			if ('mu-plugins' == basename(dirname(dirname(__FILE__))) || 'mu-plugins' == basename(dirname(__FILE__))) {
				
				// Check WP version support
				if (function_exists('load_muplugin_textdomain')) {
					load_muplugin_textdomain('visibility-warning', basename(dirname(__FILE__)).'/languages');
					return;
				}
			}
			
			// Usual wp-content/plugins directory location
			load_plugin_textdomain('visibility-warning', false, basename(dirname(__FILE__)).'/languages');
		}



		// Hooks
		// ---------------------------------------------------------------------------------------------------



		/**
		 * Remove Privacy link from Settings menu section
		 * This is a feature compatible with old WP versions
		 */
		public function remove_submenu_page() {
			if (function_exists('remove_submenu_page'))
				remove_submenu_page('options-general.php', 'options-privacy.php');
		}



		/**
		 * Check options-reading.php admin page
		 */
		public function check_current_screen($current_screen) {
			if ('options-reading' == $current_screen->id)
				add_action('admin_print_styles', array($this, 'hide_visibility_options'));
		}



		/**
		 * Hide options from options-reading.php admin page
		 */
		public function hide_visibility_options() {
			echo '<style type="text/css">.option-site-visibility{display:none}</style>';
		}



		/**
		 * Show warning in admin header area
		 */
		public function show_warning() {
			
			// Load translations
			$this->load_plugin_textdomain();
			
			// Detect old versions
			$old_versions = version_compare(get_bloginfo('version'), '3.5', '<');
			
			// Link and anchor depends of WP version
			echo '<div id="visibility-warning" class="updated fade"><p><strong>'.__('Your site is blocked for search engines.','visibility-warning').'</strong> '.sprintf(__('If you want to be visible for everyone go to <a href="%s">%s</a> or notify to site admins.', 'visibility-warning'), admin_url($old_versions? 'options-privacy.php' : 'options-reading.php'), $old_versions? __('Privacy Settings', 'visibility-warning') : __('Reading Settings', 'visibility-warning')).'</p></div>'."\n";
		}



	}
}