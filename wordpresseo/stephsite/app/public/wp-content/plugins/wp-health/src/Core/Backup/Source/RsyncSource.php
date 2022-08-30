<?php
namespace WPUmbrella\Core\Backup\Source;

if (!defined('ABSPATH')) {
    exit;
}

use Symfony\Component\Process\Process;
use WPUmbrella\Models\Backup\BackupSource;
use WPUmbrella\Models\Backup\BackupProcessCommandLine;

class RsyncSource implements BackupSource, BackupProcessCommandLine
{
    const DEFAULT_TIMEOUT = 900;

    private $name;
    private $source;
    private $options;
    private $timeout;

    /**
     * @param Namer $namer
     * @param string $source            The rsync source
     * @param array  $additionalOptions Additional rsync options (useful for excludes)
     * @param array  $defaultOptions    Default rsync options
     * @param int    $timeout
     */
    public function __construct($namer, $source, array $additionalOptions = [], array $defaultOptions = [], $timeout = self::DEFAULT_TIMEOUT)
    {
        $defaultOptions = count($defaultOptions) ? $defaultOptions : $this->getDefaultOptions();

        $this->namer = $namer;
        $this->source = $source;
        $this->options = $defaultOptions;
        $this->timeout = $timeout;

        foreach ($additionalOptions as $option) {
            $this->options[] = $option;
        }
    }

    public function getTimeout()
    {
        return apply_filters('wp_umbrella_backup_source_rsync_timeout', $this->timeout);
    }

    public function getCommandLine()
    {
        return 'rsync';
    }

    public function getDefaultOptions()
    {
        return apply_filters('wp_umbrella_default_options_rsync', ['-acrv', '--force', '--delete', '--progress', '--delete-excluded']);
    }

    public function fetch($scratchDir)
    {
        if (!function_exists('proc_open')) {
            return $this;
        }

        $args = array_merge([$this->getCommandLine()], $this->options, [$this->source, sprintf('%s/%s', $scratchDir, $this->getName())]);

        $process = new Process($args, null, null, null, $this->getTimeout());
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    public function getName()
    {
        return $this->namer->getName();
    }
}
