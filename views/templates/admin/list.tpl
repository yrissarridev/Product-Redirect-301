<style>
  .wr-url {
    display: block;
    margin-top: 2px;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    color: #999;
    text-decoration: none;
    word-break: break-all;
  }
  .wr-url:hover { color: #337ab7; text-decoration: underline; }
  .wr-meta { color: #999; font-size: 12px; margin-top: 2px; }
  .wr-arrow { text-align: center; color: #ccc; width: 24px; }
  .wr-check { width: 32px; text-align: center; }
  .wr-empty { color: #999; text-align: center; padding: 16px !important; }
  .wr-table td { vertical-align: middle; }
  .wr-section-title { display: flex; align-items: center; gap: 8px; }
  .wr-output {
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
  }
</style>

<div class="panel">
  <div class="panel-heading">
    <i class="icon-refresh"></i> Redirects 301 &mdash; añadas
  </div>

  <div class="alert alert-info" style="margin:16px 20px 0;">
    Vinos desactivados o sin stock con una añada más reciente activa. Marca las sugerencias que quieras aplicar y genera el texto para pegar en el <code>.htaccess</code>.
  </div>

  <div style="padding: 0 20px 20px;">

    <h3 class="wr-section-title">Confirmados <span class="badge">{$confirmed|count}</span></h3>
    <p class="text-muted">Año de la añada antigua &lt; año de la añada nueva.</p>
    <table class="table table-hover wr-table">
      <thead>
        <tr>
          <th class="wr-check"></th>
          <th>Producto antiguo</th>
          <th class="wr-arrow"></th>
          <th>Producto actual</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$confirmed item=row}
        <tr>
          <td class="wr-check">
            <input type="checkbox" class="wineredirects-row-checkbox" checked="checked"
                   data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
          </td>
          <td>
            <strong>{$row.old_name|escape:'html':'UTF-8'}</strong>
            <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
            <div class="wr-meta">#{$row.old_id}</div>
            <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
          </td>
          <td class="wr-arrow"><i class="icon-long-arrow-right"></i></td>
          <td>
            <strong>{$row.new_name|escape:'html':'UTF-8'}</strong>
            <span class="label label-success">stock {$row.new_qty}</span>
            <div class="wr-meta">#{$row.new_id}</div>
            <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
          </td>
        </tr>
        {foreachelse}
        <tr><td class="wr-empty" colspan="4">No hay sugerencias confirmadas pendientes.</td></tr>
        {/foreach}
      </tbody>
    </table>

    <h3 class="wr-section-title">A revisar <span class="badge">{$review|count}</span></h3>
    <p class="text-muted">El producto antiguo no tiene año detectado en el nombre &mdash; revisar antes de aceptar.</p>
    <table class="table table-hover wr-table">
      <thead>
        <tr>
          <th class="wr-check"></th>
          <th>Producto antiguo</th>
          <th class="wr-arrow"></th>
          <th>Producto actual</th>
        </tr>
      </thead>
      <tbody>
        {foreach from=$review item=row}
        <tr>
          <td class="wr-check">
            <input type="checkbox" class="wineredirects-row-checkbox"
                   data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
          </td>
          <td>
            <strong>{$row.old_name|escape:'html':'UTF-8'}</strong>
            <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
            <div class="wr-meta">#{$row.old_id}</div>
            <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
          </td>
          <td class="wr-arrow"><i class="icon-long-arrow-right"></i></td>
          <td>
            <strong>{$row.new_name|escape:'html':'UTF-8'}</strong>
            <span class="label label-success">stock {$row.new_qty}</span>
            <div class="wr-meta">#{$row.new_id}</div>
            <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
          </td>
        </tr>
        {foreachelse}
        <tr><td class="wr-empty" colspan="4">No hay sugerencias pendientes de revisar.</td></tr>
        {/foreach}
      </tbody>
    </table>

    <div class="panel-footer" style="background:transparent;border:none;padding:16px 0 0;">
      <button type="button" id="wineredirects-generate" class="btn btn-primary">
        <i class="icon-file-text"></i> Generar documento
      </button>
      <button type="button" id="wineredirects-copy" class="btn btn-default">
        <i class="icon-copy"></i> Copiar
      </button>
      <textarea id="wineredirects-output" class="form-control wr-output" rows="10" readonly style="margin-top:12px;"></textarea>
    </div>

  </div>
</div>

<script>
function wineRedirectsBuildDocumentText(lines) {
  return lines.join('\n');
}

function wineRedirectsRefreshDocument() {
  var checked = document.querySelectorAll('.wineredirects-row-checkbox:checked');
  var lines = Array.prototype.map.call(checked, function (checkbox) {
    return checkbox.getAttribute('data-line');
  });
  var textarea = document.getElementById('wineredirects-output');
  textarea.value = wineRedirectsBuildDocumentText(lines);
}

function wineRedirectsCopyDocument() {
  var textarea = document.getElementById('wineredirects-output');
  textarea.select();
  navigator.clipboard.writeText(textarea.value);
}

document.addEventListener('DOMContentLoaded', function () {
  var checkboxes = document.querySelectorAll('.wineredirects-row-checkbox');
  Array.prototype.forEach.call(checkboxes, function (checkbox) {
    checkbox.addEventListener('change', wineRedirectsRefreshDocument);
  });

  var generateButton = document.getElementById('wineredirects-generate');
  if (generateButton) {
    generateButton.addEventListener('click', wineRedirectsRefreshDocument);
  }

  var copyButton = document.getElementById('wineredirects-copy');
  if (copyButton) {
    copyButton.addEventListener('click', wineRedirectsCopyDocument);
  }
});
</script>
