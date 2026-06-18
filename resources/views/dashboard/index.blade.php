@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <div class="dashboard-header">
        <h1>Olá, {{ $producer->farm_name }}!</h1>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-card__number">{{ $totalProducts }}</div>
            <div class="stat-card__label">Produtos cadastrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__number">{{ $availableProducts }}</div>
            <div class="stat-card__label">Disponíveis</div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a class="btn btn--primary" href="{{ route('producer.products.create') }}">Adicionar produto</a>
        <a class="btn" href="{{ route('producer.profile.edit') }}">Editar perfil</a>
    </div>
</div>
@endsection
