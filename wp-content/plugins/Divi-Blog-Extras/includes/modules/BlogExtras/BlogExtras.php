<?php
class El_Blog_Module extends ET_Builder_Module {

	public $slug       = 'et_pb_blog_extras';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://diviextended.com',
		'author'     => 'Elicus',
		'author_uri' => 'https://elicus.com',
	);

	/**
	 * Track if the module is currently rendering to prevent unnecessary rendering and recursion.
	 *
	 * @var bool
	 */
	protected static $rendering = false;

	public function init() {
		$this->name             = esc_html( 'Divi Blog Extras' );
		$this->main_css_element = '%%order_class%% .et_pb_post.et_pb_post_extra';
		add_filter( 'et_builder_processed_range_value', array( $this, 'el_builder_processed_range_value' ), 10, 3 );
		add_filter( 'et_late_global_assets_list', array( $this, 'el_dbe_late_assets' ), 10, 3 );
	}

	public function el_dbe_late_assets( $assets_list, $assets_args, $et_dynamic_assets ) {
		if ( function_exists( 'et_get_dynamic_assets_path' ) && function_exists( 'et_is_cpt' ) ) {
			$cpt_suffix = et_is_cpt() ? '_cpt' : '';
			$assets_list['et_posts'] = array(
				'css' => $assets_args['assets_prefix'] . "/css/posts{$cpt_suffix}.css",
			);
			$assets_list['et_legacy_animations'] = array(
				'css' => $assets_args['assets_prefix'] . "/css/legacy_animations{$cpt_suffix}.css",
			);
			$assets_list['et_icons_all'] = array(
				'css' => $assets_args['assets_prefix'] . "/css/icons_all.css",
			);
			$assets_list['et_icons_fa'] = array(
				'css' => $assets_args['assets_prefix'] . "/css/icons_fa_all.css",
			);
			$assets_list['et_icons_social'] = array(
                'css' => $assets_args['assets_prefix'] . "/css/icons_base_social.css",
            );
			$assets_list['et_overlay'] = array(
				'css' => $assets_args['assets_prefix'] . "/css/overlay{$cpt_suffix}.css",
			);
		}
		return $assets_list;
	}

	public function get_settings_modal_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'main_content' => array(
						'title'	=> esc_html__( 'Layout', 'divi-blog-extras' ),
					),
					'slider_settings' => array(
						'title' => esc_html__( 'Slider', 'divi-blog-extras' ),
					),
					'loop_query' => array(
						'title' => esc_html__( 'Query', 'divi-blog-extras' ),
					),
					'elements' => array(
						'title' => esc_html__( 'Elements', 'divi-blog-extras' ),
					),
					'pagination' => array(
						'title' => esc_html__( 'Pagination', 'divi-blog-extras' ),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'divi-blog-extras' ),
						'priority' => 1,
					),
					'title_text' => array(
						'title'    => esc_html__( 'Title', 'divi-blog-extras' ),
						'priority' => 2,
					),
					'body_text' => array(
						'title'             => esc_html__( 'Body', 'divi-blog-extras' ),
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
						'priority' => 3,
					),
					'meta_text' => array(
						'title'    => esc_html__( 'Post Meta', 'divi-blog-extras' ),
						'priority' => 4,
					),
					'date_text' => array(
						'title'    => esc_html__( 'Full Width Post Date', 'divi-blog-extras' ),
						'priority' => 5,
					),
					'category_toggle' => array(
						'title'    => esc_html__( 'Category', 'divi-blog-extras' ),
						'priority' => 6,
					),
					'filterable_category_toggle' => array(
						'title'    		=> esc_html__( 'Filterable Category', 'divi-blog-extras' ),
						'sub_toggles'   => array(
                                                'normal'  => array(
                                                    'name' => 'Normal',
                                                ),
                                                'active' => array(
                                                    'name' => 'Active',
                                                ),
                                            ),
                        'tabbed_subtoggles' => true,
						'priority' => 7,
					),
					'read_more_settings' => array(
						'title'    => esc_html__( 'Read More Settings', 'divi-blog-extras' ),
						'priority' => 8,
					),
					'overlay' => array(
						'title'    => esc_html__( 'Overlay', 'divi-blog-extras' ),
						'priority' => 9,
					),
					'pagination'   => array(
						'title'    => esc_html__( 'Pagination', 'divi-blog-extras' ),
						'priority' => 10,
					),
					'slider_styles'	=> array(
						'title' => esc_html__( 'Slider', 'divi-blog-extras' ),
						'priority' => 11,
					),
					'mobile_settings' => array(
						'title'    => esc_html__( 'Mobile Settings', 'divi-blog-extras' ),
						'priority' => 12,
					),
					
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts' => array(
				'filterable_category' => array(
                    'label'     => esc_html__( 'Category', 'divi-blog-extras' ),
                    'font_size' => array(
                        'default'           => '18px',
                        'range_settings'    => array(
                            'min'   => '1',
                            'max'   => '100',
                            'step'  => '1',
                        ),
                        'validate_unit'     => true,
                    ),
                    'line_height' => array(
                        'default'           => '1.5em',
                        'range_settings'    => array(
                            'min'   => '0.1',
                            'max'   => '10',
                            'step'  => '0.1',
                        ),
                    ),
                    'letter_spacing' => array(
                        'default'           => '0px',
                        'range_settings'    => array(
                            'min'   => '0',
                            'max'   => '10',
                            'step'  => '1',
                        ),
                        'validate_unit' => true,
                    ),
                    'hide_text_align'   => true,
                    'css'       => array(
                        'main'          => "%%order_class%% .el-dbe-post-categories a:not(.el-dbe-active-category)",
                    ),
                    'toggle_slug'   	=> 'filterable_category_toggle',
                    'sub_toggle'    	=> 'normal',
                    'tab_slug'      	=> 'advanced',
                    'depends_on'        => array( 'use_category_filterable_blog' ),
                    'depends_show_if'   => 'on',
                ),
                'active_filterable_category' => array(
                    'label'             => esc_html__( 'Active Category', 'divi-blog-extras' ),
                    'font_size'         => array(
                        'default'           => '18px',
                        'range_settings'    => array(
                            'min'   => '1',
                            'max'   => '100',
                            'step'  => '1',
                        ),
                        'validate_unit'     => true,
                    ),
                    'line_height'       => array(
                        'default'           => '1.5em',
                        'range_settings'    => array(
                            'min'   => '0.1',
                            'max'   => '10',
                            'step'  => '0.1',
                        ),
                    ),
                    'letter_spacing'    => array(
                        'default'           => '0px',
                        'range_settings'    => array(
                            'min'   => '0',
                            'max'   => '10',
                            'step'  => '1',
                        ),
                        'validate_unit' => true,
                    ),
                    'hide_text_align'   => true,
                    'css'               => array(
                        'main'      => "%%order_class%% .el-dbe-post-categories a.el-dbe-active-category, %%order_class%% .el-dbe-mobile-active-category",
                    ),
                    'toggle_slug'       => 'filterable_category_toggle',
                    'sub_toggle'        => 'active',
                    'tab_slug'          => 'advanced',
                    'depends_on'        => array( 'use_category_filterable_blog' ),
                    'depends_show_if'   => 'on',
                ),
				'header'    => array(
					'label'          => esc_html__( 'Title', 'divi-blog-extras' ),
					'font_size'      => array(
						'default'        => '18px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'    => array(
						'default'        => '1.5em',
						'range_settings' => array(
							'min'  => '0.1',
							'max'  => '10',
							'step' => '0.1',
						),
					),
					'letter_spacing' => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'css'            => array(
						'main'      => "{$this->main_css_element} .entry-title, {$this->main_css_element} .entry-title a",
						'important' => 'all',
					),
					'header_level'   => array(
						'default' => 'h2',
					),
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'title_text',
				),
				'body'      => array(
					'label'          => esc_html__( 'Body', 'divi-blog-extras' ),
					'font_size'      => array(
						'default'        => '16px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'    => array(
						'default'        => '1.3em',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '5',
							'step' => '0.1',
						),
					),
					'letter_spacing' => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'css'            => array(
						'main' => "{$this->main_css_element} .post-content .post-data, {$this->main_css_element} .post-content .post-data p",
					),
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'body_text',
					'sub_toggle'   	=> 'p',
				),
				'body_link' => array(
					'label'           => esc_html__( 'Link', 'divi-blog-extras' ),
					'font_size'       => array(
						'default'        => '16px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'     => array(
						'default'        => '1.3em',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '5',
							'step' => '0.1',
						),
					),
					'letter_spacing'  => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'hide_text_align' => true,
					'css'             => array(
						'main' => "{$this->main_css_element} .post-content .more-link, {$this->main_css_element} .post-content .post-data a",
					),
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'body_text',
					'sub_toggle'    => 'a',
				),
				'meta'      => array(
					'label'          => esc_html__( 'Meta', 'divi-blog-extras' ),
					'font_size'      => array(
						'default'        => '14px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'    => array(
						'default'        => '1.3em',
						'range_settings' => array(
							'min'  => '0.1',
							'max'  => '10',
							'step' => '0.1',
						),
					),
					'letter_spacing' => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'css'            => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a, {$this->main_css_element} .post-meta span, {$this->main_css_element} .post-date",
					),
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'meta_text',
				),
				'post_date' => array(
					'label'           => esc_html__( 'Full Width Post Date', 'divi-blog-extras' ),
					'font_size'       => array(
						'default'        => '14px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'     => array(
						'default'        => '1.3em',
						'range_settings' => array(
							'min'  => '0.1',
							'max'  => '10',
							'step' => '0.1',
						),
					),
					'letter_spacing'  => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'css'             => array(
						'main' => "{$this->main_css_element} .post-date em",
					),
					'depends_on'      => array( 'blog_layout' ),
					'depends_show_if' => 'full_width',
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'date_text',
				),
				'pagination_number' => array(
					'label'          => esc_html__( 'Pagination Number', 'divi-blog-extras' ),
					'font_size'      => array(
						'default'        => '14px',
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'line_height'    => array(
						'default'        => '1',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '5',
							'step' => '0.1',
						),
					),
					'letter_spacing' => array(
						'default'        => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
						'validate_unit'  => true,
					),
					'hide_text_align' => true,
					'hide_text_color' => true,
					'hide_text_shadow' => true,
					'css'            => array(
						'main' => "%%order_class%% .el-blog-pagination ul .page-numbers",
					),
					'depends_on'      => array( 'pagination_type' ),
					'depends_show_if' => 'off',
					'tab_slug'		=> 'advanced',
					'toggle_slug'   => 'pagination',
				),
			),
			'background'            => array(
				'css' => array(
					'main' => "{$this->main_css_element}:not(.el_dbe_box_extended), {$this->main_css_element}:not(.image-background) .post-content, {$this->main_css_element}.el_dbe_block_extended:not(.image-background) .post-meta",
				),
			),
			'button'                => array(
				'ajax_pagination' => array(
					'label'           => esc_html__( 'Ajax Pagination Button', 'divi-blog-extras' ),
					'css'             => array(
						'main'         => '%%order_class%% .el-dbe-blog-extra .el-pagination-button',
						'limited_main' => '%%order_class%% .el-dbe-blog-extra .el-pagination-button.el-button',
					),
					'margin_padding'  => array(
						'css' => array(
							'main'         => '%%order_class%% .el-dbe-blog-extra .el-pagination-button',
							'limited_main' => '%%order_class%% .el-dbe-blog-extra .el-pagination-button.el-button',
							'important'    => 'all',
						),
					),
					'no_rel_attr'     => true,
					'use_alignment'   => false,
					'box_shadow'      => false,
					'depends_on'      => array( 'pagination_type' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'pagination',
				),
				'read_more'       => array(
					'label'           => esc_html__( 'Read More Button', 'divi-blog-extras' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} .post-content .el-read-more-btn .et_pb_button",
						'alignment' => "{$this->main_css_element} .post-content .el-read-more-btn, {$this->main_css_element} .post-content p.el-read-more-link",
					),
					'margin_padding'  => array(
						'css' => array(
							'margin'    => "{$this->main_css_element} .post-content .el-read-more-btn",
							'padding'   => "{$this->main_css_element} .post-content .el-read-more-btn .et_pb_button",
							'important' => 'all',
						),
					),
					'no_rel_attr'     => true,
					'use_alignment'   => true,
					'box_shadow'      => false,
					'depends_on'      => array( 'use_read_more_button' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'read_more_settings',
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main'		=> $this->main_css_element,
				),
			),
			'blog_margin_padding' => array(
                'filterable_categories' => array(
                    'margin_padding' => array(
                        'css' => array(
                            'margin'    => "%%order_class%% .el-dbe-post-categories li",
                            'padding'   => "%%order_class%% .el-dbe-post-categories li a",
                        ),
                    ),
                ),
            ),
            'slider_margin_padding' => array(
				'arrows' => array(
					'margin_padding' => array(
						'css' => array(
							'use_margin' => false,
							'padding'    => "%%order_class%% .swiper-button-next::after, %%order_class%% .swiper-button-prev::after",
							'important'  => 'all',
						),
					),
				),
			),
			'filters' => false,
			'borders' => array(
				'padination_number' => array(
					'label_prefix' => 'Pagination Number',
					'defaults' => array(
				        'border_radii' => 'on|2px|2px|2px|2px',
				        'border_styles' => array(
				            'width' => '1px',
				            'color' => '#ddd',
							'style' => 'solid',
				        ),
				    ),
					'css'          => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .el-blog-pagination ul .page-numbers",
							'border_styles' => "%%order_class%% .el-blog-pagination ul .page-numbers",
							'important' 	=> 'all',
						),
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'depends_on'      => array( 'pagination_type' ),
					'depends_show_if' => 'off',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
				),
				'default' => array(
					'css' => array(
						'main'        => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
						'plugin_main' => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
						'important'   => 'all',
					),
				),
			),
			'box_shadow' => array(
				'default' => array(
					'css' => array(
						'main' => "{$this->main_css_element}",
						'hover' => "%%order_class%% .et_pb_post.et_pb_post_extra:hover",
						'important' => 'all'
					),
				),
			),
			'max_width'             => array(),
			'text'                  => array(
				'use_text_orientation' => false,
			),
		);
	}

	public function get_custom_css_fields_config() {
		return array(
			'title'            => array(
				'label'    => esc_html__( 'Title', 'divi-blog-extras' ),
				'selector' => '.entry-title',
			),
			'post_meta'        => array(
				'label'    => esc_html__( 'Post Meta', 'divi-blog-extras' ),
				'selector' => '.post-meta',
			),
			'read_more'        => array(
				'label'    => esc_html__( 'Read More Link', 'divi-blog-extras' ),
				'selector' => '.post-content .more-link',
			),
			'read_more_button' => array(
				'label'    => esc_html__( 'Read More Button', 'divi-blog-extras' ),
				'selector' => '.post-content .et_pb_button',
			),
		);
	}

	public function get_fields() {

		$accent_color   	= et_get_option( 'accent_color', '#2ea3f2' );
		$merge_array    	= array(
			'current_loop_notice' => array(
				'label'           => '',
				'type'            => 'warning',
				'option_category' => 'configuration',
				'value'           => true,
				'display_if'      => true,
				'message'         => esc_html__( 'Please select a value for Posts for Current Page if you want to make this setting more useful. Leave it blank if you want to use the module like it was working before v2.6.5.', 'divi-blog-extras' ),
				'show_if'     	  => array(
					'function.isTBLayout' => 'on',
					'use_current_loop' => '-1',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'loop_query',
			),
			'use_current_loop' => array(
				'label'            => esc_html__( 'Posts For Current Page', 'divi-blog-extras' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'-1'  => esc_html__( 'Select', 'divi-blog-extras' ),
					'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					'off' => esc_html__( 'No', 'divi-blog-extras' ),
				),
				'default'          => '-1',
				'show_if'          => array(
					'function.isTBLayout' => 'on',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'loop_query',
				'description'      => esc_html__( 'Display posts for the current page. Useful on archive and index pages.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			),
			'include_current_taxonomy' => array(
				'label'            => esc_html__( 'Include Current Taxonomy Posts(Related Posts)', 'divi-blog-extras' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					'off' => esc_html__( 'No', 'divi-blog-extras' ),
				),
				'default'          => 'off',
				'show_if'           => array(
	            	'use_current_loop' => array( '-1', 'off' ),
	            ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'loop_query',
				'description'      => esc_html__( 'Here you can choose whether the posts display from current category/taxonomy.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			),
			'current_taxonomies_relation' => array(
				'label'            => esc_html__( 'Current Taxonomies Relation', 'divi-blog-extras' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'OR'   	=> esc_html__( 'OR', 'divi-blog-extras' ),
					'AND'	=> esc_html__( 'AND', 'divi-blog-extras' ),
				),
				'default'          => 'OR',
				'show_if'		   => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'on',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'loop_query',
				'description'      => esc_html__( 'This will set the relationship between current taxonomies.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			)
		);

		$plugin_options 	= get_option( ELICUS_BLOG_OPTION );
		$filterable_show_if = '';
		$animation_fields 	= array( 
			'ajax_load_more' => esc_html__( 'Ajax Load More Pagnation', 'divi-blog-extras' ),
			'numbered_pagination' => esc_html__( 'Numbered Pagination', 'divi-blog-extras' ),
			'filterable_categories' => esc_html__( 'Filterable Categories', 'divi-blog-extras' ),
		);
		if ( isset( $plugin_options['enable-blog-custom-posts'] ) && 'on' === $plugin_options['enable-blog-custom-posts'] ) {
			if ( isset( $plugin_options['blog-custom-posts'] ) && ! empty( $plugin_options['blog-custom-posts'] ) ) {
				$post_types   = array();
				$custom_posts = array_merge( array( 'post' ), explode( ',', $plugin_options['blog-custom-posts'] ) );
				$custom_posts = array_map( 'sanitize_text_field', $custom_posts );
				foreach ( $custom_posts as $custom_post ) {
					$post_obj                   = get_post_type_object( $custom_post );
					if ( is_object( $post_obj ) ) {
					    if ( is_object( $post_obj->labels ) ) {
					        if ( isset( $post_obj->labels->singular_name ) ) {
					            $post_types[ $custom_post ] = sprintf( esc_html__( '%s', 'divi-blog-extras' ), $post_obj->labels->singular_name );
					        } else {
					            $post_types[ $custom_post ] = sprintf( esc_html__( '%s', 'divi-blog-extras' ), $custom_post );
					        }
					    } else {
				            $post_types[ $custom_post ] = sprintf( esc_html__( '%s', 'divi-blog-extras' ), $custom_post );
				        }
					} else {
			            $post_types[ $custom_post ] = sprintf( esc_html__( '%s', 'divi-blog-extras' ), $custom_post );
			        }
				}

				$merge_array['post_type'] = array(
					'label'            => esc_html__( 'Post Type', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => $post_types,
					'default'          => 'post',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'Here you can choose the Post Type.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['use_category_filterable_blog'] = array(
					'label'            => esc_html__( 'Use Category Filterable Blog', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'post_type' => 'post',
						'include_current_taxonomy' => 'off',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['category_filter_orderby'] = array(
					'label'            => esc_html__( 'Category Filter Orderby', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'title'    		=> esc_html__( 'Name', 'divi-blog-extras' ),
						'slug'     		=> esc_html__( 'Slug', 'divi-blog-extras' ),
						'ID'       		=> esc_html__( 'ID', 'divi-blog-extras' ),
						'term_order'	=> esc_html__( 'Term Order', 'divi-blog-extras' ),
						'date'     		=> esc_html__( 'Date', 'divi-blog-extras' ),
						'count'			=> esc_html__( 'Count', 'divi-blog-extras' ),
						'parent'		=> esc_html__( 'Parent', 'divi-blog-extras' ),
					),
					'default'          => 'date',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'post_type' => 'post',
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can choose the order type of categories.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['category_filter_order'] = array(
					'label'            => esc_html__( 'Category Filter Order', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'ASC'    		=> esc_html__( 'ASC', 'divi-blog-extras' ),
						'DESC'     		=> esc_html__( 'DESC', 'divi-blog-extras' ),
					),
					'default'          => 'ASC',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'post_type' => 'post',
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can choose the order of categories.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['active_category'] = array(
	                'label'             => esc_html__( 'Select Active Category', 'divi-blog-extras' ),
	                'type'              => 'select',
	                'option_category'   => 'configuration',
	                'options'			=> El_Blog_Module::get_categories_options(),
					'default'			=> 'all',
	                'tab_slug'          => 'general',
	                'toggle_slug'       => 'elements',
					'show_if'           => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
						'post_type' => 'post',
		                'use_category_filterable_blog' => 'on',
		            ),
	                'description'       => esc_html__( 'Here you can choose which category should be active on page load.', 'divi-blog-extras' ),
					'computed_affects'  => array(
		                '__dbe_posts',
		            )
	            );
				$merge_array['use_hamburger_category_filter'] = array(
					'label'            => esc_html__( 'Hamburger Category Filter on Tablet & Mobile', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'post_type' => 'post',
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['show_all_posts_link'] = array(
		            'label'             => esc_html__( 'Show All Posts Link', 'divi-blog-extras' ),
		            'type'              => 'yes_no_button',
		            'option_category'   => 'configuration',
		            'options'           => array(
		                'off' => esc_html__( 'Off', 'divi-blog-extras' ),
		                'on'  => esc_html__( 'On', 'divi-blog-extras' ),
		            ),
		            'default'           => 'off',
		            'show_if'           => array(
		            	'use_current_loop' => array( '-1', 'off' ),
		                'use_category_filterable_blog' => 'on',
		                'include_current_taxonomy' => 'off',
		                'post_type' => 'post',
		            ),
		            'show_if_not' => array(
						'blog_layout' => 'slider',
					),
		            'tab_slug'          => 'general',
		            'toggle_slug'       => 'elements',
		            'description'       => esc_html__( 'Here you can define whether to show all posts link or not.', 'divi-blog-extras' ),
		            'computed_affects'  => array(
		                '__dbe_posts'
		            )
		        );
		        $merge_array['all_posts_text'] = array(
		            'label'             => esc_html__( 'All Posts Text', 'divi-blog-extras' ),
		            'type'              => 'text',
		            'option_category'   => 'configuration',
		            'default'           => esc_html__( 'All', 'divi-blog-extras' ),
		            'show_if'           => array(
		            	'use_current_loop' => array( '-1', 'off' ),
		                'use_category_filterable_blog' => 'on',
		                'include_current_taxonomy' => 'off',
		                'show_all_posts_link' => 'on',
		                'post_type' => 'post',
		            ),
		            'show_if_not' => array(
						'blog_layout' => 'slider',
					),
		            'tab_slug'          => 'general',
		            'toggle_slug'       => 'elements',
		            'description'       => esc_html__( 'Here you can define the All Posts text you would like to display.', 'divi-blog-extras' ),
		            'computed_affects'  => array(
		                '__dbe_posts'
		            )
		        );
			} else {
				$merge_array['use_category_filterable_blog'] = array(
					'label'            => esc_html__( 'Use Category Filterable Blog', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['category_filter_orderby'] = array(
					'label'            => esc_html__( 'Category Filter Orderby', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'title'    		=> esc_html__( 'Name', 'divi-blog-extras' ),
						'slug'     		=> esc_html__( 'Slug', 'divi-blog-extras' ),
						'ID'       		=> esc_html__( 'ID', 'divi-blog-extras' ),
						'term_order'	=> esc_html__( 'Term Order', 'divi-blog-extras' ),
						'date'     		=> esc_html__( 'Date', 'divi-blog-extras' ),
						'count'			=> esc_html__( 'Count', 'divi-blog-extras' ),
						'parent'		=> esc_html__( 'Parent', 'divi-blog-extras' ),
					),
					'default'          => 'date',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can choose the order type of categories.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['category_filter_order'] = array(
					'label'            => esc_html__( 'Category Filter Order', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'ASC'    		=> esc_html__( 'ASC', 'divi-blog-extras' ),
						'DESC'     		=> esc_html__( 'DESC', 'divi-blog-extras' ),
					),
					'default'          => 'ASC',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can choose the order of categories.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['active_category'] = array(
	                'label'             => esc_html__( 'Select Active Category', 'divi-blog-extras' ),
	                'type'              => 'select',
	                'option_category'   => 'configuration',
	                'options'			=> El_Blog_Module::get_categories_options(),
					'default'			=> 'all',
	                'tab_slug'          => 'general',
	                'toggle_slug'       => 'elements',
					'show_if'           => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
		                'use_category_filterable_blog' => 'on',
		            ),
	                'description'       => esc_html__( 'Here you can choose which category should be active on page load.', 'divi-blog-extras' ),
					'computed_affects'  => array(
		                '__dbe_posts',
		            )
	            );
				$merge_array['use_hamburger_category_filter'] = array(
					'label'            => esc_html__( 'Hamburger Category Filter on Tablet & Mobile', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'show_if'          => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
						'use_category_filterable_blog' => 'on',
					),
					'show_if_not' => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
				$merge_array['show_all_posts_link'] = array(
		            'label'             => esc_html__( 'Show All Posts Link', 'divi-blog-extras' ),
		            'type'              => 'yes_no_button',
		            'option_category'   => 'configuration',
		            'options'           => array(
		                'off' => esc_html__( 'Off', 'divi-blog-extras' ),
		                'on'  => esc_html__( 'On', 'divi-blog-extras' ),
		            ),
		            'default'           => 'off',
		            'show_if'           => array(
		            	'use_current_loop' => array( '-1', 'off' ),
		                'use_category_filterable_blog' => 'on',
		                'include_current_taxonomy' => 'off',
		            ),
		            'show_if_not' => array(
						'blog_layout' => 'slider',
					),
		            'tab_slug'          => 'general',
		            'toggle_slug'       => 'elements',
		            'description'       => esc_html__( 'Here you can define whether to show all posts link or not.', 'divi-blog-extras' ),
		            'computed_affects'  => array(
		                '__dbe_posts'
		            )
		        );
		        $merge_array['all_posts_text'] = array(
		            'label'             => esc_html__( 'All Posts Text', 'divi-blog-extras' ),
		            'type'              => 'text',
		            'option_category'   => 'configuration',
		            'default'           => esc_html__( 'All', 'divi-blog-extras' ),
		            'show_if'           => array(
		            	'use_current_loop' => array( '-1', 'off' ),
		                'use_category_filterable_blog' => 'on',
		                'include_current_taxonomy' => 'off',
		                'show_all_posts_link' => 'on',
		            ),
		            'show_if_not' => array(
						'blog_layout' => 'slider',
					),
		            'tab_slug'          => 'general',
		            'toggle_slug'       => 'elements',
		            'description'       => esc_html__( 'Here you can define the All Posts text you would like to display.', 'divi-blog-extras' ),
		            'computed_affects'  => array(
		                '__dbe_posts'
		            )
		        );
			}
		} else {
			$merge_array['use_category_filterable_blog'] = array(
				'label'            => esc_html__( 'Use Category Filterable Blog', 'divi-blog-extras' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					'off' => esc_html__( 'No', 'divi-blog-extras' ),
				),
				'default'          => 'off',
				'show_if'          => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'off',
				),
				'show_if_not' => array(
					'blog_layout' => 'slider',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			);
			$merge_array['category_filter_orderby'] = array(
				'label'            => esc_html__( 'Category Filter Orderby', 'divi-blog-extras' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'title'    		=> esc_html__( 'Name', 'divi-blog-extras' ),
					'slug'     		=> esc_html__( 'Slug', 'divi-blog-extras' ),
					'ID'       		=> esc_html__( 'ID', 'divi-blog-extras' ),
					'term_order'	=> esc_html__( 'Term Order', 'divi-blog-extras' ),
					'date'     		=> esc_html__( 'Date', 'divi-blog-extras' ),
					'count'			=> esc_html__( 'Count', 'divi-blog-extras' ),
					'parent'		=> esc_html__( 'Parent', 'divi-blog-extras' ),
				),
				'default'          => 'date',
				'show_if'          => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'off',
					'use_category_filterable_blog' => 'on',
				),
				'show_if_not' => array(
					'blog_layout' => 'slider',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose the order type of categories.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			);
			$merge_array['category_filter_order'] = array(
				'label'            => esc_html__( 'Category Filter Order', 'divi-blog-extras' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'ASC'    		=> esc_html__( 'ASC', 'divi-blog-extras' ),
					'DESC'     		=> esc_html__( 'DESC', 'divi-blog-extras' ),
				),
				'default'          => 'ASC',
				'show_if'          => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'off',
					'use_category_filterable_blog' => 'on',
				),
				'show_if_not' => array(
					'blog_layout' => 'slider',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose the order of categories.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			);
			$merge_array['active_category'] = array(
                'label'             => esc_html__( 'Select Active Category', 'divi-blog-extras' ),
                'type'              => 'select',
                'option_category'   => 'configuration',
                'options'			=> El_Blog_Module::get_categories_options(),
				'default'			=> 'all',
                'tab_slug'          => 'general',
                'toggle_slug'       => 'elements',
				'show_if'           => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'off',
	                'use_category_filterable_blog' => 'on',
	            ),
                'description'       => esc_html__( 'Here you can choose which category should be active on page load.', 'divi-blog-extras' ),
				'computed_affects'  => array(
	                '__dbe_posts',
	            )
            );
			$merge_array['use_hamburger_category_filter'] = array(
				'label'            => esc_html__( 'Hamburger Category Filter on Tablet & Mobile', 'divi-blog-extras' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					'off' => esc_html__( 'No', 'divi-blog-extras' ),
				),
				'default'          => 'on',
				'show_if'          => array(
					'use_current_loop' => array( '-1', 'off' ),
					'include_current_taxonomy' => 'off',
					'use_category_filterable_blog' => 'on',
				),
				'show_if_not' => array(
					'blog_layout' => 'slider',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This will turn filterable blog on and off.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			);
			$merge_array['show_all_posts_link'] = array(
	            'label'             => esc_html__( 'Show All Posts Link', 'divi-blog-extras' ),
	            'type'              => 'yes_no_button',
	            'option_category'   => 'configuration',
	            'options'           => array(
	                'off' => esc_html__( 'Off', 'divi-blog-extras' ),
	                'on'  => esc_html__( 'On', 'divi-blog-extras' ),
	            ),
	            'default'           => 'off',
	            'show_if'           => array(
	            	'use_current_loop' => array( '-1', 'off' ),
	                'use_category_filterable_blog' => 'on',
	                'include_current_taxonomy' => 'off',
	            ),
	            'show_if_not' => array(
					'blog_layout' => 'slider',
				),
	            'tab_slug'          => 'general',
	            'toggle_slug'       => 'elements',
	            'description'       => esc_html__( 'Here you can define whether to show all posts link or not.', 'divi-blog-extras' ),
	            'computed_affects'  => array(
	                '__dbe_posts'
	            )
	        );
	        $merge_array['all_posts_text'] = array(
	            'label'             => esc_html__( 'All Posts Text', 'divi-blog-extras' ),
	            'type'              => 'text',
	            'option_category'   => 'configuration',
	            'default'           => esc_html__( 'All', 'divi-blog-extras' ),
	            'show_if'           => array(
	            	'use_current_loop' => array( '-1', 'off' ),
	                'use_category_filterable_blog' => 'on',
	                'include_current_taxonomy' => 'off',
	                'show_all_posts_link' => 'on',
	            ),
	            'show_if_not' => array(
					'blog_layout' => 'slider',
				),
	            'tab_slug'          => 'general',
	            'toggle_slug'       => 'elements',
	            'description'       => esc_html__( 'Here you can define the All Posts text you would like to display.', 'divi-blog-extras' ),
	            'computed_affects'  => array(
	                '__dbe_posts'
	            )
	        );
		}
		$merge_array['posts_number']  = array(
			'label'            => esc_html__( 'Post Count', 'divi-blog-extras' ),
			'type'             => 'text',
			'option_category'  => 'configuration',
			'default'          => 10,
			'tab_slug'         => 'general',
			'toggle_slug'      => 'loop_query',
			'description'      => esc_html__( 'Choose how many posts you would like to display per page.', 'divi-blog-extras' ),
			'computed_affects' => array(
				'__dbe_posts',
			),
		);
		$merge_array['offset_number'] = array(
			'label'            => esc_html__( 'Post Offset Number', 'divi-blog-extras' ),
			'type'             => 'text',
			'option_category'  => 'configuration',
			'default'          => 0,
			'tab_slug'         => 'general',
			'toggle_slug'      => 'loop_query',
			'description'      => esc_html__( 'Choose how many posts you would like to skip. These posts will not be shown in the feed.', 'divi-blog-extras' ),
			'computed_affects' => array(
				'__dbe_posts',
			),
		);
		$merge_array['post_order']    = array(
			'label'            => esc_html__( 'Order', 'divi-blog-extras' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => array(
				'DESC' => esc_html__( 'DESC', 'divi-blog-extras' ),
				'ASC'  => esc_html__( 'ASC', 'divi-blog-extras' ),
			),
			'default'          => 'DESC',
			'tab_slug'         => 'general',
			'toggle_slug'      => 'loop_query',
			'description'      => esc_html__( 'Here you can choose the order of your posts.', 'divi-blog-extras' ),
			'computed_affects' => array(
				'__dbe_posts',
			),
		);
		$merge_array['post_order_by'] = array(
			'label'            => esc_html__( 'Order by', 'divi-blog-extras' ),
			'type'             => 'select',
			'option_category'  => 'configuration',
			'options'          => array(
				'date'     		=> esc_html__( 'Date', 'divi-blog-extras' ),
				'modified'		=> esc_html__( 'Modified Date', 'divi-blog-extras' ),
				'title'    		=> esc_html__( 'Title', 'divi-blog-extras' ),
				'name'     		=> esc_html__( 'Slug', 'divi-blog-extras' ),
				'ID'       		=> esc_html__( 'ID', 'divi-blog-extras' ),
				'rand'     		=> esc_html__( 'Random', 'divi-blog-extras' ),
				'relevance'		=> esc_html__( 'Relevance', 'divi-blog-extras' ),
				'comment_count' => esc_html__( 'Comment Count', 'divi-blog-extras' ),
				'none'     		=> esc_html__( 'None', 'divi-blog-extras' ),
			),
			'default'          => 'date',
			'tab_slug'         => 'general',
			'toggle_slug'      => 'loop_query',
			'description'      => esc_html__( 'Here you can choose the order type of your posts. When random order is selected the pagination will not work. The selected order might not be visible exactly the same when masonry layout is being selected as in masonry the images rearrange on the basis of width and height.', 'divi-blog-extras' ),
			'computed_affects' => array(
				'__dbe_posts',
			),
		);
		$merge_array['filterable_category_background_color'] = array(
            'label'             => esc_html__( 'Filterable Category Background', 'divi-blog-extras' ),
            'type'              => 'background-field',
            'base_name'         => 'filterable_category_background',
            'context'           => 'filterable_category_background_color',
            'option_category'   => 'button',
            'custom_color'      => true,
            'background_fields' => $this->generate_background_options( 'filterable_category_background', 'button', 'advanced', 'filterable_category_toggle', 'filterable_category_background_color' ),
            'mobile_options'    => true,
            'hover'             => 'tabs',
            'show_if'               => array(
            	'use_current_loop' => array( '-1', 'off' ),
                'use_category_filterable_blog'  => 'on',
                'include_current_taxonomy'  => 'off',
            ),
            'show_if_not' => array(
				'blog_layout' => 'slider',
			),
            'tab_slug'          => 'advanced',
            'toggle_slug'       => 'filterable_category_toggle',
            'sub_toggle'        => 'normal',
            'description'       => esc_html__( 'Here you can adjust the background style of the filterable category by customizing the background color, gradient, and image.', 'divi-blog-extras' ),
        );
        $merge_array['filterable_active_category_background_color'] = array(
            'label'             => esc_html__( 'Filterable Active Category Background', 'divi-blog-extras' ),
            'type'              => 'background-field',
            'base_name'         => 'filterable_active_category_background',
            'context'           => 'filterable_active_category_background_color',
            'option_category'   => 'button',
            'custom_color'      => true,
            'background_fields' => $this->generate_background_options( 'filterable_active_category_background', 'button', 'advanced', 'filterable_category_toggle', 'filterable_active_category_background_color' ),
            'mobile_options'    => true,
            'hover'             => 'tabs',
            'show_if'               => array(
            	'use_current_loop' => array( '-1', 'off' ),
                'use_category_filterable_blog'  => 'on',
                'include_current_taxonomy'  => 'off',
            ),
            'show_if_not' => array(
				'blog_layout' => 'slider',
			),
            'tab_slug'          => 'advanced',
            'toggle_slug'       => 'filterable_category_toggle',
            'sub_toggle'        => 'active',
            'description'       => esc_html__( 'Here you can adjust the background style of the filterable active category by customizing the background color, gradient, and image.', 'divi-blog-extras' ),
        );

        $merge_array = array_merge( $merge_array, $this->generate_background_options( 'filterable_category_background', 'skip', 'advanced', 'filterable_category_toggle', 'filterable_category_background_color' ) );
        $merge_array = array_merge( $merge_array, $this->generate_background_options( 'filterable_active_category_background', 'skip', 'advanced', 'filterable_category_toggle', 'filterable_active_category_background_color' ) );

		$coumputed_fields = array();

		if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
			$post_types           = isset( $custom_posts ) && ! empty( $custom_posts ) ? $custom_posts : array( 'post' );
			$post_type_taxonomies = get_object_taxonomies( $post_types, 'objects' );
			if ( ! empty( $post_type_taxonomies ) ) {
				$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
				foreach ( $post_type_taxonomies as $taxonomy_key => $post_type_taxonomy ) {
					if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
						$taxonomy_index = 'category' !== $taxonomy_key ? 'include_' . str_replace( '-', '_', $taxonomy_key ) : 'include_categories';
						$field_name     = 'category' !== $taxonomy_key ? 'et_pb_include_' . $taxonomy_key : 'et_pb_include_categories';
						$show_if        = array(
							'use_current_loop' => array( '-1', 'off' ),
							'include_current_taxonomy' => 'off',
						);
						if ( isset( $custom_posts ) && ! empty( $custom_posts ) ) {
							$show_if_posts = array();
							foreach ( $post_type_taxonomy->object_type as $object_type ) {
								array_push( $show_if_posts, esc_html( $object_type ) );
							}
							$show_if['post_type'] = $show_if_posts;
						}
						array_push( $coumputed_fields, $taxonomy_index );
						// translators: %s: taxonomy label.
						$merge_array[ sanitize_text_field( $taxonomy_index ) ] = array(
							'label'            => sprintf( esc_html__( 'Include %1$s', 'divi-blog-extras' ), $post_type_taxonomy->label ),
							'type'             => 'categories',
							'option_category'  => 'basic_option',
							'renderer_options' => array(
								'use_terms'  => true,
								'term_name'  => sanitize_text_field( $taxonomy_key ),
								'field_name' => sanitize_text_field( $field_name ),
							),
							'tab_slug'         => 'general',
							'toggle_slug'      => 'loop_query',
							'description'      => esc_html__( 'Choose which terms posts you would like to include in the feed.', 'divi-blog-extras' ),
							'computed_affects' => array(
								'__dbe_posts',
							),
						);
						if ( '' !== $show_if ) {
							$merge_array[ sanitize_text_field( $taxonomy_index ) ]['show_if'] = $show_if;
						}
					}
				}
				$merge_array['taxonomies_relation'] = array(
					'label'            => esc_html__( 'Taxonomies Relation', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'OR'   	=> esc_html__( 'OR', 'divi-blog-extras' ),
						'AND'	=> esc_html__( 'AND', 'divi-blog-extras' ),
					),
					'default'          => 'OR',
					'show_if'		   => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'This will set the relationship between taxonomies.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				);
			}
		} else {
			$show_if = isset( $custom_posts ) && ! empty( $custom_posts ) ? array( 'post_type' => 'post' ) : array();
			$show_if['include_current_taxonomy'] = 'off';
			$show_if['use_current_loop'] = array( '-1', 'off' );
			$merge_array['include_categories'] = array(
				'label'            => esc_html__( 'Include Categories', 'divi-blog-extras' ),
				'type'             => 'categories',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'loop_query',
				'description'      => esc_html__( 'Choose which categories you would like to include in the feed.', 'divi-blog-extras' ),
				'computed_affects' => array(
					'__dbe_posts',
				),
			);
			if ( ! empty( $show_if ) ) {
				$merge_array['include_categories']['show_if'] = $show_if;
			}
			array_push( $coumputed_fields, 'include_categories' );
		}

		return array_merge(
			$merge_array,
			array(
				'blog_layout'                     => array(
					'label'            => esc_html__( 'Blog Layout', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'grid_extended'         => esc_html( 'Grid Extended' ),
						'box_extended'          => esc_html( 'Box Extended' ),
						'full_width'            => esc_html( 'Full Width' ),
						'block_extended'        => esc_html( 'Block Extended' ),
						'full_width_background' => esc_html( 'Full Width Background' ),
						'vertical_grid'			=> esc_html( 'Vertical Grid '),
						'classic'               => esc_html( 'Classic' ),
						'list'					=> esc_html( 'List' ),
						'masonry'				=> esc_html( 'Masonry' ),
						'slider'				=> esc_html( 'Slider' ),
					),
					'default'          => 'grid_extended',
					'affects'          => array(
						'post_date',
						'post_date_text_align',
						'post_date_font',
						'post_date_font_size',
						'post_date_letter_spacing',
						'post_date_line_height',
						'post_date_text_color',
						'post_date_text_shadow',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the design that you want for the blog.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'slider_layout' => array(
					'label'            => esc_html__( 'Slider Variant', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'background_cover'	=> esc_html( 'Background Cover' ),
						'vertical_grid'		=> esc_html( 'Vertical Grid' ),
						'grid_extended'		=> esc_html( 'Grid Extended' ),
						'block_extended'    => esc_html( 'Block Extended' ),
					),
					'default'          => 'background_cover',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the design that you want for the masonry layout.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'slider_block_extended_image_position'  => array(
					'label'            => esc_html__( 'Featured Image Position', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'top'        => esc_html__( 'Top', 'divi-blog-extras' ),
						'background' => esc_html__( 'Background', 'divi-blog-extras' ),
						'alternate'  => esc_html__( 'Alternate', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout'	=> 'slider',
						'slider_layout'	=> 'block_extended',
					),
					'show_if_not'      => array(
						'show_thumbnail' => 'off',
					),
					'default'          => 'top',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the position of the thumbnails.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'slider_block_extended_overlay'          => array(
					'label'        => esc_html__( 'Background Overlay', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout' 							=> 'slider',
						'slider_layout'    						=> 'block_extended',
						'slider_block_extended_image_position' 	=> array( 'background', 'alternate' ),
					),
					'show_if_not'  => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'     => 'general',
					'toggle_slug'  => 'main_content',
					'description'  => esc_html__( 'This will set the background overlay color.', 'divi-blog-extras' ),
				),
				'masonry_layout'                     => array(
					'label'            => esc_html__( 'Masonry Variant', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'vertical_grid'     => esc_html( 'Vertical Grid' ),
						'grid_extended'     => esc_html( 'Grid Extended' ),
						'block_extended'    => esc_html( 'Block Extended' ),
						'background_cover'	=> esc_html( 'Background Cover' ),
					),
					'default'          => 'vertical_grid',
					'show_if'          => array(
						'blog_layout' => 'masonry',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the design that you want for the masonry layout.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'masonry_columns' => array(
					'label'            => esc_html__( 'Number of Columns', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'1' => esc_html__( '1', 'divi-blog-extras' ),
						'2' => esc_html__( '2', 'divi-blog-extras' ),
						'3' => esc_html__( '3', 'divi-blog-extras' ),
						'4' => esc_html__( '4', 'divi-blog-extras' ),
						'5' => esc_html__( '5', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' => array( 'masonry', 'vertical_grid' ),
					),
					'default'          => '3',
					'default_on_front' => '3',
					'mobile_options'   => true,
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can select the number of columns for the Masonry Grid layout and Vertical Grid layout.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'column_spacing' => array(
	                'label'             => esc_html__( 'Column Spacing', 'divi-blog-extras' ),
					'type'              => 'range',
					'option_category'  	=> 'layout',
					'range_settings'    => array(
						'min'   => '0',
						'max'   => '100',
						'step'  => '1',
					),
					'fixed_unit'		=> 'px',
					'fixed_range'       => true,
					'validate_unit'		=> true,
					'mobile_options'    => true,
					'default'           => '20px',
					'default_on_front'  => '20px',
					'show_if'          	=> array(
						'blog_layout' => array( 'masonry', 'vertical_grid' ),
					),
					'tab_slug'        	=> 'general',
					'toggle_slug'     	=> 'main_content',
					'description'       => esc_html__( 'Increase or decrease spacing between columns.', 'divi-blog-extras' ),
	            ),
				'include_posts' => array(
					'label'            => esc_html__( 'Include Posts by ID', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'default'          => '',
					'show_if'		   => array(
						'use_current_loop' => array( '-1', 'off' ),
						'include_current_taxonomy' => 'off',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'If you would like to display specific posts then enter their post ids here comma separated.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'exclude_posts' => array(
					'label'            => esc_html__( 'Exclude Posts by ID', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'default'          => '',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'If you would like to exclude specific posts from the loop then enter their post ids here comma separated.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'meta_date' => array(
					'label'            => esc_html__( 'Meta Date Format', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'default'          => 'M j, Y',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'ignore_sticky_posts' => array(
					'label'            => esc_html__( 'Ignore Sticky Posts', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'This will decide whether to ignore sticky posts or not.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_thumbnail' => array(
					'label'            => esc_html__( 'Show Featured Image', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'This will turn thumbnails on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'featured_image_size'             => array(
					'label'            => esc_html__( 'Featured Image Size', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'thumbnail' => esc_html__( 'Thumbnail', 'divi-blog-extras' ),
						'medium' => esc_html__( 'Medium', 'divi-blog-extras' ),
						'large'  => esc_html__( 'Large', 'divi-blog-extras' ),
						'full'   => esc_html__( 'Full', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'show_thumbnail' => 'on',
					),
					'default'          => 'large',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can select the size of the featured image.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'image_position'                  => array(
					'label'            => esc_html__( 'Featured Image Position', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'top'        => esc_html__( 'Top', 'divi-blog-extras' ),
						'background' => esc_html__( 'Background', 'divi-blog-extras' ),
						'alternate'  => esc_html__( 'Alternate', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' 		=> 'block_extended',
					),
					'show_if_not'      => array(
						'show_thumbnail' => 'off',
					),
					'default'          => 'top',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the position of the thumbnails.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'block_extended_overlay'          => array(
					'label'        => esc_html__( 'Background Overlay', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout'    	=> 'block_extended',
						'image_position' 	=> array( 'background', 'alternate' ),
					),
					'show_if_not'  => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'     => 'general',
					'toggle_slug'  => 'main_content',
					'description'  => esc_html__( 'This will set the background overlay color.', 'divi-blog-extras' ),
				),
				'masonry_block_extended_image_position'  => array(
					'label'            => esc_html__( 'Featured Image Position', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'top'        => esc_html__( 'Top', 'divi-blog-extras' ),
						'background' => esc_html__( 'Background', 'divi-blog-extras' ),
						'alternate'  => esc_html__( 'Alternate', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' 		=> 'masonry',
						'masonry_layout'    => 'block_extended',
					),
					'show_if_not'      => array(
						'show_thumbnail' => 'off',
					),
					'default'          => 'top',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Here you can choose the position of the thumbnails.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'masonry_block_extended_overlay'          => array(
					'label'        => esc_html__( 'Background Overlay', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout' 							=> 'masonry',
						'masonry_layout'    					=> 'block_extended',
						'masonry_block_extended_image_position' => array( 'background', 'alternate' ),
					),
					'show_if_not'  => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'     => 'general',
					'toggle_slug'  => 'main_content',
					'description'  => esc_html__( 'This will set the background overlay color.', 'divi-blog-extras' ),
				),
				'show_social_icons'               => array(
					'label'            => esc_html__( 'Show Social Icons', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' => 'classic',
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
					'description'      => esc_html__( 'Turn social sharing icons on or off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'use_overlay'                     => array(
					'label'            => esc_html__( 'Featured Image Overlay', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'layout',
					'options'          => array(
						'off' => esc_html__( 'Off', 'divi-blog-extras' ),
						'on'  => esc_html__( 'On', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'show_thumbnail' => 'on',
					),
					'default'          => 'off',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'overlay',
					'description'      => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'overlay_icon_color'              => array(
					'label'        => esc_html__( 'Overlay Icon Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'use_overlay' => 'on',
					),
					'show_if_not'  => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'overlay',
					'description'  => esc_html__( 'Here you can define a custom color for the Overlay Icon', 'divi-blog-extras' ),
				),
				'hover_overlay_color'             => array(
					'label'        => esc_html__( 'Hover Overlay Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'use_overlay' => 'on',
					),
					'show_if_not'  => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'overlay',
					'description'  => esc_html__( 'Here you can define a custom color for the Overlay', 'divi-blog-extras' ),
				),
				'hover_icon'                      => array(
					'label'            => esc_html__( 'Hover Icon Picker', 'divi-blog-extras' ),
					'type'             => 'select_icon',
					'option_category'  => 'configuration',
					'class'            => array( 'et-pb-font-icon' ),
					'show_if'          => array(
						'use_overlay' => 'on',
					),
					'show_if_not'      => array(
						'show_thumbnail' => 'off',
					),
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'overlay',
					'description'      => esc_html__( 'Here you can define a custom icon for the Overlay', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_content'                    => array(
					'label'            => esc_html__( 'Content', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'Show Excerpt', 'divi-blog-extras' ),
						'on'  => esc_html__( 'Show Content', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'use_manual_excerpt' => array(
					'label'            => esc_html__( 'Use Post Excerpts', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'default'          => 'on',
					'show_if'          => array(
						'show_content' => 'off',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'Disable this option if you want to ignore manually defined excerpts and always generate it automatically.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'excerpt_length'                  => array(
					'label'            => esc_html__( 'Excerpt Length', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'show_content' => 'off',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'loop_query',
					'description'      => esc_html__( 'Here you can define excerpt length in characters, if 0 no excerpt will be shown. However this won\'t work with the manual excerpt defined in the post.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_more'                       => array(
					'label'            => esc_html__( 'Show Read More Link', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'Off', 'divi-blog-extras' ),
						'on'  => esc_html__( 'On', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'show_content' => 'off',
					),
					'affects'          => array(
						'use_read_more_button',
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can define whether to show "read more" link after the excerpts or not.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'use_read_more_button'            => array(
					'label'            => esc_html__( 'Read More Button', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'Off', 'divi-blog-extras' ),
						'on'  => esc_html__( 'On', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'show_content' => 'off',
						'show_more'    => 'on',
					),
					'affects'          => array(
						'custom_read_more',
					),
					'default'          => 'off',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'read_more_settings',
					'description'      => esc_html__( 'Here you can define whether to show "read more" button after the excerpts or not.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'read_more_text'                  => array(
					'label'            => esc_html__( 'Read More Text', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'show_more' => 'on',
					),
					'show_if_not'      => array(
						'show_content' => 'on',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can define "read more" button/link text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_author'                     => array(
					'label'            => esc_html__( 'Show Author', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Turn on or off the Author link.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_date'                       => array(
					'label'            => esc_html__( 'Show Date', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Turn the Date on or off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_categories'                 => array(
					'label'            => esc_html__( 'Show Categories/Terms', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Turn the category/terms links on or off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'category_meta_colors'            => array(
					'label'            => esc_html__( 'Pick Colors From Categories/Terms', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'show_categories' => 'on',
					),
					'default'          => 'off',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'category_toggle',
					'description'      => esc_html__( 'Here you can choose whether or not to pick the color for category/term background from category settings in the WordPress dashboard that comes with the plugin. Also, it will be prioritize over below category/term colors\' settings.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'category_color'                  => array(
					'label'        => esc_html__( 'Category/Term Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'show_categories' => 'on',
					),
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'category_toggle',
					'description'  => esc_html__( 'Here you can define a custom color for the category/term text.', 'divi-blog-extras' ),
				),
				'category_background_color'       => array(
					'label'        => esc_html__( 'Category/Term Background', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'show_categories' => 'on',
					),
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'category_toggle',
					'description'  => esc_html__( 'Here you can define a custom color for the category/term background.', 'divi-blog-extras' ),
				),
				'category_hover_color'            => array(
					'label'        => esc_html__( 'Category/Term Hover Color', 'divi-blog-extras' ),
					'type'         => 'skip',
					'custom_color' => true,
					'show_if'      => array(
						'show_categories' => 'on',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'category_toggle',
					'description'  => esc_html__( 'Here you can define a custom color for the category/term text on hover.', 'divi-blog-extras' ),
				),
				'category_hover_background_color' => array(
					'label'        => esc_html__( 'Category/Term Hover Background', 'divi-blog-extras' ),
					'type'         => 'skip',
					'custom_color' => true,
					'show_if'      => array(
						'show_categories' => 'on',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'category_toggle',
					'description'  => esc_html__( 'Here you can define a custom color for the category/term background on hover.', 'divi-blog-extras' ),
				),
				'show_comments'                   => array(
					'label'            => esc_html__( 'Show Comment Count', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Turn Comment Count on and off.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_read_time'                  => array(
					'label'            => esc_html__( 'Show Read Time', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => sprintf(
						'%1$s <a href="%2$s" target="_blank">%3$s</a>.',
						esc_html__( 'Here you can define whether to show Read Time or not. And option to change words per minute is in the plugin', 'divi-blog-extras' ),
						esc_url( admin_url( '/options-general.php?page=divi-blog-extras-options#el-blog-post-words-per-minute' ) ),
						esc_html__( 'Settings', 'divi-blog-extras' )
					),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'read_time_text'                  => array(
					'label'            => esc_html__( 'Read Time Text', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'default'		   => esc_html__( 'min read', 'divi-blog-extras' ),
					'show_if'          => array(
						'show_read_time' => 'on',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can define custom read time text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'no_results_text'                  => array(
					'label'            => esc_html__( 'No Results Text', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
					'description'      => esc_html__( 'Here you can define custom no result text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_load_more'                  => array(
					'label'            => esc_html__( 'Show Pagination', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					),
					'affects'          => array(
						'custom_ajax_pagination',
					),
					'show_if_not'          => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Show Pagination or not.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'pagination_type'                 => array(
					'label'            => esc_html__( 'Pagination Type', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'on'  => esc_html__( 'Ajax Load More', 'divi-blog-extras' ),
						'off' => esc_html__( 'Numbered', 'divi-blog-extras' ),
					),
					'affects'          => array(
						'custom_ajax_pagination',
						'pagination_number',
						'pagination_number_font',
						'pagination_number_font_size',
						'pagination_number_letter_spacing',
						'pagination_number_line_height',
					),
					'show_if_not'          => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'show_if'          => array(
						'show_load_more' => 'on',
					),
					'default'          => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Here you can choose the Pagination Type.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'load_more_text'                  => array(
					'label'            => esc_html__( 'Load More Button Text', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'pagination_type' => 'on',
					),
					'show_if_not'      => array(
						'show_load_more' => 'off',
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Here you can define Load More Button text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'show_less_text'                  => array(
					'label'            => esc_html__( 'Show Less Button Text', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'pagination_type' => 'on',
					),
					'show_if_not'      => array(
						'show_load_more' => 'off',
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Here you can define Show Less Button text.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'loader_color'             => array(
					'label'        => esc_html__( 'Loader Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'show_load_more' => 'on',
						'pagination_type' => 'on',
					),
					'show_if_not'  => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
					'description'  => esc_html__( 'Here you can define a custom color for the ajax pagination loader.', 'divi-blog-extras' ),
				),
				'number_background_color'             => array(
					'label'        => esc_html__( 'Number Background Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'default'	   => 'transparent',
					'hover'		   => 'tabs',
					'show_if'      => array(
						'show_load_more' => 'on',
						'pagination_type' => 'off',
					),
					'show_if_not'  => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
					'description'  => esc_html__( 'Here you can define a custom background color for the pagination number.', 'divi-blog-extras' ),
				),
				'number_color'             => array(
					'label'        => esc_html__( 'Number Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'hover'		   => 'tabs',
					'show_if'      => array(
						'show_load_more' => 'on',
						'pagination_type' => 'off',
					),
					'show_if_not'  => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
					'description'  => esc_html__( 'Here you can define a custom color for the pagination number.', 'divi-blog-extras' ),
				),
				'active_number_background_color'             => array(
					'label'        => esc_html__( 'Active Number Background Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'default'	   => $accent_color,
					'hover'		   => 'tabs',
					'show_if'      => array(
						'show_load_more' => 'on',
						'pagination_type' => 'off',
					),
					'show_if_not'  => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
					'description'  => esc_html__( 'Here you can define a custom background color for the active pagination number.', 'divi-blog-extras' ),
				),
				'active_number_color'             => array(
					'label'        => esc_html__( 'Active Number Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'default'	   => '#fff',
					'hover'		   => 'tabs',
					'show_if'      => array(
						'show_load_more' => 'on',
						'pagination_type' => 'off',
					),
					'show_if_not'  => array(
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'pagination',
					'description'  => esc_html__( 'Here you can define a custom color for the active pagination number.', 'divi-blog-extras' ),
				),
				'use_wp_pagenavi'                 => array(
					'label'            => esc_html__( 'Use WP-PageNavi', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'pagination_type' => 'off',
					),
					'show_if_not'      => array(
						'show_load_more' => 'off',
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Use Pagination of WP-PageNavi plugin if installed.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'next_text'                       => array(
					'label'            => esc_html__( 'Next Link', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'pagination_type' => 'off',
						'use_wp_pagenavi' => 'off',
					),
					'show_if_not'      => array(
						'show_load_more' => 'off',
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Here you can define Next Link text in numbered pagination.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'prev_text'                       => array(
					'label'            => esc_html__( 'Prev Link', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'show_if'          => array(
						'pagination_type' => 'off',
						'use_wp_pagenavi' => 'off',
					),
					'show_if_not'      => array(
						'show_load_more' => 'off',
						'blog_layout' => 'slider',
						'post_order_by' => 'rand',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
					'description'      => esc_html__( 'Here you can define Previous Link text in numbered pagination.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'content_color'                   => array(
					'label'        => esc_html__( 'Text Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'text',
					'description'  => esc_html__( 'Here you can change the Text Color.', 'divi-blog-extras' ),
				),
				'show_thumbnail_mobile'           => array(
					'label'           => esc_html__( 'Show Featured Image', 'divi-blog-extras' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'show_if_not'     => array(
						'blog_layout' => 'full_width_background',
					),
					'default'         => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'mobile_settings',
					'description'     => esc_html__( 'This will turn thumbnails on and off on mobile devices.', 'divi-blog-extras' ),
				),
				'filterable_categories_custom_margin' => array(
	                'label'             => esc_html__( 'Filterable Categories Margin', 'divi-blog-extras' ),
	                'type'              => 'custom_padding',
	                'option_category'   => 'layout',
	                'mobile_options'    => false,
	                'hover'             => false,
	                'default'           => '|15px|15px||false|false',
	                'default_on_front'  => '|15px|15px||false|false',
	                'show_if'           => array(
	                    'use_category_filterable_blog' => 'on',
	                    'include_current_taxonomy' => 'off',
	                ),
	                'show_if_not' => array(
						'blog_layout' => 'slider',
					),
	                'tab_slug'          => 'advanced',
	                'toggle_slug'       => 'margin_padding',
	                'description'       => esc_html__( 'Margin adds extra space to the outside of the element, increasing the distance between the element and other items on the page.', 'divi-blog-extras' ),
	            ),
	            'filterable_categories_custom_padding' => array(
	                'label'             => esc_html__( 'Filterable Categories Padding', 'divi-blog-extras' ),
	                'type'              => 'custom_padding',
	                'option_category'   => 'layout',
	                'mobile_options'    => true,
	                'default'           => '10px|10px|10px|10px|true|true',
	                'default_on_front'  => '10px|10px|10px|10px|true|true',
	                'show_if'           => array(
	                    'use_category_filterable_blog' => 'on',
	                    'include_current_taxonomy' => 'off',
	                ),
	                'show_if_not' => array(
						'blog_layout' => 'slider',
					),
	                'tab_slug'          => 'advanced',
	                'toggle_slug'       => 'margin_padding',
	                'description'       => esc_html__( 'Padding adds extra space to the inside of the element, increasing the distance between the edge of the element and its inner contents.', 'divi-blog-extras' ),
	            ),
	            'link_target' => array(
					'label'            => esc_html__( 'Single Post Link Target', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => esc_html__( 'In the same window', 'divi-blog-extras' ),
						'on'  => esc_html__( 'In the new tab', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'link_options',
					'description'      => esc_html__( 'Here you can choose whether or not post link opens in a new window.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'disabled_on'                     => array(
					'label'           => esc_html__( 'Disable on', 'divi-blog-extras' ),
					'type'            => 'multiple_checkboxes',
					'options'         => array(
						'phone'   => esc_html__( 'Phone', 'divi-blog-extras' ),
						'tablet'  => esc_html__( 'Tablet', 'divi-blog-extras' ),
						'desktop' => esc_html__( 'Desktop', 'divi-blog-extras' ),
					),
					'additional_att'  => 'disable_on',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'visibility',
					'description'     => esc_html__( 'This will disable the module on selected devices', 'divi-blog-extras' ),
				),
				'admin_label'                     => array(
					'label'       => esc_html__( 'Admin Label', 'divi-blog-extras' ),
					'type'        => 'text',
					'toggle_slug' => 'admin_label',
					'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'divi-blog-extras' ),
				),
				'module_id'                       => array(
					'label'           => esc_html__( 'CSS ID', 'divi-blog-extras' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'module_class'                    => array(
					'label'           => esc_html__( 'CSS Class', 'divi-blog-extras' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'scroll_top_animation' => array(
					'label'            		=> esc_html__( 'Enable Scroll to Top Animation for', 'divi-blog-extras' ),
					'type'             		=> 'multiple_checkboxes',
					'option_category'  		=> 'basic_option',
					'options'				=> $animation_fields,
					'default'				=> 'on|on|on',
					'default_on_front'		=> 'on|on|on',
					'tab_slug'         		=> 'advanced',
					'toggle_slug'      		=> 'animation',
					'description'      		=> esc_html__( 'Here you can choose where you would like to disable the scroll to top animation.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'animation'                       => array(
					'label'            => esc_html__( 'Single Post Animation', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'configuration',
					'options'          => array(
						'top'    => esc_html__( 'Top To Bottom', 'divi-blog-extras' ),
						'left'   => esc_html__( 'Left To Right', 'divi-blog-extras' ),
						'right'  => esc_html__( 'Right To Left', 'divi-blog-extras' ),
						'bottom' => esc_html__( 'Bottom To Top', 'divi-blog-extras' ),
						'off'    => esc_html__( 'No Animation', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'animation',
					'description'      => esc_html__( 'Here you can choose the direction of the lazy-loading animation.', 'divi-blog-extras' ),
					'computed_affects' => array(
						'__dbe_posts',
					),
				),
				'slide_effect' => array(
					'label'           => esc_html__( 'Slide Effect', 'divi-blog-extras' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'slide'     => esc_html__( 'Slide', 'divi-blog-extras' ),
						'cube'      => esc_html__( 'Cube', 'divi-blog-extras' ),
						'coverflow' => esc_html__( 'Coverflow', 'divi-blog-extras' ),
						'flip'      => esc_html__( 'Flip', 'divi-blog-extras' ),
					),
					'default'         => 'slide',
					'show_if'         => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Here you can choose the slide animation effect.', 'divi-blog-extras' ),
				),
				'slides_per_view' => array(
					'label'           => esc_html__( 'Number of Slides Per View', 'divi-blog-extras' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'1' 	=> esc_html__( '1', 'divi-blog-extras' ),
						'2' 	=> esc_html__( '2', 'divi-blog-extras' ),
						'3' 	=> esc_html__( '3', 'divi-blog-extras' ),
						'4' 	=> esc_html__( '4', 'divi-blog-extras' ),
						'5' 	=> esc_html__( '5', 'divi-blog-extras' ),
						'6' 	=> esc_html__( '6', 'divi-blog-extras' ),
						'7' 	=> esc_html__( '7', 'divi-blog-extras' ),
						'8' 	=> esc_html__( '8', 'divi-blog-extras' ),
						'9'		=> esc_html__( '9', 'divi-blog-extras' ),
						'10' 	=> esc_html__( '10', 'divi-blog-extras' ),
					),
					'default'         => '2',
					'mobile_options'  => true,
					'show_if'         => array(
						'blog_layout' => 'slider',
						'slide_effect' => array( 'slide', 'coverflow' ),
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Here you can choose the number of posts to display per view.', 'divi-blog-extras' ),
				),
				'slides_per_group' => array(
					'label'           => esc_html__( 'Number of Slides Per Group', 'divi-blog-extras' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'1'  => esc_html__( '1', 'divi-blog-extras' ),
						'2'  => esc_html__( '2', 'divi-blog-extras' ),
						'3'  => esc_html__( '3', 'divi-blog-extras' ),
						'4' => esc_html__( '4', 'divi-blog-extras' ),
						'5' => esc_html__( '5', 'divi-blog-extras' ),
						'6' => esc_html__( '6', 'divi-blog-extras' ),
						'7' => esc_html__( '7', 'divi-blog-extras' ),
						'8' => esc_html__( '8', 'divi-blog-extras' ),
						'9' => esc_html__( '9', 'divi-blog-extras' ),
						'10' => esc_html__( '10', 'divi-blog-extras' ),
					),
					'default'         => '1',
					'mobile_options'  => true,
					'show_if'         => array(
						'blog_layout' => 'slider',
						'slide_effect' => array( 'slide', 'coverflow' ),
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Here you can choose the number of slides per group to slide by.', 'divi-blog-extras' ),
				),
				'space_between_slides' => array(
					'label'           => esc_html__( 'Space between Slides', 'divi-blog-extras' ),
					'type'            => 'range',
					'option_category' => 'layout',
					'range_settings'  => array(
						'min'  => '10',
						'max'  => '100',
						'step' => '1',
					),
					'show_if'         => array(
						'blog_layout' => 'slider',
						'slide_effect' => array( 'slide', 'coverflow' ),
					),
					'fixed_unit'	  => 'px',
					'default'         => '20px',
					'mobile_options'  => true,
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Move the slider or input the value to increse or decrease the space between slides.', 'divi-blog-extras' ),
				),
				'enable_coverflow_shadow' => array(
					'label'            => esc_html__( 'Enable Slide Shadow', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'show_if'          => array(
						'blog_layout' => 'slider',
						'slide_effect' => 'coverflow',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Enable Slide Shadow For Coverflow Effect.', 'divi-blog-extras' ),
				),
				'coverflow_shadow_color' => array(
					'label'        	   => esc_html__( 'Shadow Color', 'divi-blog-extras' ),
					'type'         	   => 'color-alpha',
					'custom_color' 	   => true,
					'show_if'          => array(
						'blog_layout' => 'slider',
						'slide_effect' => 'coverflow',
						'enable_coverflow_shadow' => 'on',
					),
					'default'      	   => '#ccc',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Here you can select color for the Shadow.', 'divi-blog-extras' ),
				),
				'coverflow_rotate' => array(
					'label'            => esc_html__( 'Coverflow Rotate', 'divi-blog-extras' ),
					'type'             => 'range',
					'option_category'  => 'font_option',
					'range_settings'   => array(
						'min'  => '1',
						'max'  => '360',
						'step' => '1',
					),
					'unitless'         => true,
					'show_if'          => array(
						'blog_layout' => 'slider',
						'slide_effect' => 'coverflow',
					),
					'default'          => '40',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Coverflow Rotate Slide.', 'divi-blog-extras' ),
				),
				'coverflow_depth' => array(
					'label'            => esc_html__( 'Coverflow Depth', 'divi-blog-extras' ),
					'type'             => 'range',
					'option_category'  => 'font_option',
					'range_settings'   => array(
						'min'  => '1',
						'max'  => '1000',
						'step' => '1',
					),
					'unitless'         => true,
					'show_if'          => array(
						'blog_layout' => 'slider',
						'slide_effect' => 'coverflow',
					),
					'default'          => '100',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Coverflow Depth Slide.', 'divi-blog-extras' ),
				),
				'equalize_slides_height' => array(
					'label'           => esc_html__( 'Equalize Slides Height', 'divi-blog-extras' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'Off', 'divi-blog-extras' ),
						'on'  => esc_html__( 'On', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'default'         => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Here you can choose whether or not equalize slides height.', 'divi-blog-extras' ),
				),
				'slider_loop' => array(
					'label'            => esc_html__( 'Enable Loop', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Here you can enable loop for the slides.', 'divi-blog-extras' ),
				),
				'autoplay' => array(
					'label'            => esc_html__( 'Autoplay', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'This controls the auto play the slider.', 'divi-blog-extras' ),
				),
				'autoplay_speed' => array(
					'label'            => esc_html__( 'Autoplay Delay', 'divi-blog-extras' ),
					'type'             => 'text',
					'option_category'  => 'configuration',
					'default'          => '3000',
					'show_if'          => array(
						'blog_layout' => 'slider',
						'autoplay' => 'on',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'This controls the time of the slide before the transition.', 'divi-blog-extras' ),
				),
				'pause_on_hover' => array(
					'label'            => esc_html__( 'Pause On Hover', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'on',
					'show_if'          => array(
						'blog_layout' => 'slider',
						'autoplay' => 'on',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Control for pausing slides on mouse hover.', 'divi-blog-extras' ),
				),
				'slide_transition_duration' => array(
					'label'           => esc_html__( 'Transition Duration', 'divi-blog-extras' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'default'         => '1000',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'slider_settings',
					'description'     => esc_html__( 'Here you can specify the duration of transition for each slide in miliseconds.', 'divi-blog-extras' ),
				),
				'show_arrow' => array(
					'label'            => esc_html__( 'Show Arrows', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'default_on_front' => 'off',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Choose whether or not the previous & next arrows should be visible.', 'divi-blog-extras' ),
				),
				'show_arrow_on_hover' => array(
					'label'            => esc_html__( 'Show Arrows On Hover', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'default'          => 'off',
					'default_on_front' => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'Choose whether or not the previous and next arrows should be visible.', 'divi-blog-extras' ),
				),
				'show_control_dot' => array(
					'label'            => esc_html__( 'Show Dots Pagination', 'divi-blog-extras' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => esc_html__( 'Yes', 'divi-blog-extras' ),
						'off' => esc_html__( 'No', 'divi-blog-extras' ),
					),
					'default'          => 'off',
					'default_on_front' => 'off',
					'show_if'          => array(
						'blog_layout' => 'slider',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'This setting will turn on and off the pagination of the slider.', 'divi-blog-extras' ),
				),
				'control_dot_style' => array(
					'label'            => esc_html__( 'Dots Pagination Style', 'divi-blog-extras' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'solid_dot'       => esc_html__( 'Solid Dot', 'divi-blog-extras' ),
						'transparent_dot' => esc_html__( 'Transparent Dot', 'divi-blog-extras' ),
						'stretched_dot'   => esc_html__( 'Stretched Dot', 'divi-blog-extras' ),
						'line'            => esc_html__( 'Line', 'divi-blog-extras' ),
						'rounded_line'    => esc_html__( 'Rounded Line', 'divi-blog-extras' ),
						'square_dot'      => esc_html__( 'Squared Dot', 'divi-blog-extras' ),
					),
					'show_if'          => array(
						'blog_layout' => 'slider',
						'show_control_dot' => 'on',
					),
					'default'          => 'solid_dot',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'slider_settings',
					'description'      => esc_html__( 'control dot style', 'divi-blog-extras' ),
				),
				'arrows_custom_padding' => array(
	                'label'                 => esc_html__( 'Arrows Padding', 'divi-blog-extras' ),
	                'type'                  => 'custom_padding',
	                'option_category'       => 'layout',
	                'show_if'         		=> array(
	                	'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'default'				=> '5px|10px|5px|10px|true|true',
                	'default_on_front'		=> '5px|10px|5px|10px|true|true',
	                'mobile_options'        => true,
	                'hover'                 => false,
	                'tab_slug'              => 'advanced',
	                'toggle_slug'           => 'slider_styles',
	                'description'           => esc_html__( 'Padding adds extra space to the inside of the element, increasing the distance between the edge of the element and its inner contents.', 'divi-blog-extras' ),
	            ),
				'arrow_font_size' => array(
					'label'           => esc_html__( 'Arrow Font Size', 'divi-blog-extras' ),
					'type'            => 'range',
					'option_category' => 'layout',
					'range_settings'  => array(
						'min'  => '10',
						'max'  => '100',
						'step' => '1',
					),
					'show_if'         => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'default'         => '24px',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'slider_styles',
					'description'     => esc_html__( 'Here you can choose the arrow font size.', 'divi-blog-extras' ),
				),
				'arrow_color' => array(
					'label'        => esc_html__( 'Arrow Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'slider_styles',
					'description'  => esc_html__( 'Here you can define color for the arrow', 'divi-blog-extras' ),
				),
				'arrow_background_color' => array(
					'label'        => esc_html__( 'Arrow Background', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'slider_styles',
					'description'  => esc_html__( 'Here you can choose a custom color to be used for the shape background of arrows.', 'divi-blog-extras' ),
				),
				'arrow_background_border_size' => array(
					'label'           => esc_html__( 'Arrow Background Border', 'divi-blog-extras' ),
					'type'            => 'range',
					'option_category' => 'layout',
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '10',
						'step' => '1',
					),
					'show_if' 		  => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'default'         => '0px',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'slider_styles',
					'description'     => esc_html__( 'Move the slider or input the value to increase or decrease the border size of the arrow background.', 'divi-blog-extras' ),
				),
				'arrow_background_border_color' => array(
					'label'        => esc_html__( 'Arrow Background Border Color', 'divi-blog-extras' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'show_if'      => array(
						'blog_layout' => 'slider',
						'show_arrow' => 'on',
					),
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'slider_styles',
					'description'  => esc_html__( 'Here you can choose a custom color to be used for the arrow border', 'divi-blog-extras' ),
				),
				'control_dot_active_color' => array(
					'label'        	   => esc_html__( 'Active Dot Pagination Color', 'divi-blog-extras' ),
					'type'         	   => 'color-alpha',
					'custom_color'     => true,
					'show_if'          => array(
						'blog_layout' => 'slider',
						'show_control_dot' => 'on',
					),
					'default'      	   => '#000000',
					'tab_slug'     	   => 'advanced',
					'toggle_slug'  	   => 'slider_styles',
					'description'  	   => esc_html__( 'Here you can define color for the active pagination item.', 'divi-blog-extras' ),
				),
				'control_dot_inactive_color' => array(
					'label'        	   => esc_html__( 'Inactive Dot Pagination Color', 'divi-blog-extras' ),
					'type'         	   => 'color-alpha',
					'custom_color'     => true,
					'show_if'      	   => array(
						'blog_layout' => 'slider',
						'show_control_dot' => 'on',
					),
					'default'      	   => '#cccccc',
					'tab_slug'     	   => 'advanced',
					'toggle_slug'  	   => 'slider_styles',
					'description'  	   => esc_html__( 'Here you can define color for the inactive pagination item.', 'divi-blog-extras' ),
				),
				'__dbe_posts'                     => array(
					'type'                => 'computed',
					'computed_callback'   => array( 'El_Blog_Module', 'get_blog_posts' ),
					'computed_depends_on' => array_merge(
						$coumputed_fields,
						array(
							'use_current_loop',
							'post_type',
							'posts_number',
							'offset_number',
							'post_order',
							'post_order_by',
							'include_current_taxonomy',
							'current_taxonomies_relation',
							'taxonomies_relation',
							'blog_layout',
							'slider_layout',
							'masonry_layout',
							'masonry_columns',
							'include_posts',
							'exclude_posts',
							'meta_date',
							'use_category_filterable_blog',
							'category_filter_orderby',
							'category_filter_order',
							'active_category',
							'use_hamburger_category_filter',
							'show_all_posts_link',
                    		'all_posts_text',
							'ignore_sticky_posts',
							'show_thumbnail',
							'featured_image_size',
							'image_position',
							'masonry_block_extended_image_position',
							'slider_block_extended_image_position',
							'show_social_icons',
							'use_overlay',
							'hover_icon',
							'show_content',
							'use_manual_excerpt',
							'excerpt_length',
							'show_more',
							'read_more_text',
							'show_author',
							'show_date',
							'show_categories',
							'category_meta_colors',
							'category_background_color',
							'show_comments',
							'show_read_time',
							'read_time_text',
							'no_results_text',
							'show_load_more',
							'pagination_type',
							'load_more_text',
							'show_less_text',
							'use_wp_pagenavi',
							'next_text',
							'prev_text',
							'animation',
							'ajax_pagination_use_icon',
							'ajax_pagination_icon',
							'custom_ajax_pagination',
							'use_read_more_button',
							'custom_read_more',
							'read_more_icon',
							'read_more_use_icon',
							'header_level',
							'link_target',
							'scroll_top_animation',
						)
					),
				),
			)
		);
	}

	public static function get_categories_options( $args = array(), $all = true, $key = 'term_id' ) {
		$defaults = array(
			'taxonomy' => 'category',
			'hide_empty' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		// Get the terms
		$terms = get_terms( $args );

		$options = array_combine(
			wp_list_pluck( $terms, $key ),
			wp_list_pluck( $terms, 'name' )
		);

		if ( $all === true ) {
			$options = array_replace( array( 'all' => esc_html__( 'All', 'divi-blog-extras' ) ), $options );
		}

		return $options;
	}

	public static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

		if ( self::$rendering ) {
			// We are trying to render a Blog module while a Blog module is already being rendered
			// which means we have most probably hit an infinite recursion. While not necessarily
			// the case, rendering a post which renders a Blog module which renders a post
			// which renders a Blog module is not a sensible use-case.
			return '';
		}

		/*
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		global $wp_filter;
		$wp_filter_cache = $wp_filter;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'use_current_loop'						=> '-1',
			'post_type'                				=> 'post',
			'posts_number'             				=> '10',
			'offset_number'            				=> '0',
			'blog_layout'              				=> 'grid_extended',
			'slider_layout'							=> 'background_cover',
			'masonry_layout'						=> 'vertical_grid',
			'masonry_columns'						=> '3',
			'include_posts'							=> '',
			'exclude_posts'							=> '',
			'meta_date'                				=> 'M j, Y',
			'post_order'               				=> 'DESC',
			'post_order_by'            				=> 'date',
			'include_current_taxonomy'				=> 'off',
			'current_taxonomies_relation'			=> 'OR',
			'taxonomies_relation'					=> 'OR',
			'use_category_filterable_blog'			=> 'off',
			'category_filter_orderby'				=> 'date',
			'category_filter_order'					=> 'ASC',
			'active_category'						=> 'all',
			'use_hamburger_category_filter'			=> 'on',
			'show_all_posts_link'					=> 'off',
            'all_posts_text'						=> 'All',
			'ignore_sticky_posts'					=> 'off',
			'show_thumbnail'           				=> 'on',
			'featured_image_size'      				=> 'large',
			'image_position'           				=> 'top',
			'masonry_block_extended_image_position' => 'top',
			'slider_block_extended_image_position'	=> 'top',
			'show_social_icons'        				=> 'off',
			'use_overlay'              				=> 'off',
			'hover_icon'               				=> '',
			'show_content'             				=> 'off',
			'use_manual_excerpt'					=> 'on',
			'excerpt_length'           				=> '',
			'show_more'                				=> 'on',
			'read_more_text'           				=> 'Read More',
			'show_author'              				=> 'on',
			'show_date'                				=> 'on',
			'show_categories'          				=> 'on',
			'category_meta_colors'     				=> 'off',
			'category_background_color'				=> '',
			'show_comments'            				=> 'on',
			'show_read_time'           				=> 'on',
			'read_time_text'						=> 'min read',
			'no_results_text'						=> '',
			'show_load_more'           				=> 'off',
			'pagination_type'          				=> 'on',
			'load_more_text'           				=> '',
			'show_less_text'           				=> '',
			'use_wp_pagenavi'          				=> 'off',
			'next_text'                				=> '',
			'prev_text'                				=> '',
			'animation'                				=> 'off',
			'ajax_pagination_use_icon' 				=> 'on',
			'ajax_pagination_icon'     				=> '',
			'custom_ajax_pagination'   				=> 'off',
			'use_read_more_button'     				=> 'off',
			'custom_read_more'         				=> 'off',
			'read_more_icon'           				=> '',
			'read_more_use_icon'       				=> 'off',
			'header_level'             				=> 'h2',
			'link_target'							=> 'off',
			'scroll_top_animation'					=> 'on|on|on',
		);

		// WordPress' native conditional tag is only available during page load. It'll fail during component update because
		// et_pb_process_computed_property() is loaded in admin-ajax.php. Thus, use WordPress' conditional tags on page load and
		// rely to passed $conditional_tags for AJAX call.
		$is_front_page     = (bool) et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_single         = (bool) et_fb_conditional_tag( 'is_single', $conditional_tags );
		$is_category       = (bool) et_fb_conditional_tag( 'is_category', $conditional_tags );
		$is_archive        = (bool) et_fb_conditional_tag( 'is_archive', $conditional_tags );
		$is_tax            = (bool) et_fb_conditional_tag( 'is_tax', $conditional_tags );
		$is_tag            = (bool) et_fb_conditional_tag( 'is_tag', $conditional_tags );
		$is_author         = (bool) et_fb_conditional_tag( 'is_author', $conditional_tags );
		$is_date           = (bool) et_fb_conditional_tag( 'is_date', $conditional_tags );
		$is_search         = (bool) et_fb_conditional_tag( 'is_search', $conditional_tags );
		$is_user_logged_in = (bool) et_fb_conditional_tag( 'is_user_logged_in', $conditional_tags );
		$current_post_id   = isset( $current_page['id'] ) ? (int) $current_page['id'] : 0;

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module.
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		$plugin_options = get_option( ELICUS_BLOG_OPTION );
		if ( isset( $plugin_options['blog-custom-posts'] ) ) {
			$custom_posts   = array_map( 'sanitize_text_field', explode( ',', $plugin_options['blog-custom-posts'] ) );
			$post_types     = array_merge( array( 'post' ), $custom_posts );
		} else {
			$post_types     = array( 'post' );
		}
		
		if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
			$post_type_taxonomies = get_object_taxonomies( $post_types, 'names' );
			if ( ! empty( $post_type_taxonomies ) ) {
				$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
				foreach ( $post_type_taxonomies as $taxonomy_key ) {
					if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
						$taxonomy_index              = 'category' !== $taxonomy_key ? 'include_' . esc_html( str_replace( '-', '_', $taxonomy_key ) ) : 'include_categories';
						$defaults[ $taxonomy_index ] = '';
					}
				}
			}
		} else {
			$defaults['include_categories'] = '';
		}

		$args = wp_parse_args( $args, $defaults );

		foreach ( $defaults as $key => $default ) {
			${$key} = esc_html( et_()->array_get( $args, $key, $default ) );
		}

		$overlay_class          = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';
		$processed_header_level = et_pb_process_header_level( $header_level, 'h2' );
		$processed_header_level = esc_html( $processed_header_level );
		$category_background    = $category_background_color;

		if ( 'masonry' === $blog_layout ) {
			$masonry 		= true;
			$blog_layout 	= $masonry_layout;
			$masonry_class	= 'el-masonry';
		} else {
			$masonry 		= false;
			$masonry_class	= '';
		}

		if ( 'slider' === $blog_layout ) {
			$is_slider 		= true;
			$blog_layout 	= $slider_layout;
		} else {
			$is_slider 		= false;
		}

		if ( 'on' === $use_overlay ) {
			$data_icon = '' !== $hover_icon ?
				sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				) :
				'';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				'' !== $hover_icon ? esc_attr( ' et_pb_inline_icon' ) : '',
				$data_icon
			);
		}

		if ( 'on' !== $show_content ) {
			if ( 'classic' === $blog_layout ) {
				$excerpt_length = ( '' === $excerpt_length ) ? 600 : intval( $excerpt_length );
			} else {
				$excerpt_length = ( '' === $excerpt_length ) ? 270 : intval( $excerpt_length );
			}
		}

		if ( 'on' === $show_more ) {
			$read_more_text = '' === $read_more_text ?
			esc_html__( 'Read More', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $read_more_text )
			);
		}

		if ( 'on' === $show_load_more && 'on' === $pagination_type && ! $is_slider ) {
			$load_more_text = '' === $load_more_text ?
			esc_html__( 'Load More', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $load_more_text )
			);

			$show_less_text = '' === $show_less_text ?
			esc_html__( 'Show Few', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $show_less_text )
			);
		}

		if ( 'on' === $show_load_more && 'off' === $pagination_type && ! $is_slider ) {
			$prev_text = '' === $prev_text ?
			esc_html__( '« Previous', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $prev_text )
			);

			$next_text = '' === $next_text ?
			esc_html__( 'Next »', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $next_text )
			);
		}

		if ( 'on' === $show_read_time ) {
			$read_time_text = '' === $read_time_text ?
			esc_html__( 'min read', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $read_time_text )
			);
		}

		if ( 'on' === $show_all_posts_link ) {
            $all_posts_text = '' === $all_posts_text ?
            esc_html__( 'All', 'divi-blog-extras' ) :
            sprintf(
                esc_html__( '%s', 'divi-blog-extras' ),
                esc_html( $all_posts_text )
            );
        } else {
        	$all_posts_text = '';
        }

		$query_args = array(
			'post_type'      => sanitize_text_field( $post_type ),
			'posts_per_page' => intval( $posts_number ),
			'post_status'    => 'publish',
			'offset'         => 0,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( $is_user_logged_in ) {
			$query_args['post_status'] = array( 'publish', 'private' );
		}

		if ( 'on' === $ignore_sticky_posts ) {
			$query_args['ignore_sticky_posts'] = true;
		}

		if ( ( $is_category || $is_tax ) && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$post_type               = sanitize_text_field( get_post_type( intval( $current_post_id ) ) );
			$query_args['post_type'] = $post_type;
			$object                  = get_queried_object();
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => sanitize_text_field( $object->taxonomy ),
					'field'    => 'term_id',
					'terms'    => intval( $object->term_id ),
					'operator' => 'IN',
				),
			);
		} else if ( ! $is_search ) {
			if ( 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
				$tax_query      = array();
				$plugin_options = get_option( ELICUS_BLOG_OPTION );
				if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
					$post_type_taxonomies = get_object_taxonomies( $post_type, 'names' );
					if ( ! empty( $post_type_taxonomies ) ) {
						$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
						foreach ( $post_type_taxonomies as $taxonomy_key ) {
							if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
								$taxonomy_index = 'category' !== $taxonomy_key ? 'include_' . str_replace( '-', '_', $taxonomy_key ) : 'include_categories';
								// Whitelisted and escaped above in $defaults.
								if ( isset( ${$taxonomy_index} ) && ! empty( ${$taxonomy_index} ) ) {
									array_push(
										$tax_query,
										array(
											'taxonomy' => sanitize_text_field( $taxonomy_key ),
											'field'    => 'term_id',
											'terms'    => array_map( 'intval', explode( ',', ${$taxonomy_index} ) ),
											'operator' => 'IN',
										)
									);
								}
							}
						}
					}
				} else {
					if ( isset( $include_categories ) && ! empty( $include_categories ) ) {
						array_push(
							$tax_query,
							array(
								'taxonomy' => 'category',
								'field'    => 'term_id',
								'terms'    => array_map( 'intval', explode( ',', $include_categories ) ),
								'operator' => 'IN',
							)
						);
					}
				}

				if ( ! empty( $tax_query ) ) {
					if ( count( $tax_query ) > 1 ) {
						$tax_query['relation'] = sanitize_text_field( $taxonomies_relation );
					}
					$query_args['tax_query'] = $tax_query;
				}
			}
		}

		if ( $is_author && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$query_args['author'] = intval( get_queried_object_id() );
		}

		if ( $is_tag && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$query_args['tag_id'] = intval( get_queried_object_id() );
		}

		if ( $is_date && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$post_year  = sanitize_text_field( get_query_var( 'year' ) );
			$post_month = sanitize_text_field( get_query_var( 'monthnum' ) );
			$post_day   = sanitize_text_field( get_query_var( 'day' ) );
			if ( $post_year ) {
				$query_args['year'] = et_core_esc_previously( $post_year );
			}
			if ( $post_month ) {
				$query_args['monthnum'] = et_core_esc_previously( $month );
			}
			if ( $post_day ) {
				$query_args['day'] = et_core_esc_previously( $post_day );
			}
		}

		if ( '' !== $offset_number ) {
			$query_args['offset'] = intval( $offset_number );
		}

		if ( '' !== $query_args['offset'] && -1 === $query_args['posts_per_page'] ) {
			$count_posts                  = wp_count_posts( $post_type, 'readable' );
			$published_posts              = $count_posts->publish;
			$query_args['posts_per_page'] = intval( $published_posts );
		}

		if ( 'on' === $show_load_more && ! $is_slider ) {
			$page           		= $is_front_page ? intval( get_query_var( 'page' ) ) : intval( get_query_var( 'paged' ) );
			$page           		= 1 < intval( get_query_var( 'el_dbe_page' ) ) ? intval( get_query_var( 'el_dbe_page' ) ) : $page;
			$el_dbe_page    		= $page > 0 ? $page : 1;
			$query_args['paged']  	= $el_dbe_page;
			$query_args['offset']	= ( ( intval( $el_dbe_page ) - 1 ) * intval( $posts_number ) ) + $query_args['offset'];
		}

		if ( '' !== $post_order_by ) {
			$query_args['orderby'] = sanitize_text_field( $post_order_by );
		}

		if ( '' !== $post_order ) {
			$query_args['order'] = sanitize_text_field( $post_order );
		}

		if ( '' !== $include_posts && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
			$include_posts = array_map( 'trim', explode( ',', $include_posts ) );
			$query_args['post__in'] = array_map( 'intval', $include_posts );
		}

		if ( '' !== $exclude_posts ) {
			$exclude_posts = array_map( 'trim', explode( ',', $exclude_posts ) );
			$query_args['post__not_in'] = array_map( 'intval', $exclude_posts );
		}

		if ( $is_single ) {
			if ( 'on' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
				$post_type 				= sanitize_text_field( get_post_type( intval( $current_post_id ) ) );
				$post_taxonomies 		= get_object_taxonomies( $post_type, 'names' );
				$filtered_taxonomies 	= json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
				if ( $post_taxonomies && is_array( $post_taxonomies ) ) {
					$tax_query = array();
					if ( 'AND' === $current_taxonomies_relation ) {
						$operator = 'AND';
					} else {
						$operator = 'IN';
					}
					foreach( $post_taxonomies as $taxonomy_key ) {
						if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
							$term_ids = wp_get_post_terms( intval( $current_post_id ), $taxonomy_key, array( 'fields' => 'ids' ) );
							array_push(
								$tax_query,
								array(
									'taxonomy' => sanitize_text_field( $taxonomy_key ),
									'field'    => 'term_id',
									'terms'    => array_map( 'intval', $term_ids ),
									'operator' => sanitize_text_field( $operator ),
								)
							);
						}
					}
					if ( ! empty( $tax_query ) ) {
						if ( count( $tax_query ) > 1 ) {
							$tax_query['relation'] = sanitize_text_field( $current_taxonomies_relation );
						}
						$query_args['tax_query'] = $tax_query;	
					}
				}
			}

			if ( ! isset( $query_args['post__not_in'] ) ) {
				$query_args['post__not_in'] = array( intval( $current_post_id ) );
			} else {
				$query_args['post__not_in'] = array_merge( $query_args['post__not_in'], array( intval( $current_post_id ) ) );
			}
		}

		if ( $is_search && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$query_args['post_type'] 	= 'any';
			// phpcs:ignore WordPress,GET,POST,REQUEST,NO NONCE.
			if ( isset( $_GET['s'] ) ) {
				// phpcs:ignore WordPress,GET,POST,REQUEST.
				$query_args['s'] 		= sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}
		}

		if ( ! $is_slider && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) && 'on' === $use_category_filterable_blog && 'post' === $query_args['post_type'] && 'all' !== $active_category ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => intval( $active_category ),
					'operator' => 'IN',
				)
			);
		}

		if ( isset( $taxonomies_relation ) && 'AND' === $taxonomies_relation && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
			$filter_query_args = $query_args;
			$filter_query_args['posts_per_page'] = '-1';
			$filter_query = new WP_Query( $filter_query_args );
		}

		$query = new WP_Query( $query_args );

		self::$rendering = true;

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			$total = intval( ceil( ( $query->found_posts - $offset_number ) / $query_args['posts_per_page'] ) );
		} else {
			$total = intval( ceil( ( $query->found_posts ) / $query_args['posts_per_page'] ) );
		}

		if ( $query->have_posts() ) {

			$counter = 1;
			$output  = '';

			if ( ! $is_search && ! $is_archive && ! $is_slider && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) && 'on' === $use_category_filterable_blog && 'post' === $query_args['post_type'] ) {
				$post_categories = array_filter( array_map( 'absint', explode( ',', $include_categories ) ) );
				if ( isset( $taxonomies_relation ) && 'AND' === $taxonomies_relation ) {
					$post_ids 			= wp_list_pluck( $filter_query->posts, 'ID' );
					$post_categories 	= array();
					foreach ( $post_ids as $post_id ) {
						$post_terms = get_the_terms( $post_id, 'category' );
						if ( $post_terms && ! is_wp_error( $post_terms ) ) {
							$categories 		= wp_list_pluck( $post_terms, 'term_id' );
							$post_categories 	= array_merge( $post_categories, $categories );
						}
					}
					$post_categories = array_unique( $post_categories );
				}
				if ( empty( $post_categories ) || '0' == count( $post_categories ) || '1' < count( $post_categories ) ) {
					$post_terms   = get_terms( array(
					    'taxonomy'		=> 'category',
					    'hide_empty' 	=> true,
					    'orderby'		=> sanitize_text_field( $category_filter_orderby ),
					    'order'			=> sanitize_text_field( $category_filter_order ),
					    'include' 		=> array_map( 'intval', $post_categories ),
					) );
					if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) && 1 < count( $post_terms ) ){
						$output .= '<div class="el-dbe-filterable-categories" data-hamburger-filter="'. esc_attr( $use_hamburger_category_filter ) .'">';
							if ( 'on' === $use_hamburger_category_filter ) {
								if ( 0 !== intval( $active_category ) ) {
									$mobile_active_category = get_cat_name( intval( $active_category ) );
								} else {
									$mobile_active_category = $all_posts_text;
								}
								$output .= '<div class="el-dbe-filterable-mobile-categories">';
									$output .= '<span class="el-dbe-mobile-active-category">' . esc_html( $mobile_active_category ) . '</span>';
									$output .= '<span class="el-dbe-category-mobile-menu"></span>';
								$output .= '</div>';
							}
							$output .= '<ul class="el-dbe-post-categories">';
							if ( 'on' === $show_all_posts_link ) {
								$active_category_class = 'all' === $active_category ? 'el-dbe-active-category' : '';
								$output .= '<li><a href="#" class="'. esc_attr( $active_category_class ) .'" data-category="-1">' . esc_html( $all_posts_text ) . '</a></li>';
							}
							foreach ( $post_terms as $post_term ) {
								$active_category_class = $post_term->term_id === intval( $active_category ) ? 'el-dbe-active-category' : '';
						    	$output .= '<li><a href="#" class="'. esc_attr( $active_category_class ) .'" data-category="' . esc_attr( $post_term->term_id ) . '">' . esc_html( $post_term->name ) . '</a></li>';
							}
							$output .= '</ul>';
						$output .= '</div>';
					}
				}
			}

			$blog_classes = array_map( 'sanitize_html_class', array( $blog_layout, $masonry_class ) );
			$blog_classes = implode( ' ', $blog_classes );

			if ( ! $is_slider ) {
				$output .= '<div class="el-dbe-blog-extra ' . esc_attr( $blog_classes ) . '">';
			}

			if ( $masonry ) {
				$output .= '<div class="el-isotope-container">';
				$output .= '<div class="el-isotope-item-gutter"></div>';
			}

			if ( $is_slider ) {
				$output_array = array();
			}

			while ( $query->have_posts() ) {
				$query->the_post();

				global $et_fb_processing_shortcode_object;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag.
				$et_fb_processing_shortcode_object = false;

				$post_id         = intval( get_the_ID() );
				$thumb           = '';
				$image_class     = '';
				$date_class      = '';
				$thumb           = el_get_post_thumbnail( $post_id, esc_html( $featured_image_size ), 'et_pb_post_main_image no-lazyload skip-lazy' );
				$no_thumb_class  = ( '' === $thumb || 'off' === $show_thumbnail ) ? ' et_pb_no_thumb' : '';
				$layout_class    = ' el_dbe_' . $blog_layout;
				$animation_class = ' et-waypoint et_pb_animation_' . $animation;

				if ( $masonry && 'block_extended' === $blog_layout ) {
					$image_position = $masonry_block_extended_image_position;
				}

				if ( $is_slider && 'block_extended' === $blog_layout ) {
					$image_position = $slider_block_extended_image_position;
				}

				if ( '' !== $thumb && 'on' === $show_thumbnail ) {
					if ( 'block_extended' === $blog_layout ) {
						if ( 'alternate' !== $image_position ) {
							$image_class = ' image-' . $image_position;
						} else {
							if ( 0 !== $counter % 2 ) {
								$image_class = ' image-background';
							} else {
								$image_class = ' image-top';
							}
						}
					}
				}

				if ( 'full_width' === $blog_layout ) {
					if ( 'off' === $show_date ) {
						$date_class = ' no_date';
					}
				}

				if ( 'on' !== $show_content ) {
					if ( 'on' === $show_more ) {
						if ( 'on' === $use_read_more_button ) {
							$read_more_button   = el_blog_render_button(
								array(
									'button_text'         => et_core_esc_previously( $read_more_text ),
									'button_text_escaped' => true,
									'button_url'          => esc_url( get_permalink( $post_id ) ),
									'button_custom'       => et_core_esc_previously( $custom_read_more ),
									'custom_icon'         => et_core_esc_previously( $read_more_icon ),
									'has_wrapper'         => false,
									'url_new_window'	  => esc_attr( $link_target ),
								)
							);
						}
					}
				}

				$classes = array_map( 'sanitize_html_class', get_post_class( 'et_pb_post et_pb_post_extra et_pb_text_align_left ' . $date_class . $animation_class . $layout_class . $no_thumb_class . $overlay_class . $image_class ) );

				$post_class = implode( ' ', $classes );

				if ( $masonry ) {
					$output .= '<div class="el-isotope-item">';
				}

				if ( $is_slider ) {
					$output = '';
				}

				$output .= '<article id="post-' . $post_id . '" class="' . esc_attr( $post_class ) . '" >';

				// reset the fb processing flag.
				$et_fb_processing_shortcode_object = false;
				// set the flag to indicate that we're processing internal content.
				$et_pb_rendering_column_content = true;
				// reset all the attributes required to properly generate the internal styles.
				ET_Builder_Element::clean_internal_modules_styles();

				$blog_layout = sanitize_file_name( $blog_layout );
				$blog_layout = str_replace( '-', '_', $blog_layout );

				if ( file_exists( get_stylesheet_directory() . '/divi-blog-extras/layouts/' . $blog_layout . '.php' ) ) {
					include get_stylesheet_directory() . '/divi-blog-extras/layouts/' . $blog_layout . '.php';
				} elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'layouts/' . $blog_layout . '.php' ) ) {
					include plugin_dir_path( __FILE__ ) . 'layouts/' . $blog_layout . '.php';
				}

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				if ( 'on' === $show_content ) {
					// retrieve the styles for the modules inside Blog content.
					$internal_style = ET_Builder_Element::get_style( true );
					// reset all the attributes after we retrieved styles.
					ET_Builder_Element::clean_internal_modules_styles( false );
					$et_pb_rendering_column_content = false;
					// append styles to the blog content.
					if ( $internal_style ) {
						$output .= sprintf(
							'<style type="text/css" class="et_fb_blog_extras_inner_content_styles">
	                            %1$s
	                        </style>',
							et_core_esc_previously( $internal_style )
						);
					}
				}

				$output .= '</article> <!-- et_pb_post_extra -->';

				if ( $masonry ) {
					$output .= '</div> <!-- el-isotope-item -->';
				}
				
				$et_fb_processing_shortcode_object = $global_processing_original_value;

				$counter++;

				if ( $is_slider ) {
					array_push( $output_array, $output );
				}

			}

			if ( $masonry ) {
				$output .= '</div> <!-- el-isotope-container -->';
			}

			wp_reset_postdata();

			if ( $is_slider ) {
				return et_core_intentionally_unescaped( $output_array, 'html' );
			}

			if ( 'on' === $show_load_more && ! is_search() && ! $is_slider ) {

				// Pagination.
				if ( 'on' === $pagination_type ) {
					// Load more Pagination.
					if ( $total > 1 ) {
						$load_more_page = $el_dbe_page < $total ? ( $el_dbe_page + 1 ) : 1;
						$button_text    = $el_dbe_page < $total ? $load_more_text : $show_less_text;
						$button_classes = array(
							'el-pagination-button',
							'el-button',
							'et-waypoint',
							'et_pb_animation_bottom',
							'et-animated',
						);

						if ( $el_dbe_page < $total ) {
							array_push( $button_classes, 'el-load-more' );
						} else {
							array_push( $button_classes, 'el-show-less' );
						}

						$pagenum_link       = get_pagenum_link( $load_more_page );
						$load_more_button   = el_blog_render_button(
							array(
								'button_text'         => esc_html( $button_text ),
								'button_text_escaped' => true,
								'button_url'          => esc_url( $pagenum_link ),
								'button_custom'       => et_core_esc_previously( $custom_ajax_pagination ),
								'custom_icon'         => et_core_esc_previously( $ajax_pagination_icon ),
								'has_wrapper'         => false,
								'button_classname'    => $button_classes,
							)
						);
						$output            .= '<div class="ajax-pagination">';
						$output            .= et_core_intentionally_unescaped( $load_more_button, 'html' );
						$output            .= '</div>';
					}
				} else {
					// Numbered Pagination.
					$output .= '<div class="el-blog-pagination">';

					if ( 'on' === $use_wp_pagenavi && function_exists( 'wp_pagenavi' ) ) {
						$output .= et_core_intentionally_unescaped(
							wp_pagenavi(
								array(
									'query' => $query,
									'echo'  => false,
								)
							),
							'html'
						);
					} else {
						$big     = 999999999;
						$output .= et_core_intentionally_unescaped(
							paginate_links(
								array(
									'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
									'format'    => '?paged=%#%',
									'type'      => 'list',
									'prev_text' => et_core_esc_previously( $prev_text ),
									'next_text' => et_core_esc_previously( $next_text ),
									'current'   => max( 1, $el_dbe_page ),
									'total'     => intval( $total ),
								)
							),
							'html'
						);
					}

					$output .= '</div>';

				}
			}

			if ( ! $is_slider ) {
				$output .= '</div> <!-- el-dbe-blog-extra -->';
			}

			if ( ( 'on' === $show_load_more || 'on' === $use_category_filterable_blog ) && ! $is_slider ) {
				$data_values = array(
					'use_current_loop'				=> esc_attr( $use_current_loop ),
					'blog_layout'              		=> esc_attr( $blog_layout ),
					'masonry'						=> (bool) $masonry,
					'post_type'                		=> esc_attr( $post_type ),
					'posts_number'             		=> intval( $posts_number ),
					'offset_number'            		=> esc_attr( $offset_number ),
					'include_posts'					=> esc_attr( $include_posts ),
					'exclude_posts'					=> esc_attr( $exclude_posts ),
					'meta_date'                		=> esc_attr( $meta_date ),
					'post_order'               		=> esc_attr( $post_order ),
					'post_order_by'            		=> esc_attr( $post_order_by ),
					'include_current_taxonomy'		=> esc_attr( $include_current_taxonomy ),
					'current_taxonomies_relation'	=> esc_attr( $current_taxonomies_relation ),
					'use_category_filterable_blog' 	=> esc_attr( $use_category_filterable_blog ),
					'show_thumbnail'           		=> esc_attr( $show_thumbnail ),
					'show_content'             		=> esc_attr( $show_content ),
					'show_more'                		=> esc_attr( $show_more ),
					'show_author'              		=> esc_attr( $show_author ),
					'show_date'                		=> esc_attr( $show_date ),
					'show_categories'          		=> esc_attr( $show_categories ),
					'show_comments'            		=> esc_attr( $show_comments ),
					'show_read_time'           		=> esc_attr( $show_read_time ),
					'read_time_text'				=> esc_attr( $read_time_text ),
					'show_load_more'           		=> esc_attr( $show_load_more ),
					'use_manual_excerpt'			=> esc_attr( $use_manual_excerpt ),
					'excerpt_length'           		=> esc_attr( $excerpt_length ),
					'read_more_text'           		=> esc_attr( $read_more_text ),
					'pagination_type'          		=> esc_attr( $pagination_type ),
					'use_wp_pagenavi'          		=> esc_attr( $use_wp_pagenavi ),
					'load_more_text'           		=> esc_attr( $load_more_text ),
					'show_less_text'           		=> esc_attr( $show_less_text ),
					'prev_text'                		=> esc_attr( $prev_text ),
					'next_text'                		=> esc_attr( $next_text ),
					'custom_ajax_pagination'   		=> esc_attr( $custom_ajax_pagination ),
					'ajax_pagination_use_icon' 		=> esc_attr( $ajax_pagination_use_icon ),
					'ajax_pagination_icon'     		=> esc_attr( et_pb_process_font_icon( $ajax_pagination_icon ) ),
					'use_read_more_button'     		=> esc_attr( $use_read_more_button ),
					'custom_read_more'         		=> esc_attr( $custom_read_more ),
					'read_more_use_icon'       		=> esc_attr( $read_more_use_icon ),
					'read_more_icon'           		=> esc_attr( et_pb_process_font_icon( $read_more_icon ) ),
					'show_social_icons'        		=> esc_attr( $show_social_icons ),
					'use_overlay'              		=> esc_attr( $use_overlay ),
					'hover_icon'               		=> esc_attr( et_pb_process_font_icon( $hover_icon ) ),
					'featured_image_size'      		=> esc_attr( $featured_image_size ),
					'image_position'           		=> esc_attr( $image_position ),
					'category_meta_colors'     		=> esc_attr( $category_meta_colors ),
					'category_background_color'		=> esc_attr( $category_background ),
					'animation'                		=> esc_attr( $animation ),
					'header_level'             		=> esc_attr( $processed_header_level ),
					'is_single'                		=> (bool) $is_single,
					'is_search'                		=> (bool) $is_search,
					'is_user_logged_in'        		=> (bool) $is_user_logged_in,
					'current_post_id'          		=> intval( $current_post_id ),
					'total_page'               		=> intval( $total ),
				);

				if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
					$post_type_taxonomies = get_object_taxonomies( $post_type, 'names' );
					if ( ! empty( $post_type_taxonomies ) ) {
						$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
						foreach ( $post_type_taxonomies as $taxonomy_key ) {
							if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
								$taxonomy_index = 'category' !== $taxonomy_key ? 'include_' . str_replace( '-', '_', $taxonomy_key ) : 'include_categories';
								if ( isset( $args[ $taxonomy_index ] ) && ! empty( $args[ $taxonomy_index ] ) ) {
									$data_values[ $taxonomy_index ] = esc_attr( $args[ $taxonomy_index ] );
								}
							}
						}
					}
					$data_values['taxonomies_relation'] = esc_attr( $taxonomies_relation );
				} else {
					if ( isset( $args['include_categories'] ) && ! empty( $args['include_categories'] ) ) {
						$data_values['include_categories'] = esc_attr( $args['include_categories'] );
					}
				}

				if ( ( $is_category || $is_tax ) && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
					$object                            = get_queried_object();
					$data_values['post_taxonomy']      = esc_attr( $object->taxonomy );
					$data_values['post_taxonomy_term'] = esc_attr( $object->term_id );
				}

				if ( $is_author && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
					$data_values['author'] = esc_attr( get_queried_object_id() );
				}

				if ( $is_tag && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
					$data_values['tag'] = esc_attr( get_queried_object_id() );
				}

				if ( $is_date && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
					$post_year  = esc_attr( get_query_var( 'year' ) );
					$post_month = esc_attr( get_query_var( 'monthnum' ) );
					$post_day   = esc_attr( get_query_var( 'day' ) );
					if ( $post_year ) {
						$data_values['year'] = et_core_esc_previously( $post_year );
					}
					if ( $month ) {
						$data_values['month'] = et_core_esc_previously( $post_month );
					}
					if ( $post_day ) {
						$data_values['day'] = et_core_esc_previously( $post_day );
					}
				}

				$data_values = rawurlencode( wp_json_encode( $data_values ) );
				$output     .= '<div class="el-blog-params">';
				$output     .= '<input type="hidden" class="divi-blog-extras-props" value="' . $data_values . '" />';
				$output     .= '</div> <!-- el-blog-params -->';
			}
		} else {
			if ( '' === trim( $no_results_text ) ) {
				$output = sprintf(
					'<div class="entry">
						<h2>%1$s</h2>
						<p>%2$s</p>
					</div>',
					esc_html__( 'No Results Found', 'divi-blog-extras' ),
					esc_html__( 'The posts you requested could not be found. Try changing your module settings or create some new posts.', 'divi-blog-extras' )
				);
			} else {
				$output = sprintf(
					'<div class="entry">
						%1$s
					</div>',
					esc_html( $no_results_text )
				);
			}
		}

		/*$whitelisted_animation_fields = array( 'ajax_load_more', 'numbered_pagination', 'filterable_categories' );
		$scroll_top_animation = $this->process_multiple_checkboxes_value( $scroll_top_animation, $whitelisted_animation_fields );*/

		$render_output = sprintf(
			'<div class="et_pb_posts et_pb_bg_layout_light">
                %1$s
            </div> <!-- et_pb_posts -->',
			$output
		);

		// Restore $wp_filter.
		// phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
		$wp_filter = $wp_filter_cache;
		unset( $wp_filter_cache );

		self::$rendering = false;

		return et_core_intentionally_unescaped( $output, 'html' );

	}

	public function before_render() {
		$category_hover_color             = isset( $this->props['category_hover_color'] ) ? $this->props['category_hover_color'] : '';
		$category_hover_background_color  = isset( $this->props['category_hover_background_color'] ) ? $this->props['category_hover_background_color'] : '';
		$category_color__hover            = $this->get_hover_value( 'category_color' );
		$category_background_color__hover = $this->get_hover_value( 'category_background_color' );

		if ( '' !== $category_hover_color && '' === $category_color__hover ) {
			$this->props['category_color__hover_enabled'] = 'on|hover';
			$this->props['category_color__hover']         = $category_hover_color;
		}

		if ( '' !== $category_hover_background_color && '' === $category_background_color__hover ) {
			$this->props['category_background_color__hover_enabled'] = 'on|hover';
			$this->props['category_background_color__hover']         = $category_hover_background_color;
		}

	}

	public function render( $attrs, $content, $render_slug ) {

		if ( self::$rendering ) {
			// We are trying to render a Blog module while a Blog module is already being rendered
			// which means we have most probably hit an infinite recursion. While not necessarily
			// the case, rendering a post which renders a Blog module which renders a post
			// which renders a Blog module is not a sensible use-case.
			return '';
		}

		/*
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		global $wp_filter;
		$wp_filter_cache = $wp_filter;

	
		$module_id                 				= esc_attr( $this->props['module_id'] );
		$module_class              				= esc_attr( $this->props['module_class'] );
		$use_current_loop						= esc_attr( $this->props['use_current_loop'] );
		$blog_layout               				= esc_html( $this->props['blog_layout'] );
		$slider_layout							= esc_html( $this->props['slider_layout'] );
		$masonry_layout		   	   				= esc_html( $this->props['masonry_layout'] );
		$posts_number              				= intval( $this->props['posts_number'] );
		$include_current_taxonomy  				= esc_html( $this->props['include_current_taxonomy'] );
		if ( isset( $this->props['current_taxonomies_relation'] ) ) {
			$current_taxonomies_relation		= esc_html( $this->props['current_taxonomies_relation'] );
		} else {
			$current_taxonomies_relation		= 'OR';
		}
		if ( isset( $this->props['taxonomies_relation'] ) ) {
			$taxonomies_relation				= esc_html( $this->props['taxonomies_relation'] );
		} else {
			$taxonomies_relation				= 'OR';
		}
		$include_posts							= sanitize_text_field( $this->props['include_posts'] );
		$exclude_posts							= sanitize_text_field( $this->props['exclude_posts'] );
		$meta_date                 				= sanitize_text_field( $this->props['meta_date'] );
		$use_category_filterable_blog			= esc_html( $this->props['use_category_filterable_blog'] );
		$category_filter_orderby				= esc_html( $this->props['category_filter_orderby'] );
		$category_filter_order					= esc_html( $this->props['category_filter_order'] );
		$active_category						= esc_html( $this->props['active_category'] );
		$use_hamburger_category_filter			= esc_html( $this->props['use_hamburger_category_filter'] );
		$show_all_posts_link					= esc_html( $this->props['show_all_posts_link'] );
        $all_posts_text							= esc_html( $this->props['all_posts_text'] );
		$ignore_sticky_posts					= esc_html( $this->props['ignore_sticky_posts'] );
		$show_thumbnail            				= esc_html( $this->props['show_thumbnail'] );
		$featured_image_size       				= esc_html( $this->props['featured_image_size'] );
		$image_position            				= esc_html( $this->props['image_position'] );
		$block_extended_overlay    				= esc_html( $this->props['block_extended_overlay'] );
		$masonry_block_extended_image_position	= esc_html( $this->props['masonry_block_extended_image_position'] );
		$masonry_block_extended_overlay 		= esc_html( $this->props['masonry_block_extended_overlay'] );
		$slider_block_extended_image_position	= esc_html( $this->props['slider_block_extended_image_position'] );
		$slider_block_extended_overlay 			= esc_html( $this->props['slider_block_extended_overlay'] );
		$show_content              				= esc_html( $this->props['show_content'] );
		$show_more                 				= esc_html( $this->props['show_more'] );
		$use_manual_excerpt						= esc_html( $this->props['use_manual_excerpt'] );
		$excerpt_length            				= esc_html( $this->props['excerpt_length'] );
		$read_more_text            				= esc_html( $this->props['read_more_text'] );
		$show_author               				= esc_html( $this->props['show_author'] );
		$show_date                 				= esc_html( $this->props['show_date'] );
		$show_categories           				= esc_html( $this->props['show_categories'] );
		$category_meta_colors      				= esc_html( $this->props['category_meta_colors'] );
		$category_color            				= esc_html( $this->props['category_color'] );
		$category_background       				= esc_html( $this->props['category_background_color'] );
		$category_hover_color      				= esc_html( $this->props['category_hover_color'] );
		$category_hover_background 				= esc_html( $this->props['category_hover_background_color'] );
		$category_color_hover      				= esc_html( $this->get_hover_value( 'category_color' ) );
		$category_background_hover 				= esc_html( $this->get_hover_value( 'category_background_color' ) );
		$show_comments             				= esc_html( $this->props['show_comments'] );
		$show_read_time            				= esc_html( $this->props['show_read_time'] );
		$read_time_text							= esc_html( $this->props['read_time_text'] );
		$no_results_text						= esc_html( $this->props['no_results_text'] );
		$show_load_more            				= esc_html( $this->props['show_load_more'] );
		$loader_color							= esc_html( $this->props['loader_color'] );
		$pagination_type           				= esc_html( $this->props['pagination_type'] );
		$use_wp_pagenavi           				= esc_html( $this->props['use_wp_pagenavi'] );
		$prev_text                 				= esc_html( $this->props['prev_text'] );
		$next_text                 				= esc_html( $this->props['next_text'] );
		$load_more_text            				= esc_html( $this->props['load_more_text'] );
		$show_less_text            				= esc_html( $this->props['show_less_text'] );
		$number_background_color 				= esc_html( $this->props['number_background_color'] );
		$active_number_background_color 		= esc_html( $this->props['active_number_background_color'] );
		$number_color 							= esc_html( $this->props['number_color'] );
		$active_number_color 					= esc_html( $this->props['active_number_color'] );
		$number_background_color_hover 			= esc_html( $this->get_hover_value( 'number_background_color' ) );
		$active_number_background_color_hover 	= esc_html( $this->get_hover_value( 'active_number_background_color' ) );
		$number_color_hover 					= esc_html( $this->get_hover_value( 'number_color' ) );
		$active_number_color_hover 				= esc_html( $this->get_hover_value( 'active_number_color' ) );
		$custom_ajax_pagination    				= esc_html( $this->props['custom_ajax_pagination'] );
		$ajax_pagination_icon      				= $this->props['ajax_pagination_icon'];
		$ajax_pagination_use_icon  				= esc_html( $this->props['ajax_pagination_use_icon'] );
		$use_read_more_button     				= esc_html( $this->props['use_read_more_button'] );
		$custom_read_more          				= esc_html( $this->props['custom_read_more'] );
		$read_more_icon            				= $this->props['read_more_icon'];
		$read_more_use_icon        				= esc_html( $this->props['read_more_use_icon'] );
		$offset_number             				= intval( $this->props['offset_number'] );
		$post_order               	 			= sanitize_text_field( $this->props['post_order'] );
		$post_order_by             				= sanitize_text_field( $this->props['post_order_by'] );
		$show_social_icons         				= esc_html( $this->props['show_social_icons'] );
		$overlay_icon_color        				= esc_html( $this->props['overlay_icon_color'] );
		$hover_overlay_color       				= esc_html( $this->props['hover_overlay_color'] );
		$hover_icon                				= $this->props['hover_icon'];
		$use_overlay               				= esc_html( $this->props['use_overlay'] );
		$content_color             				= esc_html( $this->props['content_color'] );
		$show_thumbnail_mobile     				= esc_html( $this->props['show_thumbnail_mobile'] );
		$animation                 				= esc_html( $this->props['animation'] );
		$scroll_top_animation					= esc_html( $this->props['scroll_top_animation'] );
		$link_target							= esc_html( $this->props['link_target'] );
		$header_level              				= esc_html( $this->props['header_level'] );

		$enable_coverflow_shadow 				= $this->props['enable_coverflow_shadow'];
		$coverflow_shadow_color 				= $this->props['coverflow_shadow_color'];
		$equalize_slides_height					= $this->props['equalize_slides_height'];
		$show_arrow 							= $this->props['show_arrow'];
		$show_arrow_on_hover 					= $this->props['show_arrow_on_hover'];
		$arrow_background_color         		= $this->props['arrow_background_color'];
		$arrow_background_color_hover 			= $this->get_hover_value( 'arrow_background_color' );
		$arrow_background_border_size   	 	= $this->props['arrow_background_border_size'];
		$arrow_background_border_color       	= $this->props['arrow_background_border_color'];
		$arrow_background_border_color_hover 	= $this->get_hover_value( 'arrow_background_border_color' );
		$show_control_dot 						= $this->props['show_control_dot'];
		$control_dot_style 						= $this->props['control_dot_style'];
		$control_dot_active_color 				= $this->props['control_dot_active_color'];
		$control_dot_inactive_color 			= $this->props['control_dot_inactive_color'];
		$slide_transition_duration				= $this->props['slide_transition_duration'];
		$arrow_color							= $this->props['arrow_color'];
		$arrow_color_hover						= $this->get_hover_value( 'arrow_color' );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$processed_header_level    = et_pb_process_header_level( $header_level, 'h2' );
		$processed_header_level    = esc_html( $processed_header_level );

		// some themes do not include these styles/scripts so we need to enqueue them in this module to support audio post format.
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );

		// include easyPieChart which is required for loading Blog module content via ajax correctly.
		wp_enqueue_script( 'easypiechart' );

		// include ET Shortcode scripts.
		wp_enqueue_script( 'et-shortcodes-js' );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module.
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		if ( 'masonry' === $blog_layout ) {
			$masonry 		= true;
			$blog_layout 	= $masonry_layout;
			$masonry_class	= 'el-masonry';
			wp_enqueue_script( "elicus-isotope-script", ELICUS_BLOG_PATH . "scripts/isotope.pkgd.min.js", array( 'jquery' ), ELICUS_BLOG_VERSION, true );
			wp_enqueue_script( "elicus-images-loaded-script", ELICUS_BLOG_PATH . "scripts/imagesloaded.pkgd.min.js", array( 'jquery' ), ELICUS_BLOG_VERSION, true );

			$masonry_columns 	= et_pb_responsive_options()->get_property_values( $this->props, 'masonry_columns' );
			$column_spacing 	= et_pb_responsive_options()->get_property_values( $this->props, 'column_spacing' );
			
			$masonry_columns['tablet'] = '' !== $masonry_columns['tablet'] ? $masonry_columns['tablet'] : $masonry_columns['desktop'];
			$masonry_columns['phone']  = '' !== $masonry_columns['phone'] ? $masonry_columns['phone'] : $masonry_columns['tablet'];

			$column_spacing['tablet'] = '' !== $column_spacing['tablet'] ? $column_spacing['tablet'] : $column_spacing['desktop'];
			$column_spacing['phone']  = '' !== $column_spacing['phone'] ? $column_spacing['phone'] : $column_spacing['tablet'];
			
			$breakpoints 	= array( 'desktop', 'tablet', 'phone' );
			$width 			= array();

			foreach ( $breakpoints as $breakpoint ) {
				if ( 1 === absint( $masonry_columns[$breakpoint] ) ) {
					$width[$breakpoint] = '100%';
				} else {
					$divided_width 	= 100 / absint( $masonry_columns[$breakpoint] );
					if ( 0.0 !== floatval( $column_spacing[$breakpoint] ) ) {
						$gutter = floatval( ( floatval( $column_spacing[$breakpoint] ) * ( absint( $masonry_columns[$breakpoint] ) - 1 ) ) / absint( $masonry_columns[$breakpoint] ) );
						$width[$breakpoint] = 'calc(' . $divided_width . '% - ' . $gutter . 'px)';
					} else {
						$width[$breakpoint] = $divided_width . '%';
					}
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $width, '%%order_class%% .el-isotope-item', 'width', $render_slug, '', 'range' );
			et_pb_responsive_options()->generate_responsive_css( $column_spacing, '%%order_class%% .el-isotope-item', array( 'margin-bottom' ), $render_slug, '', 'range' );
			et_pb_responsive_options()->generate_responsive_css( $column_spacing, '%%order_class%% .el-isotope-item-gutter', 'width', $render_slug, '', 'range' );

		} else if ( 'vertical_grid' === $blog_layout ) {
			$masonry_columns 	= et_pb_responsive_options()->get_property_values( $this->props, 'masonry_columns' );
			$column_spacing 	= et_pb_responsive_options()->get_property_values( $this->props, 'column_spacing' );
			
			$masonry_columns['tablet'] = '' !== $masonry_columns['tablet'] ? $masonry_columns['tablet'] : $masonry_columns['desktop'];
			$masonry_columns['phone']  = '' !== $masonry_columns['phone'] ? $masonry_columns['phone'] : $masonry_columns['tablet'];

			$column_spacing['tablet'] = '' !== $column_spacing['tablet'] ? $column_spacing['tablet'] : $column_spacing['desktop'];
			$column_spacing['phone']  = '' !== $column_spacing['phone'] ? $column_spacing['phone'] : $column_spacing['tablet'];
			
			$breakpoints 	= array( 'desktop', 'tablet', 'phone' );
			$width 			= array();

			foreach ( $breakpoints as $breakpoint ) {
				if ( 1 === absint( $masonry_columns[$breakpoint] ) ) {
					$width[$breakpoint] = '100%';
				} else {
					$divided_width 	= 100 / absint( $masonry_columns[$breakpoint] );
					if ( 0.0 !== floatval( $column_spacing[$breakpoint] ) ) {
						$gutter = floatval( ( floatval( $column_spacing[$breakpoint] ) * ( absint( $masonry_columns[$breakpoint] ) - 1 ) ) / absint( $masonry_columns[$breakpoint] ) );
						$width[$breakpoint] = 'calc(' . $divided_width . '% - ' . $gutter . 'px)';
					} else {
						$width[$breakpoint] = $divided_width . '%';
					}
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $width, '%%order_class%% .el_dbe_vertical_grid', 'width', $render_slug, '', 'range' );
			et_pb_responsive_options()->generate_responsive_css( $column_spacing, '%%order_class%% .el_dbe_vertical_grid', array( 'margin-bottom' ), $render_slug, '', 'range' );

			self::set_style( $render_slug, array(
                'selector'    => '%%order_class%% .vertical_grid',
                'declaration' => 'display: flex; flex-wrap: wrap; width: 100%;',
            ) );

			self::set_style( $render_slug, array(
                'selector'    => '%%order_class%% .el_dbe_vertical_grid',
                'declaration' => 'float: left;',
            ) );

            self::set_style( $render_slug, array(
                'selector'    => '%%order_class%% .el_dbe_vertical_grid .post-meta',
                'declaration' => 'align-self: flex-end;',
            ) );

			//Column Numbers
			foreach ( $masonry_columns as $device => $cols ) {
				if ( 'desktop' === $device ) {
					self::set_style( $render_slug, array(
	                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:not(:nth-child(' . absint( $cols ) . 'n+' . absint( $cols ) . '))',
	                    'declaration' => sprintf( 'margin-right: %1$s;', esc_attr( $column_spacing['desktop'] ) ),
	                    'media_query' => self::get_media_query( 'min_width_981' ),
	                ) );
	                if ( '' !== $cols ) {
						self::set_style( $render_slug, array(
		                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:nth-child(' . absint( $cols ) . 'n+1)',
		                    'declaration' => sprintf( 'clear: left;', esc_attr( $column_spacing['desktop'] ) ),
		                    'media_query' => self::get_media_query( 'min_width_981' ),
		                ) );
					}
				} else if ( 'tablet' === $device ) {
					self::set_style( $render_slug, array(
	                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:not(:nth-child(' . absint( $cols ) . 'n+' . absint( $cols ) . '))',
	                    'declaration' => sprintf( 'margin-right: %1$s;', esc_attr( $column_spacing['tablet'] ) ),
	                    'media_query' => self::get_media_query( '768_980' ),
	                ) );
	                if ( '' !== $cols ) {
						self::set_style( $render_slug, array(
		                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:nth-child(' . absint( $cols ) . 'n+1)',
		                    'declaration' => 'clear: left;',
		                    'media_query' => self::get_media_query( '768_980' ),
		                ) );
					}
				} else if ( 'phone' === $device ) {
					self::set_style( $render_slug, array(
	                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:not(:nth-child(' . absint( $cols ) . 'n+' . absint( $cols ) . '))',
	                    'declaration' => sprintf( 'margin-right: %1$s;', esc_attr( $column_spacing['phone'] ) ),
	                    'media_query' => self::get_media_query( 'max_width_767' ),
	                ) );
	                if ( '' !== $cols ) {
						self::set_style( $render_slug, array(
		                    'selector'    => '%%order_class%% .el_dbe_vertical_grid:nth-child(' . absint( $cols ) . 'n+1)',
		                    'declaration' => 'clear: left;',
		                    'media_query' => self::get_media_query( 'max_width_767' ),
		                ) );
					}
				}
			}
			$masonry 		= false;
			$masonry_class	= '';
		} else {
			$masonry 		= false;
			$masonry_class	= '';
		}

		if ( 'slider' === $blog_layout ) {
			$is_slider 		= true;
			$blog_layout 	= $slider_layout;
		} else {
			$is_slider 		= false;
		}

		if ( 'off' === $show_thumbnail_mobile ) {

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_post_extra:not(.el_dbe_full_width_background) .post-media,.et_pb_post_extra:not(.el_dbe_full_width_background) .post-media-container',
					'declaration' => 'display: none !important;',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_post_extra.el_dbe_full_width .post-content',
					'declaration' => 'margin-left: 0; padding: 0;',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_post_extra.el_dbe_block_extended.image-background .post-content,.et_pb_post_extra.el_dbe_block_extended.et_pb_no_thumb .post-content',
					'declaration' => 'padding: 20px;',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_post_extra.el_dbe_block_extended.image-top .post-categories',
					'declaration' => 'position: relative;',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_post_extra.el_dbe_list .post-media + .post-content',
					'declaration' => 'width: 100%;',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

		}

		if ( 'off' !== $show_categories ) {

			if ( isset( $category_color ) && '' !== $category_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
						'declaration' => sprintf(
							'color: %1$s !important;',
							esc_attr( $category_color )
						),
					)
				);
			}

			if ( isset( $category_background ) && '' !== $category_background ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
						'declaration' => sprintf(
							'background-color: %1$s;',
							esc_attr( $category_background )
						),
					)
				);

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
						'declaration' => sprintf(
							'border-color: %1$s;',
							esc_attr( $category_background )
						),
					)
				);
			}

			if ( isset( $category_color_hover ) && '' !== $category_color_hover ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a:hover, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a:hover',
						'declaration' => sprintf(
							'color: %1$s !important;',
							esc_attr( $category_color_hover )
						),
					)
				);
			}

			if ( isset( $category_background_hover ) && '' !== $category_background_hover ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a:hover, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a:hover',
						'declaration' => sprintf(
							'background-color: %1$s;',
							esc_attr( $category_background_hover )
						),
					)
				);

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a:hover, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a:hover',
						'declaration' => sprintf(
							'border-color: %1$s;',
							esc_attr( $category_background_hover )
						),
					)
				);
			}

			if ( 'block_extended' !== $blog_layout && 'grid_extended' !== $blog_layout && 'off' === $category_meta_colors && ( ! isset( $category_background ) || '' === $category_background ) ) {
				ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
							'declaration' => 'margin-right: 0;',
						)
					);
			}

			if ( ( isset( $category_background ) && '' !== $category_background ) || 'on' === $category_meta_colors ) {
				if ( 'box_extended' === $blog_layout || 'classic' === $blog_layout ) {
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
							'declaration' => 'padding: 5px;',
						)
					);
				}

				if ( 'full_width' === $blog_layout || 'full_width_background' === $blog_layout || 'vertical_grid' === $blog_layout ) {
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories a, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories a',
							'declaration' => 'padding: 2px 4px;',
						)
					);
				}

				if ( 'vertical_grid' === $blog_layout ) {
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .et_pb_post.et_pb_post_extra .post-categories, %%order_class%% .et_pb_post_extra.et_pb_no_thumb .post-categories',
							'declaration' => 'margin-bottom: 5px;',
						)
					);
				}
			}
		}

		if ( $masonry && 'block_extended' === $blog_layout ) {
			$image_position = $masonry_block_extended_image_position;
		}

		if ( $is_slider && 'block_extended' === $blog_layout ) {
			$image_position = $slider_block_extended_image_position;
		}

		if ( 'block_extended' === $blog_layout && ( 'background' === $image_position || 'alternate' === $image_position ) ) {

			if ( $masonry ) {
				if ( isset( $masonry_block_extended_overlay ) && ! empty( $masonry_block_extended_overlay ) ) {
					$block_extended_overlay = $masonry_block_extended_overlay;
				}
			}

			if ( $is_slider ) {
				if ( isset( $slider_block_extended_overlay ) && ! empty( $slider_block_extended_overlay ) ) {
					$block_extended_overlay = $slider_block_extended_overlay;
				}
			}

			if ( isset( $block_extended_overlay ) && ! empty( $block_extended_overlay ) ) {
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .el_dbe_block_extended.image-background .post-media:before',
						'declaration' => sprintf(
							'background: %1$s;',
							esc_attr( $block_extended_overlay )
						),
					)
				);
			}

			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .image-background .post-content',
					'declaration' => 'background-color: transparent;',
				)
			);

			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .image-background .post-meta',
					'declaration' => 'background-color: transparent; color: #fff;',
				)
			);

			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .image-background .post-content *',
					'declaration' => 'color: #fff;',
				)
			);

			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .image-background .post-meta a',
					'declaration' => 'color: #fff;',
				)
			);

		}

		if ( 'off' !== $show_more && 'grid_extended' === $blog_layout ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .el_dbe_grid_extended a.more-link',
					'declaration' => sprintf(
						'border-color: %1$s;',
						(
							isset( $this->props['body_link_text_color'] ) && ! empty( $this->props['body_link_text_color'] ) ?
							esc_attr( $this->props['body_link_text_color'] ) :
							esc_attr( et_get_option( 'accent_color', '#2ea3f2' ) )
						)
					),
				)
			);
		}

		if ( 'on' === $show_load_more && 'on' === $pagination_type && ! $is_slider ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .el-loader',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_attr( $loader_color )
					),
				)
			);
		}

		if ( '' !== $overlay_icon_color ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_attr( $overlay_icon_color )
					),
				)
			);
		}

		if ( '' !== $hover_overlay_color ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .et_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_attr( $hover_overlay_color )
					),
				)
			);
		}

		if ( '' !== $content_color ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .post-content p:not(.post-meta)',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_attr( $content_color )
					),
				)
			);
		}

		if ( 'on' === $use_read_more_button && 'on' === $custom_read_more && 'on' === $read_more_use_icon && '' !== $read_more_icon ) {
			self::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .el-read-more-btn .et_pb_button:after',
					'declaration' => 'content: attr(data-icon);',
				)
			);
		}

		if ( 'on' === $use_overlay ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);

			if ( '' !== $hover_icon ) {
				if ( class_exists( 'ET_Builder_Module_Helper_Style_Processor' ) && method_exists( 'ET_Builder_Module_Helper_Style_Processor', 'process_extended_icon' ) ) {
	                $this->generate_styles(
	                    array(
	                        'utility_arg'    => 'icon_font_family',
	                        'render_slug'    => $render_slug,
	                        'base_attr_name' => 'hover_icon',
	                        'important'      => true,
	                        'selector'       => '%%order_class%% .et_overlay:before',
	                        'processor'      => array(
	                            'ET_Builder_Module_Helper_Style_Processor',
	                            'process_extended_icon',
	                        ),
	                    )
	                );
	            }
			}
		}

		if ( 'on' !== $show_content ) {
			if ( 'classic' === $blog_layout ) {
				$excerpt_length = ( '' === $excerpt_length ) ? 600 : intval( $excerpt_length );
			} else {
				$excerpt_length = ( '' === $excerpt_length ) ? 270 : intval( $excerpt_length );
			}
		}

		if ( 'on' === $show_more ) {
			$read_more_text = ( ! isset( $read_more_text ) || '' === $read_more_text ) ?
			esc_html__( 'Read More', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $read_more_text )
			);
		}

		if ( 'on' === $show_load_more && 'on' === $pagination_type && ! $is_slider ) {
			$load_more_text = '' === $load_more_text ?
			esc_html__( 'Load More', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $load_more_text )
			);

			$show_less_text = '' === $show_less_text ?
			esc_html__( 'Show Few', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $show_less_text )
			);
		}

		if ( 'on' === $show_load_more && 'off' === $pagination_type && ! $is_slider ) {
			$prev_text = '' === $prev_text ?
			esc_html__( '« Previous', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $prev_text )
			);

			$next_text = '' === $next_text ?
			esc_html__( 'Next »', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $next_text )
			);
		}

		if ( 'on' === $show_read_time ) {
			$read_time_text = '' === $read_time_text ?
			esc_html__( 'min read', 'divi-blog-extras' ) :
			sprintf(
				esc_html__( '%s', 'divi-blog-extras' ),
				esc_html( $read_time_text )
			);
		}

		if ( 'on' === $show_all_posts_link ) {
            $all_posts_text = '' === $all_posts_text ?
            esc_html__( 'All', 'divi-blog-extras' ) :
            sprintf(
                esc_html__( '%s', 'divi-blog-extras' ),
                esc_html( $all_posts_text )
            );
        } else {
        	$all_posts_text = '';
        }

		$overlay_class = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';

		$post_type = 'post';
		if ( isset( $this->props['post_type'] ) && ! empty( $this->props['post_type'] ) ) {
			$post_type = sanitize_text_field( $this->props['post_type'] );
		}

		$args = array(
			'post_type'      => sanitize_text_field( $post_type ),
			'posts_per_page' => intval( $posts_number ),
			'post_status'    => 'publish',
			'offset'         => 0,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( is_user_logged_in() ) {
			$args['post_status'] = array( 'publish', 'private' );
		}

		if ( 'on' === $ignore_sticky_posts ) {
			$args['ignore_sticky_posts'] = true;
		}

		if ( ( is_category() || is_tax() ) && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$object            = get_queried_object();
			$post_type         = sanitize_text_field( get_post_type() );
			$args['post_type'] = $post_type;
			$args['tax_query'] = array(
				array(
					'taxonomy' => sanitize_text_field( $object->taxonomy ),
					'field'    => 'term_id',
					'terms'    => intval( $object->term_id ),
					'operator' => 'IN',
				),
			);
		} else if ( ! is_search() ) {
			if ( 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
				$tax_query      = array();
				$plugin_options = get_option( ELICUS_BLOG_OPTION );
				if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
					$post_type_taxonomies = get_object_taxonomies( $post_type, 'names' );
					if ( ! empty( $post_type_taxonomies ) ) {
						$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
						foreach ( $post_type_taxonomies as $taxonomy_key ) {
							if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
								$taxonomy_index = 'category' !== $taxonomy_key ? 'include_' . str_replace( '-', '_', $taxonomy_key ) : 'include_categories';
								$taxonomy_terms = et_()->array_get( $this->props, $taxonomy_index );
								if ( ! empty( $taxonomy_terms ) ) {
									array_push(
										$tax_query,
										array(
											'taxonomy' => sanitize_text_field( $taxonomy_key ),
											'field'    => 'term_id',
											'terms'    => array_map( 'intval', explode( ',', $taxonomy_terms ) ),
											'operator' => 'IN',
										)
									);
								}
							}
						}
					}
				} else {
					if ( isset( $this->props['include_categories'] ) && ! empty( $this->props['include_categories'] ) ) {
						array_push(
							$tax_query,
							array(
								'taxonomy' => 'category',
								'field'    => 'term_id',
								'terms'    => array_map( 'intval', explode( ',', $this->props['include_categories'] ) ),
								'operator' => 'IN',
							)
						);
					}
				}

				if ( ! empty( $tax_query ) ) {
					if ( count( $tax_query ) > 1 ) {
						if ( isset( $taxonomies_relation ) ) {
							$tax_query['relation'] = sanitize_text_field( $taxonomies_relation );
						}
					}
					$args['tax_query'] = $tax_query;
				}
			}
		}

		if ( is_author() && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$args['author'] = intval( get_queried_object_id() );
		}

		if ( is_tag() && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$args['tag_id'] = intval( get_queried_object_id() );
		}

		if ( is_date() && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$post_year  = sanitize_text_field( get_query_var( 'year' ) );
			$post_month = sanitize_text_field( get_query_var( 'monthnum' ) );
			$post_day   = sanitize_text_field( get_query_var( 'day' ) );
			if ( $post_year ) {
				$args['year'] = $post_year;
			}
			if ( $post_month ) {
				$args['monthnum'] = $post_month;
			}
			if ( $post_day ) {
				$args['day'] = $post_day;
			}
		}

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			$args['offset'] = intval( $offset_number );
		}

		if ( '' !== $args['offset'] && -1 === intval( $args['posts_per_page'] ) ) {
			$count_posts            = wp_count_posts( $post_type, 'readable' );
			$published_posts        = $count_posts->publish;
			$args['posts_per_page'] = intval( $published_posts );
		}

		if ( 'on' === $show_load_more && ! $is_slider ) {
			$page           = ( is_front_page() ) ? intval( get_query_var( 'page' ) ) : intval( get_query_var( 'paged' ) );
			$page           = 1 < intval( get_query_var( 'el_dbe_page' ) ) ? intval( get_query_var( 'el_dbe_page' ) ) : $page;
			$el_dbe_page    = ( isset( $page ) && $page > 0 ) ? $page : 1;
			$args['paged']  = $el_dbe_page;
			$args['offset'] = ( ( intval( $el_dbe_page ) - 1 ) * intval( $posts_number ) ) + $args['offset'];
		}

		if ( isset( $post_order_by ) && '' !== $post_order_by ) {
			$args['orderby'] = sanitize_text_field( $post_order_by );
		}

		if ( isset( $post_order ) && '' !== $post_order ) {
			$args['order'] = sanitize_text_field( $post_order );
		}

		if ( '' !== $include_posts && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
			$include_posts = array_map( 'trim', explode( ',', $include_posts ) );
			$args['post__in'] = array_map( 'intval', $include_posts );
		}

		if ( '' !== $exclude_posts ) {
			$exclude_posts = array_map( 'trim', explode( ',', $exclude_posts ) );
			$args['post__not_in'] = array_map( 'intval', $exclude_posts );
		}

		if ( is_single() ) {
			if ( 'on' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
				$post_type 				= sanitize_text_field( get_post_type() );
				$post_taxonomies 		= get_object_taxonomies( $post_type, 'names' );
				$filtered_taxonomies 	= json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
				if ( $post_taxonomies && is_array( $post_taxonomies ) ) {
					$tax_query = array();
					if ( 'AND' === $current_taxonomies_relation ) {
						$operator = 'AND';
					} else {
						$operator = 'IN';
					}
					foreach( $post_taxonomies as $taxonomy_key ) {
						if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
							$term_ids = wp_get_post_terms( get_the_ID(), $taxonomy_key, array( 'fields' => 'ids' ) );
							array_push(
								$tax_query,
								array(
									'taxonomy' => sanitize_text_field( $taxonomy_key ),
									'field'    => 'term_id',
									'terms'    => array_map( 'intval', $term_ids ),
									'operator' => sanitize_text_field( $operator ),
								)
							);
						}
					}
					if ( ! empty( $tax_query ) ) {
						if ( count( $tax_query ) > 1 ) {
							$tax_query['relation'] = sanitize_text_field( $current_taxonomies_relation );
						}
						$args['tax_query'] = $tax_query;
					}
				}
				$args['post_type'] = $post_type;
			}

			if ( ! isset( $args['post__not_in'] ) ) {
				$args['post__not_in'] = array( intval( get_the_ID() ) );
			} else {
				$args['post__not_in'] = array_merge( $args['post__not_in'], array( intval( get_the_ID() ) ) );
			}
		}

		if ( is_search() && in_array( $use_current_loop, array( '-1', 'on' ), true ) ) {
			$is_search 			= is_search();
			$args['post_type'] 	= 'any';
			// phpcs:ignore WordPress,GET,POST,REQUEST,NO NONCE.
			if ( isset( $_GET['s'] ) ) {
				// phpcs:ignore WordPress,GET,POST,REQUEST,NO NONCE.
				$args['s'] 		= sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}
		}

		if ( ! is_search() && ! is_archive() && ! $is_slider && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) && 'on' === $use_category_filterable_blog && 'post' === $args['post_type'] && 'all' !== $active_category && ! isset( $_POST['el_dbe_nonce'], $_POST['post_category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => intval( $active_category ),
					'operator' => 'IN',
				)
			);
		}

		if ( isset( $_POST['el_dbe_nonce'], $_POST['post_category'] ) ) {
			if ( wp_verify_nonce( sanitize_key( wp_unslash( $_POST['el_dbe_nonce'] ) ), 'elicus-blog-nonce' ) ) {
				if (
					! is_search() &&
					! is_archive() &&
					! $is_slider &&
					'off' === $include_current_taxonomy &&
					in_array( $use_current_loop, array( '-1', 'off' ), true ) &&
					'on' === $use_category_filterable_blog &&
					'post' === $args['post_type'] &&
					0 !== absint( $_POST['post_category'] )
				) {
					$tax_query 		= array();
					$plugin_options = get_option( ELICUS_BLOG_OPTION );
					if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
						$post_type_taxonomies = get_object_taxonomies( $args['post_type'], 'names' );
						if ( ! empty( $post_type_taxonomies ) ) {
							$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
							foreach ( $post_type_taxonomies as $taxonomy_key ) {
								if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
									if ( 'category' !== $taxonomy_key ) {
										$taxonomy_index = 'include_' . str_replace( '-', '_', $taxonomy_key );
										$taxonomy_terms = et_()->array_get( $this->props, $taxonomy_index );
										if ( ! empty( $taxonomy_terms ) ) {
											array_push(
												$tax_query,
												array(
													'taxonomy' => sanitize_text_field( $taxonomy_key ),
													'field'    => 'term_id',
													'terms'    => array_map( 'intval', explode( ',', $taxonomy_terms ) ),
													'operator' => 'IN',
												)
											);
										}
									}
								}
							}
						}
					}
					if ( '-1' === $_POST['post_category'] ) {
						if ( ! empty( $this->props['include_categories'] ) ) {
							array_push(
								$tax_query,
								array(
	                                'taxonomy' => 'category',
	                                'field'    => 'term_id',
	                                'terms'    => array_map( 'intval', explode( ',', $this->props['include_categories'] ) ),
	                                'operator' => 'IN'
	                            )
							);
						}
					} else {
						array_push(
							$tax_query,
							array(
                                'taxonomy' => 'category',
                                'field'    => 'term_id',
                                'terms'    => absint( wp_unslash( $_POST['post_category'] ) ),
                                'operator' => 'IN'
                            )
						);
					}
		           	if ( ! empty( $tax_query ) ) {
						if ( count( $tax_query ) > 1 ) {
							if ( isset( $taxonomies_relation ) ) {
								$tax_query['relation'] = sanitize_text_field( $taxonomies_relation );
							}
						}
						$args['tax_query'] = $tax_query;
					}
		        }
			}
	    }

	    if ( isset( $taxonomies_relation ) && 'AND' === $taxonomies_relation && in_array( $use_current_loop, array( '-1', 'off' ), true ) ) {
			$filter_query_args = $args;
			$filter_query_args['posts_per_page'] = '-1';
			$filter_query = new WP_Query( $filter_query_args );
		}

		$args = apply_filters( 'divi_blog_extras_query_args', $args );

		$query = new WP_Query( $args );

		self::$rendering = true;

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			$total = intval( ceil( ( $query->found_posts - $offset_number ) / $args['posts_per_page'] ) );
		} else {
			$total = intval( ceil( ( $query->found_posts ) / $args['posts_per_page'] ) );
		}

		if ( $query->have_posts() ) {

			$counter 	= 1;
			$output 	= '';

			if ( ! is_search() && ! is_archive() && ! $is_slider && 'off' === $include_current_taxonomy && in_array( $use_current_loop, array( '-1', 'off' ), true ) && 'on' === $use_category_filterable_blog && 'post' === $args['post_type'] ) {
				$post_categories = array_filter( array_map( 'absint', explode( ',', $this->props['include_categories'] ) ) );
				if ( isset( $taxonomies_relation ) && 'AND' === $taxonomies_relation ) {
					$post_ids 			= wp_list_pluck( $filter_query->posts, 'ID' );
					$post_categories 	= array();
					foreach ( $post_ids as $post_id ) {
						$post_terms = get_the_terms( $post_id, 'category' );
						if ( $post_terms && ! is_wp_error( $post_terms ) ) {
							$categories 		= wp_list_pluck( $post_terms, 'term_id' );
							$post_categories 	= array_merge( $post_categories, $categories );
						}
					}
					$post_categories = array_unique( $post_categories );
				}
				if ( empty( $post_categories ) || '0' == count( $post_categories ) || '1' < count( $post_categories ) ) {
					$post_terms   = get_terms( array(
					    'taxonomy'		=> 'category',
					    'orderby'		=> sanitize_text_field( $category_filter_orderby ),
					    'order'			=> sanitize_text_field( $category_filter_order ),
					    'hide_empty' 	=> true,
					    'include' 		=> array_map( 'intval', $post_categories )
					) );
					if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) && 1 < count( $post_terms ) ){
						$output .= '<div class="el-dbe-filterable-categories" data-hamburger-filter="'. esc_attr( $use_hamburger_category_filter ) .'">';
							if ( 'on' === $use_hamburger_category_filter ) {
								if ( 0 !== intval( $active_category ) ) {
									$mobile_active_category = get_cat_name( intval( $active_category ) );
								} else {
									$mobile_active_category = $all_posts_text;
								}
								$output .= '<div class="el-dbe-filterable-mobile-categories">';
									$output .= '<span class="el-dbe-mobile-active-category">' . esc_html( $mobile_active_category ) . '</span>';
									$output .= '<span class="el-dbe-category-mobile-menu"></span>';
								$output .= '</div>';
							}
							$output .= '<ul class="el-dbe-post-categories">';
							$obj_id = get_queried_object_id();
							$current_url = get_permalink( $obj_id );
							if ( 'on' === $show_all_posts_link ) {
								$active_category_class = 'all' === $active_category ? 'el-dbe-active-category' : '';
								$output .= '<li><a href="' . esc_url( $current_url ) . '" class="'. esc_attr( $active_category_class ) .'" data-term-id="-1">' . esc_html( $all_posts_text ) . '</a></li>';
							}
							foreach ( $post_terms as $post_term ) {
								$active_category_class = intval( $post_term->term_id ) === intval( $active_category ) ? 'el-dbe-active-category' : '';
						    	$output .= '<li><a href="' . esc_url( $current_url ) . '" class="'. esc_attr( $active_category_class ) .'" data-term-id="' . esc_attr( $post_term->term_id ) . '">' . esc_html( $post_term->name ) . '</a></li>';
							}

							$output .= '</ul>';
						$output .= '</div>';
					}
				}
			}

			$blog_classes = array_map( 'sanitize_html_class', array( $blog_layout, $masonry_class ) );
			$blog_classes = implode( ' ', $blog_classes );
			$blog_classes = $is_slider ? $blog_classes . ' el-dbe-blog-extra-slider ' . sanitize_html_class( $control_dot_style ) : $blog_classes;

			$output .= '<div class="el-dbe-blog-extra ' . esc_attr( $blog_classes ) . '">';

			if ( $masonry ) {
				$output .= '<div class="el-isotope-container">';
				$output .= '<div class="el-isotope-item-gutter"></div>';
			}

			if ( $is_slider ) {
				wp_enqueue_script( 'elicus-swiper-script' );
				wp_enqueue_style( 'elicus-swiper-style' );

				$output .= '<div class="swiper-container">';
				$output .= '<div class="swiper-wrapper">';
			}

			add_filter( 'wp_kses_allowed_html', array( 'El_Blog_Module', 'filter_wp_kses_post_tags' ), 10, 2 );

			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id        = intval( get_the_ID() );
				$thumb          = '';
				$image_class    = '';
				$date_class     = '';
				$thumb          = el_get_post_thumbnail( $post_id, esc_html( $featured_image_size ), 'et_pb_post_main_image no-lazyload skip-lazy' ); 
				$no_thumb_class = ( '' === $thumb || 'off' === $show_thumbnail ) ? ' et_pb_no_thumb' : '';
				$layout_class   = ' el_dbe_' . $blog_layout;

				if ( 'on' === $show_load_more && 'on' === $pagination_type ) {
					$animation = ( 'off' === $animation ) && ( 1 < $el_dbe_page ) ? 'bottom' : $animation;
				}

				$animation_class = ' et-waypoint et_pb_animation_' . $animation;

				if ( '' !== $thumb && 'on' === $show_thumbnail ) {
					if ( 'block_extended' === $blog_layout ) {
						if ( 'alternate' !== $image_position ) {
							$image_class = ' image-' . $image_position;
						} else {
							if ( 0 !== $counter % 2 ) {
								$image_class = ' image-background';
							} else {
								$image_class = ' image-top';
							}
						}
					}
				}

				if ( 'full_width' === $blog_layout ) {
					if ( 'off' === $show_date ) {
						$date_class = ' no_date';
					}
				}

				if ( 'on' !== $show_content ) {
					if ( 'on' === $show_more ) {
						if ( 'on' === $use_read_more_button ) {
							$read_more_button = $this->render_button(
								array(
									'button_text'         => et_core_esc_previously( $read_more_text ),
									'button_text_escaped' => true,
									'button_url'          => esc_url( get_permalink( $post_id ) ),
									'button_custom'       => isset( $custom_read_more ) ? $custom_read_more : 'off',
									'custom_icon'         => isset( $read_more_icon ) ? $read_more_icon : '',
									'has_wrapper'         => false,
									'url_new_window'	  => esc_attr( $link_target ),
								)
							);
						}
					}
				}

				$classes = array_map( 'sanitize_html_class', get_post_class( 'et_pb_post et_pb_post_extra et_pb_text_align_left' . $date_class . $animation_class . $layout_class . $no_thumb_class . $overlay_class . $image_class ) );

				$post_class = implode( ' ', $classes );
				$post_class = $is_slider ? $post_class . ' swiper-slide' : $post_class;

				if ( 'on' === $show_load_more || isset( $_POST['el_dbe_nonce'], $_POST['post_category'] ) ) {
					$post_class = $post_class . ' et-animated';
				}

				if ( $masonry ) {
					$output .= '<div class="el-isotope-item">';
				}

				$output .= '<article id="post-' . $post_id . '" class="' . $post_class . '" >';

				$blog_layout = sanitize_file_name( $blog_layout );
				$blog_layout = str_replace( '-', '_', $blog_layout );

				if ( file_exists( get_stylesheet_directory() . '/divi-blog-extras/layouts/' . $blog_layout . '.php' ) ) {
					include get_stylesheet_directory() . '/divi-blog-extras/layouts/' . $blog_layout . '.php';
				} elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'layouts/' . $blog_layout . '.php' ) ) {
					include plugin_dir_path( __FILE__ ) . 'layouts/' . $blog_layout . '.php';
				}

				$output .= '</article> <!-- et_pb_post_extra -->';

				if ( $masonry ) {
					$output .= '</div> <!-- el-isotope-item -->';
				}

				$counter++;
			}

			if ( $masonry ) {
				$output .= '</div> <!-- el-isotope-container -->';
			}

			remove_filter( 'wp_kses_allowed_html', array( 'El_Blog_Module', 'filter_wp_kses_post_tags' ), 10, 2 );

			wp_reset_postdata();

			if ( $is_slider ) {
				$output .= '</div> <!-- swiper-wrapper -->';

				if ( 'on' === $show_arrow ) {
					$output .= '<div class="swiper-button-next"></div>';
					$output .= '<div class="swiper-button-prev"></div>';
				}

				$output .= '</div> <!-- swiper-container -->';

				if ( 'on' === $show_control_dot ) {
					$output .= '<div class="swiper-pagination"></div>';
				}
			}

			if ( 'on' === $show_load_more && ! $is_slider ) {
				add_filter( 'get_pagenum_link', array( 'El_Blog_Module', 'filter_pagination_url' ), 10, 2 );
				// Pagination.
				if ( 'on' === $pagination_type ) {
					// Load more Pagination.
					if ( $total > 1 ) {
						$load_more_page = $el_dbe_page < $total ? ( $el_dbe_page + 1 ) : 1;
						$button_text    = $el_dbe_page < $total ? $load_more_text : $show_less_text;
						$button_classes = array(
							'el-pagination-button',
							'el-button',
						);

						if ( $el_dbe_page < $total ) {
							array_push( $button_classes, 'el-load-more' );
						} else {
							array_push( $button_classes, 'el-show-less' );
						}

						$pagenum_link     = get_pagenum_link( $load_more_page );
						$load_more_button = $this->render_button(
							array(
								'button_text'         => esc_html( $button_text ),
								'button_text_escaped' => true,
								'button_url'          => esc_url( $pagenum_link ),
								'button_custom'       => isset( $custom_ajax_pagination ) ? $custom_ajax_pagination : 'off',
								'custom_icon'         => isset( $ajax_pagination_icon ) ? $ajax_pagination_icon : '',
								'has_wrapper'         => false,
								'button_classname'    => $button_classes,
							)
						);
						$output          .= '<div class="ajax-pagination">';
						$output          .= et_core_intentionally_unescaped( $load_more_button, 'html' );
						$output          .= '</div>';

						if ( isset( $ajax_pagination_icon ) && $ajax_pagination_icon ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-dbe-blog-extra .el-pagination-button:after',
									'declaration' => 'content: attr(data-icon);',
								)
							);
						}
					}
				} else {
					// Numbered Pagination.
					$output .= '<div class="el-blog-pagination">';

					if ( 'on' === $use_wp_pagenavi && function_exists( 'wp_pagenavi' ) ) {
						$output .= et_core_intentionally_unescaped(
							wp_pagenavi(
								array(
									'query' => $query,
									'echo'  => false,
								)
							),
							'html'
						);
					} else {
						if ( $number_background_color ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination a.page-numbers',
									'declaration' => sprintf(
										'background: %1$s;',
										esc_html( $number_background_color )
									),
								)
							);
						}
						if ( $number_background_color_hover ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination a.page-numbers:hover',
									'declaration' => sprintf(
										'background: %1$s;',
										esc_html( $number_background_color_hover )
									),
								)
							);
						}
						if ( $active_number_background_color ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination .page-numbers.current',
									'declaration' => sprintf(
										'background: %1$s;',
										esc_html( $active_number_background_color )
									),
								)
							);
						}
						if ( $active_number_background_color_hover ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination .page-numbers.current:hover',
									'declaration' => sprintf(
										'background: %1$s;',
										esc_html( $active_number_background_color_hover )
									),
								)
							);
						}
						if ( $number_color ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination a.page-numbers',
									'declaration' => sprintf(
										'color: %1$s;',
										esc_html( $number_color )
									),
								)
							);
						}
						if ( $number_color_hover ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination a.page-numbers:hover',
									'declaration' => sprintf(
										'color: %1$s;',
										esc_html( $number_color_hover )
									),
								)
							);
						}
						if ( $active_number_color ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination .page-numbers.current',
									'declaration' => sprintf(
										'color: %1$s;',
										esc_html( $active_number_color )
									),
								)
							);
						}
						if ( $active_number_color_hover ) {
							self::set_style(
								$render_slug,
								array(
									'selector'    => '%%order_class%% .el-blog-pagination .page-numbers.current:hover',
									'declaration' => sprintf(
										'color: %1$s;',
										esc_html( $active_number_color_hover )
									),
								)
							);
						}
						$output .= et_core_intentionally_unescaped(
							paginate_links(
								array(
									'type'      => 'list',
									'prev_text' => et_core_esc_previously( $prev_text ),
									'next_text' => et_core_esc_previously( $next_text ),
									'current'   => max( 1, $el_dbe_page ),
									'total'     => intval( $total ),
								)
							),
							'html'
						);
					}

					$output .= '</div>';
				}
				remove_filter( 'get_pagenum_link', array( 'El_Blog_Module', 'filter_pagination_url' ), 10, 2 );
			}

			$output .= '</div> <!-- el-dbe-blog-extra -->';

		} else {
			if ( '' === trim( $no_results_text ) ) {
				$output = sprintf(
					'<div class="entry">
						<h2>%1$s</h2>
						<p>%2$s</p>
					</div>',
					esc_html__( 'No Results Found', 'divi-blog-extras' ),
					esc_html__( 'The posts you requested could not be found. Try changing your module settings or create some new posts.', 'divi-blog-extras' )
				);
			} else {
				$output = sprintf(
					'<div class="entry">
						%1$s
					</div>',
					esc_html( $no_results_text )
				);
			}
		}

		$whitelisted_animation_fields = array( 'ajax_load_more', 'numbered_pagination', 'filterable_categories' );
		$scroll_top_animation = $this->process_multiple_checkboxes_value( $scroll_top_animation, $whitelisted_animation_fields );

		$render_output = sprintf(
			'<div class="et_pb_posts et_pb_bg_layout_light" data-scroll-top-animation="%2$s">
                %1$s
                %3$s
            </div> <!-- et_pb_posts -->',
			$output,
			$scroll_top_animation,
			$is_slider ? $this->el_blog_render_slider_script() : ''
		);

		// Restore $wp_filter cached above.
		// phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
		$wp_filter = $wp_filter_cache;
		unset( $wp_filter_cache );

		self::$rendering = false;

		if ( 'off' === $include_current_taxonomy && 'on' === $use_category_filterable_blog && ! $is_slider ) {
			$options = array(
	            'normal' => array(
	                'filterable_category_background' => "%%order_class%% .el-dbe-post-categories li a:not(.el-dbe-active-category)",
	                'filterable_active_category_background' => "%%order_class%% .el-dbe-post-categories a.el-dbe-active-category",
	            ),
	        );

			$this->process_custom_background( $render_slug, $options );
		}

		$this->process_advanced_margin_padding_css( $this, $render_slug, $this->margin_padding );

		if ( $is_slider ) {
			if ( 'on' === $enable_coverflow_shadow ) {
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .swiper-container-3d .swiper-slide-shadow-left',
						'declaration' => sprintf( 'background-image: linear-gradient(to left,%1$s,rgba(0,0,0,0));', esc_attr( $coverflow_shadow_color ) ),
					)
				);
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .swiper-container-3d .swiper-slide-shadow-right',
						'declaration' => sprintf( 'background-image: linear-gradient(to right,%1$s,rgba(0,0,0,0));', esc_attr( $coverflow_shadow_color ) ),
					)
				);
			} else {
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .swiper-container-3d .swiper-slide-shadow-left, %%order_class%% .swiper-container-3d .swiper-slide-shadow-right',
						'declaration' => 'background-image: none;',
					)
				);
			}

			if ( 'on' === $show_control_dot ) {
				if ( $control_dot_inactive_color ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-pagination-bullet',
							'declaration' => sprintf( 'background: %1$s;', esc_attr( $control_dot_inactive_color ) ),
						)
					);

					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .transparent_dot .swiper-pagination-bullet',
							'declaration' => sprintf( 'border-color: %1$s;', esc_attr( $control_dot_inactive_color ) ),
						)
					);
				}

				if ( $control_dot_active_color ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-pagination-bullet.swiper-pagination-bullet-active',
							'declaration' => sprintf( 'background: %1$s;', esc_attr( $control_dot_active_color ) ),
						)
					);
				}

				if ( 'stretched_dot' === $control_dot_style && $slide_transition_duration ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .stretched_dot .swiper-pagination-bullet',
							'declaration' => sprintf( 'transition: all %1$sms ease;', intval( $slide_transition_duration ) ),
						)
					);
				}
			}

			if ( 'on' === $show_arrow ) {
				if ( $arrow_color ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev::after, %%order_class%% .swiper-button-next::after',
							'declaration' => sprintf( 'color: %1$s;', esc_attr( $arrow_color ) ),
						)
					);
				}

				if ( $arrow_color_hover ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev:hover::after, %%order_class%% .swiper-button-next:hover::after',
							'declaration' => sprintf( 'color: %1$s;', esc_attr( $arrow_color_hover ) ),
						)
					);
				}

				$arrow_font_size = et_pb_responsive_options()->get_property_values( $this->props, 'arrow_font_size' );
				if ( ! empty( array_filter( $arrow_font_size ) ) ) {
					et_pb_responsive_options()->generate_responsive_css( $arrow_font_size, '%%order_class%% .swiper-button-prev::after, %%order_class%% .swiper-button-next::after', 'font-size', $render_slug, '', 'range' );
				}

				if ( 'on' === $show_arrow_on_hover ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev',
							'declaration' => 'visibility: hidden; opacity: 0; transform: translateX(40px)translateY(-50%); transition: all 300ms ease;',
						)
					);
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-next',
							'declaration' => 'visibility: hidden; opacity: 0; transform: translateX(-40px)translateY(-50%); transition: all 300ms ease;',
						)
					);
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-container:hover .swiper-button-prev, %%order_class%% .swiper-container:hover .swiper-button-next',
							'declaration' => 'visibility: visible; opacity: 1; transform: translateX(0)translateY(-50%);',
						)
					);
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-container:hover .swiper-button-prev.swiper-button-disabled, %%order_class%% .swiper-container:hover .swiper-button-next.swiper-button-disabled',
							'declaration' => 'opacity: 0.35;',
						)
					);
				}

				if ( '' !== $arrow_background_color ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev::after, %%order_class%% .swiper-button-next::after',
							'declaration' => sprintf( 'background: %1$s;', esc_attr( $arrow_background_color ) ),
						)
					);
				}

				if ( '' !== $arrow_background_color_hover ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev:hover::after, %%order_class%% .swiper-button-next:hover::after',
							'declaration' => sprintf( 'background: %1$s;', esc_attr( $arrow_background_color_hover ) ),
						)
					);
				}

				if ( '' !== $arrow_background_border_size ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev::after, %%order_class%% .swiper-button-next::after',
							'declaration' => sprintf( 'border-width: %1$s;', esc_attr( $arrow_background_border_size ) ),
						)
					);
				}

				if ( '' !== $arrow_background_border_color ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev::after, %%order_class%% .swiper-button-next::after',
							'declaration' => sprintf( 'border-color: %1$s;', esc_attr( $arrow_background_border_color ) ),
						)
					);
				}

				if ( '' !== $arrow_background_border_color_hover ) {
					self::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .swiper-button-prev:hover::after, %%order_class%% .swiper-button-next:hover::after',
							'declaration' => sprintf( 'border-color: %1$s;', esc_attr( $arrow_background_border_color_hover ) ),
						)
					);
				}
			}

			if ( 'on' === $equalize_slides_height ) {
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .swiper-wrapper',
						'declaration' => 'align-items: stretch;',
					)
				);
				self::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .swiper-slide',
						'declaration' => 'height: auto;',
					)
				);
			}
		}

		return et_core_intentionally_unescaped( $render_output, 'html' );
	}

	/**
	 * This function dynamically creates script parameters according to the user settings
	 *
	 * @return string
	 * */
	public function el_blog_render_slider_script() {
		$order_class     			= $this->get_module_order_class( 'et_pb_blog_extras' );
		$slide_effect          		= esc_attr( $this->props['slide_effect'] );
		$show_arrow            		= esc_attr( $this->props['show_arrow'] );
		$show_control_dot          	= esc_attr( $this->props['show_control_dot'] );
		$loop                  		= esc_attr( $this->props['slider_loop'] );
		$autoplay              		= esc_attr( $this->props['autoplay'] );
		$autoplay_speed        		= intval( $this->props['autoplay_speed'] );
		$transition_duration  		= intval( $this->props['slide_transition_duration'] );
		$pause_on_hover        		= esc_attr( $this->props['pause_on_hover'] );
		$enable_coverflow_shadow 	= 'on' === $this->props['enable_coverflow_shadow'] ? 'true' : 'false';
		$coverflow_rotate 	   		= intval( $this->props['coverflow_rotate'] );
		$coverflow_depth 	   		= intval( $this->props['coverflow_depth'] );
		$slides_per_view 			= et_pb_responsive_options()->get_property_values( $this->props, 'slides_per_view', '', true );
		$space_between_slides 		= et_pb_responsive_options()->get_property_values( $this->props, 'space_between_slides', '', true );
		$slides_per_group 			= et_pb_responsive_options()->get_property_values( $this->props, 'slides_per_group', '', true );

		$autoplay_speed      		= '' !== $autoplay_speed || 0 !== $autoplay_speed ? $autoplay_speed : 3000;
		$transition_duration 		= '' !== $transition_duration || 0 !== $transition_duration ? $transition_duration : 1000;
		$loop          				= 'on' === $loop ? 'true' : 'false';
		$arrows 					= 'false';
		$dots 						= 'false';
		$autoplaySlides				= 0;
		$cube 						= 'false';
		$coverflow 					= 'false';
		$slidesPerGroup 			= 1;
		$slidesPerGroupSkip			= 0;
		$slidesPerGroup 			= 1;
		$slidesPerGroupIpad			= 1;
		$slidesPerGroupMobile		= 1;
		$slidesPerGroupSkip			= 0;
		$slidesPerGroupSkipIpad		= 0;
		$slidesPerGroupSkipMobile	= 0;

		if ( in_array( $slide_effect, array( 'slide', 'coverflow' ), true ) ) {
			$postsPerView        			= $slides_per_view['desktop'];
			$postsPerViewIpad   			= '' !== $slides_per_view['tablet'] ? $slides_per_view['tablet'] : $postsPerView;
			$postsPerViewMobile 			= '' !== $slides_per_view['phone'] ? $slides_per_view['phone'] : $postsPerViewIpad;
			$slidesSpaceBetween   			= $space_between_slides['desktop'];
			$slidesSpaceBetweenIpad  		= '' !== $space_between_slides['tablet'] ? $space_between_slides['tablet'] : $slidesSpaceBetween;
			$slidesSpaceBetweenMobile 		= '' !== $space_between_slides['phone'] ? $space_between_slides['phone'] : $slidesSpaceBetweenIpad;
			$slidesPerGroup 				= $slides_per_group['desktop'];
			$slidesPerGroupIpad				= '' !== $slides_per_group['tablet'] ? $slides_per_group['tablet'] : $slidesPerGroup;
			$slidesPerGroupMobile			= '' !== $slides_per_group['phone'] ? $slides_per_group['phone'] : $slidesPerGroupIpad;

			if ( $postsPerView > $slidesPerGroup && 1 !== $slidesPerGroup ) {
				$slidesPerGroupSkip = $postsPerView - $slidesPerGroup;
			}
			if ( $postsPerViewIpad > $slidesPerGroupIpad && 1 !== $slidesPerGroupIpad ) {
				$slidesPerGroupSkipIpad = $postsPerViewIpad - $slidesPerGroupIpad;
			}
			if ( $postsPerViewMobile > $slidesPerGroupMobile && 1 !== $slidesPerGroupMobile ) {
				$slidesPerGroupSkipMobile = $postsPerViewMobile - $slidesPerGroupMobile;
			}
		} else {
			$postsPerView        		= 1;
			$postsPerViewIpad   		= 1;
			$postsPerViewMobile 		= 1;
			$slidesSpaceBetween   		= 0;
			$slidesSpaceBetweenIpad		= 0;
			$slidesSpaceBetweenMobile	= 0;
		}

		if ( 'on' === $show_arrow ) {
			$arrows = "{    
                            nextEl: '." . esc_attr( $order_class ) . " .swiper-button-next',
                            prevEl: '." . esc_attr( $order_class ) . " .swiper-button-prev',
                    }";
		}

		if ( 'on' === $show_control_dot ) {
			$dots = "{
                        el: '." . esc_attr( $order_class ) . " .swiper-pagination',
                        clickable: true,
                    }";
		}

		if ( 'on' === $autoplay ) {
			if ( 'on' === $pause_on_hover ) {
				$autoplaySlides = '{
                                delay:' . $autoplay_speed . ',
                                disableOnInteraction: true,
                            }';
			} else {
				$autoplaySlides = '{
                                delay:' . $autoplay_speed . ',
                                disableOnInteraction: false,
                            }';
			}
		}

		if ( 'cube' === $slide_effect ) {
			$cube = '{
                        shadow: false,
                        slideShadows: false,
                    }';
		}

		if ( 'coverflow' === $slide_effect ) {
			$coverflow = '{
                            rotate: ' . $coverflow_rotate . ',
                            stretch: 0,
                            depth: ' . $coverflow_depth . ',
                            modifier: 1,
                            slideShadows : ' . $enable_coverflow_shadow . ',
                        }';
		}

		$script  = '<script type="text/javascript">';
		$script .= 'jQuery(function($) {';
		$script .= 'var ' . esc_attr( $order_class ) . '_swiper = new Swiper(\'.' . esc_attr( $order_class ) . ' .swiper-container\', {
                            slidesPerView: ' . $postsPerView . ',
                            autoplay: ' . $autoplaySlides . ',
                            spaceBetween: ' . intval( $slidesSpaceBetween ) . ',
                            slidesPerGroup: ' . $slidesPerGroup . ',
                            slidesPerGroupSkip: ' . $slidesPerGroupSkip . ',
                            effect: "' . $slide_effect . '",
                            cubeEffect: ' . $cube . ',
                            coverflowEffect: ' . $coverflow . ',
                            speed: ' . $transition_duration . ',
                            loop: ' . $loop . ',
                            pagination: ' . $dots . ',
                            navigation: ' . $arrows . ',
                            grabCursor: \'true\',
                            breakpoints: {
                            	981: {
		                          	slidesPerView: ' . $postsPerView . ',
		                          	spaceBetween: ' . intval( $slidesSpaceBetween ) . ',
                            		slidesPerGroup: ' . $slidesPerGroup . ',
                            		slidesPerGroupSkip: ' . $slidesPerGroupSkip . ',
		                        },
		                        768: {
		                          	slidesPerView: ' . $postsPerViewIpad . ',
		                          	spaceBetween: ' . intval( $slidesSpaceBetweenIpad ) . ',
		                          	slidesPerGroup: ' . $slidesPerGroupIpad . ',
                            		slidesPerGroupSkip: ' . $slidesPerGroupSkipIpad . ',
		                        },
		                        0: {
		                          	slidesPerView: ' . $postsPerViewMobile . ',
		                          	spaceBetween: ' . intval( $slidesSpaceBetweenMobile ) . ',
		                          	slidesPerGroup: ' . $slidesPerGroupMobile . ',
                            		slidesPerGroupSkip: ' . $slidesPerGroupSkipMobile . ',
		                        }
		                    },
                    });';

		if ( 'on' === $pause_on_hover && 'on' === $autoplay ) {
			$script .= '$(".' . esc_attr( $order_class ) . ' .swiper-container").on("mouseenter", function(e) {
							if ( typeof ' . esc_attr( $order_class ) . '_swiper.autoplay.stop === "function" ) {
								' . esc_attr( $order_class ) . '_swiper.autoplay.stop();
							}
                        });';
            $script .= '$(".' . esc_attr( $order_class ) . ' .swiper-container").on("mouseleave", function(e) {
        					if ( typeof ' . esc_attr( $order_class ) . '_swiper.autoplay.start === "function" ) {
                            	' . esc_attr( $order_class ) . '_swiper.autoplay.start();
                            }
                        });';
		}

		if ( 'true' !== $loop ) {
			$script .=  esc_attr( $order_class ) . '_swiper.on(\'reachEnd\', function(){
                            ' . esc_attr( $order_class ) . '_swiper.autoplay = false;
                        });';
		}

		$script .= '});</script>';

		return $script;
	}

	public static function filter_wp_kses_post_tags( $tags, $context ) {
		if ( 'post' === $context ) {
			if ( is_array( $tags ) ) {
				$iframe = array(
					'iframe' => array(
						'width' => true,
						'height' => true,
						'src' => true,
						'title' => true,
						'loading' => true,
						'allow' => true,
						'allowfullscreen' => true,
					)
				);
				array_merge( $tags, $iframe );
			}
		}
		return $tags;
	}

	public static function filter_pagination_url( $result, $pagenum ) {
		return add_query_arg( 'el_dbe_page', '', $result );
	}

	public function process_multiple_checkboxes_value( $value, $values = array() ) {
		if ( empty( $values ) && ! is_array( $values ) ) {
			return '';
		}
		
		$new_values = array();
		$value 		= explode( '|', $value );
		foreach( $value as $key => $val ) {
			if ( 'on' === strtolower( $val ) ) {
				array_push( $new_values, $values[$key] );
			}
		}
		return implode( ',', $new_values );
	}

	public function process_advanced_margin_padding_css( $module, $function_name, $margin_padding ) {
        $utils           = ET_Core_Data_Utils::instance();
        $all_values      = $module->props;
        $advanced_fields = $module->advanced_fields;

        // Disable if module doesn't set advanced_fields property and has no VB support.
        if ( ! $module->has_vb_support() && ! $module->has_advanced_fields ) {
            return;
        }

        $allowed_advanced_fields = array( 'blog_margin_padding', 'slider_margin_padding' );
        foreach ( $allowed_advanced_fields as $advanced_field ) {
            if ( ! empty( $advanced_fields[ $advanced_field ] ) ) {
                foreach ( $advanced_fields[ $advanced_field ] as $label => $form_field ) {
                    $margin_key  = "{$label}_custom_margin";
                    $padding_key = "{$label}_custom_padding";
                    if ( '' !== $utils->array_get( $all_values, $margin_key, '' ) || '' !== $utils->array_get( $all_values, $padding_key, '' ) ) {
                        $settings = $utils->array_get( $form_field, 'margin_padding', array() );
                        // Ensure main selector exists.
                        $form_field_margin_padding_css = $utils->array_get( $settings, 'css.main', '' );
                        if ( empty( $form_field_margin_padding_css ) ) {
                            $utils->array_set( $settings, 'css.main', $utils->array_get( $form_field, 'css.main', '' ) );
                        }

                        $margin_padding->update_styles( $module, $label, $settings, $function_name, $advanced_field );
                    }
                }
            }
        }
    }

    public function el_builder_processed_range_value( $result, $range, $range_string ) {
		if ( false !== strpos( $result, '0calc' ) ) {
			return $range;
		}
		return $result;
	}

	public function process_custom_background( $function_name, $options ) {

        $normal_fields = $options['normal'];
        
        foreach ( $normal_fields as $option_name => $element ) {
            
            $css_element           = $element;
            $css_element_processed = $element;

            if ( is_array( $element ) ) {
                $css_element_processed = implode( ', ', $element );
            }
            
            // Place to store processed background. It will be compared with the smaller device
            // background processed value to avoid rendering the same styles.
            $processed_background_color  = '';
            $processed_background_image  = '';
            $processed_background_blend  = '';
    
            // Store background images status because the process is extensive.
            $background_image_status = array(
                'desktop' => false,
                'tablet'  => false,
                'phone'   => false,
            );

            // Background Options Styling.
            foreach ( et_pb_responsive_options()->get_modes() as $device ) {
                $background_base_name = $option_name;
                $background_prefix    = "{$option_name}_";
                $background_style     = '';
                $is_desktop           = 'desktop' === $device;
                $suffix               = ! $is_desktop ? "_{$device}" : '';
    
                $background_color_style = '';
                $background_image_style = '';
                $background_images      = array();
    
                $has_background_color_gradient         = false;
                $has_background_image                  = false;
                $has_background_gradient_and_image     = false;
                $is_background_color_gradient_disabled = false;
                $is_background_image_disabled          = false;
    
                $background_color_gradient_overlays_image = 'off';
    
                // Ensure responsive is active.
                if ( ! $is_desktop && ! et_pb_responsive_options()->is_responsive_enabled( $this->props, "{$option_name}_color" ) ) {
                    continue;
                }

                // A. Background Gradient.
                $use_background_color_gradient = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}use_color_gradient", $device, $background_base_name, $this->fields_unprocessed );
    
                if ( 'on' === $use_background_color_gradient ) {
                    $background_color_gradient_overlays_image = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_overlays_image{$suffix}", '', true );
    
                    $gradient_properties = array(
                        'type'             => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_type{$suffix}", '', true ),
                        'direction'        => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_direction{$suffix}", '', true ),
                        'radial_direction' => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_direction_radial{$suffix}", '', true ),
                        'color_start'      => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_start{$suffix}", '', true ),
                        'color_end'        => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_end{$suffix}", '', true ),
                        'start_position'   => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_start_position{$suffix}", '', true ),
                        'end_position'     => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_end_position{$suffix}", '', true ),
                    );
    
                    // Save background gradient into background images list.
                    $background_images[] = $this->get_gradient( $gradient_properties );
    
                    // Flag to inform BG Color if current module has Gradient.
                    $has_background_color_gradient = true;
                } else if ( 'off' === $use_background_color_gradient ) {
                    $is_background_color_gradient_disabled = true;
                }
    
                // B. Background Image.
                $background_image = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}image", $device, $background_base_name, $this->fields_unprocessed );
                $parallax         = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}parallax{$suffix}", 'off' );
    
                // BG image and parallax status.
                $is_background_image_active         = '' !== $background_image && 'on' !== $parallax;
                $background_image_status[ $device ] = $is_background_image_active;
    
                if ( $is_background_image_active ) {
                    // Flag to inform BG Color if current module has Image.
                    $has_background_image = true;
    
                    // Check previous BG image status. Needed to get the correct value.
                    $is_prev_background_image_active = true;
                    if ( ! $is_desktop ) {
                        $is_prev_background_image_active = 'tablet' === $device ? $background_image_status['desktop'] : $background_image_status['tablet'];
                    }
    
                    // Size.
                    $background_size_default = ET_Builder_Element::$_->array_get( $this->fields_unprocessed, "{$background_prefix}size.default", '' );
                    $background_size         = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}size{$suffix}", $background_size_default, ! $is_prev_background_image_active );
            
                    if ( '' !== $background_size ) {
                        $background_style .= sprintf(
                            'background-size: %1$s; ',
                            esc_html( $background_size )
                        );
                    }
    
                    // Position.
                    $background_position_default = ET_Builder_Element::$_->array_get( $this->fields_unprocessed, "{$background_prefix}position.default", '' );
                    $background_position         = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}position{$suffix}", $background_position_default, ! $is_prev_background_image_active );
    
                    if ( '' !== $background_position ) {
                        $background_style .= sprintf(
                            'background-position: %1$s; ',
                            esc_html( str_replace( '_', ' ', $background_position ) )
                        );
                    }
    
                    // Repeat.
                    $background_repeat_default = ET_Builder_Element::$_->array_get( $this->fields_unprocessed, "{$background_prefix}repeat.default", '' );
                    $background_repeat         = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}repeat{$suffix}", $background_repeat_default, ! $is_prev_background_image_active );
    
                    if ( '' !== $background_repeat ) {
                        $background_style .= sprintf(
                            'background-repeat: %1$s; ',
                            esc_html( $background_repeat )
                        );
                    }
    
                    // Blend.
                    $background_blend_default = ET_Builder_Element::$_->array_get( $this->fields_unprocessed, "{$background_prefix}blend.default", '' );
                    $background_blend         = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}blend{$suffix}", $background_blend_default, ! $is_prev_background_image_active );
                    $background_blend_inherit = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}blend{$suffix}", '', true );
    
                    if ( '' !== $background_blend_inherit ) {
                        // Don't print the same image blend style.
                        if ( '' !== $background_blend ) {
                            $background_style .= sprintf(
                                'background-blend-mode: %1$s; ',
                                esc_html( $background_blend )
                            );
                        }
    
                        // Reset - If background has image and gradient, force background-color: initial.
                        if ( $has_background_color_gradient && $has_background_image && $background_blend_inherit !== $background_blend_default ) {
                            $has_background_gradient_and_image = true;
                            $background_color_style            = 'initial';
                            $background_style                  .= 'background-color: initial; ';
                        }
    
                        $processed_background_blend = $background_blend;
                    }
    
                    // Only append background image when the image is exist.
                    $background_images[] = sprintf( 'url(%1$s)', esc_html( $background_image ) );
                } else if ( '' === $background_image ) {
                    // Reset - If background image is disabled, ensure we reset prev background blend mode.
                    if ( '' !== $processed_background_blend ) {
                        $background_style .= 'background-blend-mode: normal; ';
                        $processed_background_blend = '';
                    }
    
                    $is_background_image_disabled = true;
                }
    
                if ( ! empty( $background_images ) ) {
                    // The browsers stack the images in the opposite order to what you'd expect.
                    if ( 'on' !== $background_color_gradient_overlays_image ) {
                        $background_images = array_reverse( $background_images );
                    }
    
                    // Set background image styles only it's different compared to the larger device.
                    $background_image_style = join( ', ', $background_images );
                    if ( $processed_background_image !== $background_image_style ) {
                        $background_style .= sprintf(
                            'background-image: %1$s !important;',
                            esc_html( $background_image_style )
                        );
                    }
                } else if ( ! $is_desktop && $is_background_color_gradient_disabled && $is_background_image_disabled ) {
                    // Reset - If background image and gradient are disabled, reset current background image.
                    $background_image_style = 'initial';
                    $background_style .= 'background-image: initial !important;';
                }
    
                // Save processed background images.
                $processed_background_image = $background_image_style;
    
                // C. Background Color.
                if ( ! $has_background_gradient_and_image ) {
                    // Background color `initial` was added by default to reset button background
                    // color when user disable it on mobile preview mode. However, it should
                    // be applied only when the background color is really disabled because user
                    // may use theme customizer to setup global button background color. We also
                    // need to ensure user still able to disable background color on mobile.
                    $background_color_enable  = ET_Builder_Element::$_->array_get( $this->props, "{$background_prefix}enable_color{$suffix}", '' );
                    $background_color_initial = 'off' === $background_color_enable && ! $is_desktop ? 'initial' : '';
    
                    $background_color       = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}color", $device, $background_base_name, $this->fields_unprocessed );
                    $background_color       = '' !== $background_color ? $background_color : $background_color_initial;
                    $background_color_style = $background_color;
                    
                    if ( '' !== $background_color && $processed_background_color !== $background_color ) {
                        $background_style .= sprintf(
                            'background-color: %1$s; ',
                            esc_html( $background_color )
                        );
                    }
                }
    
                // Save processed background color.
                $processed_background_color = $background_color_style;
    
                // Print background gradient and image styles.
                if ( '' !== $background_style ) {
                    $background_style_attrs = array(
                        'selector'    => $css_element_processed,
                        'declaration' => rtrim( $background_style ),
                        'priority'    => $this->_style_priority,
                    );
    
                    // Add media query attribute to background style attrs.
                    if ( 'desktop' !== $device ) {
                        $current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
                        $background_style_attrs['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
                    }
    
                    ET_Builder_Element::set_style( $function_name, $background_style_attrs );
                }
            }
            
        }

        if ( isset( $options['hover'] ) ) {
            $hover_fields = $options['hover'];
        } else {
            $hover_fields = $options['normal'];
            foreach ( $hover_fields as &$value ) {
                $value = $value . ':hover';
            }
        }

        foreach ( $hover_fields as $option_name => $element ) {

            $css_element           = $element;
            $css_element_processed = $element;
            
            if ( is_array( $element ) ) {
                $css_element_processed = implode( ', ', $element );
            }

            // Background Hover.
            if ( et_builder_is_hover_enabled( "{$option_name}_color", $this->props ) ) {

                $background_base_name       = $option_name;
                $background_prefix          = "{$option_name}_";
                $background_images_hover    = array();
                $background_hover_style     = '';

                $has_background_color_gradient_hover         = false;
                $has_background_image_hover                  = false;
                $has_background_gradient_and_image_hover     = false;
                $is_background_color_gradient_hover_disabled = false;
                $is_background_image_hover_disabled          = false;

                $background_color_gradient_overlays_image_desktop = et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_overlays_image", 'off', true );
    
                $gradient_properties_desktop = array(
                    'type'             => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_type", '', true ),
                    'direction'        => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_direction", '', true ),
                    'radial_direction' => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_direction_radial", '', true ),
                    'color_start'      => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_start", '', true ),
                    'color_end'        => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_end", '', true ),
                    'start_position'   => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_start_position", '', true ),
                    'end_position'     => et_pb_responsive_options()->get_any_value( $this->props, "{$background_prefix}color_gradient_end_position", '', true ),
                );

                $background_color_gradient_overlays_image_hover = 'off';

                // Background Gradient Hover.
                // This part is little bit different compared to other hover implementation. In
                // this case, hover is enabled on the background field, not on the each of those
                // fields. So, built in function get_value() doesn't work in this case.
                // Temporarily, we need to fetch the the value from get_raw_value().
                $use_background_color_gradient_hover = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}use_color_gradient", 'hover', $background_base_name, $this->fields_unprocessed );

                if ( 'on' === $use_background_color_gradient_hover ) {
                    // Desktop value as default.
                    $background_color_gradient_type_desktop             = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'type', '' );
                    $background_color_gradient_direction_desktop        = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'direction', '' );
                    $background_color_gradient_radial_direction_desktop = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'radial_direction', '' );
                    $background_color_gradient_color_start_desktop      = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'color_start', '' );
                    $background_color_gradient_color_end_desktop        = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'color_end', '' );
                    $background_color_gradient_start_position_desktop   = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'start_position', '' );
                    $background_color_gradient_end_position_desktop     = ET_Builder_Element::$_->array_get( $gradient_properties_desktop, 'end_position', '' );

                    // Hover value.
                    $background_color_gradient_type_hover             = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_type", $this->props, $background_color_gradient_type_desktop );
                    $background_color_gradient_direction_hover        = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_direction", $this->props, $background_color_gradient_direction_desktop );
                    $background_color_gradient_direction_radial_hover = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_direction_radial", $this->props, $background_color_gradient_radial_direction_desktop );
                    $background_color_gradient_start_hover            = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_start", $this->props, $background_color_gradient_color_start_desktop );
                    $background_color_gradient_end_hover              = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_end", $this->props, $background_color_gradient_color_end_desktop );
                    $background_color_gradient_start_position_hover   = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_start_position", $this->props, $background_color_gradient_start_position_desktop );
                    $background_color_gradient_end_position_hover     = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_end_position", $this->props, $background_color_gradient_end_position_desktop );
                    $background_color_gradient_overlays_image_hover   = et_pb_hover_options()->get_raw_value( "{$background_prefix}color_gradient_overlays_image", $this->props, $background_color_gradient_overlays_image_desktop );

                    $has_background_color_gradient_hover = true;

                    $gradient_values_hover = array(
                        'type'             => '' !== $background_color_gradient_type_hover ? $background_color_gradient_type_hover : $background_color_gradient_type_desktop,
                        'direction'        => '' !== $background_color_gradient_direction_hover ? $background_color_gradient_direction_hover : $background_color_gradient_direction_desktop,
                        'radial_direction' => '' !== $background_color_gradient_direction_radial_hover ? $background_color_gradient_direction_radial_hover : $background_color_gradient_radial_direction_desktop,
                        'color_start'      => '' !== $background_color_gradient_start_hover ? $background_color_gradient_start_hover : $background_color_gradient_color_start_desktop,
                        'color_end'        => '' !== $background_color_gradient_end_hover ? $background_color_gradient_end_hover : $background_color_gradient_color_end_desktop,
                        'start_position'   => '' !== $background_color_gradient_start_position_hover ? $background_color_gradient_start_position_hover : $background_color_gradient_start_position_desktop,
                        'end_position'     => '' !== $background_color_gradient_end_position_hover ? $background_color_gradient_end_position_hover : $background_color_gradient_end_position_desktop,
                    );

                    $background_images_hover[] = $this->get_gradient( $gradient_values_hover );
                } else if ( 'off' === $use_background_color_gradient_hover ) {
                    $is_background_color_gradient_hover_disabled = true;
                }

                // Background Image Hover.
                // This part is little bit different compared to other hover implementation. In
                // this case, hover is enabled on the background field, not on the each of those
                // fields. So, built in function get_value() doesn't work in this case.
                // Temporarily, we need to fetch the the value from get_raw_value().
                $background_image_hover = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}image", 'hover', $background_base_name, $this->fields_unprocessed );
                $parallax_hover         = et_pb_hover_options()->get_raw_value( "{$background_prefix}parallax", $this->props );

                if ( '' !== $background_image_hover && null !== $background_image_hover && 'on' !== $parallax_hover ) {
                    // Flag to inform BG Color if current module has Image.
                    $has_background_image_hover = true;

                    // Size.
                    $background_size_hover   = et_pb_hover_options()->get_raw_value( "{$background_prefix}size", $this->props );
                    $background_size_desktop = ET_Builder_Element::$_->array_get( $this->props, "{$background_prefix}size", '' );
                    $is_same_background_size = $background_size_hover === $background_size_desktop;
                    if ( empty( $background_size_hover ) && ! empty( $background_size_desktop ) ) {
                        $background_size_hover = $background_size_desktop;
                    }

                    if ( ! empty( $background_size_hover ) && ! $is_same_background_size ) {
                        $background_hover_style .= sprintf(
                            'background-size: %1$s; ',
                            esc_html( $background_size_hover )
                        );
                    }

                    // Position.
                    $background_position_hover   = et_pb_hover_options()->get_raw_value( "{$background_prefix}position", $this->props );
                    $background_position_desktop = ET_Builder_Element::$_->array_get( $this->props, "{$background_prefix}position", '' );
                    $is_same_background_position = $background_position_hover === $background_position_desktop;
                    if ( empty( $background_position_hover ) && ! empty( $background_position_desktop ) ) {
                        $background_position_hover = $background_position_desktop;
                    }

                    if ( ! empty( $background_position_hover ) && ! $is_same_background_position ) {
                        $background_hover_style .= sprintf(
                            'background-position: %1$s; ',
                            esc_html( str_replace( '_', ' ', $background_position_hover ) )
                        );
                    }

                    // Repeat.
                    $background_repeat_hover   = et_pb_hover_options()->get_raw_value( "{$background_prefix}repeat", $this->props );
                    $background_repeat_desktop = ET_Builder_Element::$_->array_get( $this->props, "{$background_prefix}repeat", '' );
                    $is_same_background_repeat = $background_repeat_hover === $background_repeat_desktop;
                    if ( empty( $background_repeat_hover ) && ! empty( $background_repeat_desktop ) ) {
                        $background_repeat_hover = $background_repeat_desktop;
                    }

                    if ( ! empty( $background_repeat_hover ) && ! $is_same_background_repeat ) {
                        $background_hover_style .= sprintf(
                            'background-repeat: %1$s; ',
                            esc_html( $background_repeat_hover )
                        );
                    }

                    // Blend.
                    $background_blend_hover = et_pb_hover_options()->get_raw_value( "{$background_prefix}blend", $this->props );
                    $background_blend_default = ET_Builder_Element::$_->array_get( $this->fields_unprocessed, "{$background_prefix}blend.default", '' );
                    $background_blend_desktop = ET_Builder_Element::$_->array_get( $this->props, "{$background_prefix}blend", '' );
                    $is_same_background_blend = $background_blend_hover === $background_blend_desktop;
                    if ( empty( $background_blend_hover ) && ! empty( $background_blend_desktop ) ) {
                        $background_blend_hover = $background_blend_desktop;
                    }

                    if ( ! empty( $background_blend_hover ) ) {
                        if ( ! $is_same_background_blend ) {
                            $background_hover_style .= sprintf(
                                'background-blend-mode: %1$s; ',
                                esc_html( $background_blend_hover )
                            );
                        }

                        // Force background-color: initial;
                        if ( $has_background_color_gradient_hover && $has_background_image_hover && $background_blend_hover !== $background_blend_default ) {
                            $has_background_gradient_and_image_hover = true;
                            $background_hover_style .= 'background-color: initial !important;';
                        }
                    }

                    // Only append background image when the image exists.
                    $background_images_hover[] = sprintf( 'url(%1$s)', esc_html( $background_image_hover ) );
                } else if ( '' === $background_image_hover ) {
                    $is_background_image_hover_disabled = true;
                }

                if ( ! empty( $background_images_hover ) ) {
                    // The browsers stack the images in the opposite order to what you'd expect.
                    if ( 'on' !== $background_color_gradient_overlays_image_hover ) {
                        $background_images_hover = array_reverse( $background_images_hover );
                    }

                    $background_hover_style .= sprintf(
                        'background-image: %1$s !important;',
                        esc_html( join( ', ', $background_images_hover ) )
                    );
                } else if ( $is_background_color_gradient_hover_disabled && $is_background_image_hover_disabled ) {
                    $background_hover_style .= 'background-image: initial !important;';
                }

                // Background Color Hover.
                if ( ! $has_background_gradient_and_image_hover ) {
                    $background_color_hover = et_pb_responsive_options()->get_inheritance_background_value( $this->props, "{$background_prefix}color", 'hover', $background_base_name, $this->fields_unprocessed );
                    $background_color_hover = '' !== $background_color_hover ? $background_color_hover : 'transparent';

                    if ( '' !== $background_color_hover ) {
                        $background_hover_style .= sprintf(
                            'background-color: %1$s !important; ',
                            esc_html( $background_color_hover )
                        );
                    }
                }

                // Print background hover gradient and image styles.
                if ( '' !== $background_hover_style ) {
                    $background_hover_style_attrs = array(
                        'selector'    => $css_element_processed,
                        'declaration' => rtrim( $background_hover_style ),
                        'priority'    => $this->_style_priority,
                    );

                    ET_Builder_Element::set_style( $function_name, $background_hover_style_attrs );
                }
            }
        }
    }

}

new El_Blog_Module();
