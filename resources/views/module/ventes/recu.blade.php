<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de vente</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        h2 { text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Reçu de vente</h2>

    <p><strong>Date :</strong> {{ $vente->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Client :</strong> {{ $vente->client?->nom ?? '—' }}</p>
    <p><strong>Vendeur :</strong> {{ $vente->user->name }}</p>
    <p><strong>Mode de paiement :</strong> {{ ucfirst($vente->mode_paiement) }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire (FCFA)</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($vente->lignes as $ligne)
            <tr>
                <td>{{ $ligne->produit->nom }}</td>
                <td>{{ $ligne->quantite }}</td>
                <td>{{ number_format($ligne->prix_unitaire) }}</td>
                <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Remise :</strong> {{ number_format($vente->remise) }} FCFA</p>
    <p><strong>Total :</strong> {{ number_format($vente->total) }} FCFA</p>

    <p style="text-align: center; margin-top: 30px;">Merci pour votre achat !</p>
</body>
</html>
