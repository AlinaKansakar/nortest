<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation and provides an update mechanism.
 *
 * @package    user-role-for-flamingo
 * @subpackage user-role-for-flamingo/includes
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

class UR4F_Activator {

	/**
	 * Called on plugin activation.
	 * Calls the create_role function.
	 */
	public static function activate( $version ) {
        self::create_role( $version );
	}

    /**
	 * Called on plugin update.
	 * Calls the create_role function.
	 */
	public static function update( $version ) {
        self::create_role( $version );
	}

    /**
	 * Called by the action or update method.
	 * Creates or updates the database.
	 */
    private static function create_role( $version ) {

        $installed_version = get_option( "ur4f_version" );
        if ( $installed_version !== $version ) {
            
            $capas = get_role( 'subscriber' )->capabilities;
            $capas['ur4f_capas'] = true;
            add_role( 'flamingo-user', 'Flamingo User', $capas );

            // Check for additional, version specific updates

            // Updating to 1.0.2
            // if(version_compare('1.0.1', $installed_version)) {
            //     // Do specific database updates that the dbDelta function doesn't cover, e.g. deleting columns
            //     $wpdb->query(
            //         "ALTER TABLE {$wpdb->prefix}my_table
            //         ADD COLUMN `count` SMALLINT(6) NOT NULL
            //         ");
            //     // Set default values for new options
            //     $rh4a_options['new_option_name'] = "default_value";
            //     update_option("rh4a_options", $rh4a_options);
            //     // Eventually remove role to assign new capabilities
            //     remove_role('flamingo-user');
            // }

            update_option( "ur4f_version", $version );

        }
    }

}
