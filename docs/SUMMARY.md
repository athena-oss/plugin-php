# Summary

### Getting Started

### Conventions

* [Method Naming](conventions.md#method-naming)
* [Browser Based Tests](conventions.md#browser-based-tests)
  * [Page Object](conventions.md#page-object)
  * [API Based Tests](conventions.md#api-based-tests)

### Troubleshooting

* [Common Selenium Issues](troubleshooting.md#common-selenium-issues)
* [Athena Does Not Reach Target URL](troubleshooting.md#athena-does-not-reach-target-url)
* [I Have Dynamic IPs](troubleshooting.md#i-have-dynamic-ips)
* [I Get Request Timeouts](troubleshooting.md#i-get-request-timeouts)

### API Tests

* [Introduction](api-tests.md#introduction)
* [Project Setup](api-tests.md#project-setup)
	* [The `report`](api-tests.md#the-report)
	* [The Listeners](api-tests.md#the-listeners)
* [Writing a Test](api-tests.md#writing-a-test)
	* [The Namespace](api-tests.md#the-namespace)
	* [The Parent Class](api-tests.md#the-parent-class)
	* [The Method Name Convention](api-tests.md#the-method-name-convention)
	* [Performing HTTP Calls](api-tests.md#performing-http-calls)
* [Execute The Test](api-tests.md#execute-the-test)
* [Reading The Report](api-tests.md#reading-the-report)
* [Configure a Proxy](api-tests.md#configure-a-proxy)
* [Parallel Tests](api-tests.md#parallel-tests)
	* [All Available Options](api-tests.md#all-available-options)
* [Final Thoughts](api-tests.md#final-thoughts)

### BDD Tests

* [Introduction](bdd-tests.md#introduction)
* [Project Setup](bdd-tests.md#project-setup)
	* [The `athena.json`](bdd-tests.md#the-athenajson)
		* [The `selenium.hub_url`](bdd-tests.md#the-seleniumhuburl)
		* [The `report`](bdd-tests.md#the-report)
	* [The `behat.yml`](bdd-tests.md#the-behatyml)
		* [Custom `extensions`](bdd-tests.md#custom-extensions)
		* [Default Suite](bdd-tests.md#default-suite)
* [Writing a Test](bdd-tests.md#writing-a-test)
	* [The Story Telling](bdd-tests.md#the-story-telling)
	* [From Words to Code](bdd-tests.md#from-words-to-code)
		* [The Namespace](bdd-tests.md#the-namespace)
		* [The Parent Class](bdd-tests.md#the-parent-class)
		* [The Browser Navigation](bdd-tests.md#the-browser-navigation)
		* [The Interaction With Elements](bdd-tests.md#the-interaction-with-elements)
		* [Out Of The Ordinary Assertions](bdd-tests.md#out-of-the-ordinary-assertions)
		* [All Pieces Together](bdd-tests.md#all-pieces-together)
* [Execute The Test](bdd-tests.md#execute-the-test)
* [Execute a Single Feature](bdd-tests.md#execute-a-single-feature)
* [Reading The Report](bdd-tests.md#reading-the-report)
* [Configure Proxy and/or Grid Hub](bdd-tests.md#configure-proxy-andor-grid-hub)
* [Parallel Tests](bdd-tests.md#parallel-tests)
	* [Features in Parallel](bdd-tests.md#features-in-parallel)
	* [Scenarios in Parallel](bdd-tests.md#scenarios-in-parallel)
	* [Features and Scenarios in Parallel](bdd-tests.md#features-and-scenarios-in-parallel))

### Browser Tests

* [Introduction](browser-tests.md#introduction)
* [Project Setup](browser-tests.md#project-setup)
    * [The `selenium.hub_url`](browser-tests.md#the-seleniumhuburl)
    * [The `report`](browser-tests.md#the-report)
* [Writing a Test](browser-tests.md#writing-a-test)
    * [The Namespace](browser-tests.md#the-namespace)
    * [The Parent Class](browser-tests.md#the-parent-class)
    * [The Method Name Convention](browser-tests.md#the-method-name-convention)
    * [Manipulating The Browser](browser-tests.md#manipulating-the-browser)
* [Execute The Test](browser-tests.md#execute-the-test)
* [Reading The Report](browser-tests.md#reading-the-report)
* [Configure Proxy and/or Grid Hub](browser-tests.md#configure-proxy-andor-grid-hub)
* [Parallel Tests](browser-tests.md#parallel-tests)
	* [All Available Options](browser-tests.md#all-available-options)

### Use PHPStorm

* [Open Edit Configurations](use-phpstorm.md#1-open-edit-configurations)
* [Add Bash Script](use-phpstorm.md#2-add-bash-script)
* [Configure Athena Binary](use-phpstorm.md#3-configure-athena-binary)
* [Execute](use-phpstorm.md#4-execute)

### Use XDebug

* [Open Edit Configurations](use-xdebug.md#1-open-edit-configurations)
* [Add PHP Remote Debug](use-xdebug.md#2-add-php-remote-debug)
* [Add Server](use-xdebug.md#3-add-server)
* [Set Path Mappings](use-xdebug.md#4-set-path-mappings)
* [Debug](use-xdebug.md#5-debug)
* [Profiling](use-xdebug.md#profiling)

### Reports

* [Introductions](reports.md#introduction)
* [Pre-Steps](reports.md#pre-steps)
* [BDD Reports](reports.md#bdd-reports)
* [Browser, API & Unit Tests Reports](reports.md#browser-api--unit-tests-reports)

### Tweaks

* [Configure PHP Version](tweaks.md#configure-php-version)
	* [Available Versions](tweaks.md#available-versions)
* [Disable XDebug](tweaks.md#disable-xdebug)
* [External Grid Hub and/or Proxy](tweaks.md#external-grid-hub-andor-proxy)
* [Unit Tests In Paralell](tweaks.md#unit-tests-in-parallel)
	* [All Available Options](tweaks.md#all-available-options)

### Config Reference

* [Properties](config-reference.md#properties)
	* [selenium](config-reference.md#selenium)
		* [hub_url](config-reference.md#huburl)
		* [connection_timeout](config-reference.md#connectiontimeout)
		* [request_timeout](config-reference.md#requesttimeout)
	* [proxy](config-reference.md#proxy)
		* [connectTimeout](config-reference.md#connecttimeout)
		* [readTimeout](config-reference.md#readtimeout)
		* [url](config-reference.md#url)
		* [port](config-reference.md#port)
		* [internalPort](config-reference.md#internalport)
		* [proxyType](config-reference.md#proxytype)
		* [httpProxy](config-reference.md#httpproxy)
		* [sslProxy](config-reference.md#sslproxy)
		* [remapHosts](config-reference.md#remaphosts)
		* [blacklist_urls](config-reference.md#blacklist_urls)
		* [recording](config-reference.md#recording)
		* [urls](config-reference.md#urls)
	* [report](config-reference.md#report)
		* [format](config-reference.md#format)
		* [outputDirectory](config-reference.md#outputdirectory)
	* [filter_directories](config-reference.md#filterdirectories)
