<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */

namespace PL\Migs\Logger;

use /** @noinspection PhpUndefinedClassInspection */
    Monolog\Logger;

class PLLogger extends Logger
{
    const PL_DEBUG = 101;

    const PL_RESULT = 202;

    /**
     * @var array
     */
    protected static $levels = [
        100 => 'DEBUG',
        101 => 'PL_DEBUG',
        200 => 'INFO',
        202 => 'PL_RESULT',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

    /**
     * @param $message
     * @param array $context
     * @return bool
     */
    public function addPLDebug($message, array $context = [])
    {
        return $this->addRecord(static::PL_DEBUG, $message, $context);
    }

    /**
     * @param $message
     * @param array $context
     * @return bool
     */
    public function addPLResult($message, array $context = [])
    {
        return $this->addRecord(static::PL_RESULT, $message, $context);
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addRecord($level, $message, array $context = [])
    {
        $context['is_exception'] = $message instanceof \Exception;
        return parent::addRecord($level, $message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addNotificationLog($message, array $context = [])
    {
        return $this->addRecord(static::INFO, $message, $context);
    }
}
