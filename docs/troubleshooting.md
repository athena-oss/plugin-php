# Common Selenium Issues

In case you have strange issues with the `Selenium`components, please check that you have assigned enough memory and cpu to your docker machine.

We strongly recommend to increase CPU VirtualBox Images processors, disk and RAM assigment in case you didn't do it on creation already:

1. Open VirtualBox
2. Right click in "default" image and power it off
3. Right click on image again and go to settings->system->processor
4. Increase "Processors" up to the biggest value inside the green area of the bar (Otherwise, performance will be decreased)
5. In the "Motherboard" Tab of the current Dialog, increase the Base Memory to at least 4096MB.
6. Click ok and do docker-machine restart default and don't forget the 'eval .. env..' part

# Athena Does Not Reach Target URL

Most cases are associated with Athena not being able to resolve the hostname. As an example, when the webserver is running on the local machine.

In order to solve this, add a proxy configuration in your `athena.json` file, which points your domain address to the IP address you expect.

**NOTE:** This requires [Proxy Server](https://github.com/athena-oss/plugin-proxy) to be running.

```
"proxy" : {
   "url" : "http://athena-proxy",
   "port" : 9090,
   "internalPort" : 9991,
   "proxyType" : "manual",
   "httpProxy" : "athena-proxy:9991",
   "sslProxy" : "athena-proxy:9991",
   "remapHosts" : {
     "mydomain.com" : "<my_ip_address>"
   }
 }
```

Refer to the [Proxy Configuration Schema](json-config-reference.md#proxy) section to understand better the full syntax.

# I Have Dynamic IPs

Inside Athena configuration file, you can choose to use `ENV[MY_CONFIGURATION_VALUE]` in case it's not a fixed value. For example, we take advantage of this feature to use in our CI server, for finding out which one is the instantiated web server IP.

This value will then be retrieved from the environment variable `MY_CONFIGURATION_VALUE`.

```
"proxy" : {
   "url" : "http://athena-proxy",
   "port" : 9090,
   "internalPort" : 9991,
   "proxyType" : "manual",
   "httpProxy" : "athena-proxy:9991",
   "sslProxy" : "athena-proxy:9991",
   "remapHosts" : {
     "mydomain.com" : "ENV[WEBSERVER_IP]"
   }
 }
```

# I Get Request Timeouts

A common issue are timeouts related to Selenium driver.

There's several aways of addressing these issues, by checking if the page load time is too big, due to high number of external resources being loaded, or simply by increasing timeouts, or checking if there's steps inside the test that are stopping execution for too long (sleeps).

Here's a link of items to which you can manipulate the values to avoid these issues:
- [Request Timeout](config-reference.md#requesttimeout)
- [Connection Timeout](config-reference.md#connectiontimeout)

You set both these values in `athena.json` with a value higher than 30 seconds.

In case you are using a proxy configuration, you can also refer to:
- [Proxy Connect Timeout](config-reference.md#connecttimeout)
- [Proxy Read Timeout](config-reference.md#readtimeout)
