# Group Admin

An authentication token corresponding to an administrator account is required to access this zone.

## Get the API docs  [/admin/docs]

### GET

Retrieve the HTML code of the API documentation.

+ Response 200

+ Response 403

        {
            "errorKey": "wrongAuthenticatedUser",
            "message": "Should be authenticated as admin instead of customer."
        }
