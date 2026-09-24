<?php
	$override_templates = el_blog_is_templates_override();
?>
<div id="el-settings-panel-general-wrap" class="el-settings-panel-group el-settings-panel-active-group">
	<ul class="el-settings-panel-submenu">
		<li class="el-settings-panel-submenu-tab el-settings-panel-active-tab">
			<span data-href="#el-settings-panel-general-general-section">General</span>
		</li>
		<?php
		if ( false !== $override_templates && is_array( $override_templates ) && ! empty( $override_templates ) ) {
			?>
			<li class="el-settings-panel-submenu-tab">
				<span data-href="#el-settings-panel-general-templates-section">Overrides</span>
			</li>
			<?php
		}
		?>
	</ul>
	<div class="el-settings-panel-sections-wrap">
		<table id="el-settings-panel-general-general-section" class="form-table el-settings-panel-section el-settings-panel-active-section">
		<?php
			settings_fields( 'el-settings-general-general-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-general-general-section' );
		?>
		</table>
		<?php
		if ( false !== $override_templates && is_array( $override_templates ) && ! empty( $override_templates ) ) {
			?>
			<table id="el-settings-panel-general-templates-section" class="form-table el-settings-panel-section">
			<?php
				settings_fields( 'el-settings-general-templates-group' );
				do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-general-templates-section' );
			?>
			</table>
			<?php
		}
		?>
	</div>
</div>
