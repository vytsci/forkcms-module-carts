<?php

namespace Backend\Modules\Carts;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * Class Config
 * @package Backend\Modules\Cart
 */
class Config extends BackendBaseConfig
{

    /**
     * The default action.
     *
     * @var    string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions.
     *
     * @var    array
     */
    protected $disabledActions = array();
}
