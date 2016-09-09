# Introduction

This page will explain all the fields available in `athena.json` file.

# Properties

Some properties are divided into sections, as they group more then one property.

## selenium

Some of Selenium properties are required for Browser based tests.

### hub_url

URL of your selenium hub.

_This property is required for Browser based tests._

Examples:
* http://192.168.99.100:4444/wd/hub
* http://127.0.0.1:4444/wd/hub

### connection_timeout

Time in seconds before throwing a timeout exception, when connecting to selenium hub.

### request_timeout

Time in seconds before throwing a timeout exception, when performing a request on selenium hub.

## proxy

Proxy group is optional. It's mostly used when mapping your local hostnames to the Selenium browser node.

Further information and, often, more detailed can be also found in [Proxy JSON Object](https://code.google.com/p/selenium/wiki/DesiredCapabilities#Proxy_JSON_Object) page.

### connectTimeout

Time, in milliseconds, before throwing a timeout, when performing a connection.

### readTimeout

Time, in milliseconds, before throwing a timeout, when performing a read operation.

### url

Endpoint responsible for managing the proxy.

Example:
* http://192.168.99.100 (OSX docker virtual machine)
* http://127.0.0.1 (Linux)

### port

Port where the responsible for managing the proxy, is listening on.

Example:
* 9090

### internalPort

Port where the proxy itself will be listening on.

Example:
* 9991

### proxyType

Type of proxy to be setup.

Examples:
* manual
* auto

### httpProxy

Endpoint of the HTTP proxy.

### sslProxy

Endpoint of the HTTPS proxy.

### remapHosts

Array of <name,ip> with the mapping of a hostname to it's IP.

Specially important, when testing against your local development environment.

Example:

```json
    "remapHosts" : {
      "mysite.com": "192.168.1.100"
    },
```

### blacklist_urls

Array of <regex,http_status_code> pairs, with URLs to be filtered, together with the status code to be given as a response.

Example:
```json
    "backlist_urls" : {
      ".*facebook.com" : 200,
      ".*gemius\\..*" : 200,
      ".*plus\\..*" : 200,
      ".*optimizely\\..*" : 200,
      ".*doubleclick.net" : 200
    },
```

### recording

Flag to enable/disable HTTP traffic recording.

This option is usually used to write *.har files.

### urls

Optional mapping for URLS.

Example:
* "/": "http://my.example.com"

From this point on, the first `/` in a URL string, will be mapped to the configured value.

You can also write a key.

Example:
* "myaccount" : "/account"

## report

If this setting is present in the configuration file, a HTML report will be written, containing information on the steps taken, when executing a test case.

### format

Format of report output. By default HTML will be set.

### outputDirectory

Target location for writing the report output.

## filter_directories

Array of strings of relative paths to the tests directory.

Examples:
* /directoryOne
* /directoryTwo
* /directoryThree/with/inner/directories
