<?php

/**
 * Documents list template
 *
 * @package qobrix
 */

?>
<?php if ($atts['documents']) : ?>
	<table id="dataTable" class="ui celled table" style="width:100%">
		<thead>
			<tr>
				<th>Document</th>
				<th>Project</th>
				<th>Department</th>
				<th>File</th>
				<th>Added On</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($atts['documents'] as $document) : ?>
				<tr>
					<td><?php echo esc_attr($document->post_title); ?></td>
					<td><?php echo esc_attr($document->project_title); ?></td>
					<td><?php echo esc_attr($document->department ?? '-'); ?></td>
					<td>
						<?php
						$file = get_field('file', $document->ID);
						if ($file) {
							echo '<a href="' . $file['url'] . '" target="_blank">Download</a>';
						}
						?>
					</td>
					<td><?php echo esc_attr(get_the_date('Y/m/d', $document->ID)); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Document</th>
				<th>Project</th>
				<th>Department</th>
				<th>File</th>
				<th>Added On</th>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>
