const assert = require('assert');
const { buildDocumentText } = require('../views/js/wineredirects.js');

let failures = 0;

function check(actual, expected, label) {
  try {
    assert.strictEqual(actual, expected);
    console.log(`PASS: ${label}`);
  } catch (e) {
    failures++;
    console.log(`FAIL: ${label} — expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)}`);
  }
}

check(
  buildDocumentText(['Redirect 301 /a.html https://x/a2.html', 'Redirect 301 /b.html https://x/b2.html']),
  'Redirect 301 /a.html https://x/a2.html\nRedirect 301 /b.html https://x/b2.html',
  'joins multiple lines with a single newline'
);
check(buildDocumentText([]), '', 'returns an empty string for no lines');
check(buildDocumentText(['only one']), 'only one', 'returns the single line unchanged when there is only one');

process.exit(failures === 0 ? 0 : 1);
