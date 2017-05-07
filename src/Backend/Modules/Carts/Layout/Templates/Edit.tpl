{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$msgEditCart|sprintf:{$item.id}|ucfirst}</h2>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <h3>{$lblCart}</h3>
    <table class="table table-hover">
      <tr>
        <th>
          {$lblId|uppercase}
        </th>
        <td>
          {$item.id}
        </td>
      </tr>
      <tr>
        <th>
          {$lblProfile|ucfirst}
        </th>
        <td>
          {$item.profile.display_name}
        </td>
      </tr>
      <tr>
        <th>
          {$lblCreatedOn|ucfirst}
        </th>
        <td>
          {$item.created_on}
        </td>
      </tr>
      <tr>
        <th>
          {$lblStatus|ucfirst}
        </th>
        <td>
          {$item.status.value}
        </td>
      </tr>
      <tr>
        <th>
          {$lblItemsCount|ucfirst}
        </th>
        <td>
          {$item.items_count}
        </td>
      </tr>
    </table>
  </div>
</div>
{option:item.items}
<div class="row fork-module-content">
  <div class="col-md-12">
    <h3>{$lblItems}</h3>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>{$lblId|uppercase}</th>
          <th>{$lblTitle|ucfirst}</th>
          <th>{$lblModule|ucfirst}</th>
          <th>{$lblQuantity|ucfirst}</th>
        </tr>
      </thead>
      <tbody>
        {iteration:item.items}
        <tr>
          <td>{$item.items.id}</td>
          <td>
            <a href="{$item.items.url}" title="{$item.items.title}" target="_blank">
              {$item.items.title}
            </a>
          </td>
          <td>{$item.items.module}</td>
          <td>{$item.items.quantity}</td>
        </tr>
        {/iteration:item.items}
      </tbody>
    </table>
  </div>
</div>
{/option:item.items}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
