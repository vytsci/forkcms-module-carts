<section class="widget widget-carts widget-carts-cart social social_head jsCartsCart">
  <ul>
    <li>
      <a href="{$var|geturlforblock:'Carts'}" title="{$lblCartsCart|ucfirst}">
        <span class="glyphicon glyphicon-shopping-cart"></span>
        {option:cart.items}
        {$lblCartsCart|sprintf:{$cart.items_count}|ucfirst}
        {/option:cart.items}
        {option:!cart.items}
        {$lblCartsCartEmpty|ucfirst}
        {/option:!cart.items}
      </a>
      {option:cart.items}
      <ul>
        {iteration:cart.items}
        <li>
          <a href="{$cart.items.url}" title="{$cart.items.title}">
            {$cart.items.title} x {$cart.items.quantity}
          </a>
        </li>
        {/iteration:cart.items}
      </ul>
      {/option:cart.items}
    </li>
  </ul>
</section>
