<x-guest-layout>

    <p class="auth-form__subtitle">
        Esta é uma área protegida. Confirme sua senha antes de continuar.
    </p>

    <form class="auth-form" method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-form__group">
            <label class="auth-form__label" for="password">Senha</label>
            <input class="auth-form__input" id="password" type="password" name="password"
                required autocomplete="current-password" autofocus>
            @error('password')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__footer">
            <button class="btn btn--primary" type="submit">Confirmar</button>
        </div>
    </form>

</x-guest-layout>
