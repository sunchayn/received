<template>
    <aside class="browser-sidebar md:w-1/4 lg:w-1/5">
        <header class="flex justify-between items-center">
            <div class="p-5 relative flex-1">
                <button class="add-button" @click="createFolder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 icon-add"><path class="secondary" fill-rule="evenodd" d="M17 11a1 1 0 0 1 0 2h-4v4a1 1 0 0 1-2 0v-4H7a1 1 0 0 1 0-2h4V7a1 1 0 0 1 2 0v4h4z"/></svg>
                    New folder
                </button>

                <div class="absolute bg-white right-0 left-0 bottom-0 px-4" style="top: 50%; transform: translateY(-50%)" v-if="showCreationForm">
                    <input
                        ref="new-folder-input"
                        type="text" class="input"
                        placeholder="Folder name"
                        v-model="newFolderName"
                        @keydown.enter.prevent="addFolder"
                        @keydown.esc.prevent="showCreationForm = false"
                    />
                </div>
            </div>

            <div class="ml-2 pr-5 md:hidden flex items-center">
                <button @click="foldersShown = true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-collection w-6"><rect width="20" height="12" x="2" y="10" class="primary" rx="2"/><path class="secondary" d="M20 8H4c0-1.1.9-2 2-2h12a2 2 0 0 1 2 2zm-2-4H6c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2z"/></svg>
                </button>
            </div>
        </header>

        <div class="browser-sidebar_folders-holder" :class="{'is-active': foldersShown}">
            <div class="browser-sidebar_folders">
                <header class="browser-sidebar_folders_header flex items-center justify-between">
                    My folders
                    <button class="md:hidden" @click="foldersShown = false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-close w-6"><path class="secondary" fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
                    </button>
                </header>

                <ul class="browser-sidebar_folders_content" v-if="! folders.loaded">
                    <li class="browser-sidebar_folders_item">
                        <div class="placeholder placeholder--text w-2/3"></div>
                    </li>

                    <li class="browser-sidebar_folders_item">
                        <div class="placeholder placeholder--text w-1/3"></div>
                    </li>
                </ul>

                <ul class="browser-sidebar_folders_content" v-else>
                    <li class="browser-sidebar_folders_item"
                        v-for="(folder, index) in folders.data"
                        :key="folder.id"
                        @click="foldersShown = false, $emit('showFolder', folder, index)"
                        :class="{'is-active' : current && current.id === folder.id}"
                    >
                        <span>{{folder.name}}</span>
                        <small class="text-gray-500">{{folder.size}}</small>
                    </li>

                    <li v-if="folders.length === 0">
                        <small class="text-gray-700">You don't have any folder.</small>
                    </li>
                </ul>
            </div>

            <footer class="py-4 px-5 text-sm" v-if="storage.loaded">
                <strong class="block leading-none">used storage</strong>
                <small class="leading-none">{{this.storage.data.used_storage}} / {{this.storage.data.total_storage}}</small>
                <div class="meter_bg">
                    <span class="meter_fg" :style="'width: ' + this.storage.data.percentage + '%;'"></span>
                </div>
            </footer>
            <footer class="py-4 px-5 text-sm" v-if="! storage.loaded">
                <small class="leading-none">
                    <div class="placeholder placeholder--text w-2/3"></div>
                </small>
                <div class="placeholder placeholder--text"></div>
            </footer>
        </div>
    </aside>
</template>

<script>
    export default {
        props: ['folders', 'current', 'storage', 'routes'],

        data() {
            return {
                showCreationForm: false,
                newFolderName: '',
                foldersShown: false,
            }
        },

        methods: {
            createFolder() {
                this.showCreationForm = true;
                this.$nextTick(() => {
                    this.$refs['new-folder-input'].focus();
                });
            },

            addFolder() {
                this.$refs['new-folder-input'].setAttribute('disabled', 'disabled');

                axios.post(this.routes.create, {name: this.newFolderName})
                    .then(response => {
                        this.$emit('folderCreated', response.data);
                        this.showCreationForm = false;
                        this.newFolderName = '';
                    })
                    .catch(error => {
                        let message = error.response.data.message || 'Unable to create a new folder.';

                        if (error.response.status === 422) {
                            message = error.response.data.errors.name[0];
                        }

                        this.error({message: message});
                    })
                    .finally(() => {
                        this.$refs['new-folder-input'].removeAttribute('disabled');
                    })
                ;
            }
        },

        notifications: {
            error: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },
        }
    }
</script>
