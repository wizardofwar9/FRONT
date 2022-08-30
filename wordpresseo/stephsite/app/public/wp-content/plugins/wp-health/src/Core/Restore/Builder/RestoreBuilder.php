<?php
namespace WPUmbrella\Core\Restore\Builder;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\RestoreKernel;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreCaretaker;
use WPUmbrella\Core\Restore\Observers\MementoObserver;
use WPUmbrella\Core\Restore\Observers\LogStateObserver;
use WPUmbrella\Core\Restore\Observers\RetsoreOnErrorObserver;
use WPUmbrella\Core\Models\Observers\Subject;

class RestoreBuilder
{
    protected $kernel;

    protected $processes;

    protected $caretaker;

    public function __construct()
    {
        $this->reset();
    }

    protected function reset()
    {
        $this->kernel = new RestoreKernel();
        $this->processes = null;
        $this->caretaker = null;
    }

    public function buildCaretaker()
    {
        $this->caretaker = new RestoreCaretaker();
    }

    protected function buildObservers(Subject $item, $observers)
    {
        if (empty($observers)) {
            return $item;
        }

        foreach ($observers as $observer) {
            switch ($observer) {
                case MementoObserver::class:
                    $item->attach(new MementoObserver());
                    break;
                case RetsoreOnErrorObserver::class:
                    $item->attach(new RetsoreOnErrorObserver());
                    break;
                case LogStateObserver::class:
                    $item->attach(new LogStateObserver());
                    break;
            }
        }

        return $item;
    }

    protected function buildProcesses($handlers)
    {
        $processes = null;
        foreach (array_reverse($handlers) as $item) {
            $handler = new $item['handler']();

            if (isset($item['observers'])) {
                $handler = $this->buildObservers($handler, $item['observers']);
            }

            if ($processes !== null) {
                $handler->linkWith($processes);
            }

            $processes = $handler;
        }

        $this->processes = $processes;
    }

    public function buildScanHandlers()
    {
        $handlers = wp_umbrella_get_service('RestoreProcessOrder')->getHandlersScanRestore();

        $this->buildProcesses($handlers);
    }

    public function buildDownloadHandlers()
    {
        $handlers = wp_umbrella_get_service('RestoreProcessOrder')->getHandlersDownloadZips();

        $this->buildProcesses($handlers);
    }

    public function buildRestoreFilesHandlers()
    {
        $handlers = wp_umbrella_get_service('RestoreProcessOrder')->getHandlersExtractZipFiles();

        $this->buildProcesses($handlers);
    }

    public function buildExtractDatabaseHandlers()
    {
        $handlers = wp_umbrella_get_service('RestoreProcessOrder')->getHandlersExtractZipDatabase();

        $this->buildProcesses($handlers);
    }

    public function buildRestoreDatabaseHandlers()
    {
        $handlers = wp_umbrella_get_service('RestoreProcessOrder')->getHandlersRestoreDatabase();

        $this->buildProcesses($handlers);
    }

    public function getKernel()
    {
        if ($this->processes !== null) {
            $this->kernel->setRestoreProcess($this->processes);
        }

        if ($this->caretaker !== null) {
            $this->kernel->setCaretaker($this->caretaker);
        }

        $kernel = $this->kernel;
        $this->reset();

        return $kernel;
    }
}
