<?php
namespace WPUmbrella\Services\Restore;

if (!defined('ABSPATH')) {
    exit;
}

class RestoreDirector
{
    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    public function buildScanRestoration()
    {
        $this->builder->buildScanHandlers();
        $this->builder->buildCaretaker();
    }

    public function buildDownloadRestoration()
    {
        $this->builder->buildDownloadHandlers();
        $this->builder->buildCaretaker();
    }

    public function buildRestoreFilesHandlers()
    {
        $this->builder->buildRestoreFilesHandlers();
        $this->builder->buildCaretaker();
    }

    public function buildRestoreDatabaseHandlers()
    {
        $this->builder->buildRestoreDatabaseHandlers();
        $this->builder->buildCaretaker();
    }

    public function buildExtractDatabaseHandlers()
    {
        $this->builder->buildExtractDatabaseHandlers();
        $this->builder->buildCaretaker();
    }
}
