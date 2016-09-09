# Configure PHP Version

`$ athena php <bdd|unit|lint|browser|api> <args...> --php-version=<version>`

To see all available versions, set `--php-version`, in any command, without any value.

Internally we check the `docker/` folder, to see if there's a docker image for &lt;version&gt;.

`$ athena php bdd my-tests/ my-tests/athena.json --browser=firefox --php-version=7.0`

## Available Versions

```bash
$ athena php bdd my-tests/ my-tests/athena.json --browser=firefox --php-version
...

[FATAL] --php-version must be set to one of the available versions: php5.6, php7.0
```

# Disable XDebug

XDebug slows your application considerably, therefore in an environment, such as CI, or simply, in case you are in a hurry and the tests are taking too long, it might help switching XDebug off.

Set `ATHENA_DISABLE_DEBUG=1` environment variable.

# External Grid Hub and/or Proxy

When `browser` and `bdd --browser=<browser>` is run, it will try to automatically link with a running Grid Hub or/and Proxy Server.

In case --skip-hub or/and --skip-proxy exists, the link will not be performed.

For performing a link with another running container, you can optionally
specify --link-hub=<container_name> and/or --link-proxy=<container_name>.

# Unit Tests In Parallel

```bash
$ athena php unit my-tests/ --parallel=<number> [<paratest-options>...]
```

```bash
$ athena php unit my-tests/ --parallel=2
```

## All Available Options

```bash
$ athena php unit my-tests/ --parallel --help
...

Usage:
  paratest [options] [--] [<path>]

Arguments:
  path                                   The path to a directory or file containing tests. (default: current directory)

Options:
  -p, --processes=PROCESSES              The number of test processes to run. [default: 5]
  -f, --functional                       Run methods instead of suites in separate processes.
      --no-test-tokens                   Disable TEST_TOKEN environment variables. (default: variable is set)
  -h, --help                             Display this help message.
      --coverage-clover=COVERAGE-CLOVER  Generate code coverage report in Clover XML format.
      --coverage-html=COVERAGE-HTML      Generate code coverage report in HTML format.
      --coverage-php=COVERAGE-PHP        Serialize PHP_CodeCoverage object to file.
  -m, --max-batch-size=MAX-BATCH-SIZE    Max batch size (only for functional mode). [default: 0]
      --filter=FILTER                    Filter (only for functional mode).
      --whitelist=WHITELIST              Directory to add to the coverage whitelist.
      --phpunit=PHPUNIT                  The PHPUnit binary to execute. (default: vendor/bin/phpunit)
      --runner=RUNNER                    Runner or WrapperRunner. (default: Runner)
      --bootstrap=BOOTSTRAP              The bootstrap file to be used by PHPUnit.
  -c, --configuration=CONFIGURATION      The PHPUnit configuration file to use.
  -g, --group=GROUP                      Only runs tests from the specified group(s).
      --exclude-group=EXCLUDE-GROUP      Don't run tests from the specified group(s).
      --stop-on-failure                  Don't start any more processes after a failure.
      --log-junit=LOG-JUNIT              Log test execution in JUnit XML format to file.
      --colors                           Displays a colored bar as a test result.
      --testsuite[=TESTSUITE]            Filter which testsuite to run
      --path=PATH                        An alias for the path argument.
```

Once the option --parallel is set, under hood paratest will replace phpunit.
