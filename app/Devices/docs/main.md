# Group Devices

## List operating systems [/devices/operatingSystems]

### GET

+ Response 200

        [
            {
                "id": "1",
                "label": "android"
            },
            {
                "id": "2",
                "label": "ios"
            }
        ]

## Attach device [/customers/{id}/devices]

### POST

+ Parameters

    + id (required, string, `123`) ... The customer ID.

+ Request

        {
            "hardwareId": "1sfqsf557845sfsf", // A unique ID representing the device
            "notificationToken": "353SQKFJ323fdsf", // The token to use with GCM, APNS or equivalent services
            "operatingSystemId": "1", // See the route above
            "operatingSystemVersion": "5.0.1 Lollipop",
            "model": "Black 16 Go Nexus 5",
            "latitude": 48.7173,
            "longitude": 2.23935
        }

+ Response 201

+ Response 401

        {
            "errorKey": "userMustAuthenticate",
            "message": "No authenticated user."
        }
