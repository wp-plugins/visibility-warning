<?php
/*
Plugin Name: Visibility Warning
Plugin URI: http://blogestudio.com/
Description: Show a warning message in admin header area when your blog visibility is blocked for search engines. Also, when your blog is visible for everyone, hides the menu link to privacy options to prevent accidental changes.
Author: Pau Iglesias, Blogestudio
Author URI: http://blogestudio.com/
Version: 1.0.1
Text Domain: visibility-warning
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/



// Avoid direct script calls via plugin URL
if ( ! function_exists( 'add_action' ) )
	die;

// This version only works in admin area
if ( ! is_admin() )
	return;



/**
 * Visibility warning plugin class
 *
 * @package WordPress
 * @subpackage Visibility Warning
 */

// Avoid declaration plugin class conflicts
if ( ! class_exists( 'CBlogestudioVisibilityWarning' ) ) {

	// Main and unique class
	class CBlogestudioVisibilityWarning {



		// Save blog_public option state
		var $blog_public = false;



		/**
		 * PHP 4 initialization compatibility
		 */
		function CBlogestudioVisibilityWarning() {
			$this->__construct();
		}



		/**
		 * Initialize plugin
		 */
		function __construct() {
			
			// Check blog public option
			$this->blog_public = get_option( 'blog_public' );
			if ( false !== $this->blog_public ) {
				
				// Blog visible for search engines
				if ( $this->blog_public == 1 )
					add_action( 'admin_init', array( $this, 'remove_submenu_page' ), 100 );
				
				// Blocked for search engines
				elseif ( $this->blog_public == 0 ) {
					add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
					add_action( 'admin_notices', array( $this, 'show_warning' ) );
				}
			}
		}



		/**
		 *  Load translation file
		 */
		function load_plugin_textdomain() {
			
			// Check if this plugin is placed in wp-content/mu-plugins directory
			if ( basename( dirname ( dirname( __FILE__ ) ) ) == 'mu-plugins' ) {
				
				// Check WP version support
				if ( function_exists( 'load_muplugin_textdomain' ) )
					load_muplugin_textdomain( 'visibility-warning', basename( dirname( __FILE__ ) ) . '/languages');
			}
			
			// Usual wp-content/plugins directory location
			else load_plugin_textdomain( 'visibility-warning', false, basename( dirname( __FILE__ ) ) . '/languages');
		}



		/**
		 * Remove Privacy link from Settings menu section
		 */
		function remove_submenu_page() {

			// Check WP version support
			if ( function_exists( 'remove_submenu_page' ) )
				remove_submenu_page( 'options-general.php', 'options-privacy.php' );
		}



		/**
		 * Show warning in admin header area
		 */
		function show_warning() {
			echo '<div id="visibility-warning" class="updated fade"><p><strong>' . __( 'Your site is blocked for search engines.', 'visibility-warning' ) . '</strong> ' . sprintf( __( 'If you want to be visible for everyone go to <a href="%s">Privacy settings</a> or notify to site admins.', 'visibility-warning' ), 'options-privacy.php' ) . '</p></div>' . "\n";
		}



	}

	// Start and set in globals
	$GLOBALS['visibility_warning'] = new CBlogestudioVisibilityWarning();

}



?>