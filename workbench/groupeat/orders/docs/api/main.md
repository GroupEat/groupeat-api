# Group Orders

## Place order [/orders]

### POST

Only activated customers are allowed to place an order.

The request must contain all the data required to attach a delivery address to the order.

To join an existing group order instead of creating a new one, just add the corresponding `groupOrderId` to the request.

The attributes dedicated to the order itself are `foodRushDurationInMinutes` (useless when joining a group order) which cannot not exceed {{ orders::maximum_foodrush_in_minutes }} minutes and the `productFormats` object that indicate the desired amount of each product format. All the product formats must of course belong to the same restaurant.

The restaurant must stay opened at least {{ restaurants::opening_duration_in_minutes }} minutes more to create a group order.

The maximum distance between the given address and the restaurant is {{ restaurants::around_distance_in_kilometers }} kilometers.

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
            "groupOrderId": 2
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
            "error_key": "foodRushTooLong",
            "message": "The FoodRush duration should not exceed 60 minutes, 70 given."
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
            "message": "The distance between the given delivery address and the restaurant #7 should be less than 10 kms."
        }

