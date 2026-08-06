<?php

declare(strict_types=1);

it('renders the selfie face guide with explicit oval dimensions', function (): void {
    $stub = file_get_contents(
        dirname(__DIR__, 2).'/stubs/resources/js/pages/form-flow/selfie/components/SelfieCapture.vue',
    );

    expect($stub)
        ->toContain('selfie-face-guide')
        ->toContain('width: min(16rem, 58vw);')
        ->toContain('height: min(20rem, 46vh);')
        ->toContain('min-height: 14rem;')
        ->not->toContain('w-64 h-80 border-4 border-white/50 rounded-full');
});
