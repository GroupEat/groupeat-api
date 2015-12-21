# Group Support

## Get the API config  [/config]

### GET

Returns the current public configuration of the API.

+ Response 200

        {
            "orders.minimumFoodrushInMinutes": 5,
            "orders.maximumFoodrushInMinutes": 60,
            "orders.maximumPreparationTimeInMinutes": 35
        }

## Ping the API  [/ping]

### POST

Handy route that logs and returns all the JSON data sent in the request body.

+ Request

        {
            "ping": "pong"
        }

+ Response 200

        {
            "ping": "pong"
        }
