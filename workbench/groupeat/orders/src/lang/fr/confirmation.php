<?php

return [

    'form' => [

        'title' => "Confirmer la commande",

        'indication' => "Merci d'indiquer l'heure de début de livraison prévue :",

    ],

    'mail' => [

        'text' => "Merci de confirmer la commande et d'indiquer son temps de préparation en cliquant sur le bouton ci-dessous :",

        'button' => "Confirmer la commande",

    ],

    'maximumPreparationTimeAlreadyExceeded' => "Le temps de préparation a été dépassé. La commande a donc été annulée.",

    'invalidPreparationTime' => "Le temps de préparation ne peut pas dépasser :maximumMinutes minutes.",

    'success' => [

        'title' => "Commande confirmée !",

        'text' => "Merci d'avoir confirmé la commande groupée. Vous avez indiqué que sa livraison commencera vers :preparedAt.",
    ],

];
