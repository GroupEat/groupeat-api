# Group Orders

## Place order [/orders]

### POST

Only activated customers are allowed to place an order.

The request must contain all the data required to attach a delivery address to the order.

The attributes dedicated to the order itself are `foodRushDurationInMinutes` which cannot not exceed {{ orders::maximum_foodrush_in_minutes }} minutes and the `productFormats` object that indicate the desired amount of each product format. All the product formats must of course belong to the same restaurant.

The restaurant must stay opened at least {{ restaurants::opening_duration_in_minutes }} minutes more to place an order.

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

+ Response 403

        {
            "status_code": 403,
            "message": "The customer #26 should be activated to place an order."
        }

