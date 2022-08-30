<?php
namespace WPUmbrella\Controller\Restore;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Models\AbstractController;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;
use WPUmbrella\Core\Restore\Builder\RestoreBuilder;

class Scan extends AbstractController
{
    public function executeGet($params)
    {
        if (!isset($params['zip_size_bytes'])) {
            return $this->returnResponse(['code' => 'missing_parameters', 'message' => 'Zip size bytes is required'], 400);
        }

        $director = wp_umbrella_get_service('RestoreDirector');

        $builder = new RestoreBuilder();
        $director->setBuilder($builder);

        $director->buildScanRestoration();
        $kernel = $builder->getKernel();

        $originator = new RestoreOriginator();
        $originator->setState([
            'zip_size' => $params['zip_size_bytes']
        ]);

        $kernel->execute($originator);

        if ($kernel->hasError()) {
            return $this->returnResponse($kernel->getError(), 400);
        }

        return $this->returnResponse(['code' => 'success'], 200);
    }
}
