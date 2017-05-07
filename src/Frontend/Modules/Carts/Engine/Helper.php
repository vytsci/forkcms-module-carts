<?php

namespace Frontend\Modules\Carts\Engine;

use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Template as FrontendTemplate;
use Common\Modules\Currencies\Engine\Helper as CommonCurrenciesHelper;

/**
 * Class Helper
 * @package Frontend\Modules\Carts\Engine
 */
class Helper
{

    /**
     * @return string
     * @throws \SpoonTemplateException
     */
    public static function getCartHtml()
    {
        $tpl = new FrontendTemplate();

        CommonCurrenciesHelper::parse($tpl);

        $tpl->assign('cart', Model::getCart(false)->toArray());

        return $tpl->getContent(FRONTEND_MODULES_PATH.'/Carts/Layout/Templates/Components/Cart.tpl');
    }

    /**
     * @param null $var
     * @param $value
     * @return mixed
     */
    public static function parseExtraFieldName($var = null, $value)
    {
        return FL::lbl('Carts'.\SpoonFilter::toCamelCase($value));
    }

    /**
     * @param null $var
     * @param $array
     * @param $key
     * @param string $default
     * @return string
     */
    public static function parseExtraFieldValue($var = null, $array, $key, $default = '')
    {
        return isset($array[$key]) && !empty($array[$key]['value']) ? $array[$key]['value'] : $default;
    }

    /**
     * @param \SpoonTemplate $tpl
     */
    public static function mapTemplateModifiers(\SpoonTemplate $tpl)
    {
        $tpl->mapModifier(
            'parseextrafieldname',
            array('Frontend\\Modules\\Carts\\Engine\\Helper', 'parseExtraFieldName')
        );
        $tpl->mapModifier(
            'parseextrafieldvalue',
            array('Frontend\\Modules\\Carts\\Engine\\Helper', 'parseExtraFieldValue')
        );
    }
}
