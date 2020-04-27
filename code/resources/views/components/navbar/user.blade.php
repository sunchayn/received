<header class="py-2 px-5 relative z-50{{ !isset($border) || $border !== false ? ' border-b border-gray-300' : '' }}">
    <nav class="flex items-center">
        <h1 class="font-black text-xl">received;</h1>
        <div class="ml-auto">
            <div class="js-dropdown dropdown">
                <button class="border-l border-gray-300 text-black pl-3 flex items-center focus:outline-none js-dropdown-trigger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 icon-dots-vertical"><path fill="black" fill-rule="evenodd" d="M12 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
                </button>

                <div class="dropdown_menu is-left js-dropdown-menu">
                    <a href="{{route('settings.index')}}" class="dropdown_menu_item">Settings</a>
                    <a href="{{route('auth.logout')}}" class="dropdown_menu_item">Logout</a>
                </div>
            </div>

        </div>
    </nav>
</header>
