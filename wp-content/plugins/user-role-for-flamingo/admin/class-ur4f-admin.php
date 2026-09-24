<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    user-role-for-flamingo
 * @subpackage user-role-for-flamingo/admin
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ur4f-settings.php';

class UR4F_Admin {

	private $plugin_name;
	private $version;
	private $UR4F_Settings;
	private $ur4f_options;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->UR4F_Settings = new UR4F_Admin_Settings();
		$this->ur4f_options = get_option("ur4f_options");
	}

    /**
	 * Register the update mechanism, called by hook 'plugins_loaded'. Important, because 
	 * when updating plugins via the auto-update mechanism, the activation hook is not called. 
	 * So no update would take place. That's why we call the update functionality every time 
	 * and compare it with the current installed version.
	 */
    public function update_plugin() {
        UR4F_Activator::update( $this->version );
    }

    /**
	 * Register the UR4F Settings Pages in the Admin menu, called by hook 'admin_menu'.
	 */
	public function register_admin_menu() {
		$this->UR4F_Settings->add_menu_page();
	}

	/**
	 * Register the UR4F Settings, called by hook 'admin_init'.
	 */
	public function register_settings() {
		$this->UR4F_Settings->register_settings();
	}

	/**
	 * Main function of this plugin, called by filter 'user_has_cap'.
	 */
	public function modify_capabilities($allcaps, $primitive_caps, $args) {

		// Taken from Flamingo plugin > includes > capabilities.php
		$flamingo_caps = array(
			'flamingo_edit_contact',
			'flamingo_edit_contacts',
			'flamingo_delete_contact',
			'flamingo_edit_inbound_message',
			'flamingo_edit_inbound_messages',
			'flamingo_delete_inbound_message',
			'flamingo_delete_inbound_messages',
			'flamingo_spam_inbound_message',
			'flamingo_unspam_inbound_message',
			'flamingo_edit_outbound_message',
			// 'flamingo_edit_outbound_messages', // currently not used by Flamingo
			'flamingo_delete_outbound_message'
		);

		// Is the current check for a flamingo meta cap?
		$requested_cap = $args[0];
		if(in_array($requested_cap, $flamingo_caps)) {
			// -> yes: check if user is flamingo user
			if(current_user_can("ur4f_capas")) {
				// -> yes: check, if requested cap is avaliable and active in ur4f options
				$cap_active = $this->ur4f_options['ur4f_'.$requested_cap] ?? null;
				if(isset($cap_active) && intval($cap_active)) {
					$allcaps[$primitive_caps[0]] = true;
				}
			}
		}
		
		return $allcaps;
	}
}
