@extends('layouts.app')

@php
    $tenantDisplayName = \Illuminate\Support\Str::headline(
        (string) (tenant('title') ?: tenant('slug') ?: tenant('id')),
    );
    $eventDescription = trim((string) (tenant('description') ?? ''));
@endphp

@section('title', __('common.landing_tenant_page_title'))

@section('content')
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950">
        @include('components.header')

        <main
            class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-6 py-16 sm:py-24">
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,color-mix(in_srgb,var(--tenant-primary)_20%,transparent),transparent)] dark:bg-[radial-gradient(ellipse_85%_55%_at_50%_-15%,color-mix(in_srgb,var(--tenant-primary)_32%,transparent),transparent)]">
            </div>
            <div
                class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-gray-700">
            </div>

            <div class="relative z-10 mx-auto w-full max-w-2xl text-center">
                <p
                    class="text-xs font-semibold uppercase tracking-[0.22em] text-gray-500 dark:text-gray-400 sm:text-sm">
                    {{ __('common.landing_tenant_headline') }}
                </p>

                <h1
                    class="mt-4 text-4xl font-extrabold leading-[1.08] tracking-tight text-gray-900 dark:text-white sm:text-5xl sm:mt-5 md:text-6xl">
                    <span
                        class="bg-gradient-to-br from-[var(--tenant-primary)] via-[color-mix(in_srgb,var(--tenant-primary)_88%,#0f172a_12%)] to-[color-mix(in_srgb,var(--tenant-primary)_55%,#334155)] bg-clip-text text-transparent dark:from-[color-mix(in_srgb,var(--tenant-primary)_95%,#f8fafc)] dark:via-[var(--tenant-primary)] dark:to-[color-mix(in_srgb,var(--tenant-primary)_70%,#94a3b8)]">
                        {{ $tenantDisplayName }}
                    </span>
                </h1>

                <div
                    class="mx-auto mt-8 h-1 w-12 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_65%,transparent)] sm:mt-10 sm:w-14">
                </div>

                @if ($eventDescription !== '')
                    <p
                        class="mx-auto max-w-lg whitespace-pre-line text-base leading-relaxed text-gray-600 dark:text-gray-300 sm:text-lg sm:leading-relaxed">
                        {{ $eventDescription }}
                    </p>
                @endif
            </div>
        </main>

        @include('components.footer')
    </div>
@endsection
