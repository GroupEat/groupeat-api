# Group Customers

## Register a customer [/customers]

### POST

It is important to send the user locale in addition to its email and password. In fact, even if the API will always use the English language, we need to know the language of the user in order to send him e-mails he can understand.

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
             "message": "Invalid credentials.",
             "status_code": 400,
             "errors": {
                 "email": [
                     "The e-mail must be a valid e-mail address."
                 ],
                 "password": [
                     "The password must be at least 6 characters."
                 ]
             }
         }
         
+ Response 403

         {
             "message": "E-mail should correspond to a Saclay campus account.",
             "status_code": 403
         }
         
## Customer [/customers/{id}]

+ Parameters

    + id (required, string, `123`) ... The customer ID

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
            "message": "Should be authenticated as customer 1 instead of 5.",
            "status_code": 401
        }

+ Response 404

### Update customer [PATCH]

Replace the customer data with the one passed in the request. However, a customer must have a first name, a last name and a phone number. That means that, when you hit this route for the first time, all of these fields must be given.

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
            "message": "Cannot update customer data.",
            "status_code": 400,
            "errors": {
                "phoneNumber": [
                    "The phone number format is invalid."
                 ]
             }
        }
            
+ Response 401
            
        {
            "message": "Should be authenticated as customer 1 instead of 5.",
            "status_code": 401
        }
            
+ Response 404

### Unregister customer [DELETE]

+ Response 200

+ Response 401

        {
            "message": "Should be authenticated as customer 1 instead of 5.",
            "status_code": 401
        }
            
+ Response 404
