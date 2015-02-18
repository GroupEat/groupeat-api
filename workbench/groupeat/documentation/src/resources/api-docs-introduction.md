FORMAT: 1A
HOST: {{ app.url }}/{{ api::prefix }}

# GroupEat Backend API

## Introduction

To make requests to the API, the desired version must be specified through the Accept header. For the current version, it will be:

```http
Accept: application/vnd.{{ api::vendor }}.{{ api::version }}+{{ api::default_format }}
```

All of the paths below must be prefixed by {{ app.url }}/{{ api::prefix }} to have the full and correct URL.

### Sending requests with the Postman Chrome extension

For POST, PUT and PATCH requests, the API expects to receive data through the request body, usually in JSON format. Thus, when creating a request with the Postman Chrome extension, the `raw` mode should be used instead of the `form-data` and `x-www-form-urlencoded` modes.

### Passing data through the URL

Passing data through the URL is allowed for GET requests only. For PUT, PATCH, DELETE and POST, the request body should be used to attach data to the request. This is partly for security reasons because writing passwords or tokens in the URL reduce privacy even if HTTPS is set up.

### Data scope

In order to be able to send meta-data (like pagination for example) in a response, the main data of interest is wrapped in the scope of the `data` sub-object. It means that, when sending a GET request with the intend of receiving a single resource from the API, its attributes will not be in the response body root but instead in the `data` sub-object.

For instance an id field in a `$response` variable should be accessed like this: `$response['data']['id']`.

This `data` scope will not be repeated in the responses below for the sake of simplicity.

### Include query parameter

Some GET routes accept an `include` parameter in their query string. It is used to load and insert resource relations in the repsonse body so that you don't have to make a second (or more) request to retrieve it. When this behavior is available, the route parameters will indicate it as well as which relations can be included. Use a `,` to list relations and a `.` to include nested relations when possible.

Example: /books?include=author,publishers.somethingelse

Included relations' data is accessible under another `data` scope.
