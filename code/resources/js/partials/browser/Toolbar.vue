<template>
    <header class="border-b border-gray-300 p-2 leading-none flex pr-5" v-if="folder">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 mr-2 icon-folder"><g><path class="secondary" d="M22 10H2V6c0-1.1.9-2 2-2h7l2 2h7a2 2 0 0 1 2 2v2z"/><rect width="20" height="12" x="2" y="8" class="primary" rx="2"/></g></svg>
            <div>
                <h1 class="mb-1">{{folder.name}}</h1>
                <small class="text-gray-700">{{folder.files.length}} files in this folder</small>
            </div>
        </div>
        <div class="ml-auto flex items-center">
            <button class="button button--outline mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cloud-download w-6 mr-2"><path class="primary" d="M15 15v-3a3 3 0 0 0-6 0v3H6a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4.5 4.5H15z"/><path class="secondary" d="M11 18.59V12a1 1 0 0 1 2 0v6.59l1.3-1.3a1 1 0 0 1 1.4 1.42l-3 3a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l1.3 1.3z"/></svg>
                <div>Download</div>
            </button>

            <button class="button button--outline mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-6 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                <div>Share folder</div>
            </button>
        </div>
    </header>

    <header class="border-b border-gray-300 px-2 py-4 leading-none flex items-center" v-else>
        <h1 class="mb-0 text-gray-700">Select a folder to browse its content</h1>
    </header>
</template>

<script>

    export default {
        props: ['folder'],

        data() {
            return {

            }
        },
    }
</script>
