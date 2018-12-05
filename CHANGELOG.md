## 0.9.0 (December 05, 2018)
- ca29730 updated composer.lock, php7.0 + 7.1 + added php7.2
- 1ff2863 better documentation how to run the test suite

## 0.8.3 (June 25, 2018)
- e2b31d6 Download xdebug package from pecl.php.net instead of xdebug.org.

## 0.8.2 (May 24, 2018)

- 84d603d Update Dockerfile to use jessie based image for php7
- fa2ea33 Update Dockerfile to use jessie based image for php7.1
- f8265ae Revert back the test status output
- 79939d0 Fix to handle multiple feature tags
- 4c0b776 Fix to handle multiple tags in features
- 9251ada screenshot settings results to variable and use &&
- 4aa48ef Change from config value from string to boolean
- 163fd33 Configuration option to turn on and off the screenshot in BDD
- 97d7ad6 adding tab escape
- e53b0fb add raw in errorMessage
- a5fca38 adding char escape of step name
- 0d168d5 replace &quote;
- 94e5208 adding cucumber report for BDD test
- 0b1f889 Added example of running athena uhit tests with php7.1
- 9e3df49 Php 7.1 support
- 26fef56 Update broken link in troubleshooting page

## 0.8.1 (May 18, 2018)

- f10dbd0 Fix to disable repot on runtime.

## 0.8.0 (May 12, 2017)

- 51952ca Update Athena.php
- abf2364 Update DispatcherRegistry.php
- e6cd098 Update LoggerFactory.php
- 910b72e Update Settings.php
- d3b7410 Update composer.json
- 0d6e922 Update json_report.twig
- 95f7208 Update json_report.twig
- d7a018a Adding composer lock file.
- e7f3dbb Update composer.json
- a0ee520 Update ApiDecorator.php
- fb5fe5d Update json_report.twig
- 071ca31 Fix missing body on http report
- e4d5851 Update json_report.twig
- 60caf75 Create json_report.twig
- 3990ca1 Update InterpreterFactory.php
- 79771a8 Update BddReportBuilder.php
- 690cbbd Update LoggerFactory.php

## 0.7.4 (January 30, 2017)

- 723009d Add more verbose to HTTP events
- 47266de Allow path ignore list for parallel tests
- b965368 Upgrade Browser Proxy client to support Guzzle 6
- f1309fe Upgrade to Guzzle 6.0
- 9d112cb Better support for spaces

## 0.7.3 (January 23, 2016)

- 597b1b2 Fix argument construction for parallel tests
- 7e58e88 Update reports.md
- 9384405 Update LoggerFactory.php

## 0.7.2 (December 12, 2016)

- Restored `BrowserDriverBuilder::fromSettings`

## 0.7.1 (December 8, 2016)

- Added `composer` sub-command
- FluentWebDriverClient is now an external dependency
- Documentation updates
- Updated FluentHttpClient dependency
- Allow setting a default value for ENV variables used in configuration

## 0.7.0 (September 9, 2016)

### Athena Plugin PHP

- Initial public release
