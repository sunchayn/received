<header class="py-2 px-5 relative z-50{{ !isset($border) || $border !== false ? ' bg-white border-b border-gray-300' : '' }}">
    <nav class="flex items-center">
        <h1 class="font-black text-xl mb-0">
            <a href="{{route('landing_page')}}" class="text-black hover:no-underline">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 482.08 106.88" style="width: 110px;"><defs><linearGradient id="a" x1="5.33" y1="54.25" x2="101.5" y2="54.25" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#2d49e4"/><stop offset="1" stop-color="#081de3"/></linearGradient><linearGradient id="b" x1="0" y1="48.08" x2="96.17" y2="48.08" xlink:href="#a"/></defs><title>logo</title><rect x="5.33" y="6.17" width="96.17" height="96.17" rx="5.85" ry="5.85" style="opacity:0.6900000000000001;fill:url(#a)"/><rect width="96.17" height="96.17" rx="6.33" ry="6.33" style="opacity:0.91;fill:url(#b)"/><path d="M36.22,71.84C44.74,68.72,49,63.78,49.07,56.68l-.8-10.32,5.39,8.91a8.22,8.22,0,0,1-6,2.06c-4.85-.15-9-3.26-8.81-8.9C39,43,43.33,39.86,48.47,40c6.83.19,10.25,5.8,10,14.86C58.11,66.72,51.14,75,39,78.62Zm2.5-46a9.61,9.61,0,1,1,19.21.56C57.77,32.1,53.58,36.13,48,36S38.55,31.54,38.72,25.84Z" style="fill:#fff"/><text transform="translate(130.92 83.72)" style="font-size:85.46275329589844px;font-family:Roboto-Bold, Roboto;font-weight:700">Recei<tspan x="214.16" y="0" style="letter-spacing:-0.006341852638698121em">v</tspan><tspan x="256.81" y="0">ed</tspan></text></svg>
            </a>
        </h1>

        <div class="ml-auto flex items-center">
            <div class="flex items-center mr-4 hidden md:block">
                <span class="badge badge--gray" title="your public bucket">{{route('send.index', ['username' => Auth::user()->username])}}</span>
            </div>

            <div class="mr-4 pl-3 border-l border-gray-300">
                <notifications
                    :routes="{
                        pull: '{{route('notifications.pull')}}',
                        read: '{{route('notifications.read')}}',
                    }"
                >
                    <button class="flex items-center select-none outline-none active:outline-none focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-notification w-6 mr-2"><circle cx="12" cy="19" r="3" class="secondary"/><path class="primary" d="M10.02 4.28L10 4a2 2 0 1 1 3.98.28A7 7 0 0 1 19 11v5a1 1 0 0 0 1 1 1 1 0 0 1 0 2H4a1 1 0 0 1 0-2 1 1 0 0 0 1-1v-5a7 7 0 0 1 5.02-6.72z"/></svg>
                        <span class="bg-gray-300 p-1 rounded inline-block leading-none text-xs">0</span>
                    </button>
                </notifications>
            </div>

            <div class="js-dropdown dropdown">
                <button class="border-l border-gray-300 text-black pl-3 flex items-center focus:outline-none js-dropdown-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 icon-dots-vertical"><path fill="black" fill-rule="evenodd" d="M12 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
                </button>

                <div class="dropdown_menu is-left js-dropdown-menu">
                    <a href="{{route('home')}}" class="dropdown_menu_item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-6 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                        Bucket
                    </a>
                    <a href="{{route('settings.index')}}" class="dropdown_menu_item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cog w-6 mr-2"><path class="primary" d="M6.8 3.45c.87-.52 1.82-.92 2.83-1.17a2.5 2.5 0 0 0 4.74 0c1.01.25 1.96.65 2.82 1.17a2.5 2.5 0 0 0 3.36 3.36c.52.86.92 1.8 1.17 2.82a2.5 2.5 0 0 0 0 4.74c-.25 1.01-.65 1.96-1.17 2.82a2.5 2.5 0 0 0-3.36 3.36c-.86.52-1.8.92-2.82 1.17a2.5 2.5 0 0 0-4.74 0c-1.01-.25-1.96-.65-2.82-1.17a2.5 2.5 0 0 0-3.36-3.36 9.94 9.94 0 0 1-1.17-2.82 2.5 2.5 0 0 0 0-4.74c.25-1.01.65-1.96 1.17-2.82a2.5 2.5 0 0 0 3.36-3.36zM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/><circle cx="12" cy="12" r="2" class="secondary"/></svg>
                        Settings
                    </a>
                    <a href="{{route('auth.logout')}}" class="dropdown_menu_item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-door-exit w-6 mr-2"><path class="primary" d="M11 4h3a1 1 0 0 1 1 1v3a1 1 0 0 1-2 0V6h-2v12h2v-2a1 1 0 0 1 2 0v3a1 1 0 0 1-1 1h-3v1a1 1 0 0 1-1.27.96l-6.98-2A1 1 0 0 1 2 19V5a1 1 0 0 1 .75-.97l6.98-2A1 1 0 0 1 11 3v1z"/><path class="secondary" d="M18.59 11l-1.3-1.3c-.94-.94.47-2.35 1.42-1.4l3 3a1 1 0 0 1 0 1.4l-3 3c-.95.95-2.36-.46-1.42-1.4l1.3-1.3H14a1 1 0 0 1 0-2h4.59z"/></svg>
                        Logout
                    </a>
                </div>
            </div>

        </div>
    </nav>
</header>
