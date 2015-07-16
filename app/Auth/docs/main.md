# Group Authentication

To access protected routes, the authentication token should be passed on each request through the `Authorization` header like so:

```http
Authorization: bearer {token}
```

## Token [/auth/token]

+ Model

        {
            "id": "1",
            "type": "customer",
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjoxLCJpYXQiOjE0MjA0OTU0ODYsImV4cCI6MjA1MTIxNTQ4Nn0.1vZ4fyrLfyNP5LLjRI64x8ne8C7TAtGf6DO_i6qS7Do",
            "activated": false
        }

### Retrieve token [PUT]

According to the RESTful principles, this route's method should be GET since it does not change the server state. However, passing the plain password in the URL query string is unsafe so a body is required but it is unusual to send a body along a GET request.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password",
        }

+ Response 200

    [Token][]

+ Response 401

        {
            "message": "Cannot authenticate with bad password.",
            "errors": {
                "password": {
                    "invalid": []
                }
            }
        }

+ Response 404

        {
            "message": "No user with mangeo@ensta.fr e-mail address found.",
            "errors": {
                "email": {
                    "notFound": []
                }
            }
        }

### Reset token [POST]

Once hit, this route will make the old token obsolete: only the new one can be used to authenticate.

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "password",
        }

+ Response 200

    [Token][]
    
+ Response 401

        {
            "message": "Cannot authenticate with bad password.",
            "errors": {
                "password": {
                    "invalid": []
                }
            }
        }

+ Response 404

        {
            "message": "No user with mangeo@ensta.fr e-mail address found.",
            "errors": {
                "email": {
                    "notFound": []
                }
            }
        }

## Password [/auth/password]

Changing or reseting a password will make the current authentication token obsolete. 

### Reset password [POST]

+ Request

        {
            "email": "customer@ensta.fr",
            "password": "theNewPassword",
            "token": "12a345qsd6..." // Delivered by the POST /auth/passwordResets route
        }

+ Response 200

    [Token][]

+ Response 403

        {
            "errorKey": "invalidPasswordResetToken",
            "message": "This password reset token is invalid."
        }

+ Response 404

        {
            "errorKey": "noUserForPasswordResetToken",
            "message": "No user corresponding to this password reset token."
        }
        
+ Response 422

        {
            "errorKey": "badPassword",
            "message": "The password must be at least six characters."
        }

### Change password [PUT]

+ Request

        {
            "email": "customer@ensta.fr",
            "oldPassword": "theOldPassword",
            "newPassword": "theNewPassword"
        }

+ Response 200

    [Token][]

+ Response 400
                
        {
            "errorKey": "passwordsMustBeDifferent",
            "message": "The new password cannot be the same as the old one."
        }

+ Response 401

        {
            "message": "Cannot authenticate with old password.",
            "errors": {
                "oldPassword": {
                    "invalid": []
                }
            }
        }
        
+ Response 404

        {
            "message": "No user with mangeo@ensta.fr e-mail address found.",
            "errors": {
                "email": {
                    "notFound": []
                }
            }
        }
        
+ Response 422

        {
            "errorKey": "badPassword",
            "message": "The password must be at least six characters."
        }
        
## Send password reset link [/auth/passwordResets]
    
### POST

+ Request

        {
            "email": "customer@ensta.fr",
        }

+ Response 200

+ Response 404

        {
            "message": "No user with mangeo@ensta.fr e-mail address found.",
            "errors": {
                "email": {
                    "notFound": []
                }
            }
        }

## Activate user [/auth/activationTokens]

### POST

Activate the user corresponding to the given token.

+ Request

        {
            "token": "12a345qsd6..."
        }

+ Response 200

+ Response 400

        {
            "errorKey": "missingActivationToken",
            "message": "A valid activation token should be given."
        }

+ Response 404

        {
            "errorKey": "noUserForActivationToken",
            "message": "Cannot retrieve user from activation token."
        }
