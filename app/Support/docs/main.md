# Group Support

## Get the API config  [/config]

### GET

Returns the current public configuration of the API.

+ Response 200

        {
            "orders.minimumFoodrushInMinutes": {{ orders.minimum_foodrush_in_minutes }},
            "orders.maximumFoodrushInMinutes": {{ orders.maximum_foodrush_in_minutes }},
            "orders.maximumPreparationTimeInMinutes": {{ orders.maximum_preparation_time_in_minutes }},
            "orders.maximumOrderFlowInMinutes": {{ orders.maximum_order_flow_in_minutes }}
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
