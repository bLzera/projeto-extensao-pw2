<x-guest-layout>

    <p style="font-size: 0.875rem; color: #6b6b6b; margin: 0 0 1.5rem;">
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
                value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__footer">
            <span></span>
            <button class="btn btn--primary" type="submit">Enviar link</button>
        </div>
    </form>

</x-guest-layout>
