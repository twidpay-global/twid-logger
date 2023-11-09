<?php

namespace twid\logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as MonologLogger;

/**
 * Logger class for handling log messages.
 */
class Logger
{
    /**
     * The Monolog logger instance.
     *
     * @var MonologLogger
     */
    protected $log;

    /**
     * Configuration settings for the logger.
     *
     * @var array
     */
    protected $config;

    /**
     * The current log channel.
     *
     * @var string
     */
    protected $channel;

    /**
     * Metadata to be included in log entries.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * Logger constructor.
     *
     * @param string $channel The log channel.
     *
     * @throws \InvalidArgumentException If channel configuration is not found.
     */
    public function __construct($channel = 'default')
    {
        $this->log = new MonologLogger($channel);

        $this->getConfig();
        $this->channel = $channel;

        if (!isset($this->config['channels'][$channel])) {
            throw new \InvalidArgumentException("Channel configuration for '$channel' not found. Please check logging.php"); //@todo check with kushal if this needs to be provided or not
        }

        $channelConfig = $this->config['channels'][$channel];

        $logPath = $channelConfig['path'];
        $logLevel = $channelConfig['level'];

        $handler = new RotatingFileHandler($logPath, 0, $logLevel);
        $handler->setFormatter(new JsonFormatter());

        $this->log->pushHandler($handler);
    }

    private function getConfig()
    {
        if (function_exists('app') && app()) {
            $this->config = include(dirname(__DIR__, 4) . '/config/tlogger.php');
        } else {
            $this->config = include(__DIR__ . '/config/tlogger.php');
        }
    }

    /**
     * Log a message with the specified data.
     *
     * @param string $message The log message.
     * @param array  $data    Additional data to be included in the log entry.
     *
     * @return bool True on success, false on failure.
     */
    public function log($message, $data = [])
    {
        $data = $this->maskFields($data);

        $data['metadata'] = $this->metadata();
        $this->maskFields($data);

        return $this->log->log($this->config['channels'][$this->channel]['level'], $message, $data);
    }

    /**
     * Retrieve metadata based on configuration settings.
     *
     * @return array Metadata to be included in log entries.
     */
    protected function metadata()
    {
        $defaultLog = $this->config['metadata'];
        $request = $_REQUEST;

        foreach ($defaultLog as $data) {
            $this->metadata[$data] = $request[$data] ?? null;
        }

        return $this->maskFields($this->metadata);
    }

    /**
     * Mask sensitive fields in the provided data.
     *
     * @param array $data Data to be masked.
     *
     * @return array Masked data.
     */
    protected function maskFields($data = [])
    {
        $fields = $this->config['mask'];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '********';
            }
        }

        return $data;
    }
}
