function buildDocumentText(lines) {
  return lines.join('\n');
}

function wineRedirectsRefreshDocument() {
  var checked = document.querySelectorAll('.wineredirects-row-checkbox:checked');
  var lines = Array.prototype.map.call(checked, function (checkbox) {
    return checkbox.getAttribute('data-line');
  });
  var textarea = document.getElementById('wineredirects-output');
  textarea.value = buildDocumentText(lines);
}

function wineRedirectsCopyDocument() {
  var textarea = document.getElementById('wineredirects-output');
  textarea.select();
  navigator.clipboard.writeText(textarea.value);
}

if (typeof document !== 'undefined') {
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
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { buildDocumentText: buildDocumentText };
}
