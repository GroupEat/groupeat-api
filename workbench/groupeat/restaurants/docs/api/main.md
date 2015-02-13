# Group Restaurants

## List restaurant categories [/restaurantCategories]

### GET

+ Response 200

        [
            {
                "id": 1,
                "label": "pizzeria"
            },
            {
                "id": 2,
                "label": "japanese"
            },
            {
                "id": 3,
                "label": "chinese"
            }
        ]

## Restaurant [/restaurants/{id}/{?include}]

+ Parameters

    + id (required, integer, `123`) ... The restaurant ID.
    + include (optional, string, `address`) ... [address, categories].
    
+ Model

        {
            "id": 1,
            "opened": false,
            "name": "Pizza di Genova",
            "phoneNumber": "0689731323",
            "minimumOrderPrice": 1088,
            "deliveryCapacity": 7,
            "discountPolicy": { // The key is the price and the value is the corresponding discount rate
                "900": 0,
                "1000": 10,
                "2000": 20,
                "2500": 30,
                "3500": 40,
                "6000": 50
            }
        }

### Get restaurant [GET]

+ Response 200

    [Restaurant][]
    
+ Response 404

## List restaurants [/restaurants/{?opened,around,latitude,longitude,include}]

### GET

Returns the list of restaurants sorted by name in alphabetical order.

+ Parameters

    + opened (optional, boolean, `true`) ... Retrieve opened restaurants only.
    + around (optional, boolean, `true`) ... Retrieve restaurants around only. Needs latitude and longitude parameters.
    + latitude (optional, float, `2.21928`) ... Client latitude.
    + longitude (optional, float, `48.711`) ... Client longitude.
    + include (optional, string, `address`) ... [address, categories].

+ Response 200

        [
            {
                "id": 6,
                ... // Same data as the GET /restaurants/{id} response
            },
            {
                "id": 7,
                ...
            }
        ]

## List food types [/food-types]

### GET

+ Response 200

        [
            {
                "id": 1,
                "label": "pizza"
            },
            {
                "id": 2,
                "label": "kebab"
            },
            {
                "id": 3,
                "label": "salad"
            }
        ]

## Get restaurant address  [/restaurants/{id}/address]

### GET

+ Parameters

    + id (required, integer, `123`) ... The restaurant ID.
    
+ Response 200

        {
            "street": "86 Rue Maurice Berteaux",
            "details": null,
            "city": "Palaiseau",
            "postcode": 91120,
            "state": "Essonne",
            "country": "France",
            "latitude": 48.7171,
            "longitude": 2.23933
        }

+ Response 404

## List restaurant products  [/restaurants/{id}/products/{?include}]

### GET

+ Parameters

    + id (required, integer, `123`) ... The restaurant ID.
    + include (optional, string, `formats`) ... [formats].

+ Response 200

        [
            {
                "id": 4,
                "typeId": 1,
                "name": "napolitaine",
                "description": "Tomate, mozzarella, anchois, câpres et olives."
            },
            {
                "id": 3,
                "typeId": 1,
                "name": "classica",
                "description": "Tomate, mozzarella et origan."
            },
            {
                "id": 2,
                "typeId": 1,
                "name": "paysanne",
                "description": "Tomate, mozzarella, poitrine fumée et œuf."
            },
            {
                "id": 1,
                "typeId": 1,
                "name": "paysanne",
                "description": "Mozzarella, basilic frais et tomates."
            }
        ]

+ Response 404

## List product formats  [/products/{id}/formats]

### GET

+ Parameters

    + id (required, integer, `123`) ... The product ID.

+ Response 200

        [
            {
                "id": 1,
                "name": "junior",
                "price": 7200
            },
            {
                "id": 2,
                "name": "sénior",
                "price": 9700
            },
            {
                "id": 3,
                "name": "méga",
                "price": 1380
            }
        ]

+ Response 404
