<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Migs\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

class PLBase extends Base
{
    /**
     * overwrite core it needs to be the exact level otherwise use different handler
     *
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return $record['level'] == $this->level;
    }
}
