# Group Admin

::: warning
<i class="fa fa-unlock-alt"></i>
An authentication token corresponding to an administrator account is required to access this zone.
:::

## Get the API docs  [/docs]

### GET

Retrieve the HTML code of the API documentation.

+ Response 200

+ Response 403

        {
            "errorKey": "wrongAuthenticatedUser",
            "message": "Should be authenticated as admin instead of customer."
        }

## Send notification [/devices/{uuid}/notifications]

+ Parameters

    + uuid (required, string, `123`) ... The device UUID

### POST

Send a push notification to the device with the given UUID. To send a *silent push*, omit the message and title fields.

+ Request

        {
            "title": "A short title",
            "message": "A message that gives more details",
            "timeToLiveInSeconds": 60, // optional
            "additionalData": {
                "keyNotReservedByApns": 1,
                "keyNotReservedByGcm": 2
            }
        }

+ Response 200
