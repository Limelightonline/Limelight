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
use Monolog\Logger;

class PLInfo extends PLBase
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/pl/info.log';

    /**
     * @var int
     */
    protected $loggerType = PLLogger::INFO;

    /**
     * @var int
     */
    protected $level = PLLogger::INFO;
}
