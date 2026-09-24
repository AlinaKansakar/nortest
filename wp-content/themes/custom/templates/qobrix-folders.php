<?php

/**
 * Document folders list template.
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
				<th>List of files</th>
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
						<a href="<?php echo esc_url(get_permalink($document->ID)); ?>">View documents</a>
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
				<th>List of files</th>
				<th>Added On</th>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>
