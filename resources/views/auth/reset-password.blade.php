<x-guest-layout>

    <form class="auth-form" method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-form__group">
            <label class="auth-form__label" for="email">E-mail</label>
            <input class="auth-form__input" id="email" type="email" name="email"
                value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')
                <span class="auth-form__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="auth-form__group">
            <label class="auth-form__label" for="password">Nova senha</label>
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
            <button class="btn btn--primary" type="submit">Redefinir senha</button>
        </div>
    </form>

</x-guest-layout>
