{option:formErrors}
<div class="alert alert-danger">
  {$formErrors}
</div>
{/option:formErrors}
{option:cart.errors}
{iteration:cart.errors}
  <div class="alert alert-danger">
      {$cart.errors}
  </div>
{/iteration:cart.errors}
{/option:cart.errors}
<section id="carts-index" class="module module-carts">
  <div class="row">
    <div class="col-md-12">
      {form:checkout}
        {option:cart}
        {option:cart.items}
        <table class="table">
          <thead>
            <tr>
              <th>
                {$lblCartsItemTitle|ucfirst}
              </th>
              <th>
                {$lblCartsItemPrice|ucfirst}
              </th>
              <th>
                {$lblCartsItemQuantity|ucfirst}
              </th>
              <th>
                {$lblCartsItemPriceTotal|ucfirst}
              </th>
            </tr>
          </thead>
          <tbody>
            {iteration:cart.items}
            <tr>
              <td>
                <p>{$cart.items.title}</p>
                {option:cart.items.errors}
                    <ul>
                        {iteration:cart.items.errors}
                            <li class="text-danger">
                              <small>{$cart.items.errors}</small>
                            </li>
                        {/iteration:cart.items.errors}
                    </ul>
                {/option:cart.items.errors}
                {option:cart.items.options}
                <ul>
                  {iteration:cart.items.options}
                  <li>
                    <small>
                      {$cart.items.options.title}
                    </small>
                  </li>
                  {/iteration:cart.items.options}
                </ul>
                {/option:cart.items.options}
              </td>
              <td>
                {$cart.items.price|formatprice}
              </td>
              <td>
                {$cart.items.quantity}
              </td>
              <td class="text-right">
                {$cart.items.price_total|formatprice}
              </td>
            </tr>
            {/iteration:cart.items}
          </tbody>
          <tbody>
            <tr>
              <td colspan="1">&nbsp;</td>
              <td class="text-right">
                <b>{$lblCartsItemsPrice|ucfirst}</b>
              </td>
              <td class="text-right">
                {$cart.items_count}
              </td>
              <td class="text-right">
                {$cart.items_price|formatprice}
              </td>
            </tr>
          </tbody>
        </table>
        <div class="btn-toolbar">
          <div class="btn-group pull-right">
            <button type="submit" class="btn btn-default">
              {$lblCartsCheckout|ucfirst}
            </button>
          </div>
        </div>
      {/form:checkout}
      {/option:cart.items}
      {option:!cart.items}
      <div class="alert alert-warning">
        {$msgCartsCartEmpty}
      </div>
      {/option:!cart.items}
      {/option:cart}
      {option:!cart}
      <div class="alert alert-warning">
        {$msgCartsCartEmpty}
      </div>
      {/option:!cart}
    </div>
  </div>
</section>
