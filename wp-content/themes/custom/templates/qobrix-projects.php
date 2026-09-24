<?php

/**
 * Projects list template
 *
 * @package qobrix
 */

?>
<?php if ($atts['projects']) : ?>
	<table id="dataTable" class="ui celled table" style="width:100%">
		<thead>
			<tr>
				<th>Projects</th>
				<th>Documents</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($atts['projects'] as $project) : ?>
				<tr>
					<td><?php echo esc_attr($project->post_title); ?></td>
					<td>
						<?php echo esc_attr($project->documents_count); ?>
						<?php if ($project->documents_count > 0) : ?>
							<a href="<?php echo esc_url(home_url('/my-documents/?project_id=' . $project->ID)); ?>">View</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Projects</th>
				<th>Documents</th>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>
