<?php

namespace Backend\Modules\Carts\Installer;

use Backend\Core\Installer\ModuleInstaller;
use Backend\Core\Engine\Model as BackendModel;

/**
 * Class Installer
 * @package Backend\Modules\Carts\Installer
 */
class Installer extends ModuleInstaller
{

    /**
     *
     */
    public function install()
    {
        $this->importSQL(dirname(__FILE__).'/Data/install.sql');

        $this->addModule('Carts');

        $this->importLocale(dirname(__FILE__).'/Data/locale.xml');

        $this->setModuleRights(1, 'Carts');
        $this->setActionRights(1, 'Carts', 'Edit');
        $this->setActionRights(1, 'Carts', 'Index');

        $this->insertExtra('Carts', 'block', 'Carts', null, null, 'N', 80000);
        $this->insertExtra('Carts', 'widget', 'CartsCart', 'Cart', null, 'N', 80001);

        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationMembersId = $this->setNavigation($navigationModulesId, 'Carts');
        $this->setNavigation(
            $navigationMembersId,
            'Overview',
            'carts/index',
            array(
                'carts/edit',
            )
        );

        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Carts', 'carts/settings');
    }
}
