<?php

namespace Backend\Modules\Carts\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Carts\Engine\Model as BackendCartsModel;
use Common\Modules\Carts\Engine\Model as CommonCartsModel;
use Common\Modules\Filter\Engine\Helper as CommonFilterHelper;
use Common\Modules\Filter\Engine\Filter;

/**
 * Class Index
 * @package Backend\Modules\Carts\Actions
 */
class Index extends BackendBaseActionIndex
{

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var BackendDataGridDB
     */
    private $dgCarts;

    /**
     *
     */
    public function execute()
    {
        parent::execute();
        $this->loadFilter();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Loads filter form
     */
    private function loadFilter()
    {
        $this->filter = new Filter();

        $this->filter
            ->addTextCriteria(
                'search',
                array('p.email', 'c.status'),
                CommonFilterHelper::OPERATOR_PATTERN
            );
    }

    /**
     * @throws \Exception
     * @throws \SpoonDatagridException
     */
    private function loadDataGrid()
    {
        $this->dgCarts = new BackendDataGridDB($this->filter->getQuery(BackendCartsModel::QRY_DG_CARTS));

        $this->dgCarts->setSortingColumns($this->dgCarts->getColumns());

        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dgCarts->addColumn(
                'edit',
                null,
                BL::getLabel('Edit'),
                BackendModel::createURLForAction('Edit', null, null, null).'&amp;id=[id]',
                BL::getLabel('Edit')
            );
        }
    }

    /**
     * @throws \SpoonTemplateException
     */
    protected function parse()
    {
        parent::parse();

        $this->filter->parse($this->tpl);

        $this->tpl->assign(
            'dgCarts',
            ($this->dgCarts->getNumResults() != 0) ? $this->dgCarts->getContent() : false
        );
    }
}
