<template>
    <aside class="browser-sidebar w-1/5">
        <header class="p-5 relative">
            <button class="add-button" @click="createFolder">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 icon-add"><path class="secondary" fill-rule="evenodd" d="M17 11a1 1 0 0 1 0 2h-4v4a1 1 0 0 1-2 0v-4H7a1 1 0 0 1 0-2h4V7a1 1 0 0 1 2 0v4h4z"/></svg>
                New folder
            </button>

            <div class="absolute bg-white top-0 right-0 left-0 bottom-0 p-5" v-if="showCreationForm">
                <input
                    ref="new-folder-input"
                    type="text" class="input"
                    placeholder="Folder name"
                    v-model="newFolderName"
                    @keydown.enter.prevent="addFolder"
                    @keydown.esc.prevent="showCreationForm = false"
                />
            </div>
        </header>

        <div class="flex-1 flex flex-col">
            <div class="browser-sidebar_folders">
                <header class="browser-sidebar_folders_header">My folders</header>

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
                        @click="$emit('showFolder', folder, index)"
                        :class="{'is-active' : current && current.id === folder.id}"
                    >
                        <span>{{folder.name}}</span>
                        <small class="text-gray-500">{{folder.size}} Mb</small>
                    </li>

                    <li v-if="folders.length === 0">
                        <small class="text-gray-700">You don't have any folder.</small>
                    </li>
                </ul>
            </div>

            <footer class="py-4 px-5 text-sm" v-if="storage.loaded">
                <strong class="block leading-none">used storage</strong>
                <small class="leading-none">{{this.storage.data.used_storage}}Go / {{this.storage.data.total_storage}}Go</small>
                <div class="meter_bg">
                    <span class="meter_fg" :style="'width: ' + storagePercentage + '%;'"></span>
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
            }
        },

        computed: {
            storagePercentage() {
                return this.storage.data.used_storage * 100 / this.storage.data.total_storage;
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

                        // Show an error notification is exists
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
