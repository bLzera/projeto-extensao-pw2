@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="profile-edit-page">
    <div class="form-card">
        <h1>Editar Perfil</h1>

        <form method="POST" action="{{ route('producer.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label" for="farm_name">Nome da Fazenda / Produtor</label>
                <input class="form-input" type="text" id="farm_name" name="farm_name"
                    value="{{ old('farm_name', $producer->farm_name) }}" required>
                @error('farm_name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="city">Cidade</label>
                <input class="form-input" type="text" id="city" name="city"
                    value="{{ old('city', $producer->city) }}" required>
                @error('city')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Descrição</label>
                <textarea class="form-textarea" id="description" name="description"
                    rows="4">{{ old('description', $producer->description) }}</textarea>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Telefone</label>
                <input class="form-input" type="text" id="phone" name="phone"
                    value="{{ old('phone', $producer->phone) }}">
                @error('phone')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="whatsapp">WhatsApp</label>
                <input class="form-input" type="text" id="whatsapp" name="whatsapp"
                    value="{{ old('whatsapp', $producer->whatsapp) }}">
                @error('whatsapp')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="contact_email">E-mail de Contato</label>
                <input class="form-input" type="email" id="contact_email" name="contact_email"
                    value="{{ old('contact_email', $producer->contact_email) }}">
                @error('contact_email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="photo">Foto</label>
                @if ($producer->photo)
                    <img class="profile-photo-preview"
                        src="{{ Storage::url($producer->photo) }}"
                        alt="Foto atual">
                @else
                    <p class="form-error">Sem foto</p>
                @endif
                <input class="form-input" type="file" id="photo" name="photo"
                    accept=".jpg,.jpeg,.png,.webp">
                @error('photo')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button class="btn btn--primary" type="submit">Salvar Alterações</button>
        </form>
    </div>
</div>
@endsection
