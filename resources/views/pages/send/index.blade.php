@extends('layouts.base')

@section('title') Sending files @endsection

@section('content')
    <div id="js-border-color-cycle"
         class="min-h-screen border-8 border-blue-200 relative z-0 overflow-hidden flex flex-col"
         style="transition: border-color 1s ease;"
    >
        <svg
            class="absolute top-0 bottom-0 right-0 left-0 pointer-events-none"
            style="transform: rotate(-5deg) scale(2, 2) translate(-20%, 5%)"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 1920 720"
        >
            <path d="M1606.089,38.615S1478.1,385.793,595.99,411.4s-909.9,347.211-909.9,347.211h1920Z" fill="rgba(220,206,241,0.08)" transform="translate(313.911 -38.615)"/>
        </svg>

        <div class="flex-1 flex flex-col h-full relative z-10">
            @if(Auth::guest())
                @include('components.navbar.guest')
            @else
                @include('components.navbar.user', ['border' => false])
            @endif
            <div class="my-auto">
                <div class="container max-w-2xl">
                    <bucket-upload
                        :routes="{
                            unlock: '{{route('send.unlock', ['username' => $username])}}',
                            upload: '{{route('send.upload', ['username' => $username])}}',
                        }"
                    >
                        <div class="card p-10 md:mx-12">
                            <h1 class="text-2xl font-bold text-black w-2/3 mx-auto text-center">Kindly enter the password to unlock the bucket.</h1>
                            <div class="form-group">
                                <div class="placeholder placeholder--input" style="height: 38px;"></div>
                            </div>
                            <div class="placeholder placeholder--button"></div>
                        </div>
                    </bucket-upload>

                    <p class="text-center mt-4 text-lg text-gray-700">Received; is a public bucket to receive and store files from your friends, co-workers and yourself.</p>
                    @include('partials.copyright')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{mix('js/landing.js')}}"></script>
@endsection

