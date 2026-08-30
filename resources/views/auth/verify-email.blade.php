<x-layout title="Verify Email">
    <section class="mx-auto flex max-w-md flex-col gap-10 px-4 py-16 sm:px-6 lg:py-24">
        <x-section-heading eyebrow="Member Account">
            Verify Your Email
        </x-section-heading>

        <p class="text-sm leading-relaxed text-body">
            Thanks for registering. Before continuing, please confirm your email address by
            clicking the link we sent you. If you did not receive the email, you can request
            another one below.
        </p>

        @if (session('status') === 'verification-link-sent')
            <p class="flex items-center gap-2 border border-success/40 bg-success/10 px-4 py-3 text-sm text-success" role="status">
                <x-icon name="check-circle" class="h-4 w-4 shrink-0" />
                A new verification link has been sent to the email address on your account.
            </p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-button variant="primary" type="submit">
                Resend Verification Email
                <x-icon name="rotate-cw" class="h-3.5 w-3.5" />
            </x-button>
        </form>
    </section>
</x-layout>
