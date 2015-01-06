# Group Authentication

To authenticate against this API, you need to pass your token on each request through the Authorization header like so:

```http
Authorization: bearer {token}
```

## Generate token [/auth/token]

### PUT

Regenerate an authentication token for an already registered user. Once hit, this route will make the old token obsolete and you will have to use the new one to authenticate.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password"
        }

+ Response 200

        {
            "id": 1,
            "type": "customer",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjoxLCJpYXQiOjE0MjA0OTU0ODYsImV4cCI6MjA1MTIxNTQ4Nn0.1vZ4fyrLfyNP5LLjRI64x8ne8C7TAtGf6DO_i6qS7Do"
        }
     
+ Response 401

        {
            "status_code": 401,
            "message": "The user corresponding to these credentials has been deleted."
        }

+ Response 403

        {
            "status_code": 403,
            "message": "Bad credentials."
        }
