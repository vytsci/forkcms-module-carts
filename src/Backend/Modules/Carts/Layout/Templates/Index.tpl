{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblCarts|ucfirst}</h2>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {form:filter}
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="search">{$lblSearch|ucfirst}</label>
              {$txtSearch} {$txtSearchError}
            </div>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="btn-toolbar">
          <div class="btn-group pull-right">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-refresh"></span>&nbsp;
              {$lblUpdateFilter|ucfirst}
            </button>
          </div>
        </div>
      </div>
    </div>
    {/form:filter}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dgCarts}
    {$dgCarts}
    {/option:dgCarts}
    {option:!dgCarts}
    <p>{$msgNoItems}</p>
    {/option:!dgCarts}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
