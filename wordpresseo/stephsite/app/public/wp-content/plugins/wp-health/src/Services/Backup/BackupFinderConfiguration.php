<?php
namespace WPUmbrella\Services\Backup;

if (!defined('ABSPATH')) {
    exit;
}

use Symfony\Component\Finder\Finder;

class BackupFinderConfiguration
{
    const MO_IN_BYTES = 1048576;

    /**
     * @return array
     */
    public function getDefaultExcludeFiles()
    {
        return apply_filters('wp_umbrella_get_default_exclude_files_backup', [
            WP_UMBRELLA_DIR_SCRATCH_BACKUP,
            WP_CONTENT_DIR . '/cache', // like wp-rocket
            WP_CONTENT_DIR . '/updraft', // backup updraft
            WP_CONTENT_DIR . '/ai1wm-backups', // backup ai1wm-backups
            'node_modules',
            'scratch-backup',
            'logs',
            'node_modules',
            ABSPATH . 'lscache', //lite speed cache
            'lscache' //lite speed cache

        ]);
    }

	/**
	 * @param $source
	 * @param $excludesOption
	 * @return array
	 */
    public function buildAndGetExcludeFiles($source, $excludesOption): array
	{
        $excludes = $this->getDefaultExcludeFiles();
        $excludes = array_merge($excludes, $excludesOption);

        try {
            $scanAbspath = scandir(ABSPATH);

            foreach ($scanAbspath as $key => $value) {
                if (!@is_dir($value)) {
                    continue;
                }

                if (in_array($value, ['.', '..', 'wp-content', 'wp-includes', 'wp-admin'])) {
                    continue;
                }

                $isOtherWP = wp_umbrella_get_service('DirectoryListing')->hasWordPressInSubfolder(realpath($value));
                if (!$isOtherWP) {
                    continue;
                }

                $excludes[] = realpath($value);
            }
        } catch (\Exception $e) {
            // No black magic
        }

        $lastCharIsSlash = substr($source, -1) === '/';

        foreach ($excludes as $key => $value) {
            $value = str_replace($source, '', $value);

            if ($lastCharIsSlash && isset($value[0]) && $value[0] === '/') {
                $value = \substr($value, 1);
            }

            $excludes[$key] = $value;
        }

        return $excludes;
    }

    public function getMaxMoBatchSize()
    {
        return apply_filters('wp_umbrella_max_mo_batch_size', 180); // 180 Mo
    }

    /**
     * ~13.2Mo Zip = 50 Mo Batch file size
     * Return the size by batch in bytes
     *
     * @return int
     */
    public function getMaxMoInBytesBatchSize()
    {
        $mo = $this->getMaxMoBatchSize();

        if (!function_exists('disk_free_space')) {
            return $mo * self::MO_IN_BYTES;
        }

        $freeSpace = @disk_free_space(ABSPATH);
        if (!$freeSpace) {
            return $mo * self::MO_IN_BYTES;
        }

        $freeSpace = $freeSpace / 1024 / 1024; // Mo
        if ($freeSpace > $mo) {
            return $mo * self::MO_IN_BYTES;
        }

        $freeSpaceDivided = $freeSpace / 2; // 50% free space

        if ($freeSpaceDivided >= $mo) {
            return $mo * self::MO_IN_BYTES;
        }

        return $freeSpaceDivided * self::MO_IN_BYTES;
    }

    public function cleanupFirstProcess()
    {
        $files = scandir(WP_UMBRELLA_DIR_SCRATCH_BACKUP);

        foreach ($files as $key => $file) {
            if (\in_array($file, ['.', '..', 'index.php'], true)) {
                continue;
            }

            $filename = sprintf('%s/%s', WP_UMBRELLA_DIR_SCRATCH_BACKUP, $file);

            if (\is_dir($filename)) {
                $this->destroyDir($filename);
            } elseif (\is_file($filename)) {
                unlink($filename);
            }
        }
    }

    /**
     *
     * @param array $options = [
     *		'exclude_files' => [] // string
     *		'source' => '' // source - default ABSPATH
     *		'since_date' => '', // string
     *		'size' => '', // string
     * ]
     *
     * @return Finder
     */
    public function getFinder(array $options): Finder
	{
        $excludesOption = $options['exclude_files'] ?? [];
        $source = apply_filters('wp_umbrella_file_source_base_directory', $options['source'] ?? ABSPATH);

        $excludes = $this->buildAndGetExcludeFiles($source, $excludesOption);

        $sinceDate = $options['since_date'] ?? null;
        $size = $options['size'] ?? null;

        $finder = new Finder();
        $finder->files()
                ->in($source)
                ->ignoreUnreadableDirs()
                ->ignoreDotFiles(false)
                ->exclude($excludes);

        if ($sinceDate !== null) {
            $finder->date($sinceDate);
        }

        if ($size !== null) {
            $finder->size($size);
        }

        return $finder;
    }

    /**
     *
     * @param string $dir
     * @return void
     */
    protected function destroyDir($dir)
    {
        try {
            if (!is_dir($dir) || is_link($dir)) {
                return unlink($dir);
            }

            foreach (scandir($dir) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (!$this->destroyDir($dir . DIRECTORY_SEPARATOR . $file)) {
                    chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                    if (!$this->destroyDir($dir . DIRECTORY_SEPARATOR . $file)) {
                        return false;
                    }
                };
            }
            return rmdir($dir);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     *
     * @param array $options = [
     *		'exclude_files' => [] // string
     *		'source' => '' // source - default ABSPATH
     *		'since_date' => '', // string
     *		'size' => '', // string
     * ]
     *
     * @return int
     */
    public function countTotalFiles($options)
    {
        try {
            set_time_limit(0);
            $finder = $this->getFinder($options);
            return $finder->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
