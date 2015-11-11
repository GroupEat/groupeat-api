# Group Customer Settings

## Settings [/customers/{id}/settings]

+ Parameters

    + id (required, string, `123`) ... The customer ID

+ Model

        {
            "notificationsEnabled": true,
            "daysWithoutNotifying": 4,
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

+ Response 400

        {
            "errorKey": "undefinedCustomerSetting",
            "message": "The customer setting with label 'undefinedSetting' does not exist."
        }
