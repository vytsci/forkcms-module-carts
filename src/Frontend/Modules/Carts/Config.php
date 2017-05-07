<?php

namespace Frontend\Modules\Carts;

use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/**
 * Class Config
 * @package Frontend\Modules\Carts
 */
class Config extends FrontendBaseConfig
{

    /**
     * The default action
     *
     * @var    string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var    array
     */
    protected $disabledActions = array();
}
