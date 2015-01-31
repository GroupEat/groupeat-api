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
                "opened": true,
                "name": "Pizza Di Genova",
                "phoneNumber": "0605040302",
                "minimumOrderPrice": 10,
                "deliveryCapacity": 7,
                "reductionPrices": "[9, 10, 20, 25, 35, 60]"
            },
            {
                "id": 7,
                "opened": true,
                "name": "Rapid Pizza",
                "phoneNumber": "0605040301",
                "minimumOrderPrice": 7,
                "deliveryCapacity": 6,
                "reductionPrices": "[9, 15, 20, 25, 40, 70]"
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
                "price": "7.2"
            },
            {
                "id": 2,
                "name": "sénior",
                "price": "9.7"
            },
            {
                "id": 3,
                "name": "méga",
                "price": "13.8"
            }
        ]

+ Response 404
