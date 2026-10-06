<?php

/**
 * Document list, or folder files for the selected document.
 *
 * @package qobrix
 */

$view = $atts['view'] ?? 'documents';

?>
<?php if ('files' === $view) : ?>
	<table id="dataTable" class="ui celled table" style="width:100%">
		<thead>
			<tr>
				<th>Title</th>
				<th>Project</th>
				<th>Department</th>
				<th>File name</th>
				<th>Addon Date</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($atts['files'] ?? [] as $file) : ?>
				<tr>
					<td><?php echo esc_html(($file['file_name'] ?? '') !== '' ? $file['file_name'] : '-'); ?></td>
					<td><?php echo esc_html(($file['project'] ?? '') !== '' ? $file['project'] : '-'); ?></td>
					<td><?php echo esc_html(($file['department'] ?? '') !== '' ? $file['department'] : '-'); ?></td>
					<td>
						<?php if (!empty($file['file_url'])) : ?>
							<a href="<?php echo esc_url($file['file_url']); ?>" target="_blank" title="<?php echo esc_html(($file['file_name'] ?? '') !== '' ? $file['file_name'] : 'Download'); ?>" download>
								Download
							</a>
						<?php else : ?>
							<?php echo esc_html(($file['file_name'] ?? '') !== '' ? $file['file_name'] : '-'); ?>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html(($file['addon_date'] ?? '') !== '' ? $file['addon_date'] : '-'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Title</th>
				<th>Project</th>
				<th>Department</th>
				<th>File name</th>
				<th>Addon Date</th>
			</tr>
		</tfoot>
	</table>
<?php else : ?>
	<table id="dataTable" class="ui celled table" style="width:100%">
		<thead>
			<tr>
				<th>Title</th>
				<th>Project</th>
				<th>Department</th>
				<th>View Document</th>
				<th>Addon Date</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($atts['documents'] ?? [] as $document) : ?>
				<tr>
					<td><?php echo esc_html($document['title'] ?? ''); ?></td>
					<td><?php echo esc_html(($document['project'] ?? '') !== '' ? $document['project'] : '-'); ?></td>
					<td><?php echo esc_html(($document['department'] ?? '') !== '' ? $document['department'] : '-'); ?></td>
					<td>
						<?php if (!empty($document['url'])) : ?>
							<a href="<?php echo esc_url($document['url']); ?>">View Document</a>
						<?php else : ?>
							<?php echo esc_html('-'); ?>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html(($document['addon_date'] ?? '') !== '' ? $document['addon_date'] : '-'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Title</th>
				<th>Project</th>
				<th>Department</th>
				<th>View Document</th>
				<th>Addon Date</th>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>
