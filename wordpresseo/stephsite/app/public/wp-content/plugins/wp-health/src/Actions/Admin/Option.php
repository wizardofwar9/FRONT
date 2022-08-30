<?php

namespace WPUmbrella\Actions\Admin;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Hooks\ActivationHook;
use WPUmbrella\Core\Hooks\ExecuteHooksBackend;

class Option implements ExecuteHooksBackend, ActivationHook
{
    public function __construct()
    {
        $this->optionService = wp_umbrella_get_service('Option');
    }

    public function hooks()
    {
        add_action('admin_init', [$this, 'init']);
		add_action('admin_post_wp_umbrella_support_option', [$this, 'supportOption']);
		add_action('admin_post_wp_umbrella_reset_log', [$this, 'resetLog']);
    }

    public function activate()
    {
        update_option('wphealth_version', WP_UMBRELLA_VERSION, false);
        $options = $this->optionService->getOptions();

        $this->optionService->setOptions($options);
    }

	public function supportOption(){
		if(!isset($_POST['_wpnonce'])){
			wp_redirect(admin_url());
			return;
		}

		if(!wp_verify_nonce($_POST['_wpnonce'], 'wp_umbrella_support_option')){
			wp_redirect(admin_url());
			return;
		}

		if(isset($_POST['wp_health_allow_tracking']) && $_POST['wp_health_allow_tracking'] === '1'){
			update_option('wp_health_allow_tracking', true);
		}
		else {
			update_option('wp_health_allow_tracking', false);
		}

		$options = $this->optionService->getOptions();
		$options['project_id'] = isset($_POST['project_id']) ? sanitize_text_field($_POST['project_id']) : '';
		$options['api_key'] = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
		$this->optionService->setOptions($options);
		wp_redirect(admin_url('/options-general.php?page=wp-umbrella-settings&support=1'));
		return;
	}

	public function resetLog(){
		if(!isset($_POST['_wpnonce'])){
			wp_redirect(admin_url());
			return;
		}

		if(!wp_verify_nonce($_POST['_wpnonce'], 'wp_umbrella_support_option')){
			wp_redirect(admin_url());
			return;
		}

		\wp_umbrella_get_service('Logger')->resetLog();

		wp_redirect(admin_url('/options-general.php?page=wp-umbrella-settings&support=1'));
		return;
	}

    /**
     * Register setting options.
     *
     * @see admin_init
     */
    public function init()
    {
        register_setting(WP_UMBRELLA_OPTION_GROUP, WP_UMBRELLA_SLUG, [$this, 'parseArgs']);
    }

    /**
     * Callback register_setting for parseArgs options.
     *
     * @param array $options
     *
     * @return array
     */
    public function parseArgs($options)
    {
        $optionsBdd = $this->optionService->getOptions();
        $newOptions = wp_parse_args($options, $optionsBdd);

        return $newOptions;
    }
}
