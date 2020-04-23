@extends('layouts.landing')

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

        <svg
            class="absolute top-0 bottom-0 right-0 left-0 pointer-events-none"
            style="transform: rotate(0deg) scale(1.1, 1) translate(0, 30%);"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 1920 720"
        >
            <path d="M1606.089,38.615S1478.1,385.793,595.99,411.4s-909.9,347.211-909.9,347.211h1920Z" fill="rgba(236,227,249,0.3)" transform="translate(313.911 -38.615)"/>
        </svg>

        <svg
            class="absolute bottom-0 right-0 left-0 pointer-events-none"
            style="transform: rotate(0) translate(0%, 50%);"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 1920 720"
        >
            <path d="M1578.853,38.615s-126.7,351.664-999.933,377.606S-341.147,758.614-341.147,758.614h1920Z" transform="translate(341.147 -38.615)" fill="#fff"/>
        </svg>


        <div class="flex-1 flex flex-col h-full relative z-10">
            @include('components.navbar.guest')
            <div class="my-auto">
                <div class="container flex max-w-6xl">
                    <div class="w-2/3 mr-10">
                        <div class="bg-lightGray text-sm text-gray-700 inline-block py-2 pr-3 mb-12 relative">
                            <div class="absolute right-0 w-screen top-0 bottom-0 bg-lightGray"></div>
                            <div class="relative z-10 inline-flex items-center">
                                Project submission for DEV x Twilio Hackathon
                                <a href="https://dev.to/devteam/announcing-the-twilio-hackathon-on-dev-2lh8" target="_blank" class="ml-10 text-gray-700 hover:text-gray-900">learn more</a>
                            </div>
                        </div>

                        <h1 class="text-4xl font-black text-black max-w-sm leading-tight">Public bucket for receiving files</h1>
                        <p class="text-2xl text-gray-700 mb-5 max-w-lg">
                            create a gateway to receive and store files from your friends, co-workers and yourself.
                        </p>
                        <a href="https://github.com/sunchayn/received" class="button button--secondary" target="_blank">Visit on GitHub</a>
                    </div>
                    <div class="w-1/3">
                        <div class="card">
                            <form action="{{route('auth.signup')}}" method="post" class="card_content">
                                <h2 class="font-bold text-lg">Create a free account</h2>
                                <ul class="mb-4 text-sm">
                                    <li>- Give assess to folder using multiple passwords.</li>
                                    <li>- Set the size and type of files you want to receive.</li>
                                    <li>- Sync your files with Cloud storage.</li>
                                </ul>

                                <div class="form-group">
                                    <phone-number>
                                        <div class="placeholder placeholder--input" style="height: 42px;"></div>
                                    </phone-number>
                                </div>

                                <div class="form-group">
                                    <input
                                        type="password"
                                        class="input"
                                        name="password"
                                        placeholder="Type a password"
                                    />
                                </div>

                                {{csrf_field()}}
                                <input type="submit" value="Sign up" class="button w-full uppercase">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-5 text-right text-sm uppercase relative z-30">
                <div class="container max-w-6xl text-right">
                    <a
                        class="text-gray-900"
                        href="https://dev.to/mazentouati/received-public-bucket-for-receiving-files-24kb"
                        target="_blank">About</a>
                    <span class="text-gray-700">&nbsp;/&nbsp;</span>
                    <a
                        class="text-gray-900"
                        href="https://sunchayn.github.io"
                        target="_blank" title="Mazen Touati">Contact</a>
                </div>
            </div>
        </div>
    </div>
@endsection
