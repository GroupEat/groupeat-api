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

        {
             "id": 1,
             "type": "customer",
             "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjoxLCJpYXQiOjE0MjA0OTU0ODYsImV4cCI6MjA1MTIxNTQ4Nn0.1vZ4fyrLfyNP5LLjRI64x8ne8C7TAtGf6DO_i6qS7Do",
             "activated": false
         }

+ Response 400

        {
            "status_code": 400,
            "error_key": "emailAlreadyTaken",
            "message": "Cannot register user with invalid credentials.",
            "errors": {
                "email": [
                    "The e-mail has already been taken."
                ],
                "password": [
                    "The password must be at least 6 characters."
                ]
            }
        }
        
+ Response 400

        {
            "status_code": 400,
            "error_key": "invalidEmail",
            "message": "Cannot register user with invalid credentials.",
            "errors":{
                "email": [
                    "The e-mail must be a valid e-mail address."
                ]
            }
        }
        
+ Response 400

        {
            "status_code": 400,
            "error_key": "passwordTooShort",
            "message": "Cannot register user with invalid credentials.",
            "errors": {
                "password": [
                    "The password must be at least 6 characters."
                ]
            }
        }

+ Response 403

        {
            "status_code": 403,
            "error_key": "emailNotFromCampus",
            "message": "E-mail should correspond to a Saclay campus account."
        }

## Customer [/customers/{id}]

+ Parameters

    + id (required, integer, `123`) ... The customer ID

+ Model

        {
            "id": 1,
            "firstName": "Jean-Nathanaël",
            "lastName": "Hérault",
            "phoneNumber": "06 05 04 03 02",
            "created_at": "2015-01-06 09:22:31",
            "updated_at": "2015-01-06 10:41:25",
            "locale": "fr",
            "deleted_at": null
        }

### Get customer [GET]

+ Response 200

    [Customer][]

+ Response 401

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

+ Response 404

### Update customer [PATCH]

Replace the customer data with the one passed in the request. However, a customer must have a first name, a last name and a phone number thus, when hitting this route for the first time, valid information for all these fields must be given.

+ Request

        {
            "firstName": "Jean-Nathanaël",
            "lastName: "Hérault",
            "phoneNumber": "06 05 04 03 02"
        }

+ Response 200

    [Customer][]

+ Response 400
            
        {
            "status_code": 400,
            "error_key": "phoneNumberFormatIsInvalid",
            "message": "Cannot save customer #6.",
            "errors": {
                "phoneNumber": [
                    "The phone number format is invalid."
                 ]
             }
        }

+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 1 instead of 5."
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

    + id (required, integer, `123`) ... The customer ID

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

## List predefined addresses  [/predefined-addresses]

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
