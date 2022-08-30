<?php
namespace WPUmbrella\Controller\Restore;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Restore\Builder\RestoreBuilder;

class ZipDownload extends AbstractController
{
    public function executePost($params)
    {
        if (!isset($params['url_download_backup_files']) && !isset($params['url_download_backup_database'])) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'Download Url is required'], 400);
        }

        if (isset($params['url_download_backup_files']) && empty($params['url_download_backup_files'])) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'Download Url is empty'], 400);
        }

        if (isset($params['url_download_backup_database']) && empty($params['url_download_backup_database'])) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'Download Url is empty'], 400);
        }

        $director = wp_umbrella_get_service('RestoreDirector');

        $builder = new RestoreBuilder();
        $director->setBuilder($builder);

        $director->buildDownloadRestoration();
        $kernel = $builder->getKernel();

        $originator = new RestoreOriginator();
        $originator->setState([
            'url_download_backup_database' => isset($params['url_download_backup_database']) ? $params['url_download_backup_database'] : null,
            'url_download_backup_files' => isset($params['url_download_backup_files']) ? $params['url_download_backup_files'] : null,
        ]);

        $kernel->execute($originator);
        if ($kernel->hasError()) {
            return $this->returnResponse($kernel->getError(), 400);
        }

        return $this->returnResponse(['code' => 'success'], 200);
    }
}
