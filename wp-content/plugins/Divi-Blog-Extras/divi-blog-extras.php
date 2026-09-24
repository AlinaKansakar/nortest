<?php
/**
 * Plugin Name: Divi Blog Extras
 * Plugin URI: https://diviextended.com/
 * Description: A powerful and highly customizable Divi blog layout plugin with Archive and Custom Post Type support.
 * Version: 2.7.0
 * Author: Elicus
 * Author URI: https://elicus.com/
 * Update URI: https://diviextended.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: divi-blog-extras
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'ELICUS_BLOG_VERSION', '2.7.0' );
define( 'ELICUS_BLOG_OPTION', 'el-divi-blog-extras' );
define( 'ELICUS_BLOG_BASENAME', plugin_basename( __FILE__ ) );
define( 'ELICUS_BLOG_PATH', plugin_dir_url( __FILE__ ) );

require_once plugin_dir_path( __FILE__ ) . 'includes/src/class-installation.php';
register_activation_hook( __FILE__, array( 'El_Blog_Installation', 'el_plugin_add_installs' ) );
register_deactivation_hook( __FILE__, array( 'El_Blog_Installation', 'el_plugin_remove_installs' ) );

if ( ! function_exists( 'el_blog_initialize_extension' ) ) {
	/**
	 * Creates the extension's main class instance.
	 *
	 * @since 2.1.0
	 */
	function el_blog_initialize_extension() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/DiviBlogExtras.php';
	}
	add_action( 'divi_extensions_init', 'el_blog_initialize_extension' );
}

require_once plugin_dir_path( __FILE__ ) . 'includes/widgets/BlogExtras/BlogExtras.php';
function el_register_blog_widgets() {
	register_widget( 'El_Blog_Widget' );
}
add_action( 'widgets_init', 'el_register_blog_widgets' );
