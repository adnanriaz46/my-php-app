/* eslint-disable @typescript-eslint/no-require-imports */
const fs = require('fs');
fs.writeFileSync('public/version.json', JSON.stringify({ version: Date.now() }));
