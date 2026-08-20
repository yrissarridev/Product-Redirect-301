<div class="panel">
  <div class="panel-heading">
    <i class="icon-sync"></i> Redirects 301 pendientes — añada antigua &rarr; añada actual
  </div>
  <div class="panel-body">

    <h3>Confirmados ({$confirmed|count})</h3>
    <p class="text-muted">A&ntilde;o de la a&ntilde;ada antigua &lt; a&ntilde;o de la a&ntilde;ada nueva.</p>
    <table class="table">
      <thead>
        <tr>
          <th></th>
          <th>Producto antiguo (desactivado / sin stock)</th>
          <th></th>
          <th>Producto actual (activo, con stock)</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$confirmed item=row}
        <tr>
          <td>
            <input type="checkbox" class="wineredirects-row-checkbox" checked="checked"
                   data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
          </td>
          <td>
            <strong>{$row.old_name|escape:'html':'UTF-8'}</strong><br>
            <small>#{$row.old_id} &middot; {if $row.old_active}sin stock{else}desactivado{/if} &middot; stock {$row.old_qty}</small><br>
            <a href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
          </td>
          <td>&rarr;</td>
          <td>
            <strong>{$row.new_name|escape:'html':'UTF-8'}</strong><br>
            <small>#{$row.new_id} &middot; stock {$row.new_qty}</small><br>
            <a href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
          </td>
        </tr>
        {foreachelse}
        <tr><td colspan="4">No hay sugerencias confirmadas pendientes.</td></tr>
        {/foreach}
      </tbody>
    </table>

    <h3>A revisar ({$review|count})</h3>
    <p class="text-muted">El producto antiguo no tiene a&ntilde;o detectado en el nombre &mdash; revisar antes de aceptar.</p>
    <table class="table">
      <thead>
        <tr>
          <th></th>
          <th>Producto antiguo (desactivado / sin stock)</th>
          <th></th>
          <th>Producto actual (activo, con stock)</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$review item=row}
        <tr>
          <td>
            <input type="checkbox" class="wineredirects-row-checkbox"
                   data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
          </td>
          <td>
            <strong>{$row.old_name|escape:'html':'UTF-8'}</strong><br>
            <small>#{$row.old_id} &middot; {if $row.old_active}sin stock{else}desactivado{/if} &middot; stock {$row.old_qty}</small><br>
            <a href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
          </td>
          <td>&rarr;</td>
          <td>
            <strong>{$row.new_name|escape:'html':'UTF-8'}</strong><br>
            <small>#{$row.new_id} &middot; stock {$row.new_qty}</small><br>
            <a href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
          </td>
        </tr>
        {foreachelse}
        <tr><td colspan="4">No hay sugerencias pendientes de revisar.</td></tr>
        {/foreach}
      </tbody>
    </table>

    <button type="button" id="wineredirects-generate" class="btn btn-default">Generar documento</button>
    <button type="button" id="wineredirects-copy" class="btn btn-default">Copiar</button>
    <textarea id="wineredirects-output" class="form-control" rows="10" readonly style="margin-top:10px;font-family:monospace;"></textarea>

  </div>
</div>
