<?php get_header(); ?>
<div id="main-content">
	<?php
	$plugin_options = get_option( ELICUS_BLOG_OPTION );
	$object         = get_queried_object();
	if ( isset( $plugin_options['variable-taxonomy-archive-layout'] ) && '' !== $plugin_options['variable-taxonomy-archive-layout'] ) {
		$variable_taxonomy_archive_layout = wp_specialchars_decode( $plugin_options['variable-taxonomy-archive-layout'], ENT_COMPAT );
		$variable_taxonomy_archive_layout = wp_unslash( $variable_taxonomy_archive_layout );
		$variable_taxonomy_archive_layout = json_decode( $variable_taxonomy_archive_layout, true );
		for ( $i = 1; $i <= $variable_taxonomy_archive_layout['counter']; $i++ ) {
			$taxonomy_fields      = explode( ',', $variable_taxonomy_archive_layout['fields'] );
			$taxonomy_name_option = $taxonomy_fields[0] . '-' . $i;
			if ( isset( $plugin_options[ $taxonomy_name_option ] ) ) {
				$site_taxonomy = $plugin_options[ $taxonomy_name_option ];
				if ( $site_taxonomy === $object->taxonomy ) {
					$taxonomy_layout_option = $taxonomy_fields[1] . '-' . $i;
					if ( isset( $plugin_options[ $taxonomy_layout_option ] ) && false !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) && 'trash' !== get_post_status( $plugin_options[ $taxonomy_layout_option ] ) ) {
						$taxonomy_layout         = $plugin_options[ $taxonomy_layout_option ];
						$taxonomy_sidebar_option = $taxonomy_fields[2] . '-' . $i;
						if ( isset( $plugin_options[ $taxonomy_sidebar_option ] ) ) {
							$taxonomy_sidebar = $plugin_options[ $taxonomy_sidebar_option ];
						}
					}
				}
			}
		}
	}

	if ( ! isset( $taxonomy_layout ) ) {
		if ( isset( $plugin_options['taxonomies-archive-layout'] ) && '' !== $plugin_options['taxonomies-archive-layout'] ) {
			$taxonomy_layout = $plugin_options['taxonomies-archive-layout'];
		}
	}

	if ( ! isset( $taxonomy_sidebar ) ) {
		if ( isset( $plugin_options['taxonomies-archive-layout'] ) && '' !== $plugin_options['taxonomies-archive-layout'] ) {
			$taxonomy_sidebar = $plugin_options['taxonomies-archive-layout-type'];
		}
	}

	if ( isset( $taxonomy_sidebar ) && '' !== $taxonomy_sidebar ) {
		if ( 'full-width' !== $taxonomy_sidebar ) {
			?>
			<div class="container">
				<div id="content-area" class="clearfix">
					<div id="left-area">
			<?php
		}
	}

	$module_id = $taxonomy_layout;
	$terms     = wp_get_post_terms( $module_id, 'layout_type' );
	if ( 'row' === $terms[0]->slug ) {
		?>
		<div id="divi-blog-extra-archive" class="et_pb_section">
			<?php echo do_shortcode( '[et_pb_section global_module="' . esc_attr( $module_id ) . '"][/et_pb_section]' ); ?>
		</div>
		<?php
	} elseif ( 'module' === $terms[0]->slug ) {
		?>
		<div id="divi-blog-extra-archive" class="et_pb_section">
			<div class="et_pb_row">
				<div class="et_pb_column et_pb_column_4_4">
					<?php echo do_shortcode( '[et_pb_section global_module="' . esc_attr( $module_id ) . '"][/et_pb_section]' ); ?>
				</div>
			</div>
		</div>
		<?php
	} else {
		echo do_shortcode( '[et_pb_section global_module="' . esc_attr( $module_id ) . '"][/et_pb_section]' );
	}

	if ( isset( $taxonomy_sidebar ) && '' !== $taxonomy_sidebar ) {
		if ( 'full-width' !== $taxonomy_sidebar ) {
			?>
				</div> <!-- #left-area -->
				<?php get_sidebar(); ?>
			</div> <!-- #content-area -->
		</div> <!-- .container -->
			<?php
		}
	}
	?>
</div> <!-- #main-content -->

<?php
get_footer();
