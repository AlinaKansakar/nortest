<?php
add_settings_section(
	'el-settings-general-general-section',
	'',
	'',
	esc_html( self::$menu_slug )
);

add_settings_field(
	'el-blog-post-words-per-minute',
	esc_html__( 'Words Per Minute', 'divi-blog-extras' ),
	array( $this, 'el_range_slider_render' ),
	esc_html( self::$menu_slug ),
	'el-settings-general-general-section',
	array(
		'field_id'  => 'blog-post-words-per-minute',
		'setting'   => esc_html( self::$option ),
		'default'   => '220',
		'id'        => 'el-blog-post-words-per-minute',
		'data-type' => 'elicus-option',
		'min'       => '50',
		'max'       => '350',
		'step'      => '5',
		'info'      => esc_html__( 'Display Post Read Time depending upon the number of words an average user reads per minute.', 'divi-blog-extras' ),
	)
);
