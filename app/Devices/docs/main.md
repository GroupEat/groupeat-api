# Group Devices

## List platforms [/devices/platforms]

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

## Device [/customers/{customerId}/devices{?include}]

+ Parameters

    + customerId (required, string, `123`) ... The customer ID

+ Model

        {
            "id": "1",
            "UUID": "8556336d027b4788",
            "model": "Nexus 5",
            "createdAt": "2015-05-18 22:36:59",
            "updatedAt": "2015-05-18 22:36:59"
        }

### List customer devices [GET]


+ Parameters

    + include (optional, string, `platform`) ... [platform]

+ Response 200

        [
            {
                "id": "1",
                "UUID": "8556336d027b4788",
                "model": "Nexus 5",
                "createdAt": "2015-05-18 22:36:59",
                "updatedAt": "2015-05-18 22:36:59"
            },
            {
                "id": "2",
                "UUID": "7476Sqfsd77",
                "model": "iPhone 6",
                "createdAt": "2015-05-26 23:06:03",
                "updatedAt": "2015-05-26 23:06:03"
            }
        ]

+ Response 403

        {
            "errorKey": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

### Attach device [POST]

+ Request

        {
            "UUID": "1sfqsf557845sfsf", // A unique ID representing the device
            "notificationToken": "353SQKFJ323fdsf", // The token to use with GCM, APNS or equivalent services, optional
            "platform": "android", // Should be one of the labels returned by the route above.
            "platformVersion": "1.0",
            "model": "Black 16 Go Nexus 5"
            "location": { // optional
                "latitude": 48.711,
                "longitude": 2.21928
            }
        }

+ Response 201

    [Device][]

+ Response 401

        {
            "errorKey": "userMustAuthenticate",
            "message": "No authenticated user."
        }

### Update device [PUT /customers/{customerId}/devices/{deviceUUID}]

+ Parameters

    + customerId (required, string, `123`) ... The customer ID
    + deviceUUID (required, string, `abc-def`) ... The device UUID

+ Request

        {
            "notificationId": "1", // optional, send if the request is made due to the reception of a notification
            "notificationToken": "353SQKFJ323fdsf", // optional, best if sent to keep a fresh one server side
            "platformVersion": "2.0",
            "location": { // optional though needed after silent push to locate the device before sending the real notification
                "latitude": 48.711,
                "longitude": 2.21928
            }
        }

+ Response 200

    [Device][]

+ Response 401

        {
            "errorKey": "userMustAuthenticate",
            "message": "No authenticated user."
        }
