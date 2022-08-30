<?php
namespace WPUmbrella\Core\Restore\ChainResponsibility;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Restore\ChainResponsibility\RestoreProcessHandler;
use WPUmbrella\Core\Restore\Memento\CaretakerHandler;
use WPUmbrella\Core\Restore\Memento\RestoreOriginator;

class DownloadBuildZipHandler extends RestoreProcessHandler implements CaretakerHandler
{
    /**
     * Copy remote file over HTTP one small chunk at a time.
     *
     * @param $infile The full URL to the remote file
     * @param $outfile The path where to save the file
     */
    public function copyFileChunked($infile, $outfile)
    {
        $chunksize = 10 * (1024 * 1024); // 10 Megs

        /**
         * parse_url breaks a part a URL into it's parts, i.e. host, path,
         * query string, etc.
         */
        $parts = parse_url($infile);
        $i_handle = fsockopen($parts['host'], 80, $errstr, $errcode, 5);
        $o_handle = fopen($outfile, 'wb');

        if ($i_handle == false || $o_handle == false) {
            return false;
        }

        if (!empty($parts['query'])) {
            $parts['path'] .= '?' . $parts['query'];
        }

        /**
         * Send the request to the server for the file
         */
        $request = "GET {$parts['path']} HTTP/1.1\r\n";
        $request .= "Host: {$parts['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($i_handle, $request);

        /**
         * Now read the headers from the remote server. We'll need
         * to get the content length.
         */
        $headers = [];
        while (!feof($i_handle)) {
            $line = fgets($i_handle);
            if ($line == "\r\n") {
                break;
            }
            $headers[] = $line;
        }

        /**
         * Look for the Content-Length header, and get the size
         * of the remote file.
         */
        $length = 0;
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Length:') === 0) {
                $length = (int)str_replace('Content-Length: ', '', $header);
                break;
            }
        }

        /**
         * Start reading in the remote file, and writing it to the
         * local file one chunk at a time.
         */
        $cnt = 0;
        while (!feof($i_handle)) {
            $buf = '';
            $buf = fread($i_handle, $chunksize);
            $bytes = fwrite($o_handle, $buf);
            if ($bytes == false) {
                return false;
            }
            $cnt += $bytes;

            /**
             * We're done reading when we've reached the conent length
             */
            if ($cnt >= $length) {
                break;
            }
        }

        fclose($i_handle);
        fclose($o_handle);
        return $cnt;
    }

    public function handle($data)
    {
        $originator = $this->getOriginatorByData($data);

        $urlDownloadBackupFiles = $originator->getValueInState('url_download_backup_files');
        $urlDownloadBackupDatabase = $originator->getValueInState('url_download_backup_database');

        $filename = sprintf('%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'backup.zip');

        if (!file_exists($filename) && !empty($urlDownloadBackupFiles)) {
            $result = $this->copyFileChunked($urlDownloadBackupFiles, $filename);
            $originator->setValueInSate('zip_files_path', $filename);
            if (!$result) {
                $this->setFailHandler($data, [
                    'error_code' => 'download_build_zip_files',
                ]);
                return false;
            }
        }

        $filename = sprintf('%s/%s', untrailingslashit(WP_UMBRELLA_DIR_TEMP_RESTORE), 'database.zip');

        if (!file_exists($filename) && !empty($urlDownloadBackupDatabase)) {
            $result = $this->copyFileChunked($urlDownloadBackupDatabase, $filename);
            $originator->setValueInSate('zip_data_path', $filename);
            if (!$result) {
                $this->setFailHandler($data, [
                    'error_code' => 'download_build_zip_database',
                ]);
                return false;
            }
        }

        $data['originator'] = $originator;

        return parent::handle($data);
    }
}
