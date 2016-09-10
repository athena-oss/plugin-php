# Athena PHP Plugin [![Build Status](https://travis-ci.org/athena-oss/plugin-php.svg?branch=master)](https://travis-ci.org/athena-oss/plugin-php)

Athena PHP Plugin is a plugin for [Athena](https://github.com/athena-oss/athena), that provides a PHP environment for you to execute and create different types of tests, and provides a fluent interface to short-cut and ease test development for PHP.

For Tests that require `Selenium` and/or `Proxy` you also need to install the [Athena Selenium Plugin](https://github.com/athena-oss/plugin-selenium) and/or [Athena Proxy Plugin](https://github.com/athena-oss/plugin-proxy).

## Main Features

* Supported Testing types :
	* API (using BDD or classic approach)
	* Browser (using BDD or classic approach)
	* Unit
	* Lint
	* Checkstyle
	* Complexity
* Parallelism
* Reports with Screenshots for when using Selenium driver (HTML)
* Reports with HTTP transanctions exposed (HTML)
* And many many more...

## How to Install ?

To install it simply run the following command :

```bash
$ athena plugins install php https://github.com/athena-oss/plugin-php.git
```

or

* On MAC OSX using [Homebrew](http://brew.sh/) :
```bash
$ brew tap athena-oss/tap
$ brew install plugin-php
```

Read the [Documentation](http://athena-oss.github.io/plugin-php) on using Athena Plugin PHP.

## Examples

### API Tests

```php
namespace Tests;

use Athena\Athena;
use Athena\Test\AthenaAPITestCase;

class HttpBinTest extends AthenaAPITestCase
{
    public function testIpEndpoint_PerformGetRequest_ShouldContainOrigin()
    {
        $result = Athena::api()
                            ->get('http://httpbin.org/ip')
                            ->then()
                            ->assertThat()
                            ->responseIsJson()
                            ->statusCodeIs(200)
                            ->retrieve()
                            ->fromJson();

        $this->assertArrayHasKey('origin', $result);
    }
}
```

### Browser Tests

```php

...

 public function testGoogle_WaitForElementExistenceAndClick_ShouldShowResultsPage()
    {
        Athena::browser()->get("http://google.pt")
            ->getElement()
            ->withName('btnI')
            ->wait(1)
            ->toBePresent()
            ->thenFind()
            ->asHtmlElement()
            ->click();
    }

...

```

### Browser Tests - BDD

#### Defining the feature scenario

```
Feature: Anonymous User performs a search

  As a Anonymous User
  I want to perform a search for a string
  So that I can get a list of results related with my search

  Scenario: Searched string returns results
    Given the current location is the home page
    When the Anonymous User writes "athena" in the search box
    And the Anonymous User performs a click in the search button
    Then the current location should be results page
```

#### Implementing

```php

...

   /**
     * @Given /^the current location is the home page$/
     */
    public function theCurrentLocationIsTheHomePage()
    {
        $this->currentLocation = $this->browser->get('https://google.com/');
    }
    /**
     * @When /^the Anonymous User writes "([^"]*)" in the search box$/
     */
    public function theAnonymousUserWritesInTheSearchBox($arg1)
    {
        $this->currentLocation
            ->find()
            ->elementWithName('q')
            ->sendKeys($arg1);
    }
    /**
     * @Given /^the Anonymous User performs a click in the search button$/
     */
    public function theAnonymousUserPerformsAClickInTheSearchButton()
    {
        $this->currentLocation
            ->find()
            ->elementWithName('btnG')
            ->click();
    }
    /**
     * @Then /^the current location should be results page$/
     */
    public function theCurrentLocationShouldBeResultsPage()
    {
        PHPUnit_Framework_Assert::assertStringEndsWith("#q=athena", Athena::browser()->getCurrentURL());
    }

...

```


## Using the plugin on the cli

```
$ athena php

...

usage: athena php <command> [<args...>]

These are the available commands for plugin [php]:
	api        Run api tests.
	bdd        Run behaviour driven tests.
	browser    Run browser tests.
	cleanup    Removes vendor related stuff.
	lint       Check files for syntax errors.
	phpcs      Analyse code smells against a custom or existing rule-set.
	phpmd      Run mess detector tests
	self-test  Executes tests to the built-in functionalities.
	unit       Run unit tests.

```
