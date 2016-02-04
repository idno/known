(function (exports) {
    exports.init = function (brevity, assert, testdata) {
        describe('shorten()', function () {
            testdata['shorten'].forEach(function (testcase) {
                it('shortens ' + testcase.text.replace(/\s+/g, ' '), function () {
                    var result = brevity.shorten(
                        testcase.text,
                        testcase.permalink,
                        testcase.permashortlink,
                        testcase.permashortcitation,
                        testcase.target_length,
                        testcase.link_length,
                        testcase.format_as_title
                    );
                    assert.equal(testcase.expected, result);
                });
            });
        });
        describe('autolink()', function () {
            testdata['autolink'].forEach(function (testcase) {
                it('autolinks ' + testcase.text.replace(/\s+/g, ' '), function () {
                    var result = brevity.autolink(testcase.text);
                    assert.equal(testcase.expected, result);
                });
            });
        });
    }
}(typeof exports === 'undefined' ? this['test']={} : exports));
