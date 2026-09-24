<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    user-role-for-flamingo
 * @subpackage user-role-for-flamingo/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class UR4F {

	protected $loader;
	protected $plugin_name;
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {
		if ( defined( 'UR4F_VERSION' ) ) {
			$this->version = UR4F_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'user-role-for-flamingo';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 * Create an instance of the db which will be used in the admin and public area.
	 * Create an instance of the http helper which wille be used in the public area.
	 *
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ur4f-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ur4f-activator.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ur4f-admin.php';
		$this->loader = new UR4F_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		load_plugin_textdomain( 'user-role-for-flamingo', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new UR4F_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'update_plugin' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_filter( 'user_has_cap', $plugin_admin, 'modify_capabilities', 10, 3 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	public function get_version() {
		return $this->version;
	}

}
