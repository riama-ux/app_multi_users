<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Vente #{{ $vente->id }}</title>
    <!-- Inclure les CSS de Bootstrap ou DashLite si vous en avez besoin pour le style de base -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css?ver=3.2.3') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css?ver=3.2.3') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f6fa;
            color: #333;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .receipt-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px; /* Largeur maximale pour un reçu */
            margin: 20px auto;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px dashed #eee;
            padding-bottom: 15px;
        }
        .header h2 {
            color: #333;
            margin-bottom: 5px;
            font-size: 1.8rem;
        }
        .header p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }
        .details-section {
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .details-section div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .details-section .label {
            font-weight: 600;
            color: #555;
        }
        .details-section .value {
            color: #333;
            text-align: right;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .product-table th, .product-table td {
            border: 1px solid #eee;
            padding: 10px;
            text-align: left;
        }
        .product-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #444;
        }
        .product-table tfoot td {
            font-weight: 600;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .product-table tfoot .total-row td {
            font-size: 1.1rem;
            font-weight: bold;
            color: #007bff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: #777;
            border-top: 2px dashed #eee;
            padding-top: 15px;
        }
        .btn-actions {
            text-align: center;
            margin-top: 20px;
        }
        .btn-actions .btn {
            margin: 0 10px;
        }

        /* Styles pour l'impression */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }
            .btn-actions {
                display: none; /* Cache les boutons lors de l'impression */
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h2>ikaStock</h2>
            <p>Reçu de Vente #{{ $vente->id }}</p>
            <p>Date: {{ $vente->date_vente->format('d/m/Y H:i') }}</p>
        </div>

        <div class="details-section">
            <div><span class="label">Client:</span> <span class="value">{{ $vente->client->nom ?? 'Client Anonyme' }}</span></div>
            @if($vente->client->telephone)
                <div><span class="label">Téléphone:</span> <span class="value">{{ $vente->client->telephone }}</span></div>
            @endif
            @if($vente->client->email)
                <div><span class="label">Email:</span> <span class="value">{{ $vente->client->email }}</span></div>
            @endif
            <div><span class="label">Vendeur:</span> <span class="value">{{ $vente->user->name ?? 'N/A' }}</span></div>
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix Unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->ligneVentes as $ligne)
                    <tr>
                        <td>{{ $ligne->produit->nom ?? 'Produit Inconnu' }}</td>
                        <td>{{ number_format($ligne->quantite, 0, ',', ' ') }}</td>
                        <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                        <td>{{ number_format($ligne->prix_total, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Total HT:</td>
                    <td>{{ number_format($vente->total_ht, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                </tr>
                @if($vente->remise > 0)
                <tr>
                    <td colspan="3" style="text-align: right;">Remise:</td>
                    <td>- {{ number_format($vente->remise, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total TTC:</td>
                    <td>{{ number_format($vente->total_ttc, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">Montant Payé:</td>
                    <td>{{ number_format($vente->montant_paye, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">Reste à Payer:</td>
                    <td>{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;">Mode de Paiement:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $vente->mode_paiement)) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Merci pour votre achat !</p>
            <p>Conservez ce reçu pour toute réclamation ou retour.</p>
        </div>
    </div>

    <div class="btn-actions">
        <button onclick="window.print()" class="btn btn-primary"><em class="icon ni ni-printer"></em> Imprimer le reçu</button>
        <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary"><em class="icon ni ni-arrow-left"></em> Retour aux ventes</a>
    </div>

    {{-- Le script d'impression automatique a été supprimé ici --}}
</body>
</html>
