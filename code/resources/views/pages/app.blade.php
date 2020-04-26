@extends('layouts.base')

@section('title') Bucket @endsection
@section('content')
    <div class="flex flex-col h-screen">
        @component('components.navbar.user')@endcomponent
        <browser
            class="flex-1 flex"
            :routes="{
                folders: '{{route('folders.all')}}',
                create: '{{route('folders.create')}}',
                delete: '{{route('folders.delete', ['folder' => '__id'])}}',
                share: '{{route('folders.share', ['folder' => '__id'])}}',
                edit: '{{route('folders.edit', ['folder' => '__id'])}}',
                storage_info: '{{route('me.storage_info')}}',
            }"
        >
            <aside class="browser-sidebar w-1/5">
                <header class="p-5">
                    <div class="placeholder placeholder--button"></div>
                </header>

                <div class="flex-1 flex flex-col">
                    <div class="browser-sidebar_folders">
                        <header class="browser-sidebar_folders_header">
                            <div class="placeholder placeholder--text w-1/3"></div>
                        </header>
                        <ul class="browser-sidebar_folders_content">
                            <li class="browser-sidebar_folders_item">
                                <div class="placeholder placeholder--text w-2/3"></div>
                            </li>

                            <li class="browser-sidebar_folders_item">
                                <div class="placeholder placeholder--text w-1/3"></div>
                            </li>
                        </ul>
                    </div>

                    <footer class="py-4 px-5 text-sm">
                        <small class="leading-none">
                            <div class="placeholder placeholder--text w-2/3"></div>
                        </small>
                        <div class="placeholder placeholder--text"></div>
                    </footer>
                </div>
            </aside>
            <div class="flex flex-col w-4/5">
                <header class="border-b border-gray-300 p-2 leading-none flex pr-5">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 mr-2 icon-folder"><g><path class="secondary" d="M22 10H2V6c0-1.1.9-2 2-2h7l2 2h7a2 2 0 0 1 2 2v2z"/><rect width="20" height="12" x="2" y="8" class="primary" rx="2"/></g></svg>
                        <div>
                            <div class="placeholder placeholder--title w-12"></div>
                            <div class="placeholder placeholder--text w-24"></div>
                        </div>
                    </div>
                    <div class="ml-auto flex items-center">
                        <div class="placeholder placeholder--button mr-2 w-40"></div>
                        <div class="placeholder placeholder--button w-40"></div>
                    </div>
                </header>
                <section class="flex-1 files">
                    <header class="files_header" id="js-scrollable-table-header">
                        <span class="files_column w-3/12">File name</span>
                        <span class="files_column w-2/12">Size</span>
                        <span class="files_column w-2/12">Type</span>
                        <span class="files_column w-3/12">Sent on</span>
                        <span class="files_column w-2/12">action</span>
                    </header>
                    <div class="files_content" id="js-scrollable-table">
                    </div>
                </section>
            </div>
        </browser>
    </div>
@endsection

{{--@section('javascript')--}}
{{--    <script>--}}
{{--        var scrollableTable = document.getElementById('js-scrollable-table');--}}
{{--        var tableHeader = document.getElementById('js-scrollable-table-header');--}}
{{--        scrollableTable.addEventListener('scroll', function(e) {--}}
{{--            console.log('scrolled');--}}
{{--            tableHeader.style.left = -e.currentTarget.scrollLeft + 'px';--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}
