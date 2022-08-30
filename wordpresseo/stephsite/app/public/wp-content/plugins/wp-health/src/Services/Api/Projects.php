<?php
namespace WPUmbrella\Services\Api;

if (!defined('ABSPATH')) {
    exit;
}

class Projects extends BaseClient
{
    /**
     * @return array
     */
    public function getProjects($token = null)
    {
        try {
            $response = wp_remote_get(WP_UMBRELLA_APP_URL . '/api/external/projects', [
                'headers' => $this->getHeadersV2($token),
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body;
    }

    /**
     * @return array
     */
    public function getProject($id, $token = null)
    {
        try {
            $response = wp_remote_get(\sprintf('%s/api/external/projects/%s', WP_UMBRELLA_APP_URL, $id), [
                'headers' => $this->getHeadersV2($token),
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body;
    }

    /**
     * @return array
     */
    public function createProjectOnApplication($data, $token = null)
    {
        try {
            $response = wp_remote_post(WP_UMBRELLA_APP_URL . '/api/external/projects', [
                'headers' => $this->getHeadersV2($token),
                'body' => json_encode($data),
                'sslverify' => false,
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        wp_umbrella_get_service('Logger')->info($body);

        return $body;
    }

    public function snapshotData($data, $token = null)
    {
        try {
            $id = wp_umbrella_get_option('project_id');
            if (!$id) {
                return;
            }

            $url = sprintf(WP_UMBRELLA_API_URL . '/v1/projects/%s/snapshot-wp-data', $id);
            $response = wp_remote_post($url, [
                'headers' => $this->getHeadersV2($token),
                'body' => json_encode($data),
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        wp_umbrella_get_service('Logger')->info($body);
        return $body;
    }
}
