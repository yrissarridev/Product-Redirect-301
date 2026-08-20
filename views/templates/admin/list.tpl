<div class="wr-panel">
  <style>
    .wr-panel {
      --wr-ink: #2a2118;
      --wr-muted: #8a7c68;
      --wr-paper: #faf7f2;
      --wr-line: #e3d9c9;
      --wr-accent: #7a1f2b;
      --wr-accent-soft: #f4e3e1;
      --wr-good: #2f6b4f;
      --wr-good-soft: #e5f0e9;
      --wr-warn: #a3661b;
      --wr-warn-soft: #f6e9d6;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--wr-ink);
      background: var(--wr-paper);
      border: 1px solid var(--wr-line);
      border-radius: 10px;
      padding: 1.75rem 2rem 2rem;
      max-width: 1200px;
    }
    .wr-panel * { box-sizing: border-box; }
    .wr-header {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 0.75rem;
      border-bottom: 1px solid var(--wr-line);
      padding-bottom: 1.1rem;
      margin-bottom: 1.4rem;
    }
    .wr-title {
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0;
      letter-spacing: -0.01em;
    }
    .wr-subtitle {
      color: var(--wr-muted);
      font-size: 0.85rem;
      margin: 0.2rem 0 0;
    }
    .wr-panel section { margin-bottom: 2rem; }
    .wr-section-head {
      display: flex;
      align-items: baseline;
      gap: 0.6rem;
      margin-bottom: 0.3rem;
    }
    .wr-section-head h3 {
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0;
    }
    .wr-count {
      display: inline-flex;
      align-items: center;
      font-family: ui-monospace, "SF Mono", Consolas, monospace;
      font-size: 0.72rem;
      padding: 0.1rem 0.5rem;
      border-radius: 999px;
      background: var(--wr-good-soft);
      color: var(--wr-good);
    }
    .wr-count.wr-count-warn { background: var(--wr-warn-soft); color: var(--wr-warn); }
    .wr-hint {
      color: var(--wr-muted);
      font-size: 0.8rem;
      margin: 0 0 0.9rem;
      max-width: 70ch;
      line-height: 1.5;
    }
    .wr-table-wrap {
      overflow-x: auto;
      border: 1px solid var(--wr-line);
      border-radius: 8px;
      background: #fff;
    }
    .wr-panel table {
      border-collapse: collapse;
      width: 100%;
      min-width: 640px;
    }
    .wr-panel thead th {
      text-align: left;
      font-size: 0.68rem;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--wr-muted);
      font-weight: 600;
      padding: 0.6rem 0.8rem;
      border-bottom: 1px solid var(--wr-line);
      background: var(--wr-paper);
    }
    .wr-panel tbody tr { border-bottom: 1px solid var(--wr-line); }
    .wr-panel tbody tr:last-child { border-bottom: none; }
    .wr-panel tbody tr:hover { background: #fbf9f5; }
    .wr-panel td { padding: 0.65rem 0.8rem; vertical-align: top; font-size: 0.85rem; }
    .wr-check-col { width: 2.5rem; text-align: center; }
    .wr-check-col input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
    .wr-arrow-col { color: var(--wr-accent); text-align: center; width: 2rem; font-size: 1rem; }
    .wr-name { font-weight: 600; }
    .wr-meta {
      font-size: 0.74rem;
      color: var(--wr-muted);
      margin-top: 0.15rem;
      font-variant-numeric: tabular-nums;
    }
    .wr-badge {
      display: inline-block;
      padding: 0.05rem 0.4rem;
      border-radius: 4px;
      font-size: 0.7rem;
    }
    .wr-badge-off { background: var(--wr-warn-soft); color: var(--wr-warn); }
    .wr-badge-stock { background: var(--wr-good-soft); color: var(--wr-good); }
    .wr-url {
      display: block;
      margin-top: 0.3rem;
      font-family: ui-monospace, "SF Mono", Consolas, monospace;
      font-size: 0.7rem;
      color: var(--wr-accent);
      text-decoration: none;
      word-break: break-all;
    }
    .wr-url:hover { text-decoration: underline; }
    .wr-empty { color: var(--wr-muted); font-style: italic; padding: 1rem 0.8rem !important; }
    .wr-actions {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      flex-wrap: wrap;
      margin-top: 0.5rem;
    }
    .wr-btn {
      font-family: inherit;
      font-size: 0.85rem;
      font-weight: 600;
      padding: 0.55rem 1.1rem;
      border-radius: 6px;
      border: 1px solid transparent;
      cursor: pointer;
      transition: filter 0.12s ease;
    }
    .wr-btn:hover { filter: brightness(0.95); }
    .wr-btn-primary { background: var(--wr-accent); color: #fff; }
    .wr-btn-secondary { background: #fff; color: var(--wr-ink); border-color: var(--wr-line); }
    .wr-output {
      width: 100%;
      margin-top: 0.9rem;
      font-family: ui-monospace, "SF Mono", Consolas, monospace;
      font-size: 0.78rem;
      color: var(--wr-ink);
      background: #fff;
      border: 1px solid var(--wr-line);
      border-radius: 8px;
      padding: 0.8rem;
      resize: vertical;
    }
  </style>

  <div class="wr-header">
    <div>
      <h2 class="wr-title">Redirects 301 &middot; añadas</h2>
      <p class="wr-subtitle">Vinos desactivados o sin stock con una añada más reciente activa, listos para redirigir.</p>
    </div>
  </div>

  <section>
    <div class="wr-section-head">
      <h3>Confirmados</h3>
      <span class="wr-count">{$confirmed|count}</span>
    </div>
    <p class="wr-hint">Año de la añada antigua &lt; año de la añada nueva &mdash; marcados por defecto.</p>
    <div class="wr-table-wrap">
      <table>
        <thead>
          <tr>
            <th class="wr-check-col"></th>
            <th>Producto antiguo</th>
            <th></th>
            <th>Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$confirmed item=row}
          <tr>
            <td class="wr-check-col">
              <input type="checkbox" class="wineredirects-row-checkbox" checked="checked"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td>
              <div class="wr-name">{$row.old_name|escape:'html':'UTF-8'}</div>
              <div class="wr-meta">#{$row.old_id} &middot; <span class="wr-badge wr-badge-off">{if $row.old_active}sin stock{else}desactivado{/if}</span></div>
              <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="wr-arrow-col">&rarr;</td>
            <td>
              <div class="wr-name">{$row.new_name|escape:'html':'UTF-8'}</div>
              <div class="wr-meta">#{$row.new_id} &middot; <span class="wr-badge wr-badge-stock">stock {$row.new_qty}</span></div>
              <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="wr-empty" colspan="4">No hay sugerencias confirmadas pendientes.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </section>

  <section>
    <div class="wr-section-head">
      <h3>A revisar</h3>
      <span class="wr-count wr-count-warn">{$review|count}</span>
    </div>
    <p class="wr-hint">El producto antiguo no tiene año detectado en el nombre &mdash; revisar antes de aceptar.</p>
    <div class="wr-table-wrap">
      <table>
        <thead>
          <tr>
            <th class="wr-check-col"></th>
            <th>Producto antiguo</th>
            <th></th>
            <th>Producto actual</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$review item=row}
          <tr>
            <td class="wr-check-col">
              <input type="checkbox" class="wineredirects-row-checkbox"
                     data-line="{$row.redirect_line|escape:'html':'UTF-8'}">
            </td>
            <td>
              <div class="wr-name">{$row.old_name|escape:'html':'UTF-8'}</div>
              <div class="wr-meta">#{$row.old_id} &middot; <span class="wr-badge wr-badge-off">{if $row.old_active}sin stock{else}desactivado{/if}</span></div>
              <a class="wr-url" href="{$row.old_url|escape:'html':'UTF-8'}" target="_blank">{$row.old_url|escape:'html':'UTF-8'}</a>
            </td>
            <td class="wr-arrow-col">&rarr;</td>
            <td>
              <div class="wr-name">{$row.new_name|escape:'html':'UTF-8'}</div>
              <div class="wr-meta">#{$row.new_id} &middot; <span class="wr-badge wr-badge-stock">stock {$row.new_qty}</span></div>
              <a class="wr-url" href="{$row.new_url|escape:'html':'UTF-8'}" target="_blank">{$row.new_url|escape:'html':'UTF-8'}</a>
            </td>
          </tr>
          {foreachelse}
          <tr><td class="wr-empty" colspan="4">No hay sugerencias pendientes de revisar.</td></tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </section>

  <div class="wr-actions">
    <button type="button" id="wineredirects-generate" class="wr-btn wr-btn-primary">Generar documento</button>
    <button type="button" id="wineredirects-copy" class="wr-btn wr-btn-secondary">Copiar</button>
  </div>
  <textarea id="wineredirects-output" class="wr-output" rows="10" readonly></textarea>

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
