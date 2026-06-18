<x-guest-layout>

    <p style="font-size: var(--fs-sm, 0.875rem); color: #6b6b6b; margin: 0 0 1rem;">
        Obrigado por se cadastrar! Antes de começar, verifique seu e-mail clicando no link que enviamos para você.
        Se não recebeu, podemos reenviar.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-form__status">
            Um novo link de verificação foi enviado para o endereço de e-mail fornecido.
        </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; gap: 1rem;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn--primary" type="submit">Reenviar e-mail</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 0.875rem; color: #6b6b6b; font-family: inherit;">
                Sair
            </button>
        </form>
    </div>

</x-guest-layout>
