<x-guest-layout>

    <p class="auth-form__subtitle">
        Esqueceu a senha? Informe seu e-mail e enviaremos um link para redefinição.
    </p>

    @if (session('status'))
        <div class="auth-form__status">{{ session('status') }}</div>
    @endif

    <form class="auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-form__group">
            <label class="auth-form__label" for="email">E-mail</label>
            <input class="auth-form__input" id="email" type="email" name="email"
                value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__footer">
            <button class="btn btn--primary" type="submit">Enviar link</button>
        </div>
    </form>

    <p class="auth-form__alt-link">
        Lembrou a senha? <a href="{{ route('login') }}">Voltar para o login</a>
    </p>

</x-guest-layout>
