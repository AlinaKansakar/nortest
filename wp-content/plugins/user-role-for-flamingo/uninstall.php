<?php

/**
 * "Magic" file which is only called when the uninstall process 
 * is invoked for this plugin.
 *
 * @package    user-role-for-flamingo
 * @author     Yannick Zipf <yannick.zipf@icloud.com>
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete role
remove_role('flamingo-user');

// Delete options
delete_option( 'ur4f_options' );
delete_option( 'ur4f_version' );
