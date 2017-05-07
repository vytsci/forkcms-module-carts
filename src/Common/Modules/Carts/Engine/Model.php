<?php

namespace Common\Modules\Carts\Engine;

use Frontend\Core\Engine\Language as FL;
use Common\Core\Model as CommonModel;
use Common\Modules\Carts\Engine\Model as CommonCartsModel;
use Common\Modules\Carts\Entity\Cart;
use Common\Modules\Carts\Entity\Item;
use Common\Modules\Carts\Entity\ItemOption;

/**
 * Class Model
 * @package Common\Modules\Carts\Engine
 */
class Model
{

    /**
     *
     */
    const TBL_CARTS = 'carts';

    /**
     *
     */
    const TBL_ITEMS = 'carts_items';

    /**
     *
     */
    const TBL_ITEMS_OPTIONS = 'carts_items_options';

    /**
     *
     */
    const QRY_ENTITY_CART =
        'SELECT
            c.*,
            (SELECT SUM(quantity) FROM carts_items WHERE cart_id = c.id) AS items_count
        FROM carts AS c WHERE c.id = ?';

    /**
     *
     */
    const QRY_ENTITY_ITEM = 'SELECT ci.* FROM carts_items AS ci WHERE ci.id = ?';

    /**
     *
     */
    const QRY_ENTITY_ITEM_OPTION = 'SELECT cio.* FROM carts_items_options AS cio WHERE cio.id = ?';

    /**
     * @param $id
     * @param $externalId
     * @param $module
     * @param array $options
     *
     * @return Item
     * @throws \SpoonDatabaseException
     */
    public static function getCartItem($id, $externalId, $module, $options = array())
    {
        $options = array_filter($options);

        $item = new Item();

        $parameters = array(
            (int)$id,
            (int)$externalId,
            $module,
        );

        if (!empty($options)) {
            $parameters[] = md5(
                implode(
                    array_map(
                        function ($key, $value) {
                            return $key.'_'.$value;
                        },
                        array_keys($options),
                        $options
                    )
                )
            );
        }

        $record = (array)CommonModel::getContainer()->get('database')->getRecord(
            'SELECT
                ci.*,
                MD5(
                    CONCAT((SELECT CONCAT(`name`, \'_\', `value`) FROM carts_items_options WHERE item_id = ci.id))
                ) AS options_hash
            FROM '.self::TBL_ITEMS.' AS ci
            WHERE ci.cart_id = ? AND ci.external_id = ? AND ci.module = ?
            HAVING options_hash '.(empty($options) ? ' IS NULL' : '= ?').'
            LIMIT 1',
            $parameters
        );

        if (!empty($record)) {
            return $item->assemble($record)->loadOptions();
        }

        $item
            ->setCartId($id)
            ->setExternalId($externalId)
            ->setModule(\SpoonFilter::toCamelCase($module));

        foreach ($options as $optionName => $optionValue) {
            $item->addOption(\SpoonFilter::toCamelCase($module), $optionName, $optionValue);
        }

        return $item;
    }

    /**
     * @todo: optimize this method so it wont load again already loaded items
     *
     * @param $id
     * @param $language
     *
     * @return array
     */
    public static function getCartItems($id, $language)
    {
        $result = array();

        $records = (array)CommonModel::getContainer()->get('database')->getRecords(
            'SELECT ci.* FROM '.self::TBL_ITEMS.' AS ci WHERE ci.cart_id = ?',
            (int)$id
        );

        foreach ($records as $record) {
            $item = new Item();
            $item
                ->assemble($record)
                ->loadOptions()
                ->loadExternalInfo();
            $result[$item->getId()] = $item;
        }

        return $result;
    }

    /**
     * @param $id
     * @param $language
     *
     * @return array
     * @throws \SpoonDatabaseException
     */
    public static function getItemOptions($id, $language)
    {
        $result = array();

        $records = (array)CommonModel::getContainer()->get('database')->getRecords(
            'SELECT cio.* FROM '.self::TBL_ITEMS_OPTIONS.' AS cio WHERE cio.item_id = ?',
            (int)$id
        );

        foreach ($records as $record) {
            $option = new ItemOption();
            $option
                ->assemble($record)
                ->loadExternalInfo();
            $result[$option->getId()] = $option;
        }

        return $result;
    }

    /**
     * @param $id
     * @param $module
     * @param $name
     *
     * @return ItemOption
     * @throws \SpoonDatabaseException
     */
    public static function getItemOption($id, $module, $name)
    {
        $option = new ItemOption();

        $record = (array)CommonModel::getContainer()->get('database')->getRecord(
            'SELECT cio.*
            FROM '.self::TBL_ITEMS_OPTIONS.' AS cio
            WHERE cio.item_id = ? AND cio.name = ? AND cio.module = ?
            LIMIT 1',
            array((int)$id, $name, $module)
        );

        if (!empty($record)) {
            $option->assemble($record);
        }

        return $option;
    }

    /**
     * @param $id
     *
     * @return array
     * @throws \SpoonDatabaseException
     */
    public static function getCartModules($id)
    {
        $records = (array)CommonModel::getContainer()->get('database')->getColumn(
            'SELECT DISTINCT ci.module
            FROM '.self::TBL_ITEMS.' AS ci
            WHERE ci.cart_id = ?',
            array((int)$id)
        );

        return $records;
    }

    /**
     * @param $id
     */
    public static function removeCartItem($id)
    {
        CommonModel::getContainer()->get('database')->delete(self::TBL_ITEMS, 'id = ?', array($id));
    }
}
