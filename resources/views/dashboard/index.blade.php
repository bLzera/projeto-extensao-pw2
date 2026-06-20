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

    <div class="ratings-panel">
        <div class="ratings-panel__header">
            <h2>Avaliações recebidas</h2>
            <div class="ratings-panel__stats">
                <span><strong>{{ $activeRatingsCount }}</strong> {{ $activeRatingsCount === 1 ? 'avaliação' : 'avaliações' }}</span>
                @if ($averageRating !== null)
                    <span>Média <strong>{{ number_format($averageRating, 1) }}</strong></span>
                @endif
                <span><strong>{{ $hiddenRatingsCount }}</strong> {{ $hiddenRatingsCount === 1 ? 'oculta' : 'ocultas' }}</span>
            </div>
        </div>

        @if ($ratings->isEmpty())
            <div class="empty-state">
                <span class="empty-state__icon">⭐</span>
                <p class="empty-state__title">Você ainda não recebeu avaliações</p>
                <p class="empty-state__desc">Quando compradores avaliarem sua loja, elas aparecem aqui.</p>
            </div>
        @else
            <div class="table-wrapper">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Comprador</th>
                            <th>Nota</th>
                            <th>Comentário</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ratings as $rating)
                            <tr>
                                <td>{{ $rating->buyer->name ?? 'Comprador' }}</td>
                                <td>
                                    <span class="star-display">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="star-display__star {{ $i <= $rating->stars ? 'star-display__star--filled' : '' }}">★</span>
                                        @endfor
                                    </span>
                                    @if ($rating->edited_at)
                                        <span class="rating-card__edited">editada</span>
                                    @endif
                                </td>
                                <td class="ratings-panel__comment">{{ $rating->comment ?: '—' }}</td>
                                <td>
                                    <span class="badge {{ $rating->hidden ? 'badge--muted' : 'badge--success' }}">
                                        {{ $rating->hidden ? 'Oculta' : 'Visível' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('dashboard.ratings.toggle', $rating) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn--sm btn--outline" type="submit">
                                            {{ $rating->hidden ? 'Exibir' : 'Ocultar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
