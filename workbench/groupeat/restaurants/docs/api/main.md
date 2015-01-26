# Group Restaurants

## List restaurant categories [/restaurant-categories]

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

## List restaurants [/restaurants/{?opened,around,latitude,longitude}]

### GET

Returns the list of restaurants sorted by name in alphabetical order.

+ Parameters

    + opened (optional, boolean, `true`) ... Retrieve opened restaurants only.
    + around (optional, boolean, `true`) ... Retrieve restaurants around only. Needs latitude and longitude parameters.
    + latitude (optional, float, `2.21928`) ... Client latitude.
    + longitude (optional, float, `48.711`) ... Client longitude.

+ Response 200

        [
            {
                "id": 4,
                "name": "Marchal Roussel S.A.S.",
                "categories": [
                    1
                ],
                "longitude": -132.547,
                "latitude": -62.6898
            },
            {
                "id": 5,
                "name": "Mathieu Tessier SA",
                "categories": [
                    1
                ],
                "longitude": 44.1582,
                "latitude": 13.5861
            },
            {
                "id": 6,
                "name": "Pizza Di Genova",
                "categories": [
                    1
                ],
                "longitude": 48.7171,
                "latitude": 2.23933
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

## List restaurant's products  [/restaurants/{id}/products/{?include}]

### GET

+ Parameters

    + id (required, integer, `123`) ... The restaurant ID.
    + include (optional, string, `formats`) ... Adding include=formats to the query string will include the formats of each product in the response.

+ Response 200

        [
            {
                "id": 4,
                "type_id": 1,
                "name": "napolitaine",
                "description": "Tomate, mozzarella, anchois, câpres et olives."
            },
            {
                "id": 3,
                "type_id": 1,
                "name": "classica",
                "description": "Tomate, mozzarella et origan."
            },
            {
                "id": 2,
                "type_id": 1,
                "name": "paysanne",
                "description": "Tomate, mozzarella, poitrine fumée et œuf."
            },
            {
                "id": 1,
                "type_id": 1,
                "name": "paysanne",
                "description": "Mozzarella, basilic frais et tomates."
            }
        ]

+ Response 200

        [
            {
                "id": 4,
                "type_id": 1,
                "name": "napolitaine",
                "description": "Tomate, mozzarella, anchois, câpres et olives.",
                "formats": {
                    "data": [
                        {
                            "id": 10,
                            "name": "junior",
                            "price": "7.5"
                        },
                        {
                            "id": 11,
                            "name": "sénior",
                            "price": "11.4"
                        },
                        {
                            "id": 12,
                            "name": "méga",
                            "price": "14.1"
                        }
                    ]
                }
            },
            {
                "id": 3,
                "type_id": 1,
                "name": "classica",
                "description": "Tomate, mozzarella et origan.",
                "formats": {
                    "data": [
                        {
                            "id": 7,
                            "name": "junior",
                            "price": "7.4"
                        },
                        {
                            "id": 8,
                            "name": "sénior",
                            "price": "9.7"
                        },
                        {
                            "id": 9,
                            "name": "méga",
                            "price": "12.8"
                        }
                    ]
                }
            },
            {
                "id": 2,
                "type_id": 1,
                "name": "paysanne",
                "description": "Tomate, mozzarella, poitrine fumée et œuf.",
                "formats": {
                    "data": [
                        {
                            "id": 4,
                            "name": "junior",
                            "price": "8.2"
                        },
                        {
                            "id": 5,
                            "name": "sénior",
                            "price": "9.5"
                        },
                        {
                            "id": 6,
                            "name": "méga",
                            "price": "12.3"
                        }
                    ]
                }
            },
            {
                "id": 1,
                "type_id": 1,
                "name": "paysanne",
                "description": "Mozzarella, basilic frais et tomates.",
                "formats": {
                    "data": [
                        {
                            "id": 1,
                            "name": "junior",
                            "price": "8.1"
                        },
                        {
                            "id": 2,
                            "name": "sénior",
                            "price": "9.6"
                        },
                        {
                            "id": 3,
                            "name": "méga",
                            "price": "13.7"
                        }
                    ]
                }
            }
        ]

+ Response 404

## List product's format  [/products/{id}/formats]

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
