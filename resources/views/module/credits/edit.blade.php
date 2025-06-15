@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Rembourser un crédit</h4>

        @include('flash-message')

        <form action="{{ route('module.credits.update', $credit->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Client --}}
            <div class="form-group">
                <label class="form-label">Client</label>
                <input type="text" class="form-control" value="{{ $credit->vente->client->nom ?? '-' }}" disabled>
            </div>

            {{-- Montant restant --}}
            <div class="form-group">
                <label class="form-label">Montant restant</label>
                <input type="text" class="form-control text-danger fw-bold" value="{{ number_format($credit->montant_restant) }} F" disabled>
            </div>

            {{-- Date d'échéance --}}
            <div class="form-group">
                <label class="form-label">Date d'échéance</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') }}" disabled>
            </div>

            {{-- Montant remboursé --}}
            <div class="form-group">
                <label class="form-label">Montant à rembourser</label>
                <input type="number" name="montant_rembourse" class="form-control @error('montant_rembourse') is-invalid @enderror"
                       value="{{ old('montant_rembourse') }}" required min="1" max="{{ $credit->montant_restant }}">
                @error('montant_rembourse') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Valider le remboursement</button>
                <a href="{{ route('module.credits.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
