<?php

namespace Frontend\Modules\Carts\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;

use Frontend\Modules\Carts\Engine\Helper as FrontendCartsHelper;

/**
 * Class Cart
 * @package Frontend\Modules\Carts\Widgets
 */
class Cart extends FrontendBaseWidget
{

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();

        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Parse the data into the template
     */
    private function parse()
    {
        $this->tpl->assign('widgetCartsCartHtml', FrontendCartsHelper::getCartHtml());
    }
}
