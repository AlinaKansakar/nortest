<?php
$custom_posts = get_post_types(
	array(
		'public'   => true,
		'_builtin' => false,
	)
);
$posts_list   = array();
foreach ( $custom_posts as $post_slug => $custom_post ) {
	$post_obj                             = get_post_type_object( $post_slug );
	$posts_list[ esc_html( $post_slug ) ] = sprintf( esc_html__( '%s', 'divi-blog-extras' ), esc_html( $post_obj->labels->singular_name ) );
}

add_settings_section(
	'el-settings-custom-posts-general-section',
	'',
	'',
	esc_html( self::$menu_slug )
);

add_settings_field(
	'el-blog-custom-posts-toggle',
	esc_html__( 'Enable Custom Post Types in Module', 'divi-blog-extras' ),
	array( $this, 'el_toggle_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-custom-posts-general-section',
	array(
		'field_id'   => 'enable-blog-custom-posts',
		'setting'    => esc_html( self::$option ),
		'default'    => 'off',
		'dependency' => 'yes',
		'dependent'  => array( 'el-blog-custom-posts' ),
		'id'         => 'el-blog-custom-posts-toggle',
		'data-type'  => 'elicus-option',
		'info'       => esc_html__( 'Here you can enable to display Custom Post Types in Divi Blog Extras module.', 'divi-blog-extras' ),
	)
);

add_settings_field(
	'el-blog-custom-posts',
	esc_html__( 'Select Custom Post Types', 'divi-blog-extras' ),
	array( $this, 'el_mutiple_checkbox_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-custom-posts-general-section',
	array(
		'field_id'     => 'blog-custom-posts',
		'setting'      => esc_html( self::$option ),
		'default'      => '',
		'depends-on'   => array( 'el-blog-custom-posts-toggle' ),
		'id'           => 'el-blog-custom-posts',
		'data-type'    => 'elicus-option',
		'list_options' => $posts_list,
		'info'         => esc_html__( 'Here you can select the Custom Post Types for which you want to enable Divi Blog Extras module on the Divi page builder.', 'divi-blog-extras' ),
	)
);

add_settings_field(
	'el-blog-custom-taxonomies-toggle',
	esc_html__( 'Enable Custom Taxonomies in Module', 'divi-blog-extras' ),
	array( $this, 'el_toggle_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-custom-posts-general-section',
	array(
		'field_id'  => 'enable-blog-custom-taxonomies',
		'setting'   => esc_html( self::$option ),
		'default'   => 'off',
		'id'        => 'el-blog-custom-taxonomies-toggle',
		'data-type' => 'elicus-option',
		'info'      => esc_html__( 'Here you can enable to dsiplay checkbox with list of Taxonomies in the Divi Blog Extras module.', 'divi-blog-extras' ),
	)
);
