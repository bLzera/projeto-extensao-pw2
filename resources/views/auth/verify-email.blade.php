<x-guest-layout>

    <p class="auth-form__subtitle">
        Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no
        link que enviamos para você. Se não recebeu, podemos reenviar.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-form__status">
            Um novo link de verificação foi enviado para o endereço de e-mail fornecido.
        </div>
    @endif

    <div class="auth-form__actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn--primary" type="submit">Reenviar e-mail</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="auth-form__link-button" type="submit">Sair</button>
        </form>
    </div>

</x-guest-layout>
