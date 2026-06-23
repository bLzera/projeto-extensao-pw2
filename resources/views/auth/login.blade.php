<x-guest-layout>

    @if (session('status'))
        <div class="auth-form__status">{{ session('status') }}</div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-form__group">
            <label class="auth-form__label" for="email">E-mail</label>
            <input class="auth-form__input" id="email" type="email" name="email"
                value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__group">
            <label class="auth-form__label" for="password">Senha</label>
            <input class="auth-form__input" id="password" type="password" name="password"
                required autocomplete="current-password">
            @error('password')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__checkbox-row">
            <div class="container__remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">Lembrar de mim</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
            @endif
        </div>

        <div class="auth-form__footer">
            <button class="btn btn--primary" type="submit">Entrar</button>
        </div>
    </form>

    <p class="auth-form__alt-link">
        Ainda não tem conta? <a href="{{ route('register') }}">Cadastre-se agora</a>
    </p>

</x-guest-layout>
