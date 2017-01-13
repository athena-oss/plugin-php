# Introduction

When something goes wrong we expect our testing framework, to provide valuable information to identify and solve as quickly as possible the issue.

HTML reports is one of Athena's strong point, as it provides detailed information; in case you run a test which uses Selenium, then screenshots and step by step information will be available.

JUnit and coverage reports in XML are available, for test cases which take advantage of PHPUnit calls. Such as `browser`, `unit` and `api` tests.

# Pre-Steps

Inside your tests repository, enable reports by copy pasting the JSON-String below, in your `athena.json` file, and creating `Report` directory in the root level.

```json
  "report" : {
    "format" : "html",
    "outputDirectory" : "./Report",
    "name" : "Custom_Name_From_Command_Line"
  }
```

_This step is common across all test types._

# BDD Reports

Go to your tests repository directory, and in your `behat.yml` insert the extensions shown below.

```yaml
default:
    extensions:
        Athena\Behat\BootstrapFileLoader:
                bootstrap_path: "/opt/athena-php/bootstrap.php"

        Athena\Event\Proxy\BehatProxyExtension: ~
```

Athena requires `BootstrapFileLoader` for injecting it's bootstrap for autoloading, and `BehatProxyExtension` to dispatch Behat events to Athena.

# Browser, API & Unit Tests Reports

Go to your tests repository directory and, if you don't have a `phpunit.xml` file, create one.

```xml
<?xml version="1.0" encoding="UTF-8"?>

<!-- http://www.phpunit.de/manual/current/en/appendixes.configuration.html -->
<phpunit
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/5.1/phpunit.xsd"
        backupGlobals="false"
        colors="true"
>

    <listeners>
        <listener class="\Athena\Event\Adapter\PhpUnitAdapter">
            <arguments>
                <object class="\Athena\Event\Dispatcher\DispatcherLocator"/>
            </arguments>
        </listener>
    </listeners>

    <testsuites>
        <testsuite name="My Test Suite">
            <directory>tests/t</directory>
        </testsuite>
    </testsuites>

</phpunit>
```

The `listeners` node must be present, without it Athena never receives PHPunit events.

Is important to remember that when you run your tests, you should point to the directory where your `phpunit.xml` is located. PHPUnit will read the information inside the xml and run the tests accordingly.
