# Group Customers

## Register a customer [/customers]

### POST

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password"
        }
            
+ Response 201
 
         {
             "id": 1,
             "type": "customer",
             "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjoxLCJpYXQiOjE0MjA0OTU0ODYsImV4cCI6MjA1MTIxNTQ4Nn0.1vZ4fyrLfyNP5LLjRI64x8ne8C7TAtGf6DO_i6qS7Do"
         }
         
+ Response 400

        {
             "message": "Invalid credentials.",
             "status_code": 400,
             "errors": {
                 "email": [
                     "The email must be a valid email address."
                 ],
                 "password": [
                     "The password must be at least 6 characters."
                 ]
             }
         }
         
+ Response 403

         {
             "message": "Email should correspond to a Saclay campus account.",
             "status_code": 403
         }
         
## Customer [/customers/{id}]

+ Parameters

    + id (required, string, `123`) ... The customer ID

+ Model

        {
            "id": 1,
            "firstName": Jean,
            "lastName": Aymard,
            "created_at": "2015-01-06 09:22:31",
            "updated_at": "2015-01-06 10:41:25",
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

### Unregister customer [DELETE]

+ Response 200

+ Response 401

            {
                "message": "Should be authenticated as customer 1 instead of 5.",
                "status_code": 401
            }
            
+ Response 404
