<?php
namespace WPUmbrella\Services;

if (!defined('ABSPATH')) {
    exit;
}

use Symfony\Component\Finder\Finder;

class DirectoryListing
{
    public function isWordPressInSubfolder()
    {
        $indexPath = realpath($GLOBALS['_SERVER']['SCRIPT_FILENAME']);
        $indexDir = dirname($indexPath);
        $absPath = realpath(ABSPATH);

        if (strncmp($absPath, $indexDir, strlen($indexDir)) === 0 && strlen($absPath) > strlen($indexDir)) {
            // absPath is /foo/bar/site, indexDir is /foo/bar (realpath and dirname both clean the path to remove duplicate and trailing slashes)
            // meaning that we hit the website via the index file in the parent directory, but the ABSPATH is nested deeper in the filesystem.
            return true;
        }
        $currentDirPath = rtrim(dirname($indexPath), '/');
        $currentDirName = basename($currentDirPath);
        $parentIndexFile = rtrim(dirname($currentDirPath), '/') . '/index.php';

        if (!file_exists($parentIndexFile)) {
            return false;
        }

        $indexText = file_get_contents($parentIndexFile);
        $searchFor = '/' . $currentDirName . '/wp-blog-header.php';

        if (stripos($indexText, $searchFor) === false) {
            return false;
        }

        return true;
    }

    public function hasWordPressInSubfolder($directory)
    {
        $indexPath = realpath($GLOBALS['_SERVER']['SCRIPT_FILENAME']);
        $indexDir = dirname($indexPath);

        $indexFile = $directory . '/index.php';

        if (!file_exists($indexFile)) {
            return false;
        }

        $indexText = file_get_contents($indexFile);

        $searchFor = '/wp-blog-header.php';

        if (stripos($indexText, $searchFor) === false) {
            return false;
        }

        return true;
    }

    public function getData($baseDirectory = ABSPATH)
    {
        $finderFiles = new Finder();
        $finderFiles->files()
                ->in($baseDirectory)
                ->ignoreUnreadableDirs()
                ->ignoreDotFiles(false)
                ->depth(0);

        $finderDirectories = new Finder();
        $finderDirectories->directories()
                ->in($baseDirectory)
                ->ignoreUnreadableDirs()
                ->ignoreDotFiles(false)
                ->depth(0);

        $directories = [];
        $files = [];

        foreach ($finderFiles as $key => $file) {
            $path = \str_replace(ABSPATH, '', $file->getRealPath());
            $size = 0;
            try {
                $size = $file->getSize();
            } catch (\Exception $e) {
                // no black magic
            }
            $files[] = [
                'file_path' => $path,
                'pathname' => $file->getRelativePathname(),
                'size' => $size,
            ];
        }
        foreach ($finderDirectories as $key => $file) {
            $path = \str_replace(ABSPATH, '', $file->getRealPath());
            $size = 0;
            try {
                $size = $file->getSize();
            } catch (\Exception $e) {
                // no black magic
            }
            $directories[] = [
                'file_path' => $path,
                'pathname' => $file->getRelativePathname(),
                'size' => $size
            ];
        }

        return [
            'directories' => $directories,
            'files' => $files
        ];
    }
}
