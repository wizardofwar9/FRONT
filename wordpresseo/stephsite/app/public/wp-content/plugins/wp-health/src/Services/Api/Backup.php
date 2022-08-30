<?php
namespace WPUmbrella\Services\Api;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupProcessedData;

class Backup extends BaseClient
{
    const NAME_SERVICE = 'BackupApi';

    /**
    * Upload directly on the cloud storage

    * @param string $signedUrl
    * @param string $filename
    * @return void
    */
    public function postBackupBySignedUrl($signedUrl, $filename)
    {
        $filePath = @realpath(sprintf('%s/%s', WP_UMBRELLA_DIR_SCRATCH_BACKUP, $filename));
        if (!file_exists($filePath)) {
            return ['success' => false];
        }

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_PUT, 1);
            curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));
            curl_setopt($curl, CURLOPT_INFILE, ($in = fopen($filePath, 'r')));
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/zip']);
            curl_setopt($curl, CURLOPT_URL, $signedUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            if (!empty($response)) {
                \wp_umbrella_get_service('Logger')->info(sprintf('backup uploaded: %s', $filename));
                return;
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
        }
    }

    /**
     *
     * @param string $filename
     * @return string
     */
    public function getSignedUrlForUpload($filename)
    {
        if (!wp_umbrella_get_api_key()) {
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            return null;
        }

        try {
            $response = wp_remote_get(sprintf('%s/v1/projects/%s/backups/signed-url?filename=%s', WP_UMBRELLA_API_URL, $projectId, $filename), [
                'headers' => $this->getHeadersV2(),
                'timeout' => 50,
            ]);
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }

        try {
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (!$body['success']) {
                return null;
            }

            return isset($body['result']['signed_url']) ? $body['result']['signed_url'] : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function postInitBackup(BackupProcessedData $model)
    {
        if (!wp_umbrella_get_api_key()) {
            \wp_umbrella_get_service('Logger')->error('No API Key for backup.');
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            \wp_umbrella_get_service('Logger')->error('No project ID for backup.');
            return null;
        }

        if (!function_exists('curl_init')) {
            \wp_umbrella_get_service('Logger')->error('No curl for backup.');
            return null;
        }

        try {
            $body = $model->getData();

            $url = sprintf(WP_UMBRELLA_API_URL . '/v1/projects/%s/backups/init', $projectId);

            $response = wp_remote_post($url, [
                'headers' => $this->getHeadersV2(),
                'body' => json_encode($body),
                'timeout' => 55,
            ]);

            $body = json_decode(wp_remote_retrieve_body($response), true);

            return $body;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }

    public function putUpdateBackupData(BackupProcessedData $model, $type)
    {
        if (!wp_umbrella_get_api_key()) {
            \wp_umbrella_get_service('Logger')->error('No API Key for backup.');
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            \wp_umbrella_get_service('Logger')->error('No project ID for backup.');
            return null;
        }

        if (!function_exists('curl_init')) {
            \wp_umbrella_get_service('Logger')->error('No curl for backup.');
            return null;
        }

        try {
            $body = $model->getData();
            $body['type'] = $type;

            $url = sprintf(WP_UMBRELLA_API_URL . '/v1/projects/%s/backups/%s', $projectId, $model->getBackupId());

            $response = wp_remote_request($url, [
                'method' => 'PUT',
                'headers' => $this->getHeadersV2(),
                'body' => json_encode($body),
                'timeout' => 55,
            ]);

            $body = json_decode(wp_remote_retrieve_body($response), true);

            return $body;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }

    public function postErrorBackup(BackupProcessedData $model, $type)
    {
        if (!wp_umbrella_get_api_key()) {
            \wp_umbrella_get_service('Logger')->error('No API Key for backup.');
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            \wp_umbrella_get_service('Logger')->error('No project ID for backup.');
            return null;
        }

        if (!function_exists('curl_init')) {
            \wp_umbrella_get_service('Logger')->error('No curl for backup.');
            return null;
        }

        try {
            $url = sprintf(WP_UMBRELLA_API_URL . '/v1/projects/%s/backups/%s/error', $projectId, $model->getBackupId());
            $body = $model->getData();
            $body['type'] = $type;

            $response = wp_remote_post($url, [
                'headers' => $this->getHeadersV2(),
                'body' => json_encode($body),
                'timeout' => 55,
            ]);

            return $response;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }

    public function postFinishBackup(BackupProcessedData $model, $type)
    {
        if (!wp_umbrella_get_api_key()) {
            \wp_umbrella_get_service('Logger')->error('No API Key for backup.');
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            \wp_umbrella_get_service('Logger')->error('No project ID for backup.');
            return null;
        }

        if (!function_exists('curl_init')) {
            \wp_umbrella_get_service('Logger')->error('No curl for backup.');
            return null;
        }

        try {
            $body = $model->getData();
            $body['type'] = $type;

            $url = sprintf(WP_UMBRELLA_API_URL . '/v1/projects/%s/backups/%s/finish', $projectId, $model->getBackupId());

            $response = wp_remote_post($url, [
                'headers' => $this->getHeadersV2(),
                'body' => json_encode($body),
                'timeout' => 55,
            ]);

            return $response;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }

    /**
     * @return array
     */
    public function postBackup($filename, BackupProcessedData $model)
    {
        if (!wp_umbrella_get_api_key()) {
            return null;
        }

        $projectId = wp_umbrella_get_option('project_id');
        if (!$projectId) {
            return null;
        }

        if (!function_exists('curl_init')) {
            \wp_umbrella_get_service('Logger')->error('No curl for backup.');
            return null;
        }

        try {
            $filePath = @realpath(sprintf('%s/%s', WP_UMBRELLA_DIR_SCRATCH_BACKUP, $filename));

            if (!file_exists($filePath)) {
                \wp_umbrella_get_service('Logger')->error([
                    'message' => sprintf('File %s does not exist', $filePath),
                    'function' => 'sendBackupByCurl',
                ]);
                return ['success' => false];
            }

            $postData = [
                'file' => new \CURLFile($filePath),
                'data' => json_encode($model->getData()),
            ];

            $headers = $this->getHeadersV2(wp_umbrella_get_option('api_key'), ['type' => 'file'], true);

            $projectId = wp_umbrella_get_option('project_id');

            @set_time_limit(900);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('%s/v1/projects/%s/backups', WP_UMBRELLA_API_URL, $projectId));
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 900);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 900);
            @curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);

            $body = json_decode($response, true);
            \wp_umbrella_get_service('Logger')->info($body);

            return $body;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return null;
        }
    }
}
