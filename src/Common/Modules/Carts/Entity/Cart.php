<?php

namespace Common\Modules\Carts\Entity;

use Common\Modules\Entities\Engine\Helper as CommonEntitiesHelper;
use Common\Modules\Entities\Engine\Entity;
use Common\Modules\Carts\Engine\Model;
use Frontend\Modules\Members\Engine\Profile;

/**
 * Class Cart
 * @package Common\Modules\Carts\Entity
 */
class Cart extends Entity
{

    protected $_table = Model::TBL_CARTS;

    protected $_query = Model::QRY_ENTITY_CART;

    protected $_columns = array(
        'profile_id',
        'order_id',
        'status',
        'callback_validate',
        'callback_checkout',
        'created_on',
    );

    protected $_relations = array(
        'profile',
        'modules',
        'items_count',
        'items_price',
        'items',
        'extra_fields',
        'errors',
    );

    protected $profileId;

    protected $orderId;

    protected $status;

    protected $callbackValidate = 'callbackCartsValidate';

    protected $callbackCheckout = 'callbackCartsCheckout';

    protected $createdOn;

    protected $profile;

    protected $modules;

    protected $itemsCount;

    protected $itemsPrice;

    protected $items = array();

    /**
     * @var Item[]
     */
    private $itemsToSave = array();

    private $errors = array();

    /**
     *
     */
    public function loadProfile()
    {
        if ($this->isLoaded()) {
            $this->profile = new Profile($this->getProfileId());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function loadItems()
    {
        if ($this->isLoaded() && empty($this->items)) {
            $this->items = Model::getCartItems($this->getId(), $this->getLanguage());
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param $profileId
     * @return $this
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @param $profileId
     * @return bool
     */
    public function isOwner($profileId)
    {
        return $this->profileId == $profileId;
    }

    /**
     * @return CartStatus
     */
    public function getStatus()
    {
        if (is_null($this->status)) {
            $this->status = new CartStatus();
        }

        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = new CartStatus($status);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackValidate()
    {
        return $this->callbackValidate;
    }

    /**
     * @param mixed $callbackValidate
     *
     * @return $this
     */
    public function setCallbackValidate($callbackValidate)
    {
        $this->callbackValidate = $callbackValidate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackCheckout()
    {
        return $this->callbackCheckout;
    }

    /**
     * @param $callbackCheckout
     * @return $this
     */
    public function setCallbackCheckout($callbackCheckout)
    {
        $this->callbackCheckout = $callbackCheckout;

        return $this;
    }

    /**
     * @param string $format
     * @return bool|int|string
     */
    public function getCreatedOn($format = 'Y-m-d H:i:s')
    {
        return CommonEntitiesHelper::getDateTime($this->createdOn, $format);
    }

    /**
     * @param $createdOn
     * @return $this
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = CommonEntitiesHelper::prepareDateTime($createdOn);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        if (is_null($this->profile)) {
            $this->loadProfile();
        }

        return $this->profile;
    }

    /**
     * @return mixed
     */
    public function getModules()
    {
        return Model::getCartModules($this->getId());
    }

    /**
     * @return mixed
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * @param $itemsCount
     * @return $this
     */
    public function setItemsCount($itemsCount)
    {
        $this->itemsCount = (int)$itemsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPrice()
    {
        if ($this->itemsPrice === null) {
            $this->itemsPrice = 0;
            foreach ($this->getItems() as $item) {
                $this->itemsPrice += $item->getPriceTotal();
            }
        }

        return $this->itemsPrice;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function addItemsCount($quantity = 1)
    {
        $this->itemsCount += $quantity;

        return $this;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function subtractItemsCount($quantity = 1)
    {
        $this->itemsCount -= $quantity;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        return (array)$this->items + (array)$this->itemsToSave;
    }

    /**
     * @param $module
     * @param $externalId
     *
     * @return Item[]
     */
    public function getItemsForModuleItem($module, $externalId)
    {
        $itemsForModuleItem = array();

        $this->loadItems();
        foreach ($this->getItems() as $item) {
            if ($item->getModule() == $module && $item->getExternalId() == $externalId) {
                $itemsForModuleItem[$item->getId()] = $item;
            }
        }

        return $itemsForModuleItem;
    }

    /**
     * @param $module
     * @param $externalId
     *
     * @return int|mixed
     */
    public function getCountItemsForModuleItem($module, $externalId)
    {
        $itemsForModuleItem = self::getItemsForModuleItem($module, $externalId);
        $count = 0;

        foreach ($itemsForModuleItem as $item) {
            $count += $item->getQuantity();
        }

        return $count;
    }

    /**
     * @param $externalId
     * @param $module
     * @param $quantity
     * @param array $options
     * @param null $callbackInfo
     * @return $this
     * @throws \Exception
     */
    public function addItem($externalId, $module, $quantity, $options = array(), $callbackInfo = null)
    {
        if (!$this->isLoaded()) {
            throw new \Exception('Item cannot be added for unsaved cart');
        }

        if (!is_array($options)) {
            throw new \Exception('Options parameter must be an array');
        }

        $item = Model::getCartItem($this->getId(), $externalId, $module, $options);

        if (!$item->isLoaded()) {
            $item
                ->setCartId($this->getId())
                ->setExternalId($externalId)
                ->setModule(\SpoonFilter::toCamelCase($module));
        }

        $item->addQuantity($quantity);

        if (isset($callbackInfo)) {
            $item->setCallbackInfo($callbackInfo);
        }

        $this->addItemsCount($quantity);

        $item->loadExternalInfo();

        if ($item->isLoaded()) {
            $this->items[$item->getId()] = $item;
        } else {
            $this->itemsToSave[] = $item;
        }

        return $this;
    }

    /**
     * @param Item $item
     * @return $this
     */
    public function removeItem(Item $item)
    {
        if (isset($this->items[$item->getId()])) {
            unset($this->items[$item->getId()]);
            $this->subtractItemsCount($item->getQuantity());
            Model::removeCartItem($item->getId());
        }

        foreach ($this->itemsToSave as $_itemIndex => $_item) {
            if ($_item == $item) {
                unset($this->itemsToSave[$_itemIndex]);
                $this->subtractItemsCount($item->getQuantity());
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $error
     * @return $this
     */
    public function addError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @param $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     *
     */
    protected function afterSave()
    {
        foreach ($this->itemsToSave as $item) {
            $item
                ->setCartId($this->getId())
                ->save()
                ->loadExternalInfo();
            $this->items[$item->getId()] = $item;
        }

        $this->itemsToSave = array();
    }
}
