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
        
## Device [/customers/{id}/devices{?include}]

+ Parameters

    + id (required, string, `123`) ... The customer ID
    + include (optional, string, `platform`) ... [platform]

+ Model

        {
            "id": "1",
            "UUID": "8556336d027b4788",
            "model": "Nexus 5",
            "createdAt": "2015-05-18 22:36:59",
            "updatedAt": "2015-05-18 22:36:59"
        }

### List customer devices [GET]

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
            "notificationToken": "353SQKFJ323fdsf", // The token to use with GCM, APNS or equivalent services
            "platform": "android", // Should be one of the labels returned by the route above.
            "model": "Black 16 Go Nexus 5"
        }

+ Response 201

    [Device][]

+ Response 401

        {
            "errorKey": "userMustAuthenticate",
            "message": "No authenticated user."
        }
