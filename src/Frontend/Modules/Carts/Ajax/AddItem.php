<?php

namespace Frontend\Modules\Carts\Ajax;

use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Engine\Language as FL;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

use Frontend\Modules\Carts\Engine\Model as FrontendCartsModel;
use Frontend\Modules\Carts\Engine\Helper as FrontendCartsHelper;

use Common\Modules\Carts\Engine\Model as CommonCartsModel;

/**
 * Class AddItem
 * @package Frontend\Modules\Carts\Ajax
 */
class AddItem extends FrontendBaseAJAXAction
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
                FL::msg('CartsCartAddItemLoginRequired')
            );
        }

        $externalId = \SpoonFilter::getPostValue('external_id', null, null, 'int');
        $module = \SpoonFilter::getPostValue('module', null, null, 'string');
        $quantity = \SpoonFilter::getPostValue('quantity', null, 1, 'int');
        $options = \SpoonFilter::getPostValue('options', null, array(), 'array');
        $errors = array();

        if (isset($externalId) && !empty($module) && $quantity > 0) {
            $cart = FrontendCartsModel::getCart();
            $cart
                ->loadItems()
                ->addItem($externalId, $module, $quantity, $options);

            $class = '\\Frontend\\Modules\\'.\SpoonFilter::toCamelCase($module).'\\Engine\\Model';
            $method = $cart->getCallbackValidate();

            $valid = true;
            if (is_callable(array($class, $method))) {
                $call = $class.'::'.$method;
                $valid = call_user_func($call, $cart, FRONTEND_LANGUAGE);
            }

            if (!$valid) {
                return $this->output(
                    self::OK,
                    array(
                        'error' => 'invalid',
                        'errors' => $cart->getErrors(),
                    ),
                    FL::msg('CartsCartAddItemInvalid')
                );
            }

            $cart->save();

            return $this->output(
                self::OK,
                array(
                    'label' => ucfirst(FL::lbl('CartsCartAddItemSuccess')),
                    'message' => FL::msg('CartsCartAddItemSuccess'),
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
            FL::msg('CartsCartAddItemBadParameters')
        );
    }
}
