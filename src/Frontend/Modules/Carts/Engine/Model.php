<?php

namespace Frontend\Modules\Carts\Engine;

use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

use Common\Core\Model as CommonModel;

use Common\Modules\Carts\Entity\Cart;

/**
 * Class Model
 * @package Frontend\Modules\Carts\Engine
 */
class Model
{

    /**
     * @var Cart
     */
    private static $cart;

    /**
     * @param bool|true $create
     * @return Cart
     */
    public static function getCart($create = true)
    {
        $id = CommonModel::getContainer()->get('session')->get('carts_cart_id');

        if (empty(self::$cart) || self::$cart->getId() != $id) {
            self::$cart = new Cart(array($id));
        }

        $profileId = null;
        if (FrontendProfilesAuthentication::isLoggedIn()) {
            $profileId = FrontendProfilesAuthentication::getProfile()->getId();
        }

        if (self::$cart->isLoaded() && self::$cart->getStatus()->isActive() && self::$cart->isOwner($profileId)) {
            self::$cart->loadItems();

            return self::$cart;
        }

        self::$cart = new Cart();
        self::$cart->setProfileId($profileId);

        if ($create) {
            self::$cart->save();
        }

        CommonModel::getContainer()->get('session')->set('carts_cart_id', self::$cart->getId());

        return self::$cart;
    }

    /**
     *
     */
    public static function clearCart()
    {
        if (isset(self::$cart) && self::$cart->isLoaded()) {
            self::$cart
                ->setStatus('inactive')
                ->save();
            self::$cart = null;
        }

        CommonModel::getContainer()->get('session')->remove('carts_cart_id');
    }
}
