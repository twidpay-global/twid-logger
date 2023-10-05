<?php

namespace twid\logger\Facades;

use Illuminate\Support\Facades\Facade;
use twid\logger\Logger;

/**
 * TLog Facade for logging messages.
 *
 * This facade provides a dynamic method for logging messages, where the method name is treated as the log channel.
 * If the method is not explicitly defined, it is treated as a channel name, and the log method is invoked on the corresponding logger instance.
 *
 * @method static bool log(string $message, array $data = []) Log a message with the specified data.
 * @method static bool info(string $message, array $data = []) Log an information message.
 * @method static bool error(string $message, array $data = []) Log an error message.
 * @method static bool alert(string $message, array $data = []) Log an alert message.
 * @method static bool inbound(string $message, array $data = []) Log an inbound message.
 * @method static bool outbound(string $message, array $data = []) Log an outbound message.
 * @method static bool debug(string $message, array $data = []) Log a debug message.
 */
class TLog extends Facade
{
    /**
     * Associative array to store logger instances based on channels.
     *
     * @var array
     */
    protected static $loggers = [];

    /**
     * Get the logger instance based on the channel name.
     *
     * @param string $channel The log channel.
     *
     * @return Logger Logger instance.
     */
    protected static function getLogger($channel = 'default')
    {
        if (!isset(self::$loggers[$channel])) {
            self::$loggers[$channel] = new Logger($channel);
        }

        return self::$loggers[$channel];
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method The method being called.
     * @param array  $args   Arguments passed to the method.
     *
     * @return mixed The result of the method call.
     */
    public static function __callStatic($method, $args)
    {
        return self::getLogger($method)->log($args[0], $args[1] ?? []);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'twid.logger';
    }
}
