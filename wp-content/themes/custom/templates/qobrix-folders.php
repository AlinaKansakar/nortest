<?php

/**
 * Folder files from each document.
 *
 * @package qobrix
 */

?>
<?php if (!empty($atts['files'])) : ?>
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
			<?php foreach ($atts['files'] as $file) : ?>
				<tr>
					<td><?php echo esc_html($file['file_name']); ?></td>
					<td><?php echo esc_html($file['project_title']); ?></td>
					<td><?php echo esc_html($file['department']); ?></td>
					<td>
						<a href="<?php echo esc_url($file['file_url']); ?>" target="_blank" download>Download</a>
					</td>
					<td><?php echo esc_html($file['added_on']); ?></td>
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
