<div id="el-settings-panel-custom-posts-wrap" class="el-settings-panel-group">
	<ul class="el-settings-panel-submenu">
		<li class="el-settings-panel-submenu-tab el-settings-panel-active-tab">
			<span data-href="#el-settings-panel-custom-posts-general-section">General</span>
		</li>
	</ul>
	<div class="el-settings-panel-sections-wrap">
		<table id="el-settings-panel-custom-posts-general-section" class="form-table el-settings-panel-section el-settings-panel-active-section">
		<?php
			settings_fields( 'el-settings-custom-posts-general-group' );
			do_settings_fields( esc_html( self::$menu_slug ), 'el-settings-custom-posts-general-section' );
		?>
		</table>
	</div>
</div>
