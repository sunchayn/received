@extends('layouts.base')

@section('title')
    Verify your phone number
@endsection

@section('content')
    <div
        class="min-h-screen relative z-0 overflow-hidden flex flex-col justify-center items-center"
        style="background: linear-gradient(to top, #ECECEC, #FAFAFA 30%, #ffffff 100%)"
    >

        <div class="container max-w-xl">

            <div class="card">
                <div class="card_content">
                    <h2 class="font-bold text-lg">Verify your phone number</h2>

                    <verify
                        :routes="{
                            submit: '{{route('auth.verify', ['verification_id' => Auth::user()->verification_id])}}',
                        }"
                    >Loading...</verify>
                </div>
            </div>

            @include('partials.copyright')

        </div>

    </div>
@endsection
