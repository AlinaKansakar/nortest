<?php

/**
 * Get login template
 *
 * Example 1: [qobrix_login]
 *
 *  @param array $args The arguments.
 *
 * @return string field value or empty string on failure.
 */
function qobrix_login_shortcode_function( $args ) {
	$args = shortcode_atts(
		[
			'forget_password_slug' => 'forget-password',
			'title' => 'Login',
		],
		$args
	);

	return qobrix_get_template_html(
		'qobrix-login',
		[
			'atts' => $args,
		]
	);
}

add_shortcode('qobrix_login', 'qobrix_login_shortcode_function');

/**
 * User projects shortcode
 * 
 * Example 1: [qobrix_user_projects]
 *
 * @param array $args The arguments.
 * @return string field value or empty string on failure.
 */
function qobrix_user_projects_shortcode_function($args) {
	if (!is_user_logged_in()) {
		return '';
	}

	$args = shortcode_atts(
		[
			'title' => 'My Projects',
		],
		$args
	);

	$projects = qobrix_get_user_projects();

	if ($projects) {
		foreach ($projects as $project) {
			$project->documents_count = get_post_count_by_meta('project', $project->ID, 'document');
		}
	}

	$args['projects'] = $projects;

	return qobrix_get_template_html(
		'qobrix-projects',
		[
			'atts' => $args,
		]
	);
}

add_shortcode('qobrix_user_projects', 'qobrix_user_projects_shortcode_function');

/**
 * User documents shortcode
 * 
 * Example 1: [qobrix_user_documents]
 *
 * @param array $args The arguments.
 * @return string field value or empty string on failure.
 */
function qobrix_user_documents_shortcode_function($args) {
	if (!is_user_logged_in()) {
		return '';
	}

	$args = shortcode_atts(
		[
			'title' => 'My Documents',
		],
		$args
	);

	$documents = [];

	// Check if project_id is set in the query parameters
	if (isset($_GET['project_id'])) {
		$project_id = intval($_GET['project_id']);
		$project = get_post($project_id);

		if ($project && in_array(get_current_user_id(), get_field('user', $project->ID))) {
			$documents = qobrix_get_project_documents($project);
		}
	} else {
		$projects = qobrix_get_user_projects();

		foreach ($projects as $project) {
			$project_documents = qobrix_get_project_documents($project);

			$documents = array_merge($documents, $project_documents);
		}
	}

	$args['documents'] = $documents;
	return qobrix_get_template_html(
		'qobrix-documents',
		[
			'atts' => $args,
		]
	);
}

add_shortcode('qobrix_user_documents', 'qobrix_user_documents_shortcode_function');

/**
 * User document folders shortcode.
 *
 * Example 1: [qobrix_user_folders]
 *
 * @param array $args The arguments.
 * @return string field value or empty string on failure.
 */
function qobrix_user_folders_shortcode_function($args) {
	if (!is_user_logged_in()) {
		return '';
	}

	$args = shortcode_atts(
		[
			'title' => 'My Documents',
		],
		$args
	);

	$documents = [];

	if (isset($_GET['project_id'])) {
		$project_id = intval($_GET['project_id']);
		$project = get_post($project_id);

		if ($project && in_array(get_current_user_id(), get_field('user', $project->ID))) {
			$documents = qobrix_get_project_documents($project);
		}
	} else {
		$projects = qobrix_get_user_projects();

		foreach ($projects as $project) {
			$project_documents = qobrix_get_project_documents($project);
			$documents = array_merge($documents, $project_documents);
		}
	}

	$args['documents'] = $documents;
	
	return qobrix_get_template_html(
		'qobrix-folders',
		[
			'atts' => $args,
		]
	);
}

add_shortcode('qobrix_user_folders', 'qobrix_user_folders_shortcode_function');

/**
 * Folder files for one document, when the current user belongs to its project.
 *
 * @param int $document_id Document post ID.
 * @return string
 */
function qobrix_get_document_files_html($document_id) {
	if (!is_user_logged_in() || !$document_id) {
		return '';
	}

	$project = get_field('project', $document_id);
	$project_id = 0;

	if (is_object($project)) {
		$project_id = $project->ID;
	} elseif (is_array($project)) {
		$first_project = reset($project);
		$project_id = is_object($first_project) ? $first_project->ID : intval($first_project);
	} else {
		$project_id = intval($project);
	}

	$project_users = $project_id ? get_field('user', $project_id) : [];
	if (!is_array($project_users)) {
		$project_users = $project_users ? [$project_users] : [];
	}

	if (!$project_id || !in_array(get_current_user_id(), $project_users)) {
		return '';
	}

	$folders = get_field('folders', $document_id);
	$files = [];
	$project_title = get_the_title($project_id);
	$department = get_post_meta($document_id, 'department', true);
	$added_on = get_the_date('Y/m/d', $document_id);

	if ($folders) {
		foreach ($folders as $folder) {
			$file = $folder['file'] ?? null;
			$file_url = '';

			if (is_array($file) && !empty($file['url'])) {
				$file_url = $file['url'];
			} elseif (is_numeric($file)) {
				$file_url = wp_get_attachment_url($file);
			} elseif (is_string($file)) {
				$file_url = $file;
			}

			$files[] = [
				'file_name' => $folder['file_name'] ?? '',
				'file_url' => $file_url,
				'project_title' => $project_title,
				'department' => $department ?: '-',
				'added_on' => $added_on,
			];
		}
	}

	return qobrix_get_template_html(
		'qobrix-document-files',
		[
			'atts' => [
				'files' => $files,
			],
		]
	);
}

/**
 * Show the document's folder files inside the Divi single layout.
 *
 * @param string $content Post content.
 * @return string
 */
function qobrix_document_files_content($content) {
	if (!is_singular('document')) {
		return $content;
	}

	static $rendered = false;
	if ($rendered) {
		return $content;
	}
	$rendered = true;

	return $content . qobrix_get_document_files_html(get_the_ID());
}

add_filter('the_content', 'qobrix_document_files_content');

/**
 * User profile shortcode
 *
 * Example 1: [qobrix_user_profile]
 *
 * @param array $args The arguments.
 * @return string field value or empty string on failure.
 */
function qobrix_user_profile_shortcode_function($args) {
	if (!is_user_logged_in()) {
		return '';
	}

    $args = shortcode_atts(
        [
            'title' => 'My Profile',
        ],
        $args
    );

    return qobrix_get_template_html(
		'qobrix-profile',
		[
			'atts' => $args,
		]
	);
}

add_shortcode('qobrix_user_profile', 'qobrix_user_profile_shortcode_function');

/**
 * Get Project Documents.
 *
 * @param WP_Post $project The project post object.
 * @return array An array of document objects associated with the project.
 */
function qobrix_get_project_documents($project) {
	$documents = [];

	$documents_query = [
		'post_type'      => 'document',
		'post_status'    => 'publish',
		'meta_query'     => [
			[
				'key'     => 'project',
				'value'   => $project->ID,
				'compare' => '=',
			],
		],
		'posts_per_page' => -1,
		'orderby'        => 'date', // Sort by publish date
    	'order'          => 'DESC', // Newest to oldest
	];

	$project_documents = get_posts($documents_query);

	if ($project_documents) {
		foreach ($project_documents as $project_document) {
			$project_document->project_id = $project->ID;
			$project_document->project_title = $project->post_title;
		}

		$documents = array_merge($documents, $project_documents);
	}

	return $documents;
}

/**
 * Get User Projects.
 *
 * @return array An array of project objects associated with the user.
 */
function qobrix_get_user_projects() {
	$user_id = get_current_user_id();
	$projects = [];

	$projects_query = [
		'post_type'      => 'project',
		'post_status'    => 'publish',
		'meta_query'     => [
			[
				'key'     => 'user',
				'value'   => '"' . $user_id . '"',
				'compare' => 'LIKE',
			],
		],
		'orderby'        => 'date',
    	'order'          => 'DESC',
		'posts_per_page' => -1,
	];

	$projects = get_posts($projects_query);

	return $projects;
}

/**
 * Get post count by meta key and value
 *
 * @param boolean|string $meta_key Meta key to look for.
 * @param boolean|string $meta_value Meta value to look for.
 * @param string $post_type Post type.
 * @return void
 */
function get_post_count_by_meta($meta_key = false, $meta_value = false, $post_type = 'post') {
	$args = array(
		'post_type'   => $post_type,
		'numberposts' => -1,
		'post_status' => 'publish',
	);

	if ($meta_key || $meta_value) {
		$meta_query = array();

		if ($meta_key) {
			$meta_query[] = array('key' => $meta_key);
		}

		if ($meta_value) {
			$meta_query[] = array('value' => $meta_value);
		}

		$args['meta_query'] = $meta_query;
	}

	$posts = get_posts($args);

	return count($posts);
}

/**
 * Returns a Qobrix HTML template as a string.
 *
 * @param string $template location.
 * @param array  $args variables to be extracted into the template's scope.
 * @param bool   $echo Echo the result instead of returning it.
 * @return false|string template or false on failure.
 */
function qobrix_get_template_html( $template, $args = [], $echo = false ) {
	ob_start();
	qobrix_get_template( $template, $args );
	$html = ob_get_clean();

	if ( ! $echo ) {
		return $html;
	}

	// phpcs:ignore
	echo $html;
}

/**
 * Includes a Qobrix template.
 *
 * @param string $name template name.
 * @param array  $args variables to be extracted into the template's scope.
 * @return bool success or failure.
 */
function qobrix_get_template( $name, $args = [] ) {
	if ( $args && is_array( $args ) ) {

		// phpcs:ignore
		extract( $args );
	}

	if ( ! substr( $name, -4 ) === '.php' ) {
		$name .= '.php';
	}

	$path = qobrix_locate_template( $name );

	if ( ! $path ) {
		trigger_error( esc_attr( "Template $name not found." ), E_USER_WARNING );

		return false;
	}

	include $path;

	return true;
}

/**
 * Locates a Qobrix template.
 *
 * @param string $template_name to locate.
 * @return bool|string path on succcess, false on failure.
 */
function qobrix_locate_template( $template_name ) {
	$path = stream_resolve_include_path( QOBRIX_TEMPLATE_OVERRIDE_DIR . $template_name . '.php' );

	return $path;
}

/**
 * Get client menu template
 *
 * Example 1: [qobrix_client_menu]
 *
 *  @param array $args The arguments.
 *
 * @return string field value or empty string on failure.
 */
function qobrix_client_menu_shortcode_function() {
	return qobrix_get_template_html(
		'qobrix-client-menu'
	);
}

add_shortcode( 'qobrix_client_menu', 'qobrix_client_menu_shortcode_function' );
