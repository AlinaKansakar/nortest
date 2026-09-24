<?php
add_settings_section(
	'el-settings-general-templates-section',
	'',
	'',
	esc_html( self::$menu_slug )
);

$templates = el_blog_override_templates();

add_settings_field(
	'el-blog-templates-override',
	esc_html__( 'Templates', 'divi-blog-extras' ),
	array( $this, 'el_list_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-general-templates-section',
	array(
		'field_id'     => 'blog-templates-override',
		'setting'      => '',
		'default'      => '',
		'id'           => 'el-blog-templates-override',
		'data-type'    => '',
		'list_options' => $templates,
		'info'         => esc_html__( 'Here you can see the list of files that have been override by you.', 'divi-blog-extras' ),
	)
);
