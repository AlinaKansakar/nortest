<?php
/**
 * @author      Elicus Technologies <hello@elicus.com>
 * @link        https://www.elicus.com/
 * @copyright   2020 Elicus Technologies Private Limited
 * @version     2.4.0
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Save theme settings to database
 *
 * @version 2.4.0
 * @return string 
 */
if ( ! function_exists( 'el_blog_panel_save_settings' ) ) {
	function el_blog_panel_save_settings() {
		check_ajax_referer( 'divi-blog-extras-panel-nonce', 'nonce', true );
		// Sanitizing $_POST['options'] in below foreach loop as it contains json values.
		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized
		$options = isset( $_POST['options'] ) ? wp_unslash( $_POST['options'] ) : '';
		if ( is_array( $options ) ) {
			foreach ( $options as $option ) {
				$type  = isset( $option['type'] ) ? sanitize_text_field( $option['type'] ) : '';
				$name  = isset( $option['name'] ) ? sanitize_text_field( $option['name'] ) : '';
				$value = isset( $option['value'] ) ? sanitize_text_field( $option['value'] ) : '';
				if ( 'elicus-option' === $type ) {
					$elicus_option = get_option( ELICUS_BLOG_OPTION );
					if ( '' === $value ) {
						if ( isset( $elicus_option[ $name ] ) ) {
							unset( $elicus_option[ $name ] );
						}
					} else {
						$elicus_option[ $name ] = $value;
					}
					update_option( ELICUS_BLOG_OPTION, $elicus_option, true );
				}
			}
		}
		exit;
	}
	add_action( 'wp_ajax_el_blog_panel_save_settings', 'el_blog_panel_save_settings' );
	add_action( 'wp_ajax_nopriv_el_blog_panel_save_settings', 'el_blog_panel_save_settings' );
}

if ( ! function_exists( 'el_blog_scan_template_files' ) ) {
	function el_blog_scan_template_files( $template_path ) {
    	$files  = @scandir( $template_path ); // @codingStandardsIgnoreLine.
		$result = array();

		if ( ! empty( $files ) ) {
			foreach ( $files as $key => $value ) {
				if ( ! in_array( $value, array( '.', '..' ), true ) ) {
					$result[] = $value;
				}
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'el_blog_is_templates_override' ) ) {
	function el_blog_is_templates_override() {
		$templates = el_blog_scan_template_files( get_stylesheet_directory() . '/divi-blog-extras/layouts/' );
		if ( is_array( $templates ) && ! empty( $templates ) ) {
			return $templates;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'el_blog_override_templates' ) ) {
	function el_blog_override_templates() {
		$override_templates = el_blog_is_templates_override();
		$templates          = array();
		if ( false !== $override_templates && is_array( $override_templates ) && ! empty( $override_templates ) ) {
			foreach ( $override_templates as $override_template ) {
				$current_version  = el_blog_get_file_version( plugin_dir_path( __DIR__ ) . 'modules/BlogExtras/layouts/' . sanitize_file_name( $override_template ) );
				$override_version = el_blog_get_file_version( get_stylesheet_directory() . '/divi-blog-extras/layouts/' . sanitize_file_name( $override_template ) );
				if ( $current_version && ( empty( $override_version ) || version_compare( $override_version, $current_version, '<' ) ) ) {
					$override_version = $override_version ? $override_version : '-';
					array_push(
						$templates,
						sprintf(
							'%1$s %2$s <strong class="el-settings-panel-code-block el-settings-panel-color-red">%3$s</strong> %4$s <strong class="el-settings-panel-code-block el-settings-panel-color-green">%5$s</strong>',
							sanitize_file_name( $override_template ),
							esc_html__( 'version', 'divi-blog-extras' ),
							floatval( $override_version ),
							esc_html__( 'is out of date. Core version is', 'divi-blog-extras' ),
							floatval( $current_version )
						)
					);
				} else {
					array_push( $templates, esc_html( $override_template ) );
				}
			}
		}
		return $templates;
	}
}

/**
 * Retrieve metadata from a file. Based on WP Core's get_file_data function.
 * get_file_data gets header data but in this case we need tags
 *
 * @param  string $file Path to the file.
 * @return string
 */
if ( ! function_exists( 'el_blog_get_file_version' ) ) {
	function el_blog_get_file_version( $file ) {

		// Avoid notices if file does not exist.
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
    	$fp = fopen( $file, 'r' ); // @codingStandardsIgnoreLine.

		// Pull only the first 8kiB of the file in.
    	$file_data = fread( $fp, 8192 ); // @codingStandardsIgnoreLine.

		// PHP will close file handle, but we are good citizens.
    	fclose( $fp ); // @codingStandardsIgnoreLine.

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version   = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
			$version = esc_html( trim( $match[1] ) );
		}

		return $version;
	}
}
