@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-dark mb-0">Liste des Transferts</h2>
        <a href="{{ route('transferts.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> Nouveau Transfert
        </a>
    </div>


    <!-- Carte pour le tableau des transferts -->
    <div class="card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">Date</th>
                            <th scope="col" class="py-3">Magasin Source</th>
                            <th scope="col" class="py-3">Magasin Destination</th>
                            <th scope="col" class="py-3">Statut</th>
                            <th scope="col" class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transferts as $transfert)
                            <tr>
                                <td class="ps-4">{{ $transfert->date_transfert->format('d/m/Y H:i') }}</td>
                                <td>{{ $transfert->magasinSource->nom }}</td>
                                <td>{{ $transfert->magasinDestination->nom }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch ($transfert->statut) {
                                            case 'attente':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                            case 'valide':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'annule':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($transfert->statut) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('transferts.show', $transfert) }}" class="btn btn-info btn-sm text-white" title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_source_id)
                                        <a href="{{ route('transferts.edit', $transfert) }}" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('transferts.destroy', $transfert) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce transfert ?')" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_destination_id)
                                        <form action="{{ route('transferts.valider', $transfert) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir valider ce transfert ?')" title="Valider">
                                                <i class="bi bi-check-circle"></i> Valider
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle me-2"></i> Aucun transfert trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if ($transferts->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $transferts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection