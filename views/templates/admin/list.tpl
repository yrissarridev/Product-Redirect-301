<style>
  #wr-root, #wr-root * {
    box-sizing: border-box;
  }
  #wr-root {
    overflow: hidden;
  }
  #wr-root .wr-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
  }
  #wr-root table {
    width: 100%;
    table-layout: fixed;
    margin: 0;
  }
  #wr-root th,
  #wr-root td {
    overflow: hidden;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  #wr-root .wr-check { width: 34px; }
  #wr-root .wr-arrow { width: 30px; text-align: center; color: #ccc; }
  #wr-root .wr-col { width: 44%; }
  #wr-root .wr-name {
    font-weight: bold;
    display: block;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  #wr-root .wr-meta {
    display: block;
    color: #999;
    font-size: 12px;
    margin-top: 3px;
  }
  #wr-root .wr-url {
    display: block;
    margin-top: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    color: #999;
    text-decoration: none;
    word-break: break-all;
    overflow-wrap: break-word;
  }
  #wr-root .wr-url:hover { color: #337ab7; text-decoration: underline; }
  #wr-root .wr-empty { color: #999; text-align: center; padding: 16px !important; }
  #wr-root .wr-section-title { clear: both; margin-top: 24px; }
  #wr-root .wr-output {
    width: 100%;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
    margin-top: 12px;
  }
  #wr-root .wr-actions {
    clear: both;
    margin-top: 16px;
  }
</style>

<div id="wr-root" class="panel">
  <div class="panel-heading">Redirects 301 &mdash; añadas</div>

  <div style="padding: 0 20px 20px;">

    <div class="alert alert-info">
      Vinos desactivados o sin stock con una añada más reciente activa. Marca las sugerencias que quieras aplicar y genera el texto para pegar en el <code>.htaccess</code>.
    </div>

    <h3 class="wr-section-title">Confirmados ({$confirmed|count})</h3>
    <p class="text-muted">Año de la añada antigua &lt; año de la añada nueva.</p>
    <div class="wr-table-wrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="wr-check"></th>
            <th class="wr-col">Producto antiguo</th>
            <th class="wr-arrow"></th>
            <th class="wr-col">Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$confirmed item=row}
          <tr>
            <td class="wr-check">
              <input type="checkbox" class="wineredirects-row-checkbox" checked="checked"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td class="wr-col">
              <span class="wr-name">{$row.old_name|escape:'html':'UTF-8'}</span>
              <span class="wr-meta">
                #{$row.old_id} &middot;
                <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
              </span>
              <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="wr-arrow">&rarr;</td>
            <td class="wr-col">
              <span class="wr-name">{$row.new_name|escape:'html':'UTF-8'}</span>
              <span class="wr-meta">
                #{$row.new_id} &middot;
                <span class="label label-success">stock {$row.new_qty}</span>
              </span>
              <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="wr-empty" colspan="4">No hay sugerencias confirmadas pendientes.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <h3 class="wr-section-title">A revisar ({$review|count})</h3>
    <p class="text-muted">El producto antiguo no tiene año detectado en el nombre &mdash; revisar antes de aceptar.</p>
    <div class="wr-table-wrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="wr-check"></th>
            <th class="wr-col">Producto antiguo</th>
            <th class="wr-arrow"></th>
            <th class="wr-col">Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$review item=row}
          <tr>
            <td class="wr-check">
              <input type="checkbox" class="wineredirects-row-checkbox"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td class="wr-col">
              <span class="wr-name">{$row.old_name|escape:'html':'UTF-8'}</span>
              <span class="wr-meta">
                #{$row.old_id} &middot;
                <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
              </span>
              <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="wr-arrow">&rarr;</td>
            <td class="wr-col">
              <span class="wr-name">{$row.new_name|escape:'html':'UTF-8'}</span>
              <span class="wr-meta">
                #{$row.new_id} &middot;
                <span class="label label-success">stock {$row.new_qty}</span>
              </span>
              <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="wr-empty" colspan="4">No hay sugerencias pendientes de revisar.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="wr-actions">
      <button type="button" id="wineredirects-generate" class="btn btn-primary">Generar documento</button>
      <button type="button" id="wineredirects-copy" class="btn btn-default">Copiar</button>
      <textarea id="wineredirects-output" class="form-control wr-output" rows="10" readonly></textarea>
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
