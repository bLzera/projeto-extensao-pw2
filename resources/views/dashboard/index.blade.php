@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <div class="dashboard-header">
        <h1>Olá, {{ $producer->farm_name }}!</h1>
    </div>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert--error">{{ session('error') }}</div>
    @endif

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

    <div class="products-table-section">
        <h2>Meus produtos</h2>

        @if ($products->isEmpty())
            <div class="empty-state">
                <span class="empty-state__icon">📦</span>
                <p class="empty-state__title">Nenhum produto cadastrado ainda</p>
                <p class="empty-state__desc"><a href="{{ route('producer.products.create') }}">Adicione seu primeiro produto</a> e comece a vender.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th>Destaque</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    @if ($product->photo)
                                        <img class="product-thumb"
                                            src="{{ $product->photo_url }}"
                                            alt="{{ $product->name }}">
                                    @else
                                        <div class="product-thumb product-thumb--empty"></div>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>
                                    <span class="badge {{ $product->is_available ? 'badge--success' : 'badge--muted' }}">
                                        {{ $product->is_available ? 'Disponível' : 'Indisponível' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('producer.products.toggleFeatured', $product) }}">
                                        @csrf
                                        @method('PATCH')
                                        @if ($product->is_featured)
                                            <button class="btn btn--sm btn--featured" type="submit" title="Remover destaque">
                                                ⭐ Em destaque
                                            </button>
                                        @else
                                            <button class="btn btn--sm btn--outline" type="submit" title="Destacar produto">
                                                Destacar
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    <div class="product-actions">
                                        <a class="btn btn--sm" href="{{ route('producer.products.edit', $product) }}">Editar</a>

                                        <form method="POST" action="{{ route('producer.products.toggle', $product) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn--sm btn--outline" type="submit">
                                                {{ $product->is_available ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('producer.products.destroy', $product) }}"
                                            onsubmit="return confirm('Deseja excluir este produto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn--sm btn--danger" type="submit">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
