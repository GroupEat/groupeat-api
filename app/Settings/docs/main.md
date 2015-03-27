# Group Customer Settings

## Settings [/customers/{id}/settings]

+ Parameters

    + id (required, string, `123`) ... The customer ID

+ Model

        {
            "notificationsEnabled": false,
            "daysWithoutNotifying": 3,
            "noNotificationAfter": "22:00:00"
        }

### List settings [GET]

+ Response 200

    [Settings][]

### Update settings [PUT]

+ Request

        {
            "notificationsEnabled": true,
            "daysWithoutNotifying": 4
        }

+ Response 200

    [Settings][]
