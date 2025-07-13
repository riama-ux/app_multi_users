@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="nk-block-title">Liste des Commandes</h3>
        <a href="{{ route('commandes.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> Nouvelle commande
        </a>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">ID</th>
                            <th scope="col" class="py-3">Fournisseur</th>
                            <th scope="col" class="py-3">Date commande</th>
                            <th scope="col" class="py-3">Statut</th>
                            <th scope="col" class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($commandes as $commande)
                            <tr>
                                <td class="ps-4">{{ $commande->id }}</td>
                                <td>{{ $commande->fournisseur->nom ?? 'N/A' }}</td>
                                <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch ($commande->statut) {
                                            case 'en attente':
                                                $badgeClass = 'badge-dim bg-warning';
                                                break;
                                            case 'validee':
                                                $badgeClass = 'bg-primary';
                                                break;
                                            case 'livree':
                                                $badgeClass = 'badge-dim bg-success';
                                                break;
                                            case 'annulee':
                                                $badgeClass = 'badge-dim bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'badge-dim bg-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($commande->statut) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('commandes.show', $commande) }}" class="btn btn-trigger btn-icon " title="Voir">
                                        <em class="icon ni ni-eye"></em>
                                    </a>
                                    
                                    @if ($commande->statut !== 'livree' && $commande->statut !== 'annulee')
                                        <a href="{{ route('commandes.edit', $commande) }}" class="btn btn-trigger btn-icon" title="Modifier">
                                            <em class="icon ni ni-edit-alt"></em>
                                        </a>
                                        
                                        <form action="{{ route('commandes.destroy', $commande) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')" class="btn btn-trigger btn-icon" title="Supprimer">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle me-2"></i> Aucune commande trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($commandes->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $commandes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection