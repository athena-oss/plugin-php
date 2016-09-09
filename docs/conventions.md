# Method Naming

The name of the test **MUST** be self explanatory, therefore **MUST** contain the *TARGET*, the *STATE* and the *RESULT*.

One of the common problems, when it comes to reading test cases/methods, is actually understanding what's happening. In a perfect world that would never happen. However we are facing reality. We can reduce dramatically issues like this, by adopting a obvious way of writing the test name.

Athena uses the `MethodName_StateUnderTest_ExpectedBehavior` naming convention.

```
testLogin_UserExistsAndHasRightCredentials_ShouldShowOkMessageAndPerformLogin
```

The example above can be easily translated to:

_Test Login IF user exists and has right credentials THEN should show ok message and perform login._

# Browser Based Tests

All tests which manipulate the Browser behaviour are included in this category.

## Page Object

> When you write tests against a web page, you need to refer to elements within that web page in order to click links and determine what's displayed. However, if you write tests that manipulate the HTML elements directly your tests will be brittle to changes in the UI. A page object wraps an HTML page, or fragment, with an application-specific API, allowing you to manipulate page elements without digging around in the HTML.
>
> — Martin Fowler

All browser based tests, must follow this pattern. Refer to [Page Object Pattern](http://martinfowler.com/bliki/PageObject.html) page, for further reading.

## API Based Tests

Whenever a API test case is written, as requirement, the response content and HTTP Status Code, must—always—be tested for validity.
