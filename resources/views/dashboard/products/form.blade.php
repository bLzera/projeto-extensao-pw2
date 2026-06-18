@extends('layouts.app')

@section('title', $product ? 'Editar Produto' : 'Novo Produto')

@section('content')
<div class="product-form-page">
    <div class="form-card form-card--wide">
        <h1>{{ $product ? 'Editar Produto' : 'Novo Produto' }}</h1>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @method($method)

            <div class="form-row">
                <div class="form-group form-group--grow">
                    <label class="form-label" for="name">Nome do produto</label>
                    <input class="form-input" type="text" id="name" name="name"
                        value="{{ old('name', $product?->name) }}" required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Categoria</label>
                    <select class="form-input" id="category_id" name="category_id" required>
                        <option value="">Selecione...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product?->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Descrição</label>
                <textarea class="form-textarea" id="description" name="description"
                    rows="3">{{ old('description', $product?->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="price">Preço (R$)</label>
                    <input class="form-input" type="number" id="price" name="price"
                        step="0.01" min="0.01"
                        value="{{ old('price', $product?->price) }}" required>
                    @error('price')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit">Unidade</label>
                    <select class="form-input" id="unit" name="unit" required>
                        @foreach (['kg', 'g', 'unidade', 'dúzia', 'caixa', 'litro', 'mL'] as $unit)
                            <option value="{{ $unit }}"
                                {{ old('unit', $product?->unit) === $unit ? 'selected' : '' }}>
                                {{ $unit }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Foto do produto{{ $product ? ' (deixe em branco para manter a atual)' : '' }}</label>
                @if ($product?->photo)
                    <div class="product-photo-preview">
                        <img src="{{ Storage::url($product->photo) }}" alt="Foto atual do produto">
                        <span class="product-photo-preview__label">Foto atual</span>
                    </div>
                @endif
                <input class="form-input" type="file" id="photo" name="photo"
                    accept=".jpg,.jpeg,.png,.webp"{{ $product ? '' : ' required' }}>
                @error('photo')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group form-group--checkbox">
                <label class="form-checkbox-label">
                    <input type="hidden" name="is_available" value="0">
                    <input type="checkbox" name="is_available" value="1"
                        {{ old('is_available', $product?->is_available ?? true) ? 'checked' : '' }}>
                    Produto disponível para venda
                </label>
            </div>

            <div class="form-actions">
                <button class="btn btn--primary" type="submit">
                    {{ $product ? 'Salvar alterações' : 'Cadastrar produto' }}
                </button>
                <a class="btn" href="{{ route('dashboard') }}">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
