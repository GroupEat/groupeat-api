# Group Orders

## Group order [/groupOrders/{id}/{?include}]

+ Parameters

    + id (required, integer, `123`) ... The group order ID
    + include (optional, string, `restaurant`) ... [restaurant]
    
+ Model

        {
            "id": 20,
            "joinable": true,
            "reduction": 0.2928,
            "createdAt": "2015-01-30 16:09:26",
            "endingAt": "2015-01-30 16:09:26"
        }

### Get group order [GET]

+ Response 200

    [Group order][]
    
+ Response 404

## List group orders [/groupOrders/{?joinable,around,latitude,longitude,include}]

### GET

+ Parameters

    + joinable (optional, boolean, `true`) ... Retrieve group orders that can currently be joined only.
    + around (optional, boolean, `true`) ... Retrieve group orders around only. Needs latitude and longitude parameters.
    + latitude (optional, float, `2.21928`) ... Client latitude.
    + longitude (optional, float, `48.711`) ... Client longitude.
    + include (optional, string, `restaurant`) ... [restaurant]
    
+ Response 200

        [
            {
                "id": 10,
                ... // Same data as the GET /group-order/{id} response
            },
            {
                "id": 12,
                ...
            }
        ]
        
## Order [/orders/{id}/{?include}]

+ Parameters

    + id (required, integer, `123`) ... The order ID
    + include (optional, string, `customer`) ... [customer, groupOrder, restaurant, deliveryAddress]
    
+ Model

        {
            "id": 8,
            "rawPrice": 46.6,
            "reducedPrice": 39.7,
            "createdAt": "2015-01-30 16:09:26",
        }

### Get order [GET]

Only the customer who created it or the corresponding restaurant can see the order. The `rawPrice` is the sum of the price of all the ordered product formats and will stay unchanged. On the other hand, the `reducedPrice` might decrease if people join the group order.

+ Response 200

    [Order][]
    
+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }
    
+ Response 404

## Get order delivery address [/orders/{id}/deliveryAddress]

### GET

+ Parameters

    + id (required, integer, `123`) ... The order ID
    
+ Response 200

        {
            "street": "Allée des techniques avancées",
            "details": "Bâtiment A, chambre 200",
            "city": "Palaiseau",
            "postcode": 91120,
            "state": "Essone",
            "country": "France",
            "latitude": 48.711,
            "longitude": 2.21928
        }

+ Response 403

        {
            "status_code": 403,
            "error_key": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

+ Response 404

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

    [Order][]

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

+ Response 422

        {
            "status_code": 422,
            "error_key": "groupOrderCannotBeJoined",
            "message": "The groupOrder #6 cannot be joined anymore."
        }

