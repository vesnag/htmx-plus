module.exports = {
  '@tags': ['htmx_plus_random_number'],
  before: function (browser) {
    browser
      .drupalInstall()
      .drupalInstallModule('htmx_plus_random_number', true)
      .drupalCreateUser({
        name: 'user',
        password: '123',
        permissions: [
          'access content',
        ],
      })
      .drupalLogin({ name: 'user', password: '123' });
  },
  after: function (browser) {
    browser
      .drupalUninstall();
  },
  'Visit a test page and get a random number': (browser) => {
    browser
      .drupalRelativeURL('/random-number')
      .waitForElementVisible('#btn-get-random-number', 1000)
      .assert.textContains('body', 'Get Random Number with HTMX')
      .click('#btn-get-random-number')
      .waitForElementVisible('#random-number-result', 1000)
      .getText('#random-number-result', function (result) {
        this.assert.ok(/^\d+$/.test(result.value), 'Random number result contains a number');
      })
      .end();
  },
};
