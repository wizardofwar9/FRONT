<?php

if (!defined('ABSPATH')) {
    exit;
}

if (isset($_GET['support']) && $_GET['support'] === '1') {
    require_once __DIR__ . '/_support.php';
    return;
}

?>

<div id="wrap-wphealth">
	<div class="wrap">
		<h1 class="screen-reader-text"><?php echo WP_UMBRELLA_NAME . ' – ' . __('Settings'); ?>
		</h1>
		<div id="js-module-wp-health" class="module-wp-health"></div>
	</div>
</div>


<input type="hidden" id="_nonce_wp_health_allow_tracking"
	value="<?php echo wp_create_nonce('wp_health_allow_tracking'); ?>">
<input type="hidden" id="_nonce_wp_health_disallow_tracking"
	value="<?php echo wp_create_nonce('wp_health_disallow_tracking'); ?>">
<input type="hidden" id="_nonce_wp_umbrella_disallow_tracking_error"
	value="<?php echo wp_create_nonce('wp_umbrella_disallow_tracking_error'); ?>">
<input type="hidden" id="_nonce_wp_health_proxy"
	value="<?php echo wp_create_nonce('wp_health_proxy'); ?>">
<input type="hidden" id="_nonce_wp_health_register"
	value="<?php echo wp_create_nonce('wp_health_register'); ?>">
<input type="hidden" id="_nonce_wp_health_login"
	value="<?php echo wp_create_nonce('wp_health_login'); ?>">
<input type="hidden" id="_nonce_wp_umbrella_valid_api_key"
	value="<?php echo wp_create_nonce('wp_umbrella_valid_api_key'); ?>">
<input type="hidden" id="_nonce_wp_umbrella_check_api_key"
	value="<?php echo wp_create_nonce('wp_umbrella_check_api_key'); ?>">
