@extends("layouts.app")

@section("title", __('common.welcome'))

@section("content")
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950">
        @include("components.header", [ "showLogout" => false, ])

        <main class="flex flex-1 items-center justify-center px-4 py-16">
            <div class="max-w-2xl space-y-8 text-center text-gray-900 dark:text-gray-100">
                <div class="space-y-4">
                    <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                        {{ __('common.title') }}<br>
                        <span class="text-primary">{{ __('common.subtitle') }}</span>
                    </h2>

                    <p class="text-xl text-gray-600 dark:text-gray-300">
                        {{ __('common.description') }}
                    </p>
                </div>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-1 text-base font-medium text-white shadow hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-400">
                        {{ __('common.get_started') }}
                    </a>
                    <a href="{{ url('/api/docs') }}"
                       class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-1 text-base font-medium text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800">
                        {{ __('common.view_docs') }}
                    </a>
                </div>
            </div>
        </main>

        @include("components.footer")
    </div>
@endsection
