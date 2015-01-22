FORMAT: 1A
HOST: {{ app.url }}/{{ api::prefix }}

# GroupEat Backend API

## Introduction

To make requests to the API you need to specify the version you want to use with the Accept header. For the current version, it will be:

```http
Accept: application/vnd.{{ api::vendor }}.{{ api::version }}+{{ api::default_format }}
```

Then you need to prefix all of the paths below by {{ app.url }}/{{ api::prefix }} to have the full and correct URL.

### About PUT, PATCH and DELETE requests

Because of some PHP limitations, you cannot send data along with a PUT, PATCH or DELETE request through the usual `form-data` parameter. Instead, you have to send the data through the `x-www-form-urlencoded`.

### Passing data through the URL

You can pass data through the URL only for GET requests. For PUT, PATCH, DELETE and POST, you should use the request body to attach data to the request. This is partly for security reasons because writing passwords or tokens in the URL reduce privacy even if HTTPS is set up.

### Data scope

In order to be able to send meta-data (like pagination for example) in a response, the main data of interest is wrapped in the scope of the `data` sub-object. It means that if you send a GET request and intend to receive a single resource from the API, its attributes will not be int the response body root but instead in the `data` sub-object. For instance an id field in a `$response` variable should be accessed like this: `$response['data']['id']`.
This `data` scope will not be repeated in the responses below for the sake of simplicity.
