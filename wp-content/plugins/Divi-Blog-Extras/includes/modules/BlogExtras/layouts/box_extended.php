<?php
/**
 * The Template for displaying Box Extended Layout
 *
 * This template can be overridden by copying it to yourtheme/divi-blog-extras/layouts/box_extended.php.
 *
 * HOWEVER, on occasion Divi-Blog-Extras will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 *
 * @author      Elicus Technologies <hello@elicus.com>
 * @link        https://www.elicus.com/
 * @copyright   2023 Elicus Technologies Private Limited
 * @version     2.6.6
 */

$post_object  = get_post( $post_id );
$post_content = el_blog_strip_shortcodes( $post_object->post_content, true );

if ( isset( $is_search ) && $is_search ) {
	// phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
	$post_type = get_post_type( $post_object );
}

if ( 'on' === $link_target ) {
	$target = '_blank';
} else {
	$target = '_self';
}

// Post Content.
$output .= '<div class="post-content">';

if ( 'on' === $show_categories ) {
	// Creating Categories links.
	$object_taxonomies = get_object_taxonomies( esc_html( $post_type ), 'objects' );
	$post_term_list    = '';
	if ( ! empty( $object_taxonomies ) ) {
		if ( 'on' !== $category_meta_colors ) {
			if ( '' === $category_background ) {
				$post_term_list = array();
			}
		}
		$filtered_taxonomies = json_decode( ELICUS_BLOG_FILTERED_TAXONOMIES );
		array_push( $filtered_taxonomies, 'post_tag' );
		foreach ( $object_taxonomies as $object_taxonomy_key => $object_taxonomy ) {
			if ( ! in_array( $object_taxonomy_key, $filtered_taxonomies, true ) ) {
				$post_terms = get_the_terms( $post_id, $object_taxonomy_key );
				if ( $post_terms && ! is_wp_error( $post_terms ) ) {
					if ( 'on' === $category_meta_colors ) {
						foreach ( $post_terms as $post_term ) {
							$color           = get_term_meta( $post_term->term_id, 'el_term_color', true );
							$color_hover     = get_term_meta( $post_term->term_id, 'el_term_hover_color', true );
							$bgcolor         = get_term_meta( $post_term->term_id, 'el_term_bgcolor', true );
							$bgcolor_hover   = get_term_meta( $post_term->term_id, 'el_term_hover_bgcolor', true );
							$color_style     = '' !== $color ? 'color: ' . $color . ' !important;' : '';
							$bgcolor_style   = '' !== $bgcolor ? 'background-color: ' . $bgcolor . ' !important;' : '';
							$style           = $color_style . $bgcolor_style;
							$post_term_list .= sprintf(
								'<a href="%1s" target="%10$s" class="el_%8$s_term el_term_%9$s" rel="category term tag" data-color="%2s" data-color-hover="%3s" data-bgcolor="%4s" data-bgcolor-hover="%5s" style="%6s">%7s</a>',
								esc_url( get_term_link( intval( $post_term->term_id ), esc_html( $object_taxonomy_key ) ) ),
								'' !== $color ? esc_attr( $color ) : '',
								'' !== $color_hover ? esc_attr( $color_hover ) : '',
								'' !== $bgcolor ? esc_attr( $bgcolor ) : '',
								'' !== $bgcolor_hover ? esc_attr( $bgcolor_hover ) : '',
								'' !== $style ? esc_attr( $style ) : '',
								esc_html( $post_term->name ),
								esc_attr( $object_taxonomy_key ),
								esc_html( $post_term->slug ),
								esc_attr( $target )
							);
						}
					} else {
						foreach ( $post_terms as $post_term ) {
							if ( '' === $category_background ) {
								array_push( $post_term_list, '<a href="' . esc_url( get_term_link( intval( $post_term->term_id ), esc_html( $object_taxonomy_key ) ) ) . '" target="' . $target . '" class="el_' . esc_attr( $object_taxonomy_key ) . '_term el_term_' . esc_attr( $post_term->slug ) . '" rel="category term tag">' . esc_html( $post_term->name ) . '</a>' );
							} else {
								$post_term_list .= sprintf(
									'<a href="%1s" target="%5$s" rel="category term tag" class="el_%3$s_term el_term_%4$s">%2s</a>',
									esc_url( get_term_link( intval( $post_term->term_id ), esc_html( $object_taxonomy_key ) ) ),
									esc_html( $post_term->name ),
									esc_attr( $object_taxonomy_key ),
									esc_html( $post_term->slug ),
									esc_attr( $target )
								);
							}
						}
					}
				}
			}
		}
	}

	if ( is_array( $post_term_list ) && ! empty( $post_term_list ) ) {
		$post_term_list = implode( ', ', $post_term_list );
	}

	if ( ! empty( $post_term_list ) ) {
		$output .= '<div class="post-categories">' . et_core_intentionally_unescaped( $post_term_list, 'html' ) . '</div>';
	}
}

$title_kses = array(
    'br' => array(
    	'class' =>  true,
    ),
    'em' => array(
    	'class' =>  true,
    ),
    'strong' => array(
    	'class' =>  true,
    ),
    'i' => array(
    	'class' =>  true,
    ),
    'hr' => array(
    	'class' =>  true,
    ),
    'ins' => array(
    	'class' =>  true,
    ),
    'del' => array(
    	'class' =>  true,
    ),
    'strike' => array(
    	'class' => true,
    ),
    'sub' => array(
    	'class' => true,
    ),
    'sup' => array(
    	'class' => true,
    ),
    'a' => array(
      	'href'     => true,
	    'rel'      => true,
	    'name'     => true,
	    'target'   => true,
	    'class' => true,
    ),
    'span' => array(
    	'class' =>  true,
    ),
    'p' => array(
    	'class' => true,
    ),
    'img' => array(
    	'alt'      => true,
        'align'    => true,
        'height'   => true,
        'loading'  => true,
        'src'      => true,
        'width'    => true,
        'class' => true,
    ),
);

// Post Title.
$output .= '<' . esc_html( $processed_header_level ) . ' class="entry-title"><a href="' . esc_url( get_the_permalink( $post_id ) ) . '" target="' . $target . '">' . wp_kses( get_the_title( $post_id ), $title_kses ) . '</a></' . esc_html( $processed_header_level ) . '>';


// Post Excerpt or Content.
if ( 'on' === $show_content ) {
	global $more;

	// page builder doesn't support more tag, so display the_content() in case of post made with page builder.
	if ( et_pb_is_pagebuilder_used( $post_id ) ) {
        // phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
		$more    = 1;
		$output .= '<div class="post-data">' . et_core_intentionally_unescaped( do_shortcode( $post_content ), 'html' ) . '</div>';
	} else {
        // phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
		$more    = null;
		$output .= '<div class="post-data">' . wp_kses_post( el_blog_strip_shortcodes( apply_filters( 'the_content', $post_content ) ) ) . '</div>';
	}
} else {
	if ( has_excerpt( $post_object ) && 'on' === $use_manual_excerpt && 0 !== intval( $excerpt_length ) && '' !== trim( $post_object->post_excerpt ) ) {
		$output .= '<div class="post-data">' . wpautop( el_blog_strip_shortcodes( get_the_excerpt( $post_id ) ) ) . '</div>';
	} else {
		if ( 0 !== intval( $excerpt_length ) ) {
			$output .= '<div class="post-data">' . wpautop( strip_shortcodes( el_blog_truncate_post( $excerpt_length, false, $post_object, true ) ) ) . '</div>';
		}
	}
}

if ( 'on' !== $show_content ) {
	if ( 'on' === $show_more ) {
		if ( 'on' === $use_read_more_button ) {
            // phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
			$more    = '<p class="el-read-more-btn">' . et_core_intentionally_unescaped( $read_more_button, 'html' ) . '</p>';
			$output .= et_core_intentionally_unescaped( $more, 'html' );
		} else {
            // phpcs:ignore WordPress,Variables,GlobalVariables,OverrideProhibited.
			$more    = 'on' === $show_more ?
					(
						sprintf(
							'<p class="el-read-more-link"><a href="%1$s" class="more-link" target="%3$s">%2$s</a></p>',
							esc_url( get_permalink( $post_id ) ),
							et_core_esc_previously( $read_more_text ),
							esc_attr( $target )
						)
					) :
					'';
			$output .= et_core_intentionally_unescaped( $more, 'html' );
		}
	}
}

// Post Meta.
if ( 'on' === $show_author || 'on' === $show_date || 'on' === $show_comments || 'on' === $show_read_time ) {
	$output .= sprintf(
		'<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
		(
			'on' === $show_author ?
			et_core_intentionally_unescaped(
				sprintf(
					'<span class="author vcard">%1$s %2$s</span>',
					get_avatar( get_the_author_meta( 'ID' ), 28 ),
					get_the_author_posts_link()
				),
				'html'
			) :
			''
		),
		(
			( 'on' === $show_author && 'on' === $show_date ) ?
			et_core_intentionally_unescaped( ' | ', 'fixed_string' ) :
			''
		),
		(
			'on' === $show_date ?
			et_get_safe_localization(
				sprintf(
					'<span class="published">%s</span>',
					esc_html( get_the_date( $meta_date, $post_id ) )
				)
			) :
			''
		),
		(
			( ( 'on' === $show_author || 'on' === $show_date ) && 'on' === $show_comments ) ?
			et_core_intentionally_unescaped( ' | ', 'fixed_string' ) :
			''
		),
		(
			'on' === $show_comments ?
			et_get_safe_localization(
				sprintf(
					'<span class="comments">%s</span>',
					sprintf(
						// translators: %s: comment.
						esc_html( _nx( '%s Comment', '%s Comments', get_comments_number( $post_id ), 'number of comments', 'divi-blog-extras' ) ),
						number_format_i18n( get_comments_number( $post_id ) )
					)
				)
			) :
			''
		),
		(
			( ( 'on' === $show_author || 'on' === $show_date || 'on' === $show_comments ) && 'on' === $show_read_time ) ?
			et_core_intentionally_unescaped( ' | ', 'fixed_string' ) :
			''
		),
		(
			'on' === $show_read_time ?
			et_get_safe_localization(
				sprintf(
					'<span class="read-time">%d %s</span>',
					intval( el_blog_estimated_read_time( apply_filters( 'the_content', $post_content ) ) ),
					esc_html( $read_time_text )
				)
			) :
			''
		)
	);
}

$output .= '</div> <!-- post-content -->';


// Post Featured Image.
if ( '' !== $thumb && 'on' === $show_thumbnail ) {
	$output .= '<div class="post-media">';
	$output .= '<a href="' . esc_url( get_the_permalink( $post_id ) ) . '" target="' . $target . '" class="entry-featured-image-url">';
	$output .= et_core_intentionally_unescaped( $thumb, 'html' );
	if ( 'on' === $use_overlay ) {
		$output .= et_core_intentionally_unescaped( $overlay_output, 'html' );
	}
	$output .= '</a>';
	$output .= '</div> <!-- post-media -->';
}
