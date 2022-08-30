<?php

if (!defined('ABSPATH')) {
    exit;
}
$options = wp_umbrella_get_service('Option')->getOptions();
$logsItems = wp_umbrella_get_service('Logger')->getLogs();

?>


<div id="wrap-wphealth">
	<div class="wrap">
		<div class="module-wp-health">
			<h1>WP Umbrella Settings - Support</h1>
			<form method="post" action="<?php echo admin_url('admin-post.php'); ?>" novalidate="novalidate">
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label for="api_key">API Key</label></th>
							<td>
								<input name="api_key" type="password" id="api_key" value="<?php echo isset($options['api_key']) ? $options['api_key'] : ''; ?>" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="project_id">Project ID</label></th>
							<td>
								<input name="project_id" type="text" id="project_id" value="<?php echo isset($options['project_id']) ? $options['project_id'] : ''; ?>" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="wp_health_allow_tracking">Allow tracking errors</label></th>
							<td>

								<input name="wp_health_allow_tracking" type="checkbox" id="wp_health_allow_tracking" value="1" <?php checked(get_option('wp_health_allow_tracking'), '1'); ?>>
							</td>
						</tr>
					</tbody>
				</table>

				<?php wp_nonce_field('wp_umbrella_support_option'); ?>
				<input type="hidden" name="action" value="wp_umbrella_support_option" />
				<?php submit_button(); ?>
			</form>
			<h2 class="font-bold text-lg mb-2">Logs</h2>
			<div class="grid grid-cols-3">
				<?php foreach($logsItems as $key => $logs): ?>
					<div>
						<h3 class="first-letter:uppercase"><?php echo $key; ?></h3>
						<div class=" divide-y divide-gray-200">
							<?php foreach($logs as $log): ?>
								<div class="py-3">
									<p class="text-sm text-gray-500 font-bold whitespace-normal break-all"><?php echo $log['time']; ?></p>
									<pre class="text-sm text-gray-500 bg-gray-50 border rounded p-1 whitespace-normal break-all"><?php echo is_string($log['message']) ? $log['message'] : print_r($log['message']); ?></pre>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
