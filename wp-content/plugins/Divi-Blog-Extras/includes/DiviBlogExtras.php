<?php

class El_Blog_DiviBlogExtras extends DiviExtension {

	/**
	 * The gettext domain for the extension's translations.
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $gettext_domain = 'divi-blog-extras';

	/**
	 * The extension's WP Plugin name.
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $name = 'Divi-Blog-Extras';

	/**
	 * The extension's version
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $version = ELICUS_BLOG_VERSION;

	/**
	 * Elicus_DiviBlogExtras constructor.
	 *
	 * @param string $name
	 * @param array  $args
	 */
	public function __construct( $name = 'Divi-Blog-Extras', $args = array() ) {
		$this->plugin_dir        = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url    = plugin_dir_url( $this->plugin_dir );
		$this->_frontend_js_data = array(
			'ajaxurl'               => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'            => wp_create_nonce( 'elicus-blog-nonce' ),
			'et_theme_accent_color' => esc_html( et_get_option( 'accent_color', '#2ea3f2' ) ),
		);

		$filtered_taxonomies = array( 'post_format', 'et_post_format', 'yst_prominent_words', 'translation_priority', 'post_translations', 'language' );
		$filtered_taxonomies = apply_filters( 'el_blog_filter_taxonomies', $filtered_taxonomies );
		$filtered_taxonomies = wp_json_encode( $filtered_taxonomies );
		if ( ! defined( 'ELICUS_BLOG_FILTERED_TAXONOMIES' ) ) {
			define( 'ELICUS_BLOG_FILTERED_TAXONOMIES', $filtered_taxonomies );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'el_blog_register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'el_blog_enqueue_scripts' ) );

		parent::__construct( $name, $args );

		$this->el_plugin_setup();
		
		add_action( 'init', array( $this, 'el_blog_load_plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . ELICUS_BLOG_BASENAME, array( $this, 'el_add_blog_action_links' ) );
		add_action( 'init', array( $this, 'el_register_taxonomy_meta' ) );
		add_action( 'wp_loaded', array( $this, 'el_add_taxonomy_meta_fields' ) );
		add_action( 'wp_ajax_el_load_posts', array( $this, 'el_load_blog_posts' ) );
		add_action( 'init', array( $this, 'el_enable_archive_template' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'el_blog_admin_scripts' ) );
		add_filter( 'pre_get_posts', array( $this, 'el_fix_pagination' ), 999 );
	}

	/**
	 * Elicus_DiviBlogExtras plugin setup function.
	 */
	public function el_plugin_setup() {
		require_once $this->plugin_dir . 'src/functions.php';
		require_once $this->plugin_dir . 'panel/init.php';
		if ( is_admin() ) {
			require_once $this->plugin_dir . 'src/class-update.php';
		}
	}

	public function el_blog_load_plugin_textdomain() {
    	load_plugin_textdomain( 'divi-blog-extras', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	public function el_blog_admin_scripts() {
		global $pagenow;
		if ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) {
			$bundle_url = "{$this->plugin_dir_url}scripts/frontend-bundle.min.js";
			wp_enqueue_style( "{$this->name}-admin-style", "{$this->plugin_dir_url}styles/style-admin.min.css", array( 'wp-color-picker' ), $this->version, false );
			wp_enqueue_script( "{$this->name}-frontend-bundle", $bundle_url, array( 'jquery', 'wp-color-picker' ), $this->version, true );
		}
	}

	public function el_blog_register_scripts() {
        wp_register_script( 'elicus-isotope-script', "{$this->plugin_dir_url}scripts/isotope.pkgd.min.js", array('jquery'), '3.0.6', true );
        wp_register_script( 'elicus-images-loaded-script', "{$this->plugin_dir_url}scripts/imagesloaded.pkgd.min.js", array('jquery'), '4.1.4', true );
        wp_register_script( 'elicus-swiper-script', "{$this->plugin_dir_url}scripts/swiper.min.js", array('jquery'), '6.4.5', true );
        wp_register_style( 'elicus-swiper-style', "{$this->plugin_dir_url}styles/swiper.min.css", array(), '6.4.5', false );
    }

	public function el_blog_enqueue_scripts() {
		if ( et_core_is_fb_enabled() ) {
			wp_enqueue_script( 'elicus-isotope-script' );
			wp_enqueue_script( 'elicus-images-loaded-script' );
			wp_enqueue_style( 'elicus-swiper-style' );
		}
	}

	public function el_add_blog_action_links( $links ) {
		$settings = array( '<a href="' . esc_url( admin_url( '/options-general.php?page=divi-blog-extras-options/' ) ) . '">' . esc_html__( 'Settings', 'divi-blog-extras' ) . '</a>' );
		return array_merge( $settings, $links );
	}

	/**
	 * Filters the main query paged arg to avoid pagination clashes with the module pagination.
	 */
	public function el_fix_pagination( $query ) {
		// phpcs:ignore WordPress,NonceVerification,NoNonce.
		if ( isset( $_GET['el_dbe_page'] ) && $query->is_main_query() ) {
			$query->set( 'el_dbe_page', $query->get( 'paged' ) );
			$query->set( 'paged', 0 );
		}
	}

	public function el_register_taxonomy_meta() {
		register_meta( 'term', 'el_term_color', '' );
		register_meta( 'term', 'el_term_hover_color', '' );
		register_meta( 'term', 'el_term_bgcolor', '' );
		register_meta( 'term', 'el_term_hover_bgcolor', '' );
	}

	public function el_add_taxonomy_meta_fields() {
		$plugin_options = get_option( ELICUS_BLOG_OPTION );
		if ( isset( $plugin_options['enable-blog-custom-posts'] ) && 'on' === $plugin_options['enable-blog-custom-posts'] ) {
			if ( isset( $plugin_options['blog-custom-posts'] ) && ! empty( $plugin_options['blog-custom-posts'] ) ) {
				$posts      = array_map( 'esc_html', array_merge( array( 'post' ), explode( ',', $plugin_options['blog-custom-posts'] ) ) );
				$taxonomies = get_object_taxonomies( $posts, 'objects' );
				if ( ! empty( $taxonomies ) ) {
					$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES );
					foreach ( $taxonomies as $taxonomy_key => $taxonomy ) {
						if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
							add_action( "{$taxonomy_key}_add_form_fields", array( $this, 'el_term_color_field' ) );
							add_action( "{$taxonomy_key}_edit_form_fields", array( $this, 'el_edit_term_color_field' ) );
							add_action( "edit_{$taxonomy_key}", array( $this, 'el_save_term_color' ) );
							add_action( "create_{$taxonomy_key}", array( $this, 'el_save_term_color' ) );
							add_filter( "manage_edit-{$taxonomy_key}_columns", array( $this, 'el_edit_term_columns' ) );
							add_filter( "manage_{$taxonomy_key}_custom_column", array( $this, 'el_manage_term_custom_column' ), 10, 3 );
						}
					}
				}
			} else {
				add_action( 'category_add_form_fields', array( $this, 'el_term_color_field' ) );
				add_action( 'category_edit_form_fields', array( $this, 'el_edit_term_color_field' ) );
				add_action( 'edit_category', array( $this, 'el_save_term_color' ) );
				add_action( 'create_category', array( $this, 'el_save_term_color' ) );
				add_filter( 'manage_edit-category_columns', array( $this, 'el_edit_term_columns' ) );
				add_filter( 'manage_category_custom_column', array( $this, 'el_manage_term_custom_column' ), 10, 3 );
			}
		} else {
			add_action( 'category_add_form_fields', array( $this, 'el_term_color_field' ) );
			add_action( 'category_edit_form_fields', array( $this, 'el_edit_term_color_field' ) );
			add_action( 'edit_category', array( $this, 'el_save_term_color' ) );
			add_action( 'create_category', array( $this, 'el_save_term_color' ) );
			add_filter( 'manage_edit-category_columns', array( $this, 'el_edit_term_columns' ) );
			add_filter( 'manage_category_custom_column', array( $this, 'el_manage_term_custom_column' ), 10, 3 );
		}
	}

	public function el_term_color_field() {

		?>
		<div class="form-field el-term-color-wrap">
			<label for="el-term-color"><?php esc_html_e( 'Text Color', 'divi-blog-extras' ); ?></label>
			<input type="text" name="el_term_color" id="el-term-color" value="" class="el-term-color-field" />
		</div>
		<div class="form-field el-term-color-wrap">
			<label for="el-term-hover-color"><?php esc_html_e( 'Hover Text Color', 'divi-blog-extras' ); ?></label>
			<input type="text" name="el_term_hover_color" id="el-term-hover-color" value="" class="el-term-color-field" />
		</div>
		<div class="form-field el-term-color-wrap">
			<label for="el-term-bgcolor"><?php esc_html_e( 'Background Color', 'divi-blog-extras' ); ?></label>
			<input type="text" name="el_term_bgcolor" id="el-term-bgcolor" value="" class="el-term-color-field" />
		</div>
		<div class="form-field el-term-color-wrap">
			<label for="el-term-hover-bgcolor"><?php esc_html_e( 'Hover Background Color', 'divi-blog-extras' ); ?></label>
			<input type="text" name="el_term_hover_bgcolor" id="el-term-hover-bgcolor" value="" class="el-term-color-field" />
		</div>
		<?php
		wp_nonce_field( 'el-term-color-nonce', 'term_color_nonce' );
	}

	public function el_edit_term_color_field( $term ) {

		$color         = get_term_meta( $term->term_id, 'el_term_color', true );
		$hover_color   = get_term_meta( $term->term_id, 'el_term_hover_color', true );
		$bgcolor       = get_term_meta( $term->term_id, 'el_term_bgcolor', true );
		$hover_bgcolor = get_term_meta( $term->term_id, 'el_term_hover_bgcolor', true );

		?>
		<tr class="form-field el-term-color-wrap">
			<th scope="row"><label for="el-term-color"><?php esc_html_e( 'Text Color', 'divi-blog-extras' ); ?></label></th>
			<td>
				<input type="text" name="el_term_color" id="el-term-color" value="<?php echo esc_attr( $color ); ?>" class="el-term-color-field" />
			</td>
		</tr>
		<tr class="form-field el-term-color-wrap">
			<th scope="row"><label for="el-term-hover-color"><?php esc_html_e( 'Text Color Hover', 'divi-blog-extras' ); ?></label></th>
			<td>
				<input type="text" name="el_term_hover_color" id="el-term-hover-color" value="<?php echo esc_attr( $hover_color ); ?>" class="el-term-color-field" />
			</td>
		</tr>
		<tr class="form-field el-term-color-wrap">
			<th scope="row"><label for="el-term-bgcolor"><?php esc_html_e( 'Background Color', 'divi-blog-extras' ); ?></label></th>
			<td>
				<input type="text" name="el_term_bgcolor" id="el-term-bgcolor" value="<?php echo esc_attr( $bgcolor ); ?>" class="el-term-color-field" />
			</td>
		</tr>
		<tr class="form-field el-term-color-wrap">
			<th scope="row"><label for="el-term-hover-bgcolor"><?php esc_html_e( 'Background Color Hover', 'divi-blog-extras' ); ?></label></th>
			<td>
				<input type="text" name="el_term_hover_bgcolor" id="el-term-hover-bgcolor" value="<?php echo esc_attr( $hover_bgcolor ); ?>" class="el-term-color-field" />
			</td>
		</tr>
		<?php
		wp_nonce_field( 'el-term-color-nonce', 'term_color_nonce' );
	}

	public function el_save_term_color( $term_id ) {

		if ( ! isset( $_POST['term_color_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['term_color_nonce'] ) ), 'el-term-color-nonce' ) ) {
			return;
		}

		$color         = isset( $_POST['el_term_color'] ) ? sanitize_text_field( wp_unslash( $_POST['el_term_color'] ) ) : '';
		$hover_color   = isset( $_POST['el_term_hover_color'] ) ? sanitize_text_field( wp_unslash( $_POST['el_term_hover_color'] ) ) : '';
		$bgcolor       = isset( $_POST['el_term_bgcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['el_term_bgcolor'] ) ) : '';
		$hover_bgcolor = isset( $_POST['el_term_hover_bgcolor'] ) ? sanitize_text_field( wp_unslash( $_POST['el_term_hover_bgcolor'] ) ) : '';

		update_term_meta( $term_id, 'el_term_color', $color );
		update_term_meta( $term_id, 'el_term_hover_color', $hover_color );
		update_term_meta( $term_id, 'el_term_bgcolor', $bgcolor );
		update_term_meta( $term_id, 'el_term_hover_bgcolor', $hover_bgcolor );

	}

	public function el_edit_term_columns( $columns ) {

		$columns['term_color']         = esc_html__( 'Text Color', 'divi-blog-extras' );
		$columns['term_hover_color']   = esc_html__( 'Hover Text Color', 'divi-blog-extras' );
		$columns['term_bgcolor']       = esc_html__( 'Background Color', 'divi-blog-extras' );
		$columns['term_hover_bgcolor'] = esc_html__( 'Hover Background Color', 'divi-blog-extras' );

		return $columns;
	}

	public function el_manage_term_custom_column( $out, $column, $term_id ) {

		if ( 'term_color' === $column ) {
			$color = get_term_meta( $term_id, 'el_term_color', true );
			$out   = sprintf( '<span class="el-term-color-block" style="background: %s;"></span>', esc_attr( $color ) );
		}

		if ( 'term_hover_color' === $column ) {
			$hover_color = get_term_meta( $term_id, 'el_term_hover_color', true );
			$out         = sprintf( '<span class="el-term-color-block" style="background: %s;"></span>', esc_attr( $hover_color ) );
		}

		if ( 'term_bgcolor' === $column ) {
			$bgcolor = get_term_meta( $term_id, 'el_term_bgcolor', true );
			$out     = sprintf( '<span class="el-term-color-block" style="background: %s;"></span>', esc_attr( $bgcolor ) );
		}

		if ( 'term_hover_bgcolor' === $column ) {
			$hover_bgcolor = get_term_meta( $term_id, 'el_term_hover_bgcolor', true );
			$out           = sprintf( '<span class="el-term-color-block" style="background: %s;"></span>', esc_attr( $hover_bgcolor ) );
		}

		return $out;
	}

	public function el_load_blog_posts() {
		if ( ! isset( $_POST['el_dbe_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['el_dbe_nonce'] ) ), 'elicus-blog-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$current_page = isset( $_POST['current_page'] ) ?
						intval( wp_unslash( $_POST['current_page'] ) ) :
						1;

		$post_category = isset( $_POST['post_category'] ) ?
						 intval( wp_unslash( $_POST['post_category'] ) ) :
						 0;

		// Sanitizing $_POST['props'] in below foreach loop as it contains json values.
		$props = isset( $_POST['props'] ) ?
				// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized
				json_decode( rawurldecode( wp_unslash( $_POST['props'] ) ), true ) :
				array();

		if ( empty( $props ) ) {
			return;
		}

		$defaults = array(
			'blog_layout'              		=> 'grid_extended',
			'masonry'						=> false,
			'post_type'                		=> 'post',
			'posts_number'             		=> '10',
			'offset_number'            		=> '0',
			'meta_date'                		=> 'M j, Y',
			'post_order'               		=> 'DESC',
			'post_order_by'            		=> 'date',
			'include_current_taxonomy'  	=> 'off',
			'current_taxonomies_relation' 	=> 'OR',
			'taxonomies_relation'			=> 'OR',
			'show_thumbnail'           		=> 'on',
			'show_content'             		=> 'off',
			'show_more'                		=> 'on',
			'show_author'              		=> 'on',
			'show_date'                		=> 'on',
			'show_categories'          		=> 'on',
			'show_comments'            		=> 'on',
			'show_read_time'           		=> 'on',
			'read_time_text'				=> 'min read',
			'show_load_more'           		=> 'off',
			'excerpt_length'           		=> '',
			'read_more_text'           		=> 'Read More',
			'pagination_type'          		=> 'on',
			'use_wp_pagenavi'          		=> 'off',
			'load_more_text'           		=> '',
			'show_less_text'           		=> '',
			'prev_text'                		=> '',
			'next_text'                		=> '',
			'custom_ajax_pagination'   		=> 'off',
			'ajax_pagination_use_icon' 		=> 'on',
			'ajax_pagination_icon'     		=> '',
			'use_read_more_button'     		=> 'off',
			'custom_read_more'         		=> 'off',
			'read_more_use_icon'       		=> 'off',
			'read_more_icon'           		=> '',
			'show_social_icons'        		=> 'off',
			'use_overlay'              		=> 'off',
			'hover_icon'               		=> '',
			'featured_image_size'      		=> 'large',
			'image_position'          		=> 'top',
			'category_meta_colors'     		=> 'off',
			'category_background_color' 	=> '',
			'animation'                		=> 'off',
			'header_level'             		=> 'h2',
			'is_single'                		=> false,
			'is_search'                		=> false,
			'is_user_logged_in'        		=> false,
			'current_post_id'          		=> '',
			'total_page'               		=> '1',
			'post_taxonomy'            		=> '',
			'post_taxonomy_term'       		=> '',
			'author'                   		=> '',
			'tag'                      		=> '',
			'year'                     		=> '',
			'month'                    		=> '',
			'day'                      		=> '',
		);

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

		foreach ( $defaults as $key => $default ) {
			${$key} = esc_html( et_()->array_get( $props, $key, $default ) );
		}
		
		$category_background    = $category_background_color;
		$processed_header_level = isset( $header_level ) ? esc_html( $header_level ) : esc_html( 'h2' );
		$valid_heading_tag      = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

		if ( ! in_array( $processed_header_level, $valid_heading_tag, true ) ) {
			$processed_header_level = esc_html( 'h2' );
		}

		$overlay_class = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';

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

		if ( 'on' === $show_load_more && 'on' === $pagination_type ) {
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

		if ( 'on' === $show_load_more && 'off' === $pagination_type ) {
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

		$query_args = array(
			'post_type'      => sanitize_text_field( $post_type ),
			'posts_per_page' => intval( $posts_number ),
			'post_status'    => 'publish',
			'offset'         => 0,
			'orderby'        => sanitize_text_field( $post_order_by ),
			'order'          => sanitize_text_field( $post_order ),
		);

		if ( $is_user_logged_in ) {
			$query_args['post_status'] = array( 'publish', 'private' );
		}

		if ( '' !== $post_taxonomy && '' !== $post_taxonomy_term ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => sanitize_text_field( $post_taxonomy ),
					'field'    => 'term_id',
					'terms'    => intval( $post_taxonomy_term ),
					'operator' => 'IN',
				),
			);
		} else if ( ! $is_search ) {
			if ( 'off' === $include_current_taxonomy ) {
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
											'taxonomy' => $taxonomy_key,
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

		if ( '' !== $author ) {
			$query_args['author'] = intval( $author );
		}

		if ( '' !== $tag ) {
			$query_args['tag_id'] = intval( $tag );
		}

		if ( '' !== $year ) {
			$query_args['year'] = sanitize_text_field( $year );
		}

		if ( '' !== $month ) {
			$query_args['monthnum'] = sanitize_text_field( $month );
		}

		if ( '' !== $day ) {
			$query_args['day'] = sanitize_text_field( $day );
		}

		if ( $is_single && ! isset( $args['post__not_in'] ) ) {
			if ( '' !== $current_post_id ) {
				if ( 'on' === $include_current_taxonomy ) {
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
				$query_args['post__not_in'] = array( intval( $current_post_id ) );
			}
		}

		if ( 'on' === $show_load_more && 'off' === $pagination_type && 'on' === $use_wp_pagenavi && function_exists( 'wp_pagenavi' ) ) {
			$query_args['paged'] = $current_page;
		}

		$query_args['offset'] = ( ( intval( $current_page ) - 1 ) * intval( $posts_number ) ) + intval( $offset_number );

		if ( $is_search ) {
			$query_args['post_type'] 	= 'any';
			if ( isset( $_GET['s'] ) ) {
				$query_args['s'] 		= sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}
		}

		if ( 'post' === $query_args['post_type'] && 0 !== absint( $post_category ) ) {
			$tax_query      = array();
			$plugin_options = get_option( ELICUS_BLOG_OPTION );
			if ( isset( $plugin_options['enable-blog-custom-taxonomies'] ) && 'on' === $plugin_options['enable-blog-custom-taxonomies'] ) {
				$post_type_taxonomies = get_object_taxonomies( $query_args['post_type'], 'names' );
				if ( ! empty( $post_type_taxonomies ) ) {
					$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES, false );
					foreach ( $post_type_taxonomies as $taxonomy_key ) {
						if ( ! in_array( $taxonomy_key, $filtered_taxonomies, true ) ) {
							if ( 'category' !== $taxonomy_key ) {
								$taxonomy_index = 'include_' . str_replace( '-', '_', $taxonomy_key );
								// Whitelisted and escaped above in $defaults.
								if ( isset( ${$taxonomy_index} ) && ! empty( ${$taxonomy_index} ) ) {
									array_push(
										$tax_query,
										array(
											'taxonomy' => $taxonomy_key,
											'field'    => 'term_id',
											'terms'    => array_map( 'intval', explode( ',', ${$taxonomy_index} ) ),
											'operator' => 'IN',
										)
									);
								}
							}
						}
					}
				}
			}
			if ( -1 === $post_category ) {
				if ( isset( $include_categories ) && ! empty( $include_categories ) ) {
					array_push(
						$tax_query,
						array(
	                        'taxonomy' => 'category',
	                        'field'    => 'term_id',
	                        'terms'    => array_map( 'intval', explode( ',', $include_categories ) ),
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
                        'terms'    => absint( $post_category ),
                        'operator' => 'IN'
                    )
				);
			}
            if ( ! empty( $tax_query ) ) {
				if ( count( $tax_query ) > 1 ) {
					$tax_query['relation'] = sanitize_text_field( $taxonomies_relation );
				}
				$query_args['tax_query'] = $tax_query;
			}
        }

		$query = new WP_Query( $query_args );

		if ( $masonry ) {
			$output = '<div class="el-isotope-item-gutter"></div>';
		} else {
			$output = '';
		}

		if ( $query->have_posts() ) {

			if ( 'post' === $query_args['post_type'] && 0 !== absint( $post_category ) ) {
				if ( '' !== $offset_number && ! empty( $offset_number ) ) {
					$total_page = intval( ceil( ( $query->found_posts - $offset_number ) / $query_args['posts_per_page'] ) );
				} else {
					$total_page = intval( ceil( ( $query->found_posts ) / $query_args['posts_per_page'] ) );
				}
			}

			if ( 'block_extended' === $blog_layout ) {
				$counter = ( intval( $current_page ) * intval( $posts_number ) ) + intval( $offset_number ) + 1;
			}

			while ( $query->have_posts() ) {
				$query->the_post();

				$post_id        = intval( get_the_ID() );
				$thumb          = '';
				$image_class    = '';
				$date_class     = '';
				$thumb          = el_get_post_thumbnail( $post_id, esc_html( $featured_image_size ), 'et_pb_post_main_image' );
				$no_thumb_class = ( '' === $thumb || 'off' === $show_thumbnail ) ? ' et_pb_no_thumb' : '';
				$layout_class   = ' el_dbe_' . $blog_layout;

				if ( 'on' === $show_load_more && 'on' === $pagination_type ) {
					$animation = ( 'off' === $animation ) && ( 1 < $current_page ) ? 'bottom' : $animation;
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
							$read_more_button = el_blog_render_button(
								array(
									'button_text'         => et_core_esc_previously( $read_more_text ),
									'button_text_escaped' => true,
									'button_url'          => esc_url( get_permalink( $post_id ) ),
									'button_custom'       => et_core_esc_previously( $custom_read_more ),
									'custom_icon'         => et_core_esc_previously( $read_more_icon ),
									'has_wrapper'         => false,
								)
							);
						}
					}
				}

				$classes = array_map( 'sanitize_html_class', get_post_class( 'et_pb_post et_pb_post_extra et_pb_text_align_left ' . $date_class . $animation_class . $layout_class . $no_thumb_class . $overlay_class . $image_class ) );

				$post_class = implode( ' ', $classes );

				if ( 'on' === $show_load_more && 'on' === $pagination_type ) {
					$post_class = $post_class . ' et-animated';
				}

				if ( $masonry ) {
					$output .= '<div class="el-isotope-item">';
				}

				$output .= '<article id="post-' . $post_id . '" class="' . esc_attr( $post_class ) . '" >';

				if ( file_exists( get_stylesheet_directory() . '/divi-blog-extras/layouts/' . sanitize_file_name( $blog_layout ) . '.php' ) ) {
					include get_stylesheet_directory() . '/divi-blog-extras/layouts/' . sanitize_file_name( $blog_layout ) . '.php';
				} elseif ( file_exists( $this->plugin_dir . 'modules/BlogExtras/layouts/' . sanitize_file_name( $blog_layout ) . '.php' ) ) {
					include $this->plugin_dir . 'modules/BlogExtras/layouts/' . sanitize_file_name( $blog_layout ) . '.php';
				}

				$output .= '</article> <!-- et_pb_post_extra -->';

				if ( $masonry ) {
					$output .= '</div> <!-- el-isotope-item -->';
				}

				if ( 'block_extended' === $blog_layout ) {
					$counter++;
				}
			}

			wp_reset_postdata();

			$pagination = '';
			if ( 'on' === $show_load_more ) {
				// Pagination.
				if ( 'on' === $pagination_type ) {
					// Load more Pagination.
					$total_page = intval( $total_page );
					if ( $total_page > 1 ) {
						$load_more_page = $current_page < $total_page ? ( $current_page + 1 ) : 1;
						$button_text    = $current_page < $total_page ? $load_more_text : $show_less_text;
						$button_classes = array(
							'el-pagination-button',
							'el-button',
							'et-waypoint',
							'et_pb_animation_bottom',
							'et-animated',
						);

						if ( $current_page < $total_page ) {
							array_push( $button_classes, 'el-load-more' );
						} else {
							array_push( $button_classes, 'el-show-less' );
						}

						$pagenum_link     = get_pagenum_link( $load_more_page );
						$load_more_button = el_blog_render_button(
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
						$pagination          .= '<div class="ajax-pagination">';
						$pagination          .= et_core_intentionally_unescaped( $load_more_button, 'html' );
						$pagination          .= '</div>';
					}
				} else {
					// Numbered Pagination.
					$pagination .= '<div class="el-blog-pagination">';

					if ( 'on' === $use_wp_pagenavi && function_exists( 'wp_pagenavi' ) ) {
						$pagination .= et_core_intentionally_unescaped(
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
						$pagination .= et_core_intentionally_unescaped(
							paginate_links(
								array(
									'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
									'format'    => '?paged=%#%',
									'type'      => 'list',
									'prev_text' => et_core_esc_previously( $prev_text ),
									'next_text' => et_core_esc_previously( $next_text ),
									'current'   => max( 1, $current_page ),
									'total'     => intval( $total_page ),
								)
							),
							'html'
						);
					}

					$pagination .= '</div>';
				}
			}
		}

		$result = array(
			et_core_intentionally_unescaped( $output, 'html' ),
			et_core_intentionally_unescaped( $pagination, 'html' )
		);

		wp_send_json( $result );
		exit;
	}

	public function el_enable_archive_template() {
		$plugin_options = get_option( ELICUS_BLOG_OPTION );
		$templates      = array( 'blog', 'taxonomy', 'tag', 'author', 'date' );
		$flag           = 0;
		$template_count = count( $templates );
		for ( $i = 0; $i < $template_count; $i++ ) {
			if ( isset( $plugin_options[ 'enable-' . $templates[ $i ] . '-archive-layout' ] ) ) {
				$archive = $plugin_options[ 'enable-' . $templates[ $i ] . '-archive-layout' ];
				if ( 'on' === $archive ) {
					$flag = 1;
				}
			}
		}
		if ( 1 === $flag ) {
			add_filter( 'archive_template', array( $this, 'el_get_blog_archive_template' ) );
			add_filter( 'body_class', array( $this, 'el_archive_body_classes' ), 10, 2 );
		}
	}

	public function el_get_blog_archive_template( $archive_template ) {
		$plugin_options = get_option( ELICUS_BLOG_OPTION );

		if ( isset( $plugin_options['enable-blog-archive-layout'] ) ) {
			$archive = $plugin_options['enable-blog-archive-layout'];
			if ( 'on' === $archive && isset( $plugin_options['blog-archive-layout'] ) && '' !== $plugin_options['blog-archive-layout'] ) {
				if ( false !== get_post_status( $plugin_options['blog-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['blog-archive-layout'] ) ) {
					$archive_template = is_category() ? $this->plugin_dir . 'archive/category.php' : $archive_template;
				}
			}
		}

		if ( isset( $plugin_options['enable-taxonomy-archive-layout'] ) ) {
			$archive = $plugin_options['enable-taxonomy-archive-layout'];
			if ( 'on' === $archive && isset( $plugin_options['variable-taxonomy-archive-layout'] ) && '' !== $plugin_options['variable-taxonomy-archive-layout'] ) {
				$variable_taxonomy_archive_layout = wp_specialchars_decode( $plugin_options['variable-taxonomy-archive-layout'], ENT_COMPAT );
				$variable_taxonomy_archive_layout = wp_unslash( $variable_taxonomy_archive_layout );
				$variable_taxonomy_archive_layout = json_decode( $variable_taxonomy_archive_layout, true );
				for ( $i = 1; $i <= $variable_taxonomy_archive_layout['counter']; $i++ ) {
					$taxonomy_fields      = explode( ',', $variable_taxonomy_archive_layout['fields'] );
					$taxonomy_name_option = $taxonomy_fields[0] . '-' . $i;
					if ( isset( $plugin_options[ $taxonomy_name_option ] ) ) {
						$taxonomy = $plugin_options[ $taxonomy_name_option ];
						if ( taxonomy_exists( $taxonomy ) ) {
							$taxonomy_layout_option = $taxonomy_fields[1] . '-' . $i;
							if ( isset( $plugin_options[ $taxonomy_layout_option ] ) && false !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) && 'trash' !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) ) {
								$archive_template = is_tax( $taxonomy ) ? $this->plugin_dir . 'archive/taxonomy.php' : $archive_template;
							}
						}
					}
				}
			}

			if ( 'on' === $archive && isset( $plugin_options['taxonomies-archive-layout'] ) && '' !== $plugin_options['taxonomies-archive-layout'] ) {
				$archive_template = is_tax() ? $this->plugin_dir . 'archive/taxonomy.php' : $archive_template;
			}
		}

		if ( isset( $plugin_options['enable-tag-archive-layout'] ) ) {
			$archive = $plugin_options['enable-tag-archive-layout'];
			if ( 'on' === $archive && isset( $plugin_options['tag-archive-layout'] ) && '' !== $plugin_options['tag-archive-layout'] ) {
				if ( false !== get_post_status( $plugin_options['tag-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['tag-archive-layout'] ) ) {
					$archive_template = is_tag() ? $this->plugin_dir . 'archive/tag.php' : $archive_template;
				}
			}
		}

		if ( isset( $plugin_options['enable-author-archive-layout'] ) ) {
			$archive = $plugin_options['enable-author-archive-layout'];
			if ( 'on' === $archive && isset( $plugin_options['author-archive-layout'] ) && '' !== $plugin_options['author-archive-layout'] ) {
				if ( false !== get_post_status( $plugin_options['author-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['author-archive-layout'] ) ) {
					$archive_template = is_author() ? $this->plugin_dir . 'archive/author.php' : $archive_template;
				}
			}
		}

		if ( isset( $plugin_options['enable-date-archive-layout'] ) ) {
			$archive = $plugin_options['enable-date-archive-layout'];
			if ( 'on' === $archive && isset( $plugin_options['date-archive-layout'] ) && '' !== $plugin_options['date-archive-layout'] ) {
				if ( false !== get_post_status( $plugin_options['date-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['date-archive-layout'] ) ) {
					$archive_template = is_date() ? $this->plugin_dir . 'archive/date.php' : $archive_template;
				}
			}
		}

		return $archive_template;
	}

	public function el_archive_body_classes( $classes, $class ) {

		$plugin_options = get_option( ELICUS_BLOG_OPTION );

		if ( is_category() && isset( $plugin_options['blog-archive-layout'] ) && '' !== $plugin_options['blog-archive-layout'] ) {
			if ( false !== get_post_status( $plugin_options['blog-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['blog-archive-layout'] ) ) {
				$classes = $this->el_set_archive_body_classes( 'blog-archive-layout-type', $classes );
			}
		}

		if ( is_tax() ) {
			$plugin_options          = get_option( ELICUS_BLOG_OPTION );
			$taxonomy_sidebar_option = '';
			$object                  = get_queried_object();
			$flag                    = 0;
			if ( isset( $plugin_options['variable-taxonomy-archive-layout'] ) && '' !== $plugin_options['variable-taxonomy-archive-layout'] ) {
				$variable_taxonomy_archive_layout = wp_specialchars_decode( $plugin_options['variable-taxonomy-archive-layout'], ENT_COMPAT );
				$variable_taxonomy_archive_layout = wp_unslash( $variable_taxonomy_archive_layout );
				$variable_taxonomy_archive_layout = json_decode( $variable_taxonomy_archive_layout, true );
				for ( $i = 1; $i <= $variable_taxonomy_archive_layout['counter']; $i++ ) {
					$taxonomy_fields      = explode( ',', $variable_taxonomy_archive_layout['fields'] );
					$taxonomy_name_option = $taxonomy_fields[0] . '-' . $i;
					if ( isset( $plugin_options[ $taxonomy_name_option ] ) ) {
						$taxonomy               = $plugin_options[ $taxonomy_name_option ];
						$taxonomy_layout_option = $taxonomy_fields[1] . '-' . $i;
						if ( isset( $plugin_options[ $taxonomy_layout_option ] ) && false !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) && 'trash' !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) ) {
							$flag = 1;
							if ( $taxonomy === $object->taxonomy ) {
								$taxonomy_sidebar_option = $taxonomy_fields[2] . '-' . $i;
							}
						}
					}
				}
			}

			if ( '' === $taxonomy_sidebar_option ) {
				if ( isset( $plugin_options['taxonomies-archive-layout'] ) && false !== get_post_status( $plugin_options['taxonomies-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['taxonomies-archive-layout'] ) ) {
					$taxonomy_sidebar_option = 'taxonomies-archive-layout-type';
					$classes                 = $this->el_set_archive_body_classes( $taxonomy_sidebar_option, $classes );
				}
			}

			if ( 1 === $flag ) {
				$classes = $this->el_set_archive_body_classes( $taxonomy_sidebar_option, $classes );
			}
		}

		if ( is_tag() && isset( $plugin_options['tag-archive-layout'] ) && '' !== $plugin_options['tag-archive-layout'] ) {
			if ( false !== get_post_status( $plugin_options['tag-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['tag-archive-layout'] ) ) {
				$classes = $this->el_set_archive_body_classes( 'tag-archive-layout-type', $classes );
			}
		}

		if ( is_author() && isset( $plugin_options['author-archive-layout'] ) && '' !== $plugin_options['author-archive-layout'] ) {
			if ( false !== get_post_status( $plugin_options['author-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['author-archive-layout'] ) ) {
				$classes = $this->el_set_archive_body_classes( 'author-archive-layout-type', $classes );
			}
		}

		if ( is_date() && isset( $plugin_options['date-archive-layout'] ) && '' !== $plugin_options['date-archive-layout'] ) {
			if ( false !== get_post_status( $plugin_options['date-archive-layout'] ) && 'trash' !== get_post_status( $plugin_options['date-archive-layout'] ) ) {
				$classes = $this->el_set_archive_body_classes( 'date-archive-layout-type', $classes );
			}
		}

		$classes[] = 'el-divi-blog-extras-archive-template';

		return $classes;
	}

	public function el_set_archive_body_classes( $archive, $classes ) {
		$plugin_options = get_option( ELICUS_BLOG_OPTION );
		if ( isset( $plugin_options[ $archive ] ) ) {
			if ( 'full-width' !== $plugin_options[ $archive ] ) {
				$classes[] = 'el-divi-blog-extras-archive-has-sidebar';
				if ( 'left-sidebar' === $plugin_options[ $archive ] ) {
					$key = array_search( 'et_right_sidebar', $classes, true );
					if ( false !== $key ) {
						unset( $classes[ $key ] );
						$classes[] = 'et_left_sidebar';
					}
				} else {
					$key = array_search( 'et_left_sidebar', $classes, true );
					if ( false !== $key ) {
						unset( $classes[ $key ] );
						$classes[] = 'et_right_sidebar';
					}
				}
			} else {
				$key = array_search( 'et_right_sidebar', $classes, true );
				if ( false !== $key ) {
					unset( $classes[ $key ] );
				}
				$key = array_search( 'et_left_sidebar', $classes, true );
				if ( false !== $key ) {
					unset( $classes[ $key ] );
				}
				$classes[] = 'et_pb_pagebuilder_layout';
			}
		}
		return $classes;
	}
}
new El_Blog_DiviBlogExtras();
