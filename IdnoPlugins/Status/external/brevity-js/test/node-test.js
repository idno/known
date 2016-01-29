var fs = require('fs');
var content = fs.readFileSync('tests.json', 'utf8')
var testdata = JSON.parse(content);
require('./test.js').init(require('../brevity.js'), require('assert'), testdata);
