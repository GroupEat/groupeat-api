<style>
    table {
        border-collapse:collapse;
        width:90%;
    }
    th, td {
        border:1px solid black;
        width:20%;
    }
    td {
        text-align:center;
    }
    caption {
        font-weight:bold
    }
</style>
<p>{{ $customer->fullName }} a créé une commande groupée à {{ $groupOrder->creationTime }} terminant au plus tard à {{ $groupOrder->endingTime }}.</p>

<p>Les produits suivants ont été commandés :</p>

{{ $order->htmlTable }}

<p>Le montant de cette commande est de {{ $order->rawPrice }} sans appliquer de réduction.</p>

<p>L'adresse de livraison est : {{ $deliveryAddress }}.</p>

<p>Vous pouvez joindre ce client par mail à {{ $customer->mailTo }} ou par téléphone au {{ $customer->phoneNumber }}.</p>
