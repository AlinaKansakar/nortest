<?php
	$site_taxonomies = get_taxonomies(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'objects'
	);
	?>
<div id="el-settings-panel-archives-wrap" class="el-settings-panel-group">
	<ul class="el-settings-panel-submenu">
		<li class="el-settings-panel-submenu-tab el-settings-panel-active-tab">
			<span data-href="#el-settings-panel-archives-category-section">Category</span>
		</li>
		<li class="el-settings-panel-submenu-tab">
			<span data-href="#el-settings-panel-archives-tag-section">Tag</span>
		</li>
		<li class="el-settings-panel-submenu-tab">
			<span data-href="#el-settings-panel-archives-author-section">Author</span>
		</li>
		<li class="el-settings-panel-submenu-tab">
			<span data-href="#el-settings-panel-archives-date-section">Date</span>
		</li>
		<?php
		if ( $site_taxonomies && is_array( $site_taxonomies ) ) {
			?>
			<li class="el-settings-panel-submenu-tab">
				<span data-href="#el-settings-panel-archives-taxonomy-section">Taxonomy</span>
			</li>
			<?php
		}
		?>
	</ul>
	<div class="el-settings-panel-sections-wrap">
		<table id="el-settings-panel-archives-category-section" class="form-table el-settings-panel-section el-settings-panel-active-section">
		<?php
			settings_fields( 'el-settings-archives-category-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-archives-category-section' );
		?>
		</table>
		<table id="el-settings-panel-archives-tag-section" class="form-table el-settings-panel-section">
		<?php
			settings_fields( 'el-settings-archives-tag-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-archives-tag-section' );
		?>
		</table>
		<table id="el-settings-panel-archives-author-section" class="form-table el-settings-panel-section">
		<?php
			settings_fields( 'el-settings-archives-author-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-archives-author-section' );
		?>
		</table>
		<table id="el-settings-panel-archives-date-section" class="form-table el-settings-panel-section">
		<?php
			settings_fields( 'el-settings-archives-date-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-archives-date-section' );
		?>
		</table>
		<?php
		if ( $site_taxonomies && is_array( $site_taxonomies ) ) {
			?>
			<table id="el-settings-panel-archives-taxonomy-section" class="form-table el-settings-panel-section">
			<?php
				settings_fields( 'el-settings-archives-taxonomy-group' );
				do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-archives-taxonomy-section' );
			?>
			</table>
			<?php
		}
		?>
	</div>
</div>
