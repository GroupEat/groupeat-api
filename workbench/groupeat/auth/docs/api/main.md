# Group Authentication

To authenticate against this API, you need to pass your token on each request through the Authorization header like so:

```http
Authorization: bearer {token}
```

## Token [/auth/token]

+ Model

        {
            "id": 1,
            "type": "customer",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjoxLCJpYXQiOjE0MjA0OTU0ODYsImV4cCI6MjA1MTIxNTQ4Nn0.1vZ4fyrLfyNP5LLjRI64x8ne8C7TAtGf6DO_i6qS7Do",
            "activated": false
        }

### Retrieve token [PUT]

Retrieve the authentication token of an already registered user. This route should be a GET but for security reasons (the plain password should not appear in a URL) it is a PUT.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password",
        }

+ Response 200

    [Token][]

+ Response 401

        {
            "status_code": 401,
            "message": "Bad credentials."
        }
        
### Generate token [POST]

Regenerate an authentication token for an already registered user. Once hit, this route will make the old token obsolete and you will have to use the new one to authenticate.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password",
        }
        
+ Response 200

    [Token][]
   
+ Response 401

        {
            "status_code": 401,
            "message": "Bad credentials."
        }
        
## Send password reset link [/auth/reset-password]

### POST

Send a password reset link to the given e-mail address and revoke the previous authentication token. Once the link has been clicked, the user will have to fill a form to reset its password. The new token will then have to be asked.

+ Request

        {
            "email": "customer@ensta.fr",
        }

+ Response 200

+ Response 404 

        {
            "status_code": 404,
            "message": "No user with this e-mail address found."
        }