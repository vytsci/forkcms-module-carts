<?php

namespace Backend\Modules\Carts\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/**
 * Class Settings
 * @package Backend\Modules\Carts\Actions
 */
class Settings extends BackendBaseActionEdit
{

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Loads the settings form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('settings');
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            if ($this->frm->isCorrect()) {
                $this->redirect(BackendModel::createURLForAction('Settings').'&report=saved');
            }
        }
    }

    protected function parse()
    {
        parent::parse();
    }
}
