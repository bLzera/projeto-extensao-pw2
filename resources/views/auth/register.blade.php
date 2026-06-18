<x-guest-layout>

    <form class="auth-form" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form__group">
            <label class="auth-form__label" for="name">Nome</label>
            <input class="auth-form__input" id="name" type="text" name="name"
                value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__group">
            <label class="auth-form__label" for="email">E-mail</label>
            <input class="auth-form__input" id="email" type="email" name="email"
                value="{{ old('email') }}" required autocomplete="username">
            @error('email')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__group">
            <label class="auth-form__label" for="password">Senha</label>
            <input class="auth-form__input" id="password" type="password" name="password"
                required autocomplete="new-password">
            @error('password')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__group">
            <label class="auth-form__label" for="password_confirmation">Confirmar senha</label>
            <input class="auth-form__input" id="password_confirmation" type="password"
                name="password_confirmation" required autocomplete="new-password">
            @error('password_confirmation')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__footer">
            <a href="{{ route('login') }}">Já tem conta?</a>
            <button class="btn btn--primary" type="submit">Cadastrar</button>
        </div>
    </form>

</x-guest-layout>
