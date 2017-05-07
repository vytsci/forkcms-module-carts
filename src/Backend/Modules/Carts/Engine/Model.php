<?php

namespace Backend\Modules\Carts\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;

use Common\Modules\Carts\Engine\Model as CommonCartsModel;

/**
 * Class Model
 * @package Backend\Modules\Carts\Engine
 */
class Model
{
    const QRY_DG_CARTS =
        'SELECT
            c.id,
            p.display_name,
            c.status,
            (SELECT COUNT(*) FROM carts_items WHERE cart_id = c.id) AS items_count,
            c.created_on
        FROM carts AS c
        LEFT JOIN profiles AS p ON p.id = c.profile_id';
}
