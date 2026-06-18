@extends('layouts.app')

@section('title', 'Configurar Perfil')

@section('content')
<div class="setup-page">
    <div class="form-card">
        <h1>Configurar Perfil</h1>

        <form method="POST" action="{{ route('producer.setup.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label" for="farm_name">Nome da Fazenda / Produtor</label>
                <input class="form-input" type="text" id="farm_name" name="farm_name"
                    value="{{ old('farm_name') }}" required>
                @error('farm_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="city">Cidade</label>
                <input class="form-input" type="text" id="city" name="city"
                    value="{{ old('city') }}" required>
                @error('city')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Descrição</label>
                <textarea class="form-textarea" id="description" name="description"
                    rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Telefone</label>
                <input class="form-input" type="text" id="phone" name="phone"
                    value="{{ old('phone') }}">
                @error('phone')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="whatsapp">WhatsApp</label>
                <input class="form-input" type="text" id="whatsapp" name="whatsapp"
                    value="{{ old('whatsapp') }}">
                @error('whatsapp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_email">E-mail de Contato</label>
                <input class="form-input" type="email" id="contact_email" name="contact_email"
                    value="{{ old('contact_email') }}">
                @error('contact_email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Foto</label>
                <input class="form-input" type="file" id="photo" name="photo"
                    accept=".jpg,.jpeg,.png,.webp">
                @error('photo')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button class="btn btn--primary" type="submit">Salvar Perfil</button>
        </form>
    </div>
</div>
@endsection
