<?php

require_once 'vendor/autoload.php';

add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/style.css' );

  wp_enqueue_style('semantic', get_stylesheet_directory_uri() . '/includes/css/semantic.min.css');
  wp_enqueue_style('dataTables-semanticui', get_stylesheet_directory_uri() . '/includes/css/dataTables.semanticui.min.css');

  wp_enqueue_style('nortest-css', get_stylesheet_directory_uri() . '/includes/css/custom.css');

  wp_enqueue_script('dataTables-js', get_stylesheet_directory_uri() . '/includes/scripts/jquery.dataTables.min.js', ['jquery'], get_style_version('/includes/scripts/jquery.dataTables.min.js'), true);
  wp_enqueue_script('dataTables-semanticui', get_stylesheet_directory_uri() . '/includes/scripts/dataTables.semanticui.min.js', ['jquery'], get_style_version('/includes/scripts/dataTables.semanticui.min.js'), true);
  wp_enqueue_script('dataTables-semantic', get_stylesheet_directory_uri() . '/includes/scripts/semantic.min.js', ['jquery'], get_style_version('/includes/scripts/semantic.min.js'), true);

  wp_enqueue_script( 'custom_js', get_stylesheet_directory_uri() . '/includes/scripts/custom.js', ['jquery'], get_style_version( '/includes/scripts/custom.js' ), true );
  wp_localize_script(
      'custom_js',
      'ajax_object',
      [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
      ]
    );
} );

if ( ! defined( 'QOBRIX_TEMPLATE_OVERRIDE_DIR' ) ) {
      define( 'QOBRIX_TEMPLATE_OVERRIDE_DIR', get_stylesheet_directory() . '/templates/' );
}

// Define theme's images URI.
if ( ! defined( 'IMAGES_URI' ) ) {
  define( 'IMAGES_URI', get_stylesheet_directory_uri() . '/images/' );
}

// Define theme's functions directory.
if ( ! defined( 'THEME_LIB_DIR' ) ) {
  define( 'THEME_LIB_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR );
}

/**
 * Load all functions which are placed in theme's folder
 *
 * @param string $dir Directory to load files from.
 * @return void
 */
function load_includes( string $dir ) : void {
  $it = new RecursiveDirectoryIterator( $dir );
  $it = new RecursiveIteratorIterator( $it );
  $it = new RegexIterator( $it, '#.php$#' );
  foreach ( $it as $include ) {
    if ( $include->isReadable() ) {
      require_once( $include->getPathname() );
    }
  }
}

load_includes( THEME_LIB_DIR );

\QobrixClasses\QobrixUserRegistration::init();
\QobrixClasses\QobrixUserProfile::init();

/**
 * Generating css and js versions to avoid browser caching
 *
 * @param string $path Path.
 * @return string
 */
function get_style_version( $path ) {
  $theme_data = wp_get_theme();
  $theme_version = $theme_data->version;
  $randomizr = filemtime( __DIR__ . $path );
  $theme_version = substr_replace( $theme_version, '', -1 );

  return ($theme_version . $randomizr);
}

/**
 * Property Redirect.
 */
add_action( 'template_redirect', 'custom_page_not_found_redirect' );
function custom_page_not_found_redirect() {
  $my_projects_slug = get_admin_page_slug( get_field( 'my_projects_page', 'options' ) );
  $my_documents_slug = get_admin_page_slug( get_field( 'my_documents_page', 'options' ) );
  $my_profile_slug = get_admin_page_slug( get_field( 'my_profile_page', 'options' ) );
  $login_slug = get_admin_page_slug( get_field( 'login_page', 'options' ) );

  if ( is_page( $login_slug ) ) {
    if ( is_user_logged_in() ) {
      wp_redirect( home_url( '/' . $my_projects_slug . '/' ) );
    }
  } elseif ( is_page( $my_projects_slug ) || is_page( $my_documents_slug ) || is_page( $my_profile_slug ) ) {
    if ( !is_user_logged_in() ) {
      wp_redirect( home_url( '/' . $login_slug . '/' ) );
    }
  }
  
}

/**
 * Get url slug
 *
 * @param string $current_url page urk.
 * @return string $slug page slug
 */
function get_admin_page_slug( $current_url ) {
  $post_id = url_to_postid($current_url);
  $slug = get_post_field( 'post_name', $post_id );
  return $slug;
}

/*
 * 
 * Certificate Validation
 * 
 */

add_filter('wpcf7_validate_text*', 'fast_certificate_check', 20, 2);
add_filter('wpcf7_validate_text', 'fast_certificate_check', 20, 2);

function fast_certificate_check($result, $tag) {
    // Only run this for our specific report-number field
    if ($tag->name !== 'report-number') {
        return $result;
    }

    $company_name = isset($_POST['company-name']) ? sanitize_text_field($_POST['company-name']) : '';
    $report_number = isset($_POST['report-number']) ? sanitize_text_field($_POST['report-number']) : '';

    // Database Query
    $args = array(
        'post_type'      => 'certificate-info',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids', // Faster query: only get IDs
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'company_name', 'value' => $company_name, 'compare' => '='),
            array('key' => 'report_number', 'value' => $report_number, 'compare' => '=')
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {        
        add_filter('wpcf7_display_message', function($message, $status) {
    		return "This certificate is valid.";
		}, 10, 2); 
        $result->invalidate($tag, "This certificate is valid.");
        
        // Change the box color to green via a tiny bit of CSS injected into the response
        add_action('wp_footer', function() {
            echo '<style>.wpcf7-not-valid-tip { color: #008000 !important; }</style>';
        });

    } else {
        $result->invalidate($tag, "No valid certificate found with those details.");
		
    }

    return $result;
}

