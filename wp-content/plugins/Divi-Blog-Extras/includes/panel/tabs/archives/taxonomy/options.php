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

$site_taxonomies = get_taxonomies(
	array(
		'public'   => true,
		'_builtin' => false,
	),
	'objects'
);

if ( $site_taxonomies && is_array( $site_taxonomies ) ) {
	$taxonomy_list       = array();
	$taxonomy_list['']   = esc_html__( 'Select', 'divi-blog-extras' );
	$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES );
	foreach ( $site_taxonomies as $key => $site_taxonomy ) {
		if ( ! in_array( $key, $filtered_taxonomies, true ) ) {
			$taxonomy_list[ esc_html( $key ) ] = esc_html( $site_taxonomy->label ) . '(' . ucwords( implode( ', ', array_map( 'esc_html', $site_taxonomy->object_type ) ) ) . ')';
		}
	}

	add_settings_section(
		'el-settings-archives-taxonomy-section',
		'',
		'',
		esc_html( self::$menu_slug )
	);

	add_settings_field(
		'el-taxonomy-archive-layout-toggle',
		esc_html__( 'Enable Taxonomy Archive Layout', 'divi-blog-extras' ),
		array( $this, 'el_toggle_render' ),
		esc_html( self::$menu_slug ),
		'el-settings-archives-taxonomy-section',
		array(
			'field_id'   => 'enable-taxonomy-archive-layout',
			'setting'    => esc_html( self::$option ),
			'default'    => 'off',
			'dependency' => 'yes',
			'dependent'  => array( 'el-global-taxonomy-archive-layout', 'el-variable-taxonomy-archive-layout' ),
			'id'         => 'el-taxonomy-archive-layout-toggle',
			'data-type'  => 'elicus-option',
			'info'       => esc_html__( 'Enable this to use Divi Blog Extras for Taxonomy Archives. In order to use this feature a layout must be saved first in the Divi Library and have Divi Blog Extras module on it.', 'divi-blog-extras' ),
		)
	);

	$fields = array(
		array(
			esc_html__( 'Select Layout from Library', 'divi-blog-extras' ),
			'el_dropdown_render',
			array(
				'field_id'     => 'taxonomies-archive-layout',
				'setting'      => esc_html( self::$option ),
				'default'      => '',
				'id'           => 'el-taxonomies-archive-layout',
				'data-type'    => 'elicus-option',
				'list_options' => $modules_list,
				'info'         => esc_html__( 'Select a layout from the Divi Library to use as Taxonomy Archive. The layout must be saved first and have Divi Blog Extras module on it.', 'divi-blog-extras' ),
			),
		),
		array(
			esc_html__( 'Select Sidebar', 'divi-blog-extras' ),
			'el_dropdown_render',
			array(
				'field_id'     => 'taxonomies-archive-layout-type',
				'setting'      => esc_html( self::$option ),
				'default'      => 'right-sidebar',
				'id'           => 'el-taxonomies-archive-layout-type',
				'data-type'    => 'elicus-option',
				'list_options' => $layout_list,
				'info'         => esc_html__( 'Here you can select sidebar position for Taxonomy Archive.', 'divi-blog-extras' ),
			),
		),
	);

	add_settings_field(
		'el-global-taxonomy-archive-layout',
		esc_html__( 'Assign Global Taxonomy Archive Layout', 'divi-blog-extras' ),
		array( $this, 'el_fieldset_render' ),
		esc_html( self::$menu_slug ),
		'el-settings-archives-taxonomy-section',
		array(
			'field_id'   => 'global-taxonomy-archive-layout',
			'setting'    => '',
			'default'    => '',
			'fields'     => $fields,
			'depends-on' => array( 'el-taxonomy-archive-layout-toggle' ),
			'id'         => 'el-global-taxonomy-archive-layout',
			'data-type'  => '',
			'info'       => esc_html__( 'Here you can specify a global layout from Divi Library for all Taxonomy Archives.', 'divi-blog-extras' ),
		)
	);

	$fields = array(
		array(
			esc_html__( 'Select Taxonomy', 'divi-blog-extras' ),
			'el_dropdown_render',
			array(
				'field_id'     => 'taxonomy-name-1',
				'setting'      => esc_html( self::$option ),
				'default'      => '',
				'id'           => 'el-taxonomy-name-1',
				'data-type'    => 'elicus-option',
				'list_options' => $taxonomy_list,
				'info'         => esc_html__( 'Select a taxonomy', 'divi-blog-extras' ),
			),
		),
		array(
			esc_html__( 'Select Layout from Library', 'divi-blog-extras' ),
			'el_dropdown_render',
			array(
				'field_id'     => 'taxonomy-archive-layout-1',
				'setting'      => esc_html( self::$option ),
				'default'      => '',
				'id'           => 'el-taxonomy-archive-layout-1',
				'data-type'    => 'elicus-option',
				'list_options' => $modules_list,
				'info'         => esc_html__( 'Select a layout from the Divi Library to use as taxonomy archive. The layout must be saved first and have Divi Blog Extras module on it.', 'divi-blog-extras' ),
			),
		),
		array(
			esc_html__( 'Select Sidebar', 'divi-blog-extras' ),
			'el_dropdown_render',
			array(
				'field_id'     => 'taxonomy-archive-layout-type-1',
				'setting'      => esc_html( self::$option ),
				'default'      => 'right-sidebar',
				'id'           => 'el-taxonomy-archive-layout-type-1',
				'data-type'    => 'elicus-option',
				'list_options' => $layout_list,
				'info'         => esc_html__( 'Select taxonomy archive sidebar position here.', 'divi-blog-extras' ),
			),
		),
	);

	$tax_repeater_value = wp_json_encode(
		array(
			'counter' => '1',
			'fields'  => 'taxonomy-name,taxonomy-archive-layout,taxonomy-archive-layout-type',
		)
	);
	add_settings_field(
		'el-variable-taxonomy-archive-layout',
		esc_html__( 'Assign separate Layout for each Taxonomy', 'divi-blog-extras' ),
		array( $this, 'el_repeater_render' ),
		esc_html( self::$menu_slug ),
		'el-settings-archives-taxonomy-section',
		array(
			'field_id'   => 'variable-taxonomy-archive-layout',
			'setting'    => esc_html( self::$option ),
			'default'    => $tax_repeater_value,
			'fields'     => $fields,
			'depends-on' => array( 'el-taxonomy-archive-layout-toggle' ),
			'id'         => 'el-variable-taxonomy-archive-layout',
			'data-type'  => 'elicus-option',
			'info'       => esc_html__( 'Here you can specify separate layout for each Taxonomy. This setting has a higher importance and it will override the global Taxonomy for selected Taxonomies.', 'divi-blog-extras' ),
		)
	);
}
