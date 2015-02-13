<?php

return [

    'orderedProducts' => "Sa commande :orderRef contient les produits suivants :",

    'orderRawPrice' => "Le montant de cette commande est de :rawPrice sans appliquer de réduction.",

    'deliveryAddress' => "L'adresse de livraison est :",

    'customerCanBeReached' => "Vous pouvez joindre ce client par téléphone au :phoneNumber.",

    'created' => [

        'subject' => "Nouvelle commande GroupEat",

        'whoAndWhen' => ":customerFullName a créé une commande groupée :groupOrderRef à :creationTime terminant au plus tard à :endingTime.",

    ],

    'joined' => [

        'subject' => "Un utilisateur a rejoint une commande GroupEat",

        'whoAndWhen' => ":customerFullName a rejoint à :creationTime la commande groupée :groupOrderRef terminant au plus tard à :endingTime.",

    ],

    'ended' => [

        'subject' => "Une commande GroupEat vient de se finir",

        'indication' => "La commande groupée :groupOrderRef commencée à :creationTime est complète.",

        'discountAndPrice' => "La réduction finale est de :discountRate% ce qui donne un prix total de :totalDiscountedPrice.",

        'composedOf' => "Elle comporte une unique commande :|Elle comporte les :count commandes suivantes :",

    ]

];
