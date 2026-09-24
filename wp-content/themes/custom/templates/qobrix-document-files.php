<?php

/**
 * Files inside one document, from the folders repeater.
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
			<?php foreach ($atts['files'] as $row) : ?>
				<tr>
					<td><?php echo esc_html($row['file_name']); ?></td>
					<td><?php echo esc_html($row['project_title']); ?></td>
					<td><?php echo esc_html($row['department']); ?></td>
					<td>
						<?php if (!empty($row['file_url'])) : ?>
							<a href="<?php echo esc_url($row['file_url']); ?>" download>Download</a>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html($row['added_on']); ?></td>
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
