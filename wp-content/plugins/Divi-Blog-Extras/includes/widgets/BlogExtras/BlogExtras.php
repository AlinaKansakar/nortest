<?php
class El_Blog_Widget extends WP_Widget {

	public function __construct() {
		// Instantiate the parent object.
		parent::__construct(
			'divi_blog_extras',
			esc_html( 'Divi Blog Extras' ),
			array( 'description' => esc_html__( 'An advanced blog widget that gives you control on how to display suggested posts in the sidebar.', 'divi-blog-extras' ) )
		);
		add_action( 'admin_enqueue_scripts', array( &$this, 'widget_admin_enqueue_scripts' ) );
	}

	public function widget_admin_enqueue_scripts( $hook_suffix ) {
		if ( 'widgets.php' === $hook_suffix ) {
			wp_enqueue_style( 'el-blog-widget-style', plugins_url( 'styles/blog-widget.min.css', dirname( __FILE__ ) ), array( 'wp-color-picker' ), ELICUS_BLOG_VERSION );
			wp_enqueue_script( 'el-blog-widget-script', plugins_url( 'scripts/blog-widget.min.js', dirname( __FILE__ ) ), array( 'jquery', 'wp-color-picker' ), ELICUS_BLOG_VERSION, true );
		}
	}

	public function widget( $args, $instance ) {
		// Widget output.
		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		$image_shape_class = esc_attr( $instance['image_shape'] );

		$posts = '<div class="el-blog-widget">';

		if ( 'list' === $instance['layout'] ) {
			$list_query_args = array(
				'post_type'      => 'post',
				'posts_per_page' => intval( $instance['number_of_posts'] ),
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'offset'		 => isset( $instance['list_offset'] ) ? intval( $instance['list_offset'] ) : 0,
			);

			if ( is_user_logged_in() ) {
				$list_query_args['post_status'] = array( 'publish', 'private' );
			}

			if ( is_single() ) {
				$list_query_args['post__not_in'] = array( intval( get_queried_object_id() ) );
			}

			if ( ! empty( $instance['list_category'] ) ) {
				$list_query_args['tax_query'] = array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'category',
						'field'    => 'term_id',
						'terms'    => array_map( 'sanitize_text_field', $instance['list_category'] ),
						'operator' => 'IN',
					),
				);
			}

			$query = new WP_Query( $list_query_args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$post_id       = intval( get_the_ID() );
					$image_class   = 'yes' !== $instance['image'] || ! has_post_thumbnail() ? ' no-image' : '';
					$image         = '';
					$post_meta     = '';
					$excerpt 	   = '';
					$category_list = '';

					if ( 'yes' === $instance['image'] && has_post_thumbnail() ) {
						$image = sprintf(
							'<div class="el-single-post-thumbnail"><a href="%1$s">%2$s</a></div>',
							esc_url( get_permalink() ),
							get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => $image_shape_class ) )
						);
					}

					if ( 'yes' === $instance['show_excerpt'] ) {
						$excerpt = sprintf(
							'<p class="el-single-post-excerpt">%1$s</p>',
							get_the_excerpt()
						);
					}

					$title = sprintf(
						'<h5 class="post-title"><a href="%1$s">%2$s</a></h5>',
						esc_url( get_permalink() ),
						esc_html( get_the_title() )
					);

					if ( 'yes' === $instance['category'] ) {
						$categories = get_the_category( $post_id );
						if ( ! is_wp_error( $categories ) ) {
							$category_list = array();
							foreach ( $categories as $category ) {
								array_push( $category_list, '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="category tag">' . esc_html( $category->name ) . '</a>' );
							}
							$category_list = implode( ', ', $category_list );
						}
					}

					if ( 'yes' === $instance['author'] || 'yes' === $instance['date'] || 'yes' === $instance['category'] ) {
						$post_meta = sprintf(
							'<p class="el-single-post-meta">%1$s%2$s%3$s%4$s%5$s</p>',
							(
								'yes' === $instance['author']
								? sprintf(
									'<span class="author vcard"><a href="%1$s" target="_blank">%2$s</a></span>',
									esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
									esc_html( get_the_author() )
								)
								: ''
							),
							(
								'yes' === $instance['author'] && 'yes' === $instance['date']
								? ' | '
								: ''

							),
							(
								'yes' === $instance['date']
								? sprintf(
									'<span class="date">%1$s</span>',
									esc_html( get_the_date() )
								)
								: ''
							),
							(
								'yes' === $instance['date'] && 'yes' === $instance['category'] && '' !== $category_list
								? ' | '
								: ''

							),
							(
								'yes' === $instance['category'] && '' !== $category_list
								? sprintf(
									'<span class="category">%1$s</span>',
									et_core_intentionally_unescaped( $category_list, 'html' )
								)
								: ''
							)
						);
					}

					$posts .= sprintf(
						'<div id="%1$s" class="el-single-post%2$s">
                                            %3$s
                                            <div class="el-single-post-data">
                                            %4$s
                                            %5$s
                                            %6$s
                                            </div>
                                        </div>',
						esc_html( 'el-single-post-' . $post_id ),
						et_core_esc_previously( $image_class ),
						et_core_intentionally_unescaped( $image, 'html' ),
						et_core_intentionally_unescaped( $title, 'html' ),
						et_core_intentionally_unescaped( $excerpt, 'html' ),
						et_core_intentionally_unescaped( $post_meta, 'html' )
					);
				}
				wp_reset_postdata();

			} else {
				$posts  = '<h1>' . esc_html__( 'No Results Found', 'divi-blog-extras' ) . '</h1>';
				$posts .= '<p>' . esc_html__( 'The post you requested could not be found. Try changing your widget settings or add some new posts.', 'divi-blog-extras' ) . '</p>';
			}
		} else {
			$number_of_tabs = intval( $instance['number_of_tabs'] );
			$tab_items      = '';

			for ( $i = 1; $i <= $number_of_tabs; $i++ ) {
				$active     = 1 === $i ? ' active' : '';
				$tab_items .= sprintf(
					'<li class="%1$s%2$s">%3$s</li>',
					esc_attr( sanitize_title_with_dashes( esc_attr( $instance[ 'tab_' . $i . '_title' ] ), '', 'save' ) . '-tab' ),
					esc_attr( $active ),
					(
						'' !== esc_attr( $instance[ 'tab_' . $i . '_title' ] )
						? esc_html( $instance[ 'tab_' . $i . '_title' ] )
						: esc_html( 'Tab ' . $i )
					)
				);
			}

			$posts .= sprintf(
				'<ul class="el-blog-widget-tabs col-%1$s">%2$s</ul>',
				esc_attr( $number_of_tabs ),
				et_core_intentionally_unescaped( $tab_items, 'html' )
			);

			$posts .= '<div class="el-blog-widget-tabbed-posts">';

			for ( $i = 1; $i <= $number_of_tabs; $i++ ) {

				${'tab_' . $i . '_args'} = array(
					'post_type'      => 'post',
					'posts_per_page' => intval( $instance['number_of_posts'] ),
					'post_status'    => 'publish',
					'orderby'        => 'date',
					'order'          => 'DESC',
					'offset'		 => isset( $instance[ 'tab_' . $i . '_offset' ] ) ? intval( $instance[ 'tab_' . $i . '_offset' ] ) : 0,
				);

				if ( is_single() ) {
					${'tab_' . $i . '_args'}['post__not_in'] = array( intval( get_queried_object_id() ) );
				}

				if ( ! empty( $instance[ 'tab_' . $i . '_category' ] ) ) {
					${'tab_' . $i . '_args'}['tax_query'] = array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'category',
							'field'    => 'term_id',
							'terms'    => array_map( 'sanitize_text_field', $instance[ 'tab_' . $i . '_category' ] ),
							'operator' => 'IN',
						),
					);
				}

				${'tab_' . $i . '_query'} = new WP_Query( ${'tab_' . $i . '_args'} );

				$active = 1 === $i ? ' active' : '';

				if ( ${'tab_' . $i . '_query'}->have_posts() ) {

					$posts .= sprintf(
						'<div class="%1$s blog-widget-tab-content%2$s">',
						esc_html( sanitize_title_with_dashes( esc_attr( $instance[ 'tab_' . $i . '_title' ] ), '', 'save' ) . '-tab-content' ),
						$active
					);

					while ( ${'tab_' . $i . '_query'}->have_posts() ) {
						${'tab_' . $i . '_query'}->the_post();

						$post_id       = intval( get_the_ID() );
						$image_class   = 'yes' !== $instance['image'] || ( ! has_post_thumbnail() ) ? ' no-image' : '';
						$image         = '';
						$post_meta     = '';
						$excerpt 	   = '';
						$category_list = '';

						if ( 'yes' === $instance['image'] && has_post_thumbnail() ) {
							$image = sprintf(
								'<div class="el-single-post-thumbnail"><a href="%1$s">%2$s</a></div>',
								esc_url( get_permalink() ),
								get_the_post_thumbnail( $post_id, 'thumbnail', array( 'class' => $image_shape_class ) )
							);
						}

						if ( 'yes' === $instance['show_excerpt'] ) {
							$excerpt = sprintf(
								'<p class="el-single-post-excerpt">%1$s</p>',
								get_the_excerpt()
							);
						}

						$title = sprintf(
							'<h5 class="post-title"><a href="%1$s">%2$s</a></h5>',
							esc_url( get_permalink() ),
							esc_html( get_the_title() )
						);

						if ( 'yes' === $instance['category'] ) {
							$categories = get_the_category( $post_id );
							if ( ! is_wp_error( $categories ) ) {
								$category_list = array();
								foreach ( $categories as $category ) {
									array_push( $category_list, '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" rel="category tag">' . esc_html( $category->name ) . '</a>' );
								}
								$category_list = implode( ', ', $category_list );
							}
						}

						if ( 'yes' === $instance['author'] || 'yes' === $instance['date'] || 'yes' === $instance['category'] ) {
							$post_meta = sprintf(
								'<p class="el-single-post-meta">%1$s%2$s%3$s%4$s%5$s</p>',
								(
									'yes' === $instance['author']
									? sprintf(
										'<span class="author vcard"><a href="%1$s" target="_blank">%2$s</a></span>',
										esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
										esc_html( get_the_author() )
									)
									: ''
								),
								(
									'yes' === $instance['author'] && 'yes' === $instance['date']
									? ' | '
									: ''

								),
								(
									'yes' === $instance['date']
									? sprintf(
										'<span class="date">%1$s</span>',
										esc_html( get_the_date() )
									)
									: ''
								),
								(
									'yes' === $instance['date'] && 'yes' === $instance['category'] && '' !== $category_list
									? ' | '
									: ''

								),
								(
									'yes' === $instance['category'] && '' !== $category_list
									? sprintf(
										'<span class="category">%1$s</span>',
										et_core_intentionally_unescaped( $category_list, 'html' )
									)
									: ''
								)
							);
						}

						$posts .= sprintf(
							'<div id="%1$s" class="el-single-post%2$s">
                                                %3$s
                                                <div class="el-single-post-data">
                                                %4$s
                                                %5$s
                                                %6$s
                                                </div>
                                            </div>',
							esc_html( 'el-single-post-' . $post_id ),
							esc_attr( $image_class ),
							et_core_intentionally_unescaped( $image, 'html' ),
							et_core_intentionally_unescaped( $title, 'html' ),
							et_core_intentionally_unescaped( $excerpt, 'html' ),
							et_core_intentionally_unescaped( $post_meta, 'html' )
						);
					}
					$posts .= '</div>';
					wp_reset_postdata();
				}
			}
			$posts .= '</div>';
		}

		$posts .= '</div>';

		$output = sprintf(
			'<div class="el_dbe_widget_posts %1$s">
                %2$s
            </div> <!-- el_dbe_widget_posts -->',
			esc_attr( $instance['layout'] ),
			et_core_intentionally_unescaped( $posts, 'html' )
		);

		$widget_id = '#' . esc_attr( $args['widget_id'] );

		$styles = '<style>';

		if ( ! empty( $instance['title_color'] ) ) {
			$styles .= $widget_id . ' .el-blog-widget .post-title,' . $widget_id . ' .el-blog-widget .post-title a { color: ' . esc_attr( $instance['title_color'] ) . ' }';
		}

		if ( ! empty( $instance['title_hover_color'] ) ) {
			$styles .= $widget_id . ' .el-blog-widget .post-title:hover,' . $widget_id . ' .el-blog-widget .post-title a:hover { color: ' . esc_attr( $instance['title_hover_color'] ) . ' }';
		}

		if ( ! empty( $instance['meta_color'] ) ) {
			$styles .= $widget_id . ' .el-blog-widget .el-single-post-meta span,' . $widget_id . ' .el-blog-widget .el-single-post-meta a { color: ' . esc_attr( $instance['meta_color'] ) . ' }';
		}

		if ( ! empty( $instance['meta_hover_color'] ) ) {
			$styles .= $widget_id . ' .el-blog-widget .el-single-post-meta span:hover,' . $widget_id . ' .el-blog-widget .el-single-post-meta a:hover { color: ' . esc_attr( $instance['meta_hover_color'] ) . ' }';
		}

		if ( ! empty( $instance['post_separator_color'] ) ) {
			$styles .= $widget_id . ' .el-blog-widget .el-single-post { border-bottom-color: ' . esc_attr( $instance['post_separator_color'] ) . ' }';
		}

		if ( 'tabbed' === $instance['layout'] ) {
			if ( ! empty( $instance['tab_background_color'] ) ) {
				$styles .= $widget_id . ' .el-blog-widget .el-blog-widget-tabs { background: ' . esc_attr( $instance['tab_background_color'] ) . ' }';
			}

			if ( ! empty( $instance['tab_color'] ) ) {
				$styles .= $widget_id . ' .el-blog-widget .el-blog-widget-tabs > li { color: ' . esc_attr( $instance['tab_color'] ) . ' }';
			}

			if ( ! empty( $instance['active_tab_background_color'] ) ) {
				$styles .= $widget_id . ' .el-blog-widget .el-blog-widget-tabs > li.active { background: ' . esc_attr( $instance['active_tab_background_color'] ) . ' }';
			}

			if ( ! empty( $instance['active_tab_color'] ) ) {
				$styles .= $widget_id . ' .el-blog-widget .el-blog-widget-tabs > li.active { color: ' . esc_attr( $instance['active_tab_color'] ) . ' }';
			}

			if ( ! empty( $instance['tab_border_color'] ) ) {
				$styles .= $widget_id . ' .el-blog-widget .el-blog-widget-tabs > li { border-right-color: ' . esc_attr( $instance['tab_border_color'] ) . ' }';
			}
		}
		$styles .= '</style>';

		$output .= $styles;
		echo et_core_intentionally_unescaped( $output, 'html' );

		echo wp_kses_post( $args['after_widget'] );
	}

	public function update( $new_instance, $old_instance ) {
		// Save widget options.
		$instance                                = array();
		$instance['title']                       = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['number_of_posts']             = isset( $new_instance['number_of_posts'] ) ? sanitize_text_field( $new_instance['number_of_posts'] ) : '';
		$instance['show_excerpt']             	 = isset( $new_instance['show_excerpt'] ) ? sanitize_text_field( $new_instance['show_excerpt'] ) : '';
		$instance['list_offset']             	 = isset( $new_instance['list_offset'] ) ? sanitize_text_field( $new_instance['list_offset'] ) : 0;
		$instance['number_of_tabs']              = isset( $new_instance['number_of_tabs'] ) ? sanitize_text_field( $new_instance['number_of_tabs'] ) : '';
		$instance['layout']                      = isset( $new_instance['layout'] ) ? sanitize_text_field( $new_instance['layout'] ) : '';
		$instance['image']                       = isset( $new_instance['image'] ) ? sanitize_text_field( $new_instance['image'] ) : '';
		$instance['image_shape']                 = isset( $new_instance['image_shape'] ) ? sanitize_text_field( $new_instance['image_shape'] ) : '';
		$instance['author']                      = isset( $new_instance['author'] ) ? sanitize_text_field( $new_instance['author'] ) : '';
		$instance['category']                    = isset( $new_instance['category'] ) ? sanitize_text_field( $new_instance['category'] ) : '';
		$instance['date']                        = isset( $new_instance['date'] ) ? sanitize_text_field( $new_instance['date'] ) : '';
		$instance['title_color']                 = isset( $new_instance['title_color'] ) ? sanitize_text_field( $new_instance['title_color'] ) : '';
		$instance['title_hover_color']           = isset( $new_instance['title_hover_color'] ) ? sanitize_text_field( $new_instance['title_hover_color'] ) : '';
		$instance['meta_color']                  = isset( $new_instance['meta_color'] ) ? sanitize_text_field( $new_instance['meta_color'] ) : '';
		$instance['meta_hover_color']            = isset( $new_instance['meta_hover_color'] ) ? sanitize_text_field( $new_instance['meta_hover_color'] ) : '';
		$instance['post_separator_color']        = isset( $new_instance['post_separator_color'] ) ? sanitize_text_field( $new_instance['post_separator_color'] ) : '';
		$instance['tab_background_color']        = isset( $new_instance['tab_background_color'] ) ? sanitize_text_field( $new_instance['tab_background_color'] ) : '';
		$instance['tab_color']                   = isset( $new_instance['tab_color'] ) ? sanitize_text_field( $new_instance['tab_color'] ) : '';
		$instance['active_tab_background_color'] = isset( $new_instance['active_tab_background_color'] ) ? sanitize_text_field( $new_instance['active_tab_background_color'] ) : '';
		$instance['active_tab_color']            = isset( $new_instance['active_tab_color'] ) ? sanitize_text_field( $new_instance['active_tab_color'] ) : '';
		$instance['tab_border_color']            = isset( $new_instance['tab_border_color'] ) ? sanitize_text_field( $new_instance['tab_border_color'] ) : '';
		$instance['list_category']               = isset( $new_instance['list_category'] ) ? array_map( 'intval', $new_instance['list_category'] ) : array();
		$instance['tab_1_category']              = isset( $new_instance['tab_1_category'] ) ? array_map( 'intval', $new_instance['tab_1_category'] ) : array();
		$instance['tab_2_category']              = isset( $new_instance['tab_2_category'] ) ? array_map( 'intval', $new_instance['tab_2_category'] ) : array();
		$instance['tab_3_category']              = isset( $new_instance['tab_3_category'] ) ? array_map( 'intval', $new_instance['tab_3_category'] ) : array();
		$instance['tab_1_title']                 = isset( $new_instance['tab_1_title'] ) ? sanitize_text_field( $new_instance['tab_1_title'] ) : '';
		$instance['tab_2_title']                 = isset( $new_instance['tab_2_title'] ) ? sanitize_text_field( $new_instance['tab_2_title'] ) : '';
		$instance['tab_3_title']                 = isset( $new_instance['tab_3_title'] ) ? sanitize_text_field( $new_instance['tab_3_title'] ) : '';
		$instance['tab_1_offset']                = isset( $new_instance['tab_1_offset'] ) ? sanitize_text_field( $new_instance['tab_1_offset'] ) : '';
		$instance['tab_2_offset']                = isset( $new_instance['tab_2_offset'] ) ? sanitize_text_field( $new_instance['tab_2_offset'] ) : '';
		$instance['tab_3_offset']                = isset( $new_instance['tab_3_offset'] ) ? sanitize_text_field( $new_instance['tab_3_offset'] ) : '';

		return $instance;
	}

	public function form( $instance ) {
		// Output admin widget options form.
		$title                       = isset( $instance['title'] ) ? $instance['title'] : '';
		$number_of_posts             = isset( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : '';
		$show_excerpt				 = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : 'no';
		$list_offset             	 = isset( $instance['list_offset'] ) ? $instance['list_offset'] : 0;
		$number_of_tabs              = isset( $instance['number_of_tabs'] ) ? $instance['number_of_tabs'] : '';
		$layout                      = isset( $instance['layout'] ) ? $instance['layout'] : '';
		$image                       = ( isset( $instance['image'] ) && 'yes' === $instance['image'] ) ? 'checked="checked"' : '';
		$author                      = ( isset( $instance['author'] ) && 'yes' === $instance['author'] ) ? 'checked=checked' : '';
		$category                    = ( isset( $instance['category'] ) && 'yes' === $instance['category'] ) ? 'checked=checked' : '';
		$date                        = ( isset( $instance['date'] ) && 'yes' === $instance['date'] ) ? 'checked=checked' : '';
		$image_shape                 = isset( $instance['image_shape'] ) ? $instance['image_shape'] : '';
		$title_color                 = isset( $instance['title_color'] ) ? $instance['title_color'] : '';
		$title_hover_color           = isset( $instance['title_hover_color'] ) ? $instance['title_hover_color'] : '';
		$meta_color                  = isset( $instance['meta_color'] ) ? $instance['meta_color'] : '';
		$meta_hover_color            = isset( $instance['meta_hover_color'] ) ? $instance['meta_hover_color'] : '';
		$post_separator_color        = isset( $instance['post_separator_color'] ) ? $instance['post_separator_color'] : '';
		$tab_background_color        = isset( $instance['tab_background_color'] ) ? $instance['tab_background_color'] : '';
		$tab_color                   = isset( $instance['tab_color'] ) ? $instance['tab_color'] : '';
		$active_tab_background_color = isset( $instance['active_tab_background_color'] ) ? $instance['active_tab_background_color'] : '';
		$active_tab_color            = isset( $instance['active_tab_color'] ) ? $instance['active_tab_color'] : '';
		$tab_border_color            = isset( $instance['tab_border_color'] ) ? $instance['tab_border_color'] : '';
		$list_category               = isset( $instance['list_category'] ) ? $instance['list_category'] : array();
		$tab_1_category              = isset( $instance['tab_1_category'] ) ? $instance['tab_1_category'] : array();
		$tab_2_category              = isset( $instance['tab_2_category'] ) ? $instance['tab_2_category'] : array();
		$tab_3_category              = isset( $instance['tab_3_category'] ) ? $instance['tab_3_category'] : array();
		$tab_1_title                 = isset( $instance['tab_1_title'] ) ? $instance['tab_1_title'] : '';
		$tab_2_title                 = isset( $instance['tab_2_title'] ) ? $instance['tab_2_title'] : '';
		$tab_3_title                 = isset( $instance['tab_3_title'] ) ? $instance['tab_3_title'] : '';
		$tab_1_offset                = isset( $instance['tab_1_offset'] ) ? $instance['tab_1_offset'] : 0;
		$tab_2_offset                = isset( $instance['tab_2_offset'] ) ? $instance['tab_2_offset'] : 0;
		$tab_3_offset                = isset( $instance['tab_3_offset'] ) ? $instance['tab_3_offset'] : 0;

		$tab_1_category 			 = ! is_array( $tab_1_category ) ? array( $tab_1_category ) : $tab_1_category;
		$tab_2_category 			 = ! is_array( $tab_2_category ) ? array( $tab_2_category ) : $tab_2_category;
		$tab_3_category 		  	 = ! is_array( $tab_3_category ) ? array( $tab_3_category ) : $tab_3_category;
		$list_category				 = ! is_array( $list_category )	? array( $list_category ) : $list_category;

		$layouts = array(
			'list'   => esc_html__( 'List', 'divi-blog-extras' ),
			'tabbed' => esc_html__( 'Tabbed', 'divi-blog-extras' ),
		);

		$shapes = array(
			'square' => esc_html__( 'Square', 'divi-blog-extras' ),
			'round'  => esc_html__( 'Round', 'divi-blog-extras' ),
		);

		$yes_no = array(
			'yes' => esc_html__( 'Yes', 'divi-blog-extras' ),
			'no'  => esc_html__( 'No', 'divi-blog-extras' ),
		);

		$tabs = array(
			'2' => esc_html__( '2', 'divi-blog-extras' ),
			'3' => esc_html__( '3', 'divi-blog-extras' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
			)
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'divi-blog-extras' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p class="blog-widget-layout">
			<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout:', 'divi-blog-extras' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
			<?php
			foreach ( $layouts as $key => $value ) {
				$selected = $key === $layout ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<?php $tabbed_active = 'tabbed' === $layout ? ' active' : ''; ?>
		<p class="number-of-tabs<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_tabs' ) ); ?>"><?php esc_html_e( 'Number of Tabs:', 'divi-blog-extras' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_tabs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_tabs' ) ); ?>">
			<?php
			foreach ( $tabs as $key => $value ) {
				$selected = $key === $number_of_tabs ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<p class="blog-widget-tabs<?php echo esc_attr( $tabbed_active ); ?>">
			<?php $tab_active = 2 < $number_of_tabs ? ' active' : ''; ?>
			<span class="tabs active">
				<span class="tab-label"><?php esc_html_e( 'Tab 1', 'divi-blog-extras' ); ?></span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_1_title' ) ); ?>"><?php esc_html_e( 'Tab 1 Title:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_1_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_1_title' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_1_title ); ?>">
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_1_category' ) ); ?>"><?php esc_html_e( 'Select Post Categories:', 'divi-blog-extras' ); ?></label>
					<select size="3" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_1_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_1_category' ) ); ?>[]" multiple>
					<?php
					foreach ( $terms as $term ) {
						$selected = in_array( $term->term_id, $tab_1_category, true ) ? 'selected' : '';
						?>
						<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php
					}
					?>
					</select>
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_1_offset' ) ); ?>"><?php esc_html_e( 'Offset:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_1_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_1_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_1_offset ); ?>">
				</span>
			</span>
			<span class="tabs active">
				<span class="tab-label"><?php esc_html_e( 'Tab 2', 'divi-blog-extras' ); ?></span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_2_title' ) ); ?>"><?php esc_html_e( 'Tab 2 Title:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_2_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_2_title' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_2_title ); ?>">
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_2_category' ) ); ?>"><?php esc_html_e( 'Select Post Categories:', 'divi-blog-extras' ); ?></label>
					<select size="3" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_2_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_2_category' ) ); ?>[]" multiple>
					<?php
					foreach ( $terms as $term ) {
						$selected = in_array( $term->term_id, $tab_2_category, true ) ? 'selected' : '';
						?>
						<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php
					}
					?>
					</select>
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_2_offset' ) ); ?>"><?php esc_html_e( 'Offset:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_2_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_2_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_2_offset ); ?>">
				</span>
			</span>
			<span class="tabs<?php echo esc_attr( $tab_active ); ?>">
				<span class="tab-label"><?php esc_html_e( 'Tab 3', 'divi-blog-extras' ); ?></span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_3_title' ) ); ?>"><?php esc_html_e( 'Tab 3 Title:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_3_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_3_title' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_3_title ); ?>">
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_3_category' ) ); ?>"><?php esc_html_e( 'Select Post Categories:', 'divi-blog-extras' ); ?></label>
					<select size="3" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_3_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_3_category' ) ); ?>[]" multiple>
					<?php
					foreach ( $terms as $term ) {
						$selected = in_array( $term->term_id, $tab_3_category, true ) ? 'selected' : '';
						?>
						<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php
					}
					?>
					</select>
				</span>
				<span class="tabs-data">
					<label for="<?php echo esc_attr( $this->get_field_id( 'tab_3_offset' ) ); ?>"><?php esc_html_e( 'Offset:', 'divi-blog-extras' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tab_3_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_3_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_3_offset ); ?>">
				</span>
			</span>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php esc_html_e( 'Number of Posts:', 'divi-blog-extras' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" type="text" value="<?php echo esc_attr( $number_of_posts ); ?>">
			<em><?php esc_html_e( 'Write -1 for all posts', 'divi-blog-extras' ); ?></em>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>"><?php esc_html_e( 'Show Excerpt:', 'divi-blog-extras' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_excerpt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_excerpt' ) ); ?>">
			<?php
			foreach ( $yes_no as $key => $value ) {
				$selected = $key === $show_excerpt ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<?php $list_active = 'list' === $layout ? ' active' : ''; ?>
		<p class="blog-widget-list-offset<?php echo esc_attr( $list_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'list_offset' ) ); ?>"><?php esc_html_e( 'Offset:', 'divi-blog-extras' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'list_offset' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_offset' ) ); ?>" type="text" value="<?php echo esc_attr( $list_offset ); ?>">
		</p>
		<p class="blog-widget-list-category<?php echo esc_attr( $list_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'list_category' ) ); ?>"><?php esc_html_e( 'Select Post Categories:', 'divi-blog-extras' ); ?></label>
			<select size="3" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'list_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'list_category' ) ); ?>[]" multiple>
			<?php
			foreach ( $terms as $term ) {
				$selected = in_array( $term->term_id, $list_category, true ) ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<p class="blog-widget-featured-image">
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="checkbox" value="yes" <?php echo esc_attr( $image ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Show Featured Image', 'divi-blog-extras' ); ?></label>
		</p>
		<?php $image_active = isset( $instance['image'] ) && 'yes' === $instance['image'] ? ' active' : ''; ?>
		<p class="blog-widget-featured-image-shape<?php echo esc_attr( $image_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_shape' ) ); ?>"><?php esc_html_e( 'Featured Image Shape:', 'divi-blog-extras' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_shape' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_shape' ) ); ?>">
			<?php
			foreach ( $shapes as $key => $value ) {
				$selected = $key === $image_shape ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $value ); ?></option>
				<?php
			}
			?>
			</select>
		</p>
		<p>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>" type="checkbox" value="yes" <?php echo esc_attr( $author ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"><?php esc_html_e( 'Show Author', 'divi-blog-extras' ); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'date' ) ); ?>" type="checkbox" value="yes" <?php echo esc_attr( $date ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'date' ) ); ?>"><?php esc_html_e( 'Show Date', 'divi-blog-extras' ); ?></label>
		</p>
		<p>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" type="checkbox" value="yes" <?php echo esc_attr( $category ); ?>>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Show Category', 'divi-blog-extras' ); ?></label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_color' ) ); ?>"><?php esc_html_e( 'Post Title Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'title_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_color' ) ); ?>" type="text" value="<?php echo esc_attr( $title_color ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title_hover_color' ) ); ?>"><?php esc_html_e( 'Post Title Hover Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'title_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_hover_color' ) ); ?>" type="text" value="<?php echo esc_attr( $title_hover_color ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'meta_color' ) ); ?>"><?php esc_html_e( 'Post Meta Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'meta_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'meta_color' ) ); ?>" type="text" value="<?php echo esc_attr( $meta_color ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'meta_hover_color' ) ); ?>"><?php esc_html_e( 'Post Meta Hover Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'meta_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'meta_hover_color' ) ); ?>" type="text" value="<?php echo esc_attr( $meta_hover_color ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_separator_color' ) ); ?>"><?php esc_html_e( 'Post Separator Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'post_separator_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_separator_color' ) ); ?>" type="text" value="<?php echo esc_attr( $post_separator_color ); ?>">
		</p>
		<p class="tab-colors<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tab_background_color' ) ); ?>"><?php esc_html_e( 'Tabs Background Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'tab_background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_background_color' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_background_color ); ?>">
		</p>
		<p class="tab-colors<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tab_color' ) ); ?>"><?php esc_html_e( 'Tabs Text Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'tab_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_color' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_color ); ?>">
		</p>
		<p class="tab-colors<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'active_tab_background_color' ) ); ?>"><?php esc_html_e( 'Active Tab Background Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'active_tab_background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'active_tab_background_color' ) ); ?>" type="text" value="<?php echo esc_attr( $active_tab_background_color ); ?>">
		</p>
		<p class="tab-colors<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'active_tab_color' ) ); ?>"><?php esc_html_e( 'Active Tab Text Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'active_tab_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'active_tab_color' ) ); ?>" type="text" value="<?php echo esc_attr( $active_tab_color ); ?>">
		</p>
		<p class="tab-colors<?php echo esc_attr( $tabbed_active ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'tab_border_color' ) ); ?>"><?php esc_html_e( 'Tabs Border Color', 'divi-blog-extras' ); ?></label>
			<input class="widefat el-color-field" id="<?php echo esc_attr( $this->get_field_id( 'tab_border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tab_border_color' ) ); ?>" type="text" value="<?php echo esc_attr( $tab_border_color ); ?>">
		</p>
		<?php
	}
}
