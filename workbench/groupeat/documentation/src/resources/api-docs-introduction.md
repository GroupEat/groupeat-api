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

Because of some PHP limitations, you cannot send data along with a PUT, PATCH or DELETE request through the usual `form-data` parameter. Instead, you have to send the data through the `x-www-form-urlencoded`. Of course, you can still send parameters by query string if you like it but it may not be the most elegant solution and it will be less secure.
