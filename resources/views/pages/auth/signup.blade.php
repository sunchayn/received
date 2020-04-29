@extends('layouts.base')

@section('title')
    Signup
@endsection

@section('content')
    <div
        class="min-h-screen relative z-0 overflow-hidden flex flex-col justify-center items-center"
        style="background: linear-gradient(to top, #ECECEC, #FAFAFA 30%, #ffffff 100%)"
    >

        <div class="container max-w-xl">

            <div class="card">
                <div class="card_content">
                    <h2 class="font-bold text-lg">Create a free account</h2>
                    <ul class="mb-4 text-sm">
                        <li>- Give access to folder using multiple passwords.</li>
                        <li class="italic">- Set the size and type of files you want to receive.</li>
                        <li class="italic">- Sync your files with Cloud storage.</li>
                    </ul>

                    <signup
                        :routes="{
                            submit: '{{route('auth.signup')}}',
                        }"

                        :old-phone="'{{old('phone_number')}}'"
                        :country="'{{old('country')}}'"

                        :pre-errors="{{$errors->toJson()}}"
                    >Loading...</signup>
                </div>
            </div>

            @include('partials.copyright')

        </div>

    </div>
@endsection
