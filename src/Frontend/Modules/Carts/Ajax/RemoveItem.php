<?php

namespace Frontend\Modules\Carts\Ajax;

use Common\Modules\Carts\Entity\Item;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Language as FL;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

use Frontend\Modules\Carts\Engine\Model as FrontendCartsModel;
use Frontend\Modules\Carts\Engine\Helper as FrontendCartsHelper;

use Common\Modules\Carts\Engine\Model as CommonCartsModel;

/**
 * Class RemoveItem
 * @package Frontend\Modules\Carts\Ajax
 */
class RemoveItem extends FrontendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            return $this->output(
                self::OK,
                array(
                    'error' => 'login'
                ),
                FL::msg('CartsCartRemoveItemLoginRequired')
            );
        }

        $id = \SpoonFilter::getPostValue('id', null, null, 'int');

        $cart = FrontendCartsModel::getCart();
        $item = new Item(array($id));
        if ($item->isLoaded()) {
            $cart->removeItem($item);

            //@todo: we need to invalidate reserved items, so they can return to the market.

            return $this->output(
                self::OK,
                array(
                    'label' => ucfirst(FL::lbl('CartsCartRemoveItemSuccess')),
                    'message' => FL::msg('CartsCartRemoveItemSuccess'),
                    'html' => FrontendCartsHelper::getCartHtml(),
                )
            );
        }

        $errors[] = 'Bad parameters';
        return $this->output(
            self::OK,
            array(
                'error' => 'parameters',
            ),
            FL::msg('CartsCartRemoveItemBadParameters')
        );
    }
}
