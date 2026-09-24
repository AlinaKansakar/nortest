<?php get_header(); ?>
<div id="main-content">
	<?php
	$plugin_options = get_option( ELICUS_BLOG_OPTION );
	if ( isset( $plugin_options['tag-archive-layout-type'] ) ) {
		if ( 'full-width' !== $plugin_options['tag-archive-layout-type'] ) {
			?>
			<div class="container">
				<div id="content-area" class="clearfix">
					<div id="left-area">
			<?php
		}
	}

	$module_id = $plugin_options['tag-archive-layout'];
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

	if ( isset( $plugin_options['tag-archive-layout-type'] ) ) {
		if ( 'full-width' !== $plugin_options['tag-archive-layout-type'] ) {
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
