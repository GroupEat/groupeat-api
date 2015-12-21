# Group Orders

## Order [/orders/{id}/{?include}]

+ Parameters

    + id (required, string, `123`) ... The order ID
    + include (optional, string, `customer`) ... [customer, groupOrder, deliveryAddress, productFormats]

+ Model

        {
            "id": "8",
            "rawPrice": 4660,
            "discountedPrice": 3970,
            "createdAt": "2015-01-30 16:09:26",
            "comment": "Please add some meat to my vegan pizza..." // optional
        }

### Get order [GET]

Only the customer who created it or the corresponding restaurant can see the order. The `rawPrice` is the sum of the price of all the ordered product formats and will stay unchanged. On the other hand, the `discountedPrice` might decrease if people join the group order.

+ Response 200

    [Order][]

+ Response 403

        {
            "errorKey": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

+ Response 404

## Get order delivery address [/orders/{id}/deliveryAddress]

### GET

+ Parameters

    + id (required, string, `123`) ... The order ID

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
            "errorKey": "wrongAuthenticatedUser",
            "message": "Should be authenticated as customer 5 instead of 6."
        }

+ Response 404

## Create group order [/orders]

### POST

Only activated customers are allowed to create a group order.

The request must contain all the data required to attach a delivery address to the order.

The attributes dedicated to the order itself are `foodRushDurationInMinutes` which must be between {{ orders.minimum_foodrush_in_minutes }} and {{ orders.maximum_foodrush_in_minutes }} minutes and the `productFormats` object that indicate the desired quantity of each product format. All the product formats must of course belong to the same restaurant.

To create a group order, the restaurant must stay opened long enough to let the minimum foodrush time ({{ orders.minimum_foodrush_in_minutes }} minutes) and the maximum preparation time ({{ restaurants.maximum_preparation_time_in_minutes }} minutes) flow.

The distance between the given address and the restaurant must be less than {{ restaurants.around_distance_in_kilometers }} kilometers.

+ Request

        {
            "foodRushDurationInMinutes": 30,
            "productFormats": {"1": 2, "2": 3},
            "deliveryAddress": {
                "street": "Allée des techniques avancées",
                "details": "Bâtiment A, chambre 200",
                "latitude": 48.711042,
                "longitude": 2.219278,
            },
            "comment": "Please add some meat to my vegan pizza..." // optional, limited to 1000 characters
        }

+ Response 201

    [Order][]

+ Response 400

        {
            "errorKey": "missingProductFormats",
            "message": "The product formats object is required to place an order"
        }

+ Response 400

        {
            "errorKey": "missingDeliveryAddress",
            "message": "The delivery address object is required to place an order"
        }

+ Response 403

        {
            "errorKey": "userShouldBeActivated",
            "message": "The customer #26 should be activated to place an order."
        }

+ Response 403

         {
             "errorKey": "missingCustomerInformation",
             "message": "The attributes [firstName, lastName, phoneNumber] are missing for customer customer #35"
         }

+ Response 404

        {
            "errorKey": "unexistingProductFormats",
            "message": "The product formats #175 do not exist."
        }

+ Response 422

        {
            "errorKey": "invalidFoodRushDuration",
            "message": "The FoodRush duration must be between 5 and 60 minutes, 70 given."
        }

+ Response 422

        {
            "errorKey": "noProductFormats",
            "message": "There must be at least one product format."
        }

+ Response 422

        {
            "errorKey": "productFormatsFromDifferentRestaurants",
            "message": "The product formats must belong to the same restaurant."
        }

+ Response 422

        {
            "errorKey": "deliveryDistanceTooLong",
            "message": "The delivery distance should be less than 7 kms, 10 given."
        }

+ Response 422

        {
            "errorKey": "restaurantDeliveryCapacityExceeded",
            "message": "The restaurant #6 cannot deliver more than 10 items in the same group order, 11 items asked."
        }

+ Response 422

        {
            "errorKey": "minimumGroupOrderPriceNotReached",
            "message": "The order price is 810 but must be greater than 1100."
        }

+ Response 422

        {
            "errorKey": "groupOrderAlreadyExisting",
            "message": "A group order already exists for the restaurant #6."
        }

## List customer's orders  [/customers/{id}/orders/{?include}]

### GET

+ Parameters

    + id (required, string, `123`) ... The customer ID
    + include (optional, string, `groupOrder`) ... [groupOrder, restaurant, deliveryAddress]

+ Response 200

        [
            {
                "id": "10",
                ... // Same data as the GET /orders/{id} response
            },
            {
                "id": "12",
                ...
            }
        ]

## List customer's orders in specific group order  [/customers/{customerId}/groupOrders/{groupOrderId}/orders/{?include}]

### GET

+ Parameters

    + customerId (required, string, `123`) ... The customer ID
    + groupOrderId (required, string, `123`) ... The group order ID
    + include (optional, string, `restaurant`) ... [restaurant, deliveryAddress]

+ Response 200

        [
            {
                "id": "10",
                ... // Same data as the GET /orders/{id} response
            },
            {
                "id": "12",
                ...
            }
        ]

## Group order [/groupOrders/{id}/{?include}]

+ Parameters

    + id (required, string, `123`) ... The group order ID.
    + include (optional, string, `restaurant`) ... [restaurant].

+ Model

        {
            "id": "20",
            "joinable": false,
            "totalRawPrice": 2966,
            "discountRate": 28, // Percentage
            "createdAt": "2015-01-30 16:09:26",
            "remainingCapacity": 5, // The number of product formats that can still be added
            "closedAt": "2015-01-30 16:19:26",
            "endingAt": "2015-01-30 16:39:26",
            "confirmed": true, // The restaurant must confirm the ended group order to attest that he will deliver it
            "preparedAt": "2015-01-30 17:09:26" // Indicates approximately the beginning of the delivery round
        }

### Get group order [GET]

+ Response 200

    [Group order][]

+ Response 404

## List group orders [/groupOrders/{?joinable,around,latitude,longitude,include}]

### GET

+ Parameters

    + joinable (optional, boolean, `1`) ... Retrieve group orders that can currently be joined only.
    + around (optional, boolean, `1`) ... Retrieve group orders around only. Needs latitude and longitude parameters.
    + latitude (optional, float, `2.21928`) ... Client latitude.
    + longitude (optional, float, `48.711`) ... Client longitude.
    + include (optional, string, `restaurant`) ... [restaurant]

+ Response 200

        [
            {
                "id": "10",
                ... // Same data as the GET /groupOrders/{id} response
            },
            {
                "id": "12",
                ...
            }
        ]

## Join group order [/groupOrders/{id}/orders]

+ Parameters

    + id (required, string, `123`) ... The group order ID.

### POST

Only activated customers are allowed to join a group order.

Same request than the *Create group order* route except that the `foodRushDurationInMinutes` field is not needed. The possible error responses are also the same except that the `invalidFoodRushDuration` and `groupOrderAlreadyExisting` errors cannot occur.

To join a group order, the distance between the first delivery address and the given one must be less than {{ orders.joinable_distance_in_kilometers }} kilometers.

+ Response 201

    [Order][]

+ Response 422

        {
            "errorKey": "groupOrderCannotBeJoined",
            "message": "The groupOrder #6 cannot be joined anymore."
        }

## Push external order [/restaurants/{id}/externalOrders]

+ Parameters

    + id (required, string, `123`) ... The restaurant ID.

### POST

Only restaurants are allowed to push an external order.

The possible error responses are the same than the *Create group order* route except that the `userShouldBeActivated`, `invalidFoodRushDuration` and `deliveryDistanceTooLong` errors cannot occur.

The foodrush duration is automatically set to {{ orders.external_order_foodrush_in_minutes }} minutes.

+ Request

        {
            "productFormats": {"1": 2, "2": 3},
            "deliveryAddress": {
                "street": "Allée des techniques avancées",
                "details": "Bâtiment A, chambre 200",
                "latitude": 48.711042,
                "longitude": 2.219278,
            },
            "customer": {
                "firstName": "Jean",
                "lastName": "Michel",
                "phoneNumber": "0605040302"
            },
            "comment": "Please add some meat to my vegan pizza..." // optional, limited to 1000 characters
        }

+ Response 201

    [Order][]

+ Response 403

        {
            "errorKey": "wrongAuthenticatedUser",
            "message":"Should be authenticated as restaurant 9 instead of 10."
        }
