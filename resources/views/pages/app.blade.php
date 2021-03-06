@extends('layouts.base')

@section('title') Bucket @endsection
@section('content')
    <div class="flex flex-col h-screen">
        @component('components.navbar.user')@endcomponent
        <browser
            class="flex flex-wrap md:flex-no-wrap md:flex-1 "
            :routes="{
                folders: '{{route('folders.all')}}',
                create: '{{route('folders.create')}}',
                delete: '{{route('folders.delete', ['folder' => '__id'])}}',
                share: '{{route('folders.share', ['folder' => '__id'])}}',
                revoke: '{{route('folders.revoke', ['folder' => '__id'])}}',
                edit: '{{route('folders.edit', ['folder' => '__id'])}}',
                download: '{{route('folders.download', ['folder' => '__id'])}}',
                file_download: '{{route('files.download', ['file' => '__id'])}}',
                file_delete: '{{route('files.delete', ['file' => '__id'])}}',
                storage_info: '{{route('me.storage_info')}}',
            }"
        >
            <aside class="browser-sidebar w-full md:w-1/5">
                <header class="p-5">
                    <div class="placeholder placeholder--button"></div>
                </header>

                <div class="md:flex-1 flex flex-col">
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
                    <div class="placeholder placeholder--button mr-2 w-1/3"></div>
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
