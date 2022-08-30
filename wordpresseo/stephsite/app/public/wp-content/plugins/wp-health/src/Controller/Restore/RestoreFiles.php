<?php
namespace WPUmbrella\Controller\Restore;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Restore\Builder\RestoreBuilder;

class RestoreFiles extends AbstractController
{
    public function executePost($params)
    {
        $director = wp_umbrella_get_service('RestoreDirector');

        $builder = new RestoreBuilder();
        $director->setBuilder($builder);

        $director->buildRestoreFilesHandlers();
        $kernel = $builder->getKernel();

        $originator = new RestoreOriginator();
        $originator->setState([
            'zip_files_path' => sprintf('%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'backup.zip'),
        ]);

        $kernel->execute($originator);

        if ($kernel->hasError()) {
            return $this->returnResponse($kernel->getError(), 400);
        }

        return $this->returnResponse(['code' => 'success'], 200);
    }
}
