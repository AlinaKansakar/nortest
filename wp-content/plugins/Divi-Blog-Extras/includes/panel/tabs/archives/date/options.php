<?php
$query_args   = array(
	'post_type'      => 'et_pb_layout',
	'posts_per_page' => -1,
	'meta_query'     => array(
		array(
			'key'     => '_et_pb_predefined_layout',
			'value'   => 'on',
			'compare' => 'NOT EXISTS',
		),
	),
);
$modules_list = '';
$query        = new WP_Query( $query_args );
if ( $query->have_posts() ) {
	$modules_list     = array();
	$modules_list[''] = esc_html__( 'Select', 'divi-blog-extras' );
	while ( $query->have_posts() ) {
		$query->the_post();
		if ( false !== strpos( get_the_content(), 'et_pb_blog_extras' ) ) {
			$modules_list[ intval( get_the_ID() ) ] = ucwords( esc_html( get_the_title() ) );
		}
	}
	wp_reset_postdata();
}

if ( empty( $modules_list ) || 1 === count( $modules_list ) ) {
	$modules_list = sprintf(
		'<a href="%1$s" title="%2$s">%2$s</a> %3$s.',
		esc_url( admin_url( '/edit.php?post_type=et_pb_layout' ) ),
		esc_html__( 'Create', 'divi-blog-extras' ),
		esc_html__( 'at least one Divi Library layout with Divi Blog Extras module in it.', 'divi-blog-extras' )
	);
}

$layout_list = array(
	'left-sidebar'  => esc_html__( 'Left Sidebar', 'divi-blog-extras' ),
	'right-sidebar' => esc_html__( 'Right Sidebar', 'divi-blog-extras' ),
	'full-width'    => esc_html__( 'Full Width', 'divi-blog-extras' ),
);

add_settings_section(
	'el-settings-archives-date-section',
	'',
	'',
	esc_html( self::$menu_slug )
);

add_settings_field(
	'el-date-archive-layout-toggle',
	esc_html__( 'Enable Date Archive Layout', 'divi-blog-extras' ),
	array( $this, 'el_toggle_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-archives-date-section',
	array(
		'field_id'   => 'enable-date-archive-layout',
		'setting'    => esc_html( self::$option ),
		'default'    => 'off',
		'dependency' => 'yes',
		'dependent'  => array( 'el-assign-date-archive-layout' ),
		'id'         => 'el-date-archive-layout-toggle',
		'data-type'  => 'elicus-option',
		'info'       => esc_html__( 'Enable this to use Divi Blog Extras module on Date Archives. In order to use this feature a layout must be saved first in the Divi Library and have Divi Blog Extras module on it.', 'divi-blog-extras' ),
	)
);

$fields = array(
	array(
		esc_html__( 'Select Layout from Library', 'divi-blog-extras' ),
		'el_dropdown_render',
		array(
			'field_id'     => 'date-archive-layout',
			'setting'      => esc_html( self::$option ),
			'default'      => '',
			'id'           => 'el-date-archive-layout',
			'data-type'    => 'elicus-option',
			'list_options' => $modules_list,
			'info'         => esc_html__( 'Select a layout from the Divi Library to use as date archive. The layout must be saved first and have Divi Blog Extras module on it.', 'divi-blog-extras' ),
		),
	),
	array(
		esc_html__( 'Select Sidebar', 'divi-blog-extras' ),
		'el_dropdown_render',
		array(
			'field_id'     => 'date-archive-layout-type',
			'setting'      => esc_html( self::$option ),
			'default'      => 'right-sidebar',
			'id'           => 'el-date-archive-layout-type',
			'data-type'    => 'elicus-option',
			'list_options' => $layout_list,
			'info'         => esc_html__( 'Here you can select sidebar position for Date Archive.', 'divi-blog-extras' ),
		),
	),
);

add_settings_field(
	'el-assign-date-archive-layout',
	esc_html__( 'Assign Global Date Archive Layout', 'divi-blog-extras' ),
	array( $this, 'el_fieldset_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-archives-date-section',
	array(
		'field_id'   => 'assign-date-archive-layout',
		'setting'    => '',
		'default'    => '',
		'fields'     => $fields,
		'depends-on' => array( 'el-date-archive-layout-toggle' ),
		'id'         => 'el-assign-date-archive-layout',
		'data-type'  => '',
		'info'       => esc_html__( 'Here you can specify a global layout from Divi Library for all Date Archives.', 'divi-blog-extras' ),
	)
);
