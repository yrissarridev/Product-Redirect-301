<style>
  #pr-root, #pr-root * {
    box-sizing: border-box;
  }
  #pr-root {
    overflow: hidden;
  }
  #pr-root .pr-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
  }
  #pr-root table {
    width: 100%;
    table-layout: fixed;
    margin: 0;
  }
  #pr-root th,
  #pr-root td {
    overflow: hidden;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  #pr-root .pr-check { width: 34px; }
  #pr-root .pr-arrow { width: 30px; text-align: center; color: #ccc; }
  #pr-root .pr-col { width: 44%; }
  #pr-root .pr-name-col { width: 40%; }
  #pr-root .pr-line-col {
    width: 60%;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    color: #999;
  }
  #pr-root .pr-name {
    font-weight: bold;
    display: block;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  #pr-root .pr-meta {
    display: block;
    color: #999;
    font-size: 12px;
    margin-top: 3px;
  }
  #pr-root .pr-url {
    display: block;
    margin-top: 3px;
    font-family: Consolas, Monaco, monospace;
    font-size: 11px;
    color: #999;
    text-decoration: none;
    word-break: break-all;
    overflow-wrap: break-word;
  }
  #pr-root .pr-url:hover { color: #337ab7; text-decoration: underline; }
  #pr-root .pr-empty { color: #999; text-align: center; padding: 16px !important; }
  #pr-root .pr-section-title {
    clear: both;
    margin-top: 24px;
    margin-bottom: 4px;
    font-size: 17px;
    font-weight: bold;
    line-height: 1.3;
  }
  #pr-root .pr-hint {
    color: #999;
    font-size: 13px;
    margin-bottom: 10px;
  }
  #pr-root .pr-alert {
    background: #d9edf7;
    border: 1px solid #bce8f1;
    color: #31708f;
    border-radius: 4px;
    padding: 10px 15px;
    font-size: 13px;
    margin-bottom: 10px;
  }
  #pr-root .pr-output {
    width: 100%;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
    margin-top: 12px;
  }
  #pr-root .pr-actions {
    clear: both;
    margin-top: 16px;
  }
  #pr-root .pr-btn-apply {
    background: #1b5e20;
  }
  #pr-root .pr-btn-apply:hover {
    background: #164a1a;
  }
  #pr-root .pr-cron-box {
    background: #f8f8f8;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 14px 16px;
    margin-bottom: 10px;
  }
  #pr-root .pr-cron-row {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    align-items: center;
  }
  #pr-root .pr-cron-url {
    flex: 1;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
  }
  #pr-root .pr-cron-crontab {
    display: block;
    background: #272822;
    color: #f8f8f2;
    font-family: Consolas, Monaco, monospace;
    font-size: 12px;
    padding: 10px 12px;
    border-radius: 4px;
    margin-bottom: 10px;
    overflow-x: auto;
    white-space: pre;
  }
  #pr-root .pr-cron-status {
    font-size: 13px;
    color: #555;
  }
  #pr-root .pr-cron-status.pr-cron-error {
    color: #a94442;
    font-weight: bold;
  }
</style>

<div id="pr-root" class="panel">
  <div class="panel-heading">Redirects 301 &mdash; productos</div>

  <div style="padding: 0 20px 20px;">

    <div class="pr-alert">
      Productos desactivados o sin stock con una versión más reciente activa (mismo nombre, distinto año). Marca las sugerencias que quieras aplicar y genera el texto para pegar en el <code>.htaccess</code>.
    </div>

    <div class="pr-section-title">Confirmados ({$confirmed|count})</div>
    <div class="pr-hint">Año del producto antiguo &lt; año del producto nuevo.</div>
    <div class="pr-table-wrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="pr-check"><input type="checkbox" class="pr-select-all" checked="checked" title="Marcar/desmarcar todos"></th>
            <th class="pr-col">Producto antiguo</th>
            <th class="pr-arrow"></th>
            <th class="pr-col">Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$confirmed item=row}
          <tr>
            <td class="pr-check">
              <input type="checkbox" class="productredirect-row-checkbox" checked="checked"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td class="pr-col">
              <span class="pr-name">{$row.old_name|escape:'html':'UTF-8'}</span>
              <span class="pr-meta">
                #{$row.old_id} &middot;
                <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
              </span>
              <a class="pr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="pr-arrow">&rarr;</td>
            <td class="pr-col">
              <span class="pr-name">{$row.new_name|escape:'html':'UTF-8'}</span>
              <span class="pr-meta">
                #{$row.new_id} &middot;
                <span class="label label-success">stock {$row.new_qty}</span>
              </span>
              <a class="pr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="pr-empty" colspan="4">No hay sugerencias confirmadas pendientes.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="pr-section-title">A revisar ({$review|count})</div>
    <div class="pr-hint">El producto antiguo no tiene año detectado en el nombre &mdash; revisar antes de aceptar.</div>
    <div class="pr-table-wrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="pr-check"><input type="checkbox" class="pr-select-all" title="Marcar/desmarcar todos"></th>
            <th class="pr-col">Producto antiguo</th>
            <th class="pr-arrow"></th>
            <th class="pr-col">Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$review item=row}
          <tr>
            <td class="pr-check">
              <input type="checkbox" class="productredirect-row-checkbox"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td class="pr-col">
              <span class="pr-name">{$row.old_name|escape:'html':'UTF-8'}</span>
              <span class="pr-meta">
                #{$row.old_id} &middot;
                <span class="label label-danger">{if $row.old_active}sin stock{else}desactivado{/if}</span>
              </span>
              <a class="pr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="pr-arrow">&rarr;</td>
            <td class="pr-col">
              <span class="pr-name">{$row.new_name|escape:'html':'UTF-8'}</span>
              <span class="pr-meta">
                #{$row.new_id} &middot;
                <span class="label label-success">stock {$row.new_qty}</span>
              </span>
              <a class="pr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="pr-empty" colspan="4">No hay sugerencias pendientes de revisar.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="pr-actions">
      <button type="button" id="productredirect-generate" class="btn btn-primary">Generar documento</button>
      <button type="button" id="productredirect-copy" class="btn btn-default">Copiar</button>
      <form method="POST" action="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" id="pr-apply-form" style="display:inline;">
        <input type="hidden" name="lines_text" id="pr-apply-lines-text">
        <button type="submit" name="submitAplicarHtaccess" value="1" id="productredirect-apply" class="btn btn-primary pr-btn-apply">&#9889; Aplicar al .htaccess</button>
      </form>
      <textarea id="productredirect-output" class="form-control pr-output" rows="10" readonly></textarea>
    </div>

    <div class="pr-section-title">Obsoletos ({$obsolete|count})</div>
    <div class="pr-hint">El producto de origen ha vuelto a estar activo y con stock &mdash; el redirect ya no debería existir, está devolviendo a los clientes a otro producto en vez de dejarles comprar este.</div>
    <div class="pr-table-wrap">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="pr-check"><input type="checkbox" class="pr-select-all" checked="checked" title="Marcar/desmarcar todos"></th>
            <th class="pr-name-col">Producto (ya disponible de nuevo)</th>
            <th class="pr-line-col">Línea a eliminar del .htaccess</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$obsolete item=row}
          <tr>
            <td class="pr-check">
              <input type="checkbox" class="productredirect-obsolete-checkbox" checked="checked"
                     data-line="{$row.line|escape:'html':'UTF-8'}">
            </td>
            <td class="pr-name-col">
              <span class="pr-name">{$row.name|escape:'html':'UTF-8'}</span>
              <span class="pr-meta">
                #{$row.id} &middot;
                <span class="label label-success">stock {$row.qty}</span>
              </span>
            </td>
            <td class="pr-line-col">{$row.line|escape:'html':'UTF-8'}</td>
          </tr>
          {foreachelse}
          <tr><td class="pr-empty" colspan="3">No hay redirects obsoletos ahora mismo.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>

    <div class="pr-actions">
      <button type="button" id="productredirect-generate-obsolete" class="btn btn-primary">Generar lista para eliminar</button>
      <button type="button" id="productredirect-copy-obsolete" class="btn btn-default">Copiar</button>
      <textarea id="productredirect-output-obsolete" class="form-control pr-output" rows="6" readonly placeholder="Estas líneas hay que buscarlas y borrarlas del .htaccess, no añadirlas."></textarea>
    </div>

    <div class="pr-section-title">Cron automático (cada 24h)</div>
    <div class="pr-hint">
      Aplica solo los <strong>Confirmados</strong> (año detectado sin ambigüedad) y desactiva el producto antiguo &mdash; igual que el botón de arriba, pero sin intervención manual.
      También quita del .htaccess los <strong>Obsoletos</strong> (el producto de origen vuelve a estar activo y con stock).
      Los <strong>A revisar</strong> nunca se aplican automáticamente, se quedan siempre para revisarlos a mano.
    </div>
    <div class="pr-cron-box">
      <div class="pr-cron-row">
        <input type="text" id="pr-cron-url" class="form-control pr-cron-url" value="{$cron_url|escape:'html':'UTF-8'}" readonly onclick="this.select();">
        <button type="button" id="pr-cron-copy-url" class="btn btn-default">Copiar URL</button>
      </div>

      <div class="pr-hint" style="margin-bottom:4px;">Añade esta línea al crontab del servidor (una vez al día, por ejemplo a las 4:00 de la madrugada):</div>
      <code class="pr-cron-crontab">0 4 * * * curl -s "{$cron_url|escape:'html':'UTF-8'}" &gt;&gt; /var/log/productredirect-cron.log 2&gt;&amp;1</code>

      <div class="pr-cron-status{if $cron_last_is_error} pr-cron-error{/if}">
        {if $cron_last_run}
          Última ejecución: {$cron_last_run|escape:'html':'UTF-8'} &mdash;
          {if $cron_last_status == 'ok'}
            {$cron_last_added|intval} redirect(s) añadidos, {$cron_last_deactivated|intval} producto(s) desactivado(s), {$cron_last_removed|intval} obsoleto(s) eliminado(s).
          {else}
            {$cron_last_status|escape:'html':'UTF-8'}
          {/if}
        {else}
          Todavía no se ha ejecutado ningún cron.
        {/if}
      </div>

      <form method="POST" action="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" style="margin-top:10px;" onsubmit="return confirm('Esto invalida la URL de cron anterior (tendrás que actualizar el crontab). ¿Continuar?');">
        <button type="submit" name="submitRegenerarCronToken" value="1" class="btn btn-default btn-sm">Regenerar token</button>
      </form>
    </div>

  </div>
</div>

<script>
function productRedirectBuildDocumentText(lines) {
  return lines.join('\n');
}

function productRedirectRefreshDocument() {
  var checked = document.querySelectorAll('.productredirect-row-checkbox:checked');
  var lines = Array.prototype.map.call(checked, function (checkbox) {
    return checkbox.getAttribute('data-line');
  });
  var textarea = document.getElementById('productredirect-output');
  textarea.value = productRedirectBuildDocumentText(lines);
}

function productRedirectCopyDocument() {
  var textarea = document.getElementById('productredirect-output');
  textarea.select();
  navigator.clipboard.writeText(textarea.value);
}

function productRedirectRefreshObsoleteDocument() {
  var checked = document.querySelectorAll('.productredirect-obsolete-checkbox:checked');
  var lines = Array.prototype.map.call(checked, function (checkbox) {
    return checkbox.getAttribute('data-line');
  });
  var textarea = document.getElementById('productredirect-output-obsolete');
  textarea.value = productRedirectBuildDocumentText(lines);
}

function productRedirectCopyObsoleteDocument() {
  var textarea = document.getElementById('productredirect-output-obsolete');
  textarea.select();
  navigator.clipboard.writeText(textarea.value);
}

function productRedirectSyncSelectAll(table) {
  var master = table.querySelector('.pr-select-all');
  if (!master) {
    return;
  }
  var rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
  if (rowCheckboxes.length === 0) {
    master.checked = false;
    master.indeterminate = false;
    return;
  }
  var checkedCount = table.querySelectorAll('tbody input[type="checkbox"]:checked').length;
  master.checked = checkedCount === rowCheckboxes.length;
  master.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
}

document.addEventListener('DOMContentLoaded', function () {
  var checkboxes = document.querySelectorAll('.productredirect-row-checkbox');
  Array.prototype.forEach.call(checkboxes, function (checkbox) {
    checkbox.addEventListener('change', function () {
      productRedirectRefreshDocument();
      productRedirectSyncSelectAll(checkbox.closest('table'));
    });
  });

  var generateButton = document.getElementById('productredirect-generate');
  if (generateButton) {
    generateButton.addEventListener('click', productRedirectRefreshDocument);
  }

  var copyButton = document.getElementById('productredirect-copy');
  if (copyButton) {
    copyButton.addEventListener('click', productRedirectCopyDocument);
  }

  var applyForm = document.getElementById('pr-apply-form');
  if (applyForm) {
    applyForm.addEventListener('submit', function (event) {
      productRedirectRefreshDocument();
      var lines = document.getElementById('productredirect-output').value;
      document.getElementById('pr-apply-lines-text').value = lines;

      var count = lines.split('\n').filter(function (l) { return l.trim() !== ''; }).length;
      if (count === 0) {
        event.preventDefault();
        alert('No hay ninguna línea marcada para aplicar.');
        return;
      }
      if (!window.confirm('Se van a añadir ' + count + ' redirect(s) 301 al .htaccess de la tienda real. ¿Continuar?')) {
        event.preventDefault();
      }
    });
  }

  var obsoleteCheckboxes = document.querySelectorAll('.productredirect-obsolete-checkbox');
  Array.prototype.forEach.call(obsoleteCheckboxes, function (checkbox) {
    checkbox.addEventListener('change', function () {
      productRedirectRefreshObsoleteDocument();
      productRedirectSyncSelectAll(checkbox.closest('table'));
    });
  });

  var generateObsoleteButton = document.getElementById('productredirect-generate-obsolete');
  if (generateObsoleteButton) {
    generateObsoleteButton.addEventListener('click', productRedirectRefreshObsoleteDocument);
  }

  var copyObsoleteButton = document.getElementById('productredirect-copy-obsolete');
  if (copyObsoleteButton) {
    copyObsoleteButton.addEventListener('click', productRedirectCopyObsoleteDocument);
  }

  var cronCopyButton = document.getElementById('pr-cron-copy-url');
  if (cronCopyButton) {
    cronCopyButton.addEventListener('click', function () {
      var input = document.getElementById('pr-cron-url');
      input.select();
      navigator.clipboard.writeText(input.value);
    });
  }

  var selectAllBoxes = document.querySelectorAll('.pr-select-all');
  Array.prototype.forEach.call(selectAllBoxes, function (master) {
    var table = master.closest('table');
    productRedirectSyncSelectAll(table);
    master.addEventListener('change', function () {
      var rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
      Array.prototype.forEach.call(rowCheckboxes, function (checkbox) {
        checkbox.checked = master.checked;
      });
      master.indeterminate = false;
      if (table.querySelector('.productredirect-row-checkbox')) {
        productRedirectRefreshDocument();
      }
      if (table.querySelector('.productredirect-obsolete-checkbox')) {
        productRedirectRefreshObsoleteDocument();
      }
    });
  });
});
</script>
