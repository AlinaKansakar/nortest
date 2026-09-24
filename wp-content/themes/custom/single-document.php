<?php

/**
 * Single document fallback when Divi Theme Builder is not used.
 *
 * @package qobrix
 */

get_header();
?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
				<?php echo qobrix_get_document_files_html(get_the_ID()); ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
