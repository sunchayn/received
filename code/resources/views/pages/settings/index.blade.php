@extends('layouts.base')

@section('title') Settings @endsection

@section('content')
    <div class="bg-gray-100 min-h-screen">
        @component('components.navbar.user')@endcomponent
        <div class="container max-w-4xl my-5">
            <settings
                class="flex mb-5"

                :routes="{
                    profile: {
                        username: '{{route('settings.username')}}',
                        submit:  '{{route('settings.profile')}}',
                    },

                    account: {
                        change_phone: '{{route('settings.change_phone')}}',
                        submit:  '{{route('settings.password')}}',
                    },

                    notifications: {
                        submit:  '{{route('settings.notifications')}}',
                    },

                    sharedFolders: {
                        revoke:  '{{route('folders.revoke', ['folder' => '__id'])}}',
                        changePassword:  '{{route('folders.changePassword', ['folder' => '__id'])}}',
                        bucket: '{{route('home')}}',
                    },
                }"

                :data="{
                    profile: {
                        username: '{{Auth::user()->username}}',
                        url: '{{str_replace('/__r', '', route('send.index', ['username' => '__r']))}}',
                        fill: {{json_encode(['name' => Auth::user()->name, 'email' => Auth::user()->email])}},
                    },

                    account: {
                        phone: '{{Auth::user()->phone_number}}',
                        country_code: '{{Auth::user()->country_code}}',
                    },

                    notifications: {
                        fill: {{Auth::user()->notificationPrefs->toJson()}},
                    },

                    sharedFolders: {
                        folders: {{Auth::user()->shared->toJson()}},
                    },
                    subscription: {
                        data: {{Auth::user()->subscription ? Auth::user()->subscription->load('plan')->toJson() : null}},
                    },
                }"
            >
                <aside class="w-1/4 settings-sidebar">
                    <nav>
                        <div class="settings-sidebar_item mb-3 placeholder"></div>
                        <div class="settings-sidebar_item mb-3 placeholder"></div>
                        <div class="settings-sidebar_item mb-3 placeholder"></div>
                        <div class="settings-sidebar_item placeholder"></div>

                    </nav>
                </aside>
                <div class="card mb-0 p-5 flex-1">
                    <div class="placeholder placeholder--title w-56"></div>
                    <hr class="my-2">

                    <form action="">
                        <div class="form-group">
                            <div class="placeholder placeholder--text w-40"></div>
                            <div class="placeholder placeholder--text w-2/3"></div>
                            <div class="placeholder placeholder--input"></div>
                            <div class="placeholder placeholder--text"></div>
                        </div>

                        <div class="form-group">
                            <div class="placeholder placeholder--text w-40"></div>
                            <div class="placeholder placeholder--input"></div>
                        </div>

                        <div class="form-group">
                            <div class="placeholder placeholder--text w-40"></div>
                            <div class="placeholder placeholder--text w-2/3"></div>
                            <div class="placeholder placeholder--input"></div>
                        </div>
                    </form>
                </div>
            </settings>
            @include('partials.copyright')
        </div>
    </div>
@endsection

@section('javascript')
@endsection

