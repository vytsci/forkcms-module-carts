<?php

namespace Common\Modules\Carts\Entity;

use Common\Modules\Entities\Engine\Helper as CommonEntitiesHelper;
use Common\Modules\Entities\Engine\Entity;
use Common\Modules\Carts\Engine\Model;

/**
 * Class ItemOption
 * @package Common\Modules\Carts\Entity
 */
class ItemOption extends Entity
{

    protected $_table = Model::TBL_ITEMS_OPTIONS;

    protected $_query = Model::QRY_ENTITY_ITEM_OPTION;

    protected $_columns = array(
        'item_id',
        'module',
        'name',
        'value',
        'callback_info',
    );

    protected $_relations = array(
        'title',
    );

    protected $itemId;

    protected $module;

    protected $name;

    protected $value;

    protected $callbackInfo = 'callbackCartsItemsOptionsInfo';

    protected $title;

    protected $extraFields = array();

    protected $extraValues = array();

    private $loadExternalInfo = true;

    /**
     *
     */
    public function loadExternalInfo()
    {
        if ($this->loadExternalInfo) {
            $class = '\\Frontend\\Modules\\'.$this->getModule().'\\Engine\\Model';
            $method = $this->getCallbackInfo();

            if (is_callable(array($class, $method))) {
                $call = $class.'::'.$method;

                $item = call_user_func($call, $this->getName(), $this->getValue(), $this->determineLanguage());

                if (isset($item['title'])) {
                    $this->setTitle($item['title']);
                    unset($item['title']);
                }

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
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = preg_replace('/[^A-Za-z0-9 ]/', '_', $name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * @return mixed
     */
    public function getTitle()
    {
        return isset($this->title) ? $this->title : $this->getName().': '.$this->getValue();
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
}
