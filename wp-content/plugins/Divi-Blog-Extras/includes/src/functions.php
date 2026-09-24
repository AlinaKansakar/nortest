<?php
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

if ( ! function_exists( 'el_get_post_thumbnail' ) ) {
	function el_get_post_thumbnail( $post_id, $size, $class, $print = false, $url = false ) {
		if ( ! $post_id ) {
			return;
		}

		$thumb     = '';
		$thumb_url = '';
		$atts      = array();
		if ( has_post_thumbnail( $post_id ) ) {
			$attach_id = get_post_thumbnail_id( $post_id );
			if ( 0 !== $attach_id && '' !== $attach_id && '0' !== $attach_id ) {
				$atts['alt'] = get_post_meta( $attach_id, '_wp_attachment_image_alt', true );
			} else {
				$atts['alt'] = get_the_title( $post_id );
			}
			if ( $class ) {
				$atts['class'] = $class;
			}
			$atts['loading'] = 'lazy';
			$thumb     = get_the_post_thumbnail( $post_id, esc_attr( $size ), $atts );
			$thumb_url = get_the_post_thumbnail_url( $post_id, esc_attr( $size ) );
		} else {
			$post_object         = get_post( $post_id );
			$unprocessed_content = $post_object->post_content;

			// truncate Post based shortcodes if Divi Builder enabled to avoid infinite loops.
			if ( function_exists( 'et_strip_shortcodes' ) ) {
				$unprocessed_content = et_strip_shortcodes( $post_object->post_content, true );
			}

			// Check if content should be overridden with a custom value.
			$custom = apply_filters( 'et_first_image_use_custom_content', false, $unprocessed_content, $post_object );
			// apply the_content filter to execute all shortcodes and get the correct image from the processed content.
			$processed_content = false === $custom ? apply_filters( 'the_content', $unprocessed_content ) : $custom;

			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $processed_content, $matches );
			if ( isset( $matches[1][0] ) ) {
				$image = trim( $matches[1][0] );
			}

			if ( isset( $image ) ) {
				$attach_id = attachment_url_to_postid( $image );
				if ( 0 !== $attach_id && '' !== $attach_id && '0' !== $attach_id ) {
					$atts['alt'] = get_post_meta( $attach_id, '_wp_attachment_image_alt', true );
				} else {
					$atts['alt'] = get_the_title( $post_id );
				}
				if ( $class ) {
					$atts['class'] = esc_attr( $class );
				}
				$atts['loading'] = 'lazy';
				$thumb_url = wp_get_attachment_image_url( $attach_id, esc_attr( $size ) );
				$thumb     = wp_get_attachment_image( $attach_id, esc_attr( $size ), false, $atts );
			}
		}

		if ( $print ) {
			if ( $url ) {
				echo esc_url( $thumb_url );
			} else {
				echo et_core_intentionally_unescaped( $thumb, 'html' );
			}
		} else {
			if ( $url ) {
				return esc_url( $thumb_url );
			} else {
				return et_core_intentionally_unescaped( $thumb, 'html' );
			}
		}
	}
}

if ( ! function_exists( 'el_blog_strip_shortcodes' ) ) {
	function el_blog_strip_shortcodes( $content, $truncate_post_based_shortcodes_only = false ) {
		global $shortcode_tags;

		$content = trim( $content );

		$strip_content_shortcodes = array(
			'et_pb_code',
			'et_pb_fullwidth_code',
		);

		// list of post-based shortcodes.
		if ( $truncate_post_based_shortcodes_only ) {
			$strip_content_shortcodes = array(
				'et_pb_post_slider',
				'et_pb_fullwidth_post_slider',
				'et_pb_blog',
				'et_pb_blog_extras',
				'et_pb_comments',
				'dipl_modal',
				'el_modal_popup'
			);
		}

		foreach ( $strip_content_shortcodes as $shortcode_name ) {
			$regex = sprintf(
				'(\[%1$s[^\]]*\][^\[]*\[\/%1$s\]|\[%1$s[^\]]*\])',
				esc_html( $shortcode_name )
			);

			$content = preg_replace( $regex, '', $content );
		}

		// do not proceed if we need to truncate post-based shortcodes only.
		if ( $truncate_post_based_shortcodes_only ) {
			return $content;
		}

		$shortcode_tag_names = array();
		foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
			if ( 0 !== strpos( $shortcode_tag_name, 'et_pb_' ) ) {
				continue;
			}

			$shortcode_tag_names[] = $shortcode_tag_name;
		}

		$et_shortcodes = implode( '|', $shortcode_tag_names );

		$regex_opening_shortcodes = sprintf( '(\[(%1$s)[^\]]+\])', esc_html( $et_shortcodes ) );
		$regex_closing_shortcodes = sprintf( '(\[\/(%1$s)\])', esc_html( $et_shortcodes ) );

		$content = preg_replace( $regex_opening_shortcodes, '', $content );
		$content = preg_replace( $regex_closing_shortcodes, '', $content );

		return et_core_intentionally_unescaped( $content, 'html' );
	}
}

if ( ! function_exists( 'el_blog_truncate_post' ) ) {
	function el_blog_truncate_post( $amount, $echo = true, $post_object = '', $strip_shortcodes = false ) {
		global $shortname;

		$post_excerpt = '';
		$post_excerpt = apply_filters( 'the_excerpt', $post_object->post_excerpt );

		if ( 'on' === et_get_option( $shortname . '_use_excerpt' ) && '' !== $post_excerpt ) {
			if ( $echo ) {
				echo et_core_intentionally_unescaped( $post_excerpt, 'html' );
			} else {
				return $post_excerpt;
			}
		} else {
			// get the post content.
			$truncate = $post_object->post_content;

			// remove caption shortcode from the post content.
			$truncate = preg_replace( '@\[caption[^\]]*?\].*?\[\/caption]@si', '', $truncate );

			// remove post nav shortcode from the post content.
			$truncate = preg_replace( '@\[et_pb_post_nav[^\]]*?\].*?\[\/et_pb_post_nav]@si', '', $truncate );

			// Remove audio shortcode from post content to prevent unwanted audio file on the excerpt
			// due to unparsed audio shortcode.
			$truncate = preg_replace( '@\[audio[^\]]*?\].*?\[\/audio]@si', '', $truncate );

			// Remove embed shortcode from post content.
			$truncate = preg_replace( '@\[embed[^\]]*?\].*?\[\/embed]@si', '', $truncate );

			if ( $strip_shortcodes ) {
				$truncate = el_blog_strip_shortcodes( $truncate );
				$truncate = et_builder_strip_dynamic_content( $truncate );
			} else {
				// apply content filters.
				$truncate = el_blog_strip_shortcodes( $truncate, true );
				$truncate = apply_filters( 'the_content', $truncate );
			}

			// decide if we need to append dots at the end of the string.
			if ( strlen( $truncate ) <= $amount ) {
				$echo_out = '';
			} else {
				$echo_out = '...';
				if ( $amount > 3 ) {
					$amount = $amount - 3;
				}
			}

			// trim text to a certain number of characters, also remove spaces from the end of a string ( space counts as a character ).
			$truncate = rtrim( et_wp_trim_words( $truncate, $amount, '' ) );

			// remove the last word to make sure we display all words correctly.
			if ( '' !== $echo_out ) {
				$new_words_array = (array) explode( ' ', $truncate );
				array_pop( $new_words_array );

				$truncate = implode( ' ', $new_words_array );

				// append dots to the end of the string.
				if ( '' !== $truncate ) {
					$truncate .= $echo_out;
				}
			}

			if ( $echo ) {
				echo et_core_intentionally_unescaped( $truncate, 'html' );
			} else {
				return et_core_intentionally_unescaped( $truncate, 'html' );
			}
		}
	}
}

if ( ! function_exists( 'el_blog_render_button' ) ) {
	function el_blog_render_button( $args = array() ) {
		// Prepare arguments.
		$defaults = array(
			'button_id'           => '',
			'button_classname'    => array(),
			'button_custom'       => '',
			'button_rel'          => '',
			'button_text'         => '',
			'button_text_escaped' => false,
			'button_url'          => '',
			'custom_icon'         => '',
			'custom_icon_tablet'  => '',
			'custom_icon_phone'   => '',
			'display_button'      => true,
			'has_wrapper'         => true,
			'url_new_window'      => '',
			'multi_view_data'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Do not proceed if display_button argument is false.
		if ( ! $args['display_button'] ) {
			return '';
		}

		$button_text = $args['button_text_escaped'] ? $args['button_text'] : esc_html( $args['button_text'] );

		// Do not proceed if button_text argument is empty and not having multi view value.
		if ( '' === $button_text && ! $args['multi_view_data'] ) {
			return '';
		}

		// Button classname.
		$button_classname = array( 'et_pb_button' );

		if ( ( '' !== $args['custom_icon'] || '' !== $args['custom_icon_tablet'] || '' !== $args['custom_icon_phone'] ) && 'on' === $args['button_custom'] ) {
			$button_classname[] = 'et_pb_custom_button_icon';
		}

		// Add multi view CSS hidden helper class when button text is empty on desktop mode.
		if ( '' === $button_text && $args['multi_view_data'] ) {
			$button_classname[] = 'et_multi_view_hidden';
		}

		if ( ! empty( $args['button_classname'] ) ) {
			$button_classname = array_merge( $button_classname, $args['button_classname'] );
		}

		// Custom icon data attribute.
		$use_data_icon = '' !== $args['custom_icon'] && 'on' === $args['button_custom'];
		$data_icon     = $use_data_icon ? sprintf(
            ' data-icon="%1$s"',
            wp_doing_ajax() && ! ET_BUILDER_LOAD_ON_AJAX ? esc_attr( $args['custom_icon'] ) : esc_attr( et_pb_process_font_icon( $args['custom_icon'] ) )
        ) : '';

        $use_data_icon_tablet = '' !== $args['custom_icon_tablet'] && 'on' === $args['button_custom'];
        $data_icon_tablet     = $use_data_icon_tablet ? sprintf(
            ' data-icon-tablet="%1$s"',
            wp_doing_ajax() && ! ET_BUILDER_LOAD_ON_AJAX ? esc_attr( $args['custom_icon_tablet'] ) : esc_attr( et_pb_process_font_icon( $args['custom_icon_tablet'] ) )
        ) : '';

        $use_data_icon_phone = '' !== $args['custom_icon_phone'] && 'on' === $args['button_custom'];
        $data_icon_phone     = $use_data_icon_phone ? sprintf(
            ' data-icon-phone="%1$s"',
             wp_doing_ajax() && ! ET_BUILDER_LOAD_ON_AJAX ? esc_attr( $args['custom_icon_phone'] ) : esc_attr( et_pb_process_font_icon( $args['custom_icon_phone'] ) )
        ) : '';

		// Render button.
		return sprintf(
			'%6$s<a%8$s class="%5$s" href="%1$s"%3$s%4$s%9$s%10$s%11$s>%2$s</a>%7$s',
			esc_url( $args['button_url'] ),
			et_core_esc_previously( $button_text ),
			( 'on' === $args['url_new_window'] ? ' target="_blank"' : '' ),
			et_core_esc_previously( $data_icon ),
			esc_attr( implode( ' ', array_unique( $button_classname ) ) ), // #5
			$args['has_wrapper'] ? '<div class="et_pb_button_wrapper">' : '',
			$args['has_wrapper'] ? '</div>' : '',
			'' !== $args['button_id'] ? sprintf( ' id="%1$s"', esc_attr( $args['button_id'] ) ) : '',
			et_core_esc_previously( $data_icon_tablet ),
			et_core_esc_previously( $data_icon_phone ), // #10
			et_core_esc_previously( $args['multi_view_data'] )
		);
	}
}

if ( ! function_exists( 'el_blog_word_count' ) ) {
	function el_blog_word_count($string) {
	    preg_match_all( "/\S+/", $string, $matches );
	    return count($matches[0]);
	}
}

if ( ! function_exists( 'el_blog_estimated_read_time' ) ) {
	function el_blog_estimated_read_time( $content = '' ) {
		$plugin_options = get_option( ELICUS_BLOG_OPTION );
		if ( isset( $plugin_options['blog-post-words-per-minute'] ) ) {
			$wpm = intval( $plugin_options['blog-post-words-per-minute'] );
		} else {
			$wpm = 220;
		}
		$wpm           = 0 === $wpm ? 220 : $wpm;
		$clean_content = el_blog_strip_shortcodes( $content );
		$clean_content = strip_shortcodes( $clean_content );
		// phpcs:ignore WordPress,Function,Discouraged.
		$clean_content = wp_strip_all_tags( $clean_content );
		$word_count    = el_blog_word_count( $clean_content );
		$time          = ceil( floatval( $word_count / $wpm ) );
		$time          = ( 0 === $time ) ? 1 : $time;
		return $time;
	}
}
