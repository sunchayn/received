@extends('layouts.base')

@section('title')
    Sign in to continue
@endsection

@section('content')
    <div
        class="min-h-screen relative z-0 overflow-hidden flex flex-col justify-center items-center"
        style="background: linear-gradient(to top, #ECECEC, #FAFAFA 30%, #ffffff 100%)"
    >

        <div class="container max-w-xl">

            <div class="card">
                <div class="card_content">
                    <h2 class="font-bold text-lg mb-0">Sign in to continue</h2>
                    <p>you don't have an account? <a href="{{route('auth.signup')}}">Sign up</a></p>

                    <signin
                        :routes="{
                            submit: '{{route('auth.signin')}}',
                            twoFa: {
                                submit: '{{route('auth.2fa')}}'
                            }
                        }"
                    >Loading...</signin>

                    <p class="mt-2 mb-0"><a href="#">I forget my password</a></p>
                </div>
            </div>

            @include('partials.copyright')

        </div>

    </div>
@endsection
