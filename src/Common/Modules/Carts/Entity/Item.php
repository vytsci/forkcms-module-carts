<?php

namespace Common\Modules\Carts\Entity;

use Common\Modules\Entities\Engine\Helper as CommonEntitiesHelper;
use Common\Modules\Entities\Engine\Entity;
use Common\Modules\Carts\Engine\Model;

/**
 * Class Item
 * @package Common\Modules\Carts\Entity
 */
class Item extends Entity
{

    protected $_table = Model::TBL_ITEMS;

    protected $_query = Model::QRY_ENTITY_ITEM;

    protected $_columns = array(
        'cart_id',
        'external_id',
        'module',
        'quantity',
        'callback_info',
        'added_on',
    );

    protected $_relations = array(
        'options',
        'title',
        'url',
        'price',
        'price_total',
        'extra_fields',
        'extra_values',
        'errors',
    );

    protected $cartId;

    protected $externalId;

    protected $module;

    protected $quantity = 0;

    protected $callbackInfo = 'callbackCartsItemsInfo';

    protected $addedOn;

    protected $options;

    protected $title;

    protected $url;

    protected $price;

    protected $extraFields = array();

    protected $extraValues = array();

    private $loadExternalInfo = true;

    private $errors = array();

    /**
     * @var ItemOption[]
     */
    private $optionsToSave = array();

    /**
     * @return $this
     */
    public function loadOptions()
    {
        if ($this->isLoaded() && $this->options === null) {
            $this->options = Model::getItemOptions($this->getId(), $this->determineLanguage());
        }

        return $this;
    }

    /**
     *
     */
    public function loadExternalInfo()
    {
        if ($this->loadExternalInfo) {
            $class = '\\Frontend\\Modules\\' . $this->getModule() . '\\Engine\\Model';
            $method = $this->getCallbackInfo();

            if (is_callable(array($class, $method))) {
                $call = $class . '::' . $method;

                $item = call_user_func(
                    $call,
                    $this->getExternalId(),
                    $this->getOptionsValuesPairs(),
                    $this->determineLanguage()
                );

                if (empty($item) || !isset($item['title']) || !isset($item['url']) || !isset($item['price'])) {
                    $this->delete();
                }

                $this->setTitle($item['title']);
                unset($item['title']);
                $this->setUrl($item['url']);
                unset($item['url']);
                $this->setPrice($item['price']);
                unset($item['price']);

                if (!empty($item)) {
                    foreach ($item as $extraField => $extraValue) {
                        $this->addExtraValue($extraField, $extraValue);
                    }
                }

                $this->loadExternalInfo = false;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @param mixed $cartId
     * @return $this
     */
    public function setCartId($cartId)
    {
        $this->cartId = $cartId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param mixed $externalId
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int)$quantity;

        return $this;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function addQuantity($quantity)
    {
        $this->quantity += $quantity;

        return $this;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function subtractQuantity($quantity)
    {
        $this->quantity -= $quantity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackInfo()
    {
        return $this->callbackInfo;
    }

    /**
     * @param $callbackInfo
     * @return $this\
     */
    public function setCallbackInfo($callbackInfo)
    {
        $this->callbackInfo = $callbackInfo;

        return $this;
    }

    /**
     * @param string $format
     * @return bool|int|string
     */
    public function getAddedOn($format = 'Y-m-d H:i:s')
    {
        return CommonEntitiesHelper::getDateTime($this->addedOn, $format);
    }

    /**
     * @param $addedOn
     * @return $this
     */
    public function setAddedOn($addedOn)
    {
        $this->addedOn = CommonEntitiesHelper::prepareDateTime($addedOn);

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        $options = $this->getOptionsValuesPairs();

        return isset($options[$name]);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getOptionValue($name)
    {
        $options = $this->getOptionsValuesPairs();

        return isset($options[$name]) ? $options[$name] : null;
    }

    /**
     * @return mixed
     */
    public function hasOptions()
    {
        return (isset($this->options) && !empty($this->options)) || !empty($this->optionsToSave);
    }

    /**
     * @return ItemOption[]
     */
    public function getOptions()
    {
        return (array)$this->options + (array)$this->optionsToSave;
    }

    /**
     * @return array
     */
    public function getOptionsValuesPairs()
    {
        $result = array();

        if ($this->hasOptions()) {
            foreach ($this->getOptions() as $option) {
                $result[$option->getName()] = $option->getValue();
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptionsTitlesPairs()
    {
        $result = array();

        if ($this->hasOptions()) {
            foreach ($this->getOptions() as $option) {
                $result[$option->getName()] = $option->getTitle();
            }
        }

        return $result;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceTotal()
    {
        return $this->getPrice() * $this->getQuantity();
    }

    /**
     * @return mixed
     */
    public function getExtraFields()
    {
        return $this->extraFields;
    }

    /**
     * @return mixed
     */
    public function getExtraValues()
    {
        return $this->extraValues;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addExtraValue($name, $value)
    {
        $this->extraValues[$name] = array('value' => $value);
        $this->extraFields[$name] = array('value' => $name);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors) && is_array($this->errors);
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
     * @param $module
     * @param $name
     * @param $value
     * @param null $callbackInfo
     * @return $this
     * @throws \Exception
     */
    public function addOption($module, $name, $value, $callbackInfo = null)
    {
        $option = Model::getItemOption($this->getId(), $module, $name);

        if (!$option->isLoaded()) {
            $option
                ->setItemId($this->getId())
                ->setModule($module)
                ->setName($name);
        }

        $option->setValue($value);

        if (isset($callbackInfo)) {
            $option->setCallbackInfo($callbackInfo);
        }

        $option->loadExternalInfo();

        if ($this->isLoaded()) {
            $this->options[$option->getId()] = $option;
        } else {
            $this->optionsToSave[] = $option;
        }

        return $this;
    }

    /**
     *
     */
    protected function afterSave()
    {
        foreach ($this->optionsToSave as $option) {
            $option
                ->setItemId($this->getId())
                ->save();
        }
    }
}
