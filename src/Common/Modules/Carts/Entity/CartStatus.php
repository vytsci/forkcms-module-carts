<?php

namespace Common\Modules\Carts\Entity;

use Common\Modules\Entities\Engine\EnumValue;

/**
 * Class CartStatus
 * @package Common\Modules\Carts\Entity
 */
class CartStatus extends EnumValue
{

    /**
     * @var string
     */
    protected $defaultValue = 'active';

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getValue() == 'active';
    }

    /**
     * @return bool
     */
    public function isInactive()
    {
        return $this->getValue() == 'inactive';
    }

    /**
     * @param bool $lazyLoad
     * @return array
     */
    public function toArray($lazyLoad = true)
    {
        return parent::toArray() + array(
            'is_active' => $this->isActive(),
            'is_inactive' => $this->isInactive(),
        );
    }
}
