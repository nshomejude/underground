{{-- Renders an error inside the site chrome, and falls back to the standalone document if
     anything in the chrome (site settings, navigation, session, auth) throws.
     Needs the same variables as errors.minimal, plus optional $withAccount (adds "My account" /
     "Sign in" by auth state). --}}
@php
    $data = get_defined_vars();
    unset($data['__data'], $data['obLevel'], $data['__env'], $data['app'], $data['errors'], $data['exception']);
    $actions = $data['actions'] ?? [];
    $standaloneActions = $actions;

    try {
        if (! empty($withAccount)) {
            $actions[] = auth()->check() ? ['My account', '/account'] : ['Sign in', '/login'];
        }

        $html = view('errors._chromed', array_merge($data, ['actions' => $actions]))->render();
    } catch (\Throwable) {
        $html = view('errors.minimal', array_merge($data, ['actions' => $standaloneActions]))->render();
    }
@endphp
{!! $html !!}
