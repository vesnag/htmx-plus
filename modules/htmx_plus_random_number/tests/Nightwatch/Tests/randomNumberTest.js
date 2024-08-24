module.exports = {
  '@tags': ['htmx_plus_random_number'],
  before: function (browser) {
    console.log('Starting test setup...');
    browser
      .drupalInstall()
      .drupalInstallModule('htmx_plus_random_number', true);
  },
  after: function (browser) {
    console.log('Cleaning up after test...');
    browser
      .drupalUninstall();
  },
  'Visit a test page and create some test page': (browser) => {
    console.log('Starting test...');
    browser
      .drupalRelativeURL('/random-number')
      .assert.textContains('body', 'Get Random Number with HTMX')
      .click('#btn-get-random-number')
      .waitForElementVisible('#random-number-result', 1000, function (result) {
        console.log('Random number result visible:', result.status === 0);
      })
      .getText('#random-number-result', function (result) {
        console.log('Random number result text:', result.value);
        this.assert.ok(/^\d+$/.test(result.value), 'Random number result contains a number');
      })
      .saveScreenshot('screenshots/random-number-result.png')
      .end();
  },
};
