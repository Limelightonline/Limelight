<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Logger\Handler;

use PL\Migs\Logger\PLLogger;
use /** @noinspection PhpUndefinedClassInspection */
    Monolog\Logger;

class PLDebug extends PLBase
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/pl/debug.log';

    /**
     * @var int
     */
    protected $loggerType = PLLogger::PL_DEBUG;

    /**
     * @var int
     */
    protected $level = PLLogger::PL_DEBUG;
}
