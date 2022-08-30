<?php
namespace WPUmbrella\Core\Backup\Source;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupSource;
use WPUmbrella\Core\Iterator\LimitBySizeIterator;
use WPUmbrella\Models\Backup\BackupProcessCommandLine;
use ZipArchive;

class FinderBySizeSource implements BackupSource, BackupProcessCommandLine
{
    const DEFAULT_TIMEOUT = 900;

    protected $name;
    protected $source;
    protected $sinceDate = null;
    protected $size = null;
    protected $timeout;

    /**
     * @param Namer $name
     * @param string $source            The rsync source
     * @param int    $timeout
     */
    public function __construct($namer, $source, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->namer = $namer;
        $this->source = $source;
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return apply_filters('wp_umbrella_backup_source_finder_batch_timeout', $this->timeout);
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    public function setSinceDate($sinceDate)
    {
        $this->sinceDate = $sinceDate;
        return $this;
    }

    public function getCommandLine()
    {
        return null;
    }

    public function fetch($scratchDir)
    {
        $dataObj = wp_umbrella_get_service('BackupBatchData')->getData('file');

        $finder = wp_umbrella_get_service('BackupFinderConfiguration')->getFinder([
            'source' => $this->source,
            'since_date' => $this->sinceDate,
            'size' => $this->size,
            'exclude_files' => $dataObj->getExcludeFiles(),
        ]);

        $current = $dataObj->getCurrentBatchProcessor();

        $iterator = new LimitBySizeIterator($finder->getIterator(), $current);
        $iterator->setMaxSize($dataObj->getMaxMoInBytesBatchSize());

        $fileZip = sprintf('%s/%s.zip', $scratchDir, $this->getName());

        try {
            $zip = new ZipArchive();
            $zip->open($fileZip, ZipArchive::CREATE);

            foreach ($iterator as $file) {
                $realPath = $file->getRealPath();
                if (!\file_exists($realPath)) {
                    continue;
                }
                if (\strpos($realPath, '.DS_Store') !== false) {
                    continue;
                }

                $zip->addFile($file->getRealPath(), $file->getRelativePathname());
            }

            $zip->close();
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return [
                'success' => false,
                'iterator_position' => $iterator->getPosition(),
                'processed_data' => $dataObj
            ];
        }

        return [
            'success' => true,
            'iterator_position' => $iterator->getPosition(),
            'processed_data' => $dataObj
        ];
    }

    public function getName()
    {
        return $this->namer->getName();
    }
}
