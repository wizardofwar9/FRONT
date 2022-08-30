<?php
namespace WPUmbrella\Services\Restore;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\Observers\MementoObserver;
use WPUmbrella\Core\Restore\Observers\LogStateObserver;
use WPUmbrella\Core\Restore\Observers\RetsoreOnErrorObserver;

class RestoreProcessOrder
{
    public function getHandlersScanRestore()
    {
        return [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\DiskSpaceHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\MemoryLimitHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                ]
            ],
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\FilesystemPermissions::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ];
    }

    public function getHandlersPrepareDataFiles()
    {
        return [

            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\WordPressDataFilesHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ];
    }

    public function getHandlersPrepareDataSql()
    {
        return [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\WordPressDataSqlHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ];
    }

    public function getHandlersDownloadZips()
    {
        return [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\CleanUpHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\InitProcessHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\DownloadBuildZipHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ];
    }

    public function getHandlersExtractZipFiles()
    {
        $init = $this->getHandlersPrepareDataFiles();

        return array_merge($init, [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\RestoreFilesHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ]);
    }

    public function getHandlersExtractZipDatabase()
    {
        $init = $this->getHandlersPrepareDataSql();

        return array_merge($init, [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\ExtractDatabaseHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ]);
    }

    public function getHandlersRestoreDatabase()
    {
        $init = $this->getHandlersPrepareDataSql();
        return array_merge($init, [
            [
                'handler' => \WPUmbrella\Core\Restore\ChainResponsibility\RestoreDatabaseHandler::class,
                'observers' => [
                    MementoObserver::class,
                    LogStateObserver::class,
                    RetsoreOnErrorObserver::class,
                ]
            ],
        ]);
    }
}
