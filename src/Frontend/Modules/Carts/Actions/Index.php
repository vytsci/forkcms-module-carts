<?php

namespace Frontend\Modules\Carts\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Modules\Profiles\Engine\Authentication as FrontendProfilesAuthentication;

use Common\Modules\Currencies\Engine\Model as CommonCurrenciesModel;
use Common\Modules\Currencies\Engine\Helper as CommonCurrenciesHelper;

use Frontend\Modules\Carts\Engine\Model as FrontendCartsModel;

use Common\Modules\Orders\Entity\Order;
use Common\Modules\Payments\Entity\Payment;
use Common\Modules\Carts\Entity\Cart;

/**
 * Class Category
 * @package Frontend\Modules\Events\Actions
 */
class Index extends FrontendBaseBlock
{
    /**
     * @var FrontendForm
     */
    private $frm;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Payment
     */
    private $payment;

    /**
     *
     */
    public function execute()
    {
        if (!FrontendProfilesAuthentication::isLoggedIn()) {
            $this->redirect(
                FrontendNavigation::getURLForBlock(
                    'Profiles',
                    'Login'
                ).'?queryString='.FrontendNavigation::getURLForBlock('Carts'),
                307
            );
        }

        parent::execute();

        $this->loadData();

        $this->loadForm();
        $this->validateForm();

        $this->loadTemplate();
        $this->parse();
    }

    private function loadData()
    {
        $this->cart = FrontendCartsModel::getCart(false);
    }

    /**
     *
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm('checkout');

        $this->frm->addButton('clear', 1);
        $this->frm->addButton('checkout', 1);
    }

    /**
     *
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            if ($this->getContainer()->get('request')->get('clear', false)) {
                FrontendCartsModel::clearCart();
                $this->cart = null;

                return;
            }

            if (!isset($this->cart) || !$this->cart->isLoaded()) {
                $this->frm->addError(FL::err('CartsCartDoesNotExist'));

                return;
            }

            $this->cart->loadItems();

            $modules = $this->cart->getModules();
            foreach ($modules as $module) {
                $class = '\\Frontend\\Modules\\'.\SpoonFilter::toCamelCase($module).'\\Engine\\Model';
                $method = $this->cart->getCallbackValidate();

                $valid = true;
                if (is_callable(array($class, $method))) {
                    $call = $class.'::'.$method;
                    $valid = call_user_func($call, $this->cart, FRONTEND_LANGUAGE);
                }

                if (!$valid) {
                    $this->frm->addError(FL::err('CartsCartIsNotValid'));
                }
            }

            if ($this->frm->isCorrect()) {
                $this->order = new Order();
                $this->order
                    ->setCustomerProfileId(FrontendProfilesAuthentication::getProfile()->getId())
                    ->save()
                    ->setUrl(FrontendNavigation::getURLForBlock('Orders').'/'.$this->order->getId());
                $this->cart
                    ->setOrderId($this->order->getId())
                    ->save();

                $amount = 0;
                foreach ($this->cart->getItems() as $item) {
                    if ($item->isLoaded()) {
                        $this->order->addItem(
                            $item->getExternalId(),
                            $item->getModule(),
                            $item->getTitle(),
                            $item->getPrice(),
                            $item->getQuantity(),
                            $item->getOptionsTitlesPairs()
                        );
                        $amount += $item->getPrice() * $item->getQuantity();
                    }
                }

                $this->order->loadItems();
                foreach ($modules as $module) {
                    $class = '\\Frontend\\Modules\\'.\SpoonFilter::toCamelCase($module).'\\Engine\\Model';
                    $method = $this->cart->getCallbackCheckout();

                    if (is_callable(array($class, $method))) {
                        $call = $class.'::'.$method;
                        call_user_func($call, $this->cart, FRONTEND_LANGUAGE);
                    }
                }

                $this->payment = new Payment();
                $this->payment
                    ->setProfileId(FrontendProfilesAuthentication::getProfile()->getId())
                    ->setAmount($amount)
                    ->setCurrency(CommonCurrenciesModel::getDefaultCurrency()->getCode())
                    ->setModule('Orders')
                    ->setExternalId($this->order->getId())
                    ->save();

                $this->order
                    ->setPayment($this->payment)
                    ->setCart($this->cart)
                    ->setBillable()
                    ->save();

                $this->cart
                    ->setStatus('inactive')
                    ->save();
                FrontendCartsModel::clearCart();

                $this->redirect(FrontendNavigation::getURLForBlock('Payments').'/'.$this->payment->getId());
            }
        }
    }

    /**
     *
     */
    private function parse()
    {
        $this->frm->parse($this->tpl);

        CommonCurrenciesHelper::parse($this->tpl);

        $this->tpl->assign('formErrors', $this->frm->getErrors()?$this->frm->getErrors():false);
        $this->tpl->assign('cart', isset($this->cart)?$this->cart->toArray():false);
    }
}
