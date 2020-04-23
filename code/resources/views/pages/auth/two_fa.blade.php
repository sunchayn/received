@extends('layouts.base')

@section('title')
    Confirm your identity
@endsection

@section('content')
    <div
        class="min-h-screen relative z-0 overflow-hidden flex flex-col justify-center items-center"
        style="background: linear-gradient(to top, #ECECEC, #FAFAFA 30%, #ffffff 100%)"
    >

        <div class="container max-w-xl">

            <div class="card">
                <div class="card_content">
                    <h2 class="font-bold text-lg">Confirm your identity</h2>

                    <two-factor-auth
                        :routes="{
                            submit: '{{route('auth.2fa')}}',
                            }"
                    >Loading...</two-factor-auth>
                </div>
            </div>

            @include('partials.copyright')

        </div>

    </div>
@endsection
