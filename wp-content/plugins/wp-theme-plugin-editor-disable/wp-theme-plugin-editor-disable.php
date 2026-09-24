<?php
/**
 * Plugin Name: WP Theme Plugin Editor Disable
 * Plugin URI: http://wordpress.org/plugins/wp-theme-plugin-editor-disable/
 * Description: This plugin disable Wordpress Theme Plugin Editor. To disable Theme Plugin Editor, activate this plugin. WordPress, by default, allows you the ability edit theme and plugin files directly from within the WordPress control panel. If you never change files directly on your WordPress site, there's absolutely no need to have this feature available to you or your administrators.
 *
 * Author: Yslo
 * Version: 1.0.0
 * Author URI: http://profiles.wordpress.org/yslo
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!defined('DISALLOW_FILE_EDIT')) define( 'DISALLOW_FILE_EDIT', true );
