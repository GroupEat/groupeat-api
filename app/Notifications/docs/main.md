# Group Notifications

## Send notification [/groupOrders/{id}/notifications]

+ Parameters

    + id (required, string, `123`) ... The group order ID

### POST

::: warning
#### <i class="fa fa-exclamation-circle"></i> Warning
This route is for testing purpose only, it may be removed soon.
:::

The user corresponding to the authentication token must have already attached a device by calling the appropriate route.

+ Response 200

+ Response 401

        {
            "errorKey": "userMustAuthenticate",
            "message": "No authenticated user."
        }

+ Response 404
