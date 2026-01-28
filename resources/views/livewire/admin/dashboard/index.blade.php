@extends("layouts.app")

@section("title", __('common.dashboard'))

@section("content")
    <div class="min-h-screen flex flex-col bg-background">
        @include("components.header", [ "showLogout" => true, ])

        <main class="container mx-auto flex-1 px-4 py-8">
            <div class="space-y-8">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ __('common.dashboard_title') }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ __('common.dashboard_description') }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white text-gray-900 shadow-sm dark:border-gray-200 dark:bg-white dark:text-gray-900">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-200">
                            <h3 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-900">
                                {{ __('common.overview') }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1 dark:text-gray-600">
                                {{ __('common.overview_description') }}
                            </p>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-600">
                                {{ __('common.overview_content') }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white text-gray-900 shadow-sm dark:border-gray-200 dark:bg-white dark:text-gray-900">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-200">
                            <h3 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-900">
                                {{ __('common.statistics') }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1 dark:text-gray-600">
                                {{ __('common.statistics_description') }}
                            </p>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-600">
                                {{ __('common.statistics_content') }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white text-gray-900 shadow-sm dark:border-gray-200 dark:bg-white dark:text-gray-900">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-200">
                            <h3 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-900">
                                {{ __('common.activity') }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1 dark:text-gray-600">
                                {{ __('common.activity_description') }}
                            </p>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 dark:text-gray-600">
                                {{ __('common.activity_content') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include("components.footer")
    </div>
@endsection
