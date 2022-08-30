<?php
namespace WPUmbrella\Core\Backup\Source;

if (!defined('ABSPATH')) {
    exit;
}

use Symfony\Component\Process\Process;
use WPUmbrella\Models\Backup\BackupSource;
use WPUmbrella\Models\Backup\BackupProcessCommandLine;

class MySqlDumpSource implements BackupSource, BackupProcessCommandLine
{
    const DEFAULT_USER = 'root';
    const DEFAULT_SSH_PORT = 22;
    const DEFAULT_TIMEOUT = 900;

    protected $name;
    protected $database;
    protected $host;
    protected $user;
    protected $password;
    protected $sshHost;
    protected $sshUser;
    protected $sshPort;
    protected $timeout;

    /**
     * @param string      $name
     * @param string      $database
     * @param array       $databaseConnexion
     * @param array       $sshConnexion
     * @param int         $timeout
     */
    public function __construct($namer, $database, $databaseConnexion = [], $sshConnexion = [], $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->namer = $namer;
        $this->database = $database;
        $this->host = isset($databaseConnexion['host']) ? $databaseConnexion['host'] : self::DEFAULT_USER;
        $this->user = isset($databaseConnexion['user']) ? $databaseConnexion['user'] : null;
        $this->password = isset($databaseConnexion['password']) ? $databaseConnexion['password'] : null;
        $this->sshHost = isset($sshConnexion['host']) ? $sshConnexion['host'] : null;
        $this->sshUser = isset($sshConnexion['user']) ? $sshConnexion['user'] : null;
        $this->sshPort = isset($sshConnexion['port']) ? $sshConnexion['port'] : self::DEFAULT_SSH_PORT;
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return apply_filters('wp_umbrella_backup_source_mysqldump_timeout', $this->timeout);
    }

    public function getCommandLine()
    {
        return 'mysqldump';
    }

    protected function retryWithOnlyRoot($scratchDir)
    {
        $args = [];
        $args[] = $this->getCommandLine();
        $args[] = sprintf('-u%s', $this->user);
        $args[] = $this->database;

        $process = new Process($args, null, null, null, $this->getTimeout());
        try {
            $process->run();
            return $process;
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            return $process;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($scratchDir)
    {
        if (!function_exists('proc_open')) {
            return;
        }

        $args = [];

        if (null !== $this->sshHost && null !== $this->sshUser) {
            $args[] = 'ssh';
            $args[] = sprintf('%s@%s', $this->sshUser, $this->sshHost);
            $args[] = sprintf('-p %s', $this->sshPort);
        }

        $args[] = 'mysqldump';
        $args[] = sprintf('-u%s', $this->user);

        if (null !== $this->host) {
            $args[] = sprintf('-h%s', $this->host);
        }

        if (null !== $this->password) {
            $args[] = sprintf('-p%s', $this->password);
        }

        $args[] = $this->database;

        $process = new Process($args, null, null, null, $this->getTimeout());
        try {
            $process->run();
        } catch (\Exception $e) {
        }

        if (!$process->isSuccessful()) {
            $process = $this->retryWithOnlyRoot($scratchDir);
        }

        if (!$process->isSuccessful()) {
            return;
        }

        try {
            if (!\file_exists(sprintf('%s/%s', $scratchDir, $this->getName()))) {
                @\mkdir(sprintf('%s/%s', $scratchDir, $this->getName()), 0777, true);
            }
            file_put_contents(sprintf('%s/%s/%s.sql', $scratchDir, $this->getName(), $this->database), $process->getOutput());
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->namer->getName();
    }
}
