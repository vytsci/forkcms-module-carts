<?php

namespace Backend\Modules\Carts\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Common\Modules\Carts\Engine\Model as CommonCartsModel;
use Common\Modules\Carts\Entity\Cart;

/**
 * Class Edit
 * @package Backend\Modules\Events\Actions
 */
class Edit extends BackendBaseActionEdit
{

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        $this->cart = new Cart(array($this->id), BL::getActiveLanguages());
        $this->cart
            ->loadProfile()
            ->loadItems();

        if (!$this->cart->isLoaded()) {
            $this->redirect(BackendModel::createURLForAction('Index').'&error=non-existing');
        }

        parent::execute();

        $this->parse();
        $this->display();
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('item', $this->cart->toArray());
    }
}
