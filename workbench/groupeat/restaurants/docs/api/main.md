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
    
