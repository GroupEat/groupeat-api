# Group Customers

## Register customer [/customers]

### POST

The user locale should be sent in addition to the classical email and password fields. In fact, even if the API will always use the English language, we need to know the language of the user in order to send him e-mails he can understand.

An e-mail will be sent to the given address with an activation link that must be clicked in order to fully activate the created account.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password",
            "locale": "fr"
        }

+ Response 201

    [Token][]
        
+ Response 400

        {
            "status_code": 400,
            "error_key": "unavailableLocale",
            "message": "The locale ru should belong to [fr]."
        }

+ Response 422

        {
            "status_code": 422,
            "message": "Cannot register user with invalid credentials.",
            "errors": {
                "email": {
                    "email": [], // Invalid email format
                    "notFromCampus": [],
                    "alreadyTaken": [
                        "user_credentials"
                    ]
                },
                "password": {
                    "min": [
                        "6"
                    ]
                }
            }
        }

## Customer [/customers/{id}]

+ Parameters

    + id (required, string, `123`) ... The customer ID

+ Model

        {
            "id": "1",
            "email": "contact@mangeo.fr",
            "firstName": "Jean-Nathanaël",
            "lastName": "Hérault",
            "phoneNumber": "06 05 04 03 02",
            "locale": "fr",
            "activated": true
        }

### Get customer [GET]

+ Response 200

    [Customer][]

+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

+ Response 404

### Update customer [PUT]

Replace the customer data with the one passed in the request. However, a customer must have a first name, a last name and a phone number thus, when hitting this route for the first time, valid information for all these fields must be given.

+ Request

        {
            "firstName": "Jean-Nathanaël",
            "lastName": "Hérault",
            "phoneNumber": "06 05 04 03 02"
        }

+ Response 200

    [Customer][]

+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 1 instead of 5."
        }

+ Response 422
            
        {
            "status_code": 422,
            "message": "Cannot save customer #6.",
            "errors": {
                "phoneNumber": {
                    "regex": [
                        "/^0[0-9]([ .-]?[0-9]{2}){4}$/"
                    ]
                }
            }
        }

### Unregister customer [DELETE]

+ Response 200

+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 1 instead of 5."
        }

## Address [/customers/{id}/address]

+ Parameters

    + id (required, string, `123`) ... The customer ID

+ Model

        {
            "street": "Allée des techniques avancées",
            "details": "Bâtiment A, chambre 200",
            "city": "Palaiseau",
            "postcode": 91120,
            "state": "Essone",
            "country": "France",
            "latitude": 48.711042,
            "longitude": 2.219278
        }

### Get address [GET]

+ Response 200

    [Address][]
    
+ Response 404

### Add/Update address [PUT]

For the MVP, all the addresses must be valid campus addresses. That's why the only writable attributes are: street, details, latitude and longitude. Logically, all of these fields should change together because they are not independent. That's why this request is a PUT and not a PATCH...

+ Request

        {
            "street": "Allée des techniques avancées",
            "details": "Bâtiment A, chambre 200",
            "latitude": 48.711042,
            "longitude": 2.219278
        }

+ Response 200

    [Address][]
    
+ Response 404

+ Response 422

        {
            "status_code": 422,
            "error_key": "validationErrors",
            "errors": {
                "longitude": {
                    "numeric": []
                }
            },
            "message": "Cannot save address #6."
        }

## List predefined addresses  [/predefinedAddresses]

### GET

+ Response 200

        [
            {
                "street": "Boulevard des Maréchaux",
                "details": "Foyer de l'ENSTA ParisTech",
                "city": "Palaiseau",
                "postcode": 91120,
                "state": "Essonne",
                "country": "France",
                "latitude": 2.21874,
                "longitude": 48.711
            },
            {
                "street": "Avenue Augustin Fresnel",
                "details": "BôBar",
                "city": "Palaiseau",
                "postcode": 91120,
                "state": "Essonne",
                "country": "France",
                "latitude": 2.21108,
                "longitude": 48.7117
            },
            {
                "street": "2 Avenue Augustin Fresnel",
                "details": "Institut d'Optique",
                "city": "Palaiseau",
                "postcode": 91120,
                "state": "Essonne",
                "country": "France",
                "latitude": 2.20349,
                "longitude": 48.7139
            }
        ]
