# Group Orders

## List group orders [/group-orders/{?opened,around,latitude,longitude}]

### GET

+ Parameters

    + opened (optional, boolean, `true`) ... Retrieve group orders that can currently be joined only.
    + around (optional, boolean, `true`) ... Retrieve group orders around only. Needs latitude and longitude parameters.
    + latitude (optional, float, `2.21928`) ... Client latitude.
    + longitude (optional, float, `48.711`) ... Client longitude.
    
+ Response 200

        [
            {
                "id": 20,
                "opened": true,
                "restaurant": {
                    "id": 7,
                    "name": "Toujours ouvert",
                    "categories": [
                        1
                    ]
                },
                "createdAt": "2015-01-30 16:09:26",
                "endingAt": "2015-01-30 16:09:26"
            }
        ]

## Place order [/orders]

### POST

Only activated customers are allowed to place an order.

The request must contain all the data required to attach a delivery address to the order.

To join an existing group order instead of creating a new one, just add the corresponding `groupOrderId` to the request.

The attributes dedicated to the order itself are `foodRushDurationInMinutes` (useless when joining a group order) which must be between {{ orders::minimum_foodrush_in_minutes }} and {{ orders::maximum_foodrush_in_minutes }} minutes and the `productFormats` object that indicate the desired amount of each product format. All the product formats must of course belong to the same restaurant.

The restaurant must stay opened at least {{ restaurants::opening_duration_in_minutes }} minutes more to create a group order.

When creating a group order, the distance between the given address and the restaurant must be less than {{ restaurants::around_distance_in_kilometers }} kilometers. To join a group order, the distance between the first delivery address and the given one must be less than {{ orders::around_distance_in_kilometers }} kilometers.

+ Request

        {
            "foodRushDurationInMinutes": 30,
            "productFormats": {"1": 2, "2": 3},
            "street": "Allée des techniques avancées",
            "details": "Bâtiment A, chambre 200",
            "latitude": 48.711042,
            "longitude": 2.219278
        }

+ Response 201

        {
            "id": 8,
            "groupOrderId": 2,
            "rawPrice": 46.6
        }

+ Response 403

        {
            "status_code": 403,
            "error_key": "userShouldBeActivated",
            "message": "The customer #26 should be activated to place an order."
        }

+ Response 404

        {
            "status_code": 404,
            "error_key": "unexistingProductFormats",
            "message": "The product formats #175 do not exist."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "invalidFoodRushDuration",
            "message": "The FoodRush duration must be between 5 and 60 minutes, 70 given."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "noProductFormats",
            "message": "There must be at least one product format."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "productFormatsFromDifferentRestaurants",
            "message": "The product formats must belong to the same restaurant."
        }
        
+ Response 422

        {
            "status_code": 422,
            "error_key": "deliveryDistanceTooLong",
            "message": "The delivery distance should be less than 7 kms, 10 given."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "restaurantDeliveryCapacityExceeded",
            "message": "The restaurant #6 cannot deliver more than 10 items in the same group order, 11 items asked."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "minimumOrderPriceNotReached",
            "message": "The order price is 8.1 but must be greater than 11."
        }

+ Response 422

        {
            "status_code": 422,
            "error_key": "groupOrderAlreadyExisting",
            "message": "A group order already exists for the restaurant #6."
        }
