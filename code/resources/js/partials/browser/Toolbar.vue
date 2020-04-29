<template>
    <fragment>
        <header class="browser_toolbar block text-right leading-snug md:leading-normal" v-if="deleted">
            <small>Are you sure you want to delete this folder? your files will be permanently deleted.</small>
            <br />

            <button
                type="button"
                class="text-sm outline-none text-gray-600 hover:text-gray-800 mr-2"
                @click="$emit('delete', folder.id, index)">confirm</button>

            <button
                type="button"
                class="text-sm outline-none active:outline-none focus:outline-none text-gray-600 hover:text-gray-800"
                @click="deleted=false">undo</button>
        </header>

        <template v-else>
            <header class="browser_toolbar" v-if="folder">
                <div class="flex-1 flex items-center md:max-w-3xl">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 mr-2 icon-folder"><g><path class="secondary" d="M22 10H2V6c0-1.1.9-2 2-2h7l2 2h7a2 2 0 0 1 2 2v2z"/><rect width="20" height="12" x="2" y="8" class="primary" rx="2"/></g></svg>
                    <div class="flex items-start">
                        <div class="flex flex-col items-start">
                            <div class="relative">
                                <h1
                                    :key="1"
                                    v-if="! editingFolderName"
                                    class="mb-1 select-none inline-block mr-2"
                                    @click="editFolderName"
                                    title="Edit folder name."
                                >{{folder.name}}</h1>

                                <h1
                                    v-if="editingFolderName"
                                    :key="2"
                                    ref="folderName"
                                    contenteditable
                                    class="mb-1 outline-none inline-block mr-2"
                                    @blur="editingFolderName = false"
                                    @keydown.enter.prevent="updateFolderName"
                                >{{folder.name}}</h1>

                                <span class="badge absolute z-20 right-0 transform translate-x-full" v-if="folder.is_shared">shared</span>
                            </div>
                            <small class="text-gray-700">{{folder.files.length}} files in this folder</small>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <button class="button button--outline button--slim mr-2 hidden md:inline-flex" @click="showFolderSharing" v-if="folder.is_shared === false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-6 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                        <div class="flex-1 whitespace-no-wrap">Share folder</div>
                    </button>

                    <Dropdown ref="dropdown" :left="true">
                        <template #trigger>
                            <div class="button button--outline button--slim">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cog w-6"><path class="primary" d="M6.8 3.45c.87-.52 1.82-.92 2.83-1.17a2.5 2.5 0 0 0 4.74 0c1.01.25 1.96.65 2.82 1.17a2.5 2.5 0 0 0 3.36 3.36c.52.86.92 1.8 1.17 2.82a2.5 2.5 0 0 0 0 4.74c-.25 1.01-.65 1.96-1.17 2.82a2.5 2.5 0 0 0-3.36 3.36c-.86.52-1.8.92-2.82 1.17a2.5 2.5 0 0 0-4.74 0c-1.01-.25-1.96-.65-2.82-1.17a2.5 2.5 0 0 0-3.36-3.36 9.94 9.94 0 0 1-1.17-2.82 2.5 2.5 0 0 0 0-4.74c.25-1.01.65-1.96 1.17-2.82a2.5 2.5 0 0 0 3.36-3.36zM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/><circle cx="12" cy="12" r="2" class="secondary"/></svg>
                            </div>
                        </template>

                        <template #menu>

                            <button class="dropdown_menu_item md:hidden" @click="showFolderSharing" v-if="folder.is_shared === false">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-5 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                                <div class="flex-1 whitespace-no-wrap">Share folder</div>
                            </button>

                            <button
                                class="dropdown_menu_item"
                                @click="revokeAccess"
                                v-if="folder.is_shared === true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 mr-2 icon-lock"><g><path class="secondary" d="M12 10v3a2 2 0 0 0-1 3.73V18a1 1 0 0 0 1 1v3H5a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h7z"/><path class="primary" d="M12 19a1 1 0 0 0 1-1v-1.27A2 2 0 0 0 12 13v-3h3V7a3 3 0 0 0-6 0v3H7V7a5 5 0 1 1 10 0v3h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7v-3z"/></g></svg>
                                <div class="flex-1 whitespace-no-wrap">Revoke access</div>
                            </button>

                            <button class="dropdown_menu_item" @click="download">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cloud-download w-5 mr-2"><path class="primary" d="M15 15v-3a3 3 0 0 0-6 0v3H6a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4.5 4.5H15z"/><path class="secondary" d="M11 18.59V12a1 1 0 0 1 2 0v6.59l1.3-1.3a1 1 0 0 1 1.4 1.42l-3 3a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l1.3 1.3z"/></svg>
                                <div class="flex-1">Download</div>
                            </button>

                            <button class="dropdown_menu_item" @click="deleted=true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-trash w-5 mr-2"><path class="primary" d="M5 5h14l-.89 15.12a2 2 0 0 1-2 1.88H7.9a2 2 0 0 1-2-1.88L5 5zm5 5a1 1 0 0 0-1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0-1-1zm4 0a1 1 0 0 0-1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0-1-1z"/><path class="secondary" d="M8.59 4l1.7-1.7A1 1 0 0 1 11 2h2a1 1 0 0 1 .7.3L15.42 4H19a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2h3.59z"/></svg>
                                <div class="flex-1">Delete</div>
                            </button>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <header class="browser_toolbar" v-else>
                <h1 class="text-gray-700 my-2">Select a folder to browse its content</h1>
            </header>
        </template>

        <FolderPassword
            v-if="folder"
            ref="folderSharing"
            :route="routes.share.replace('__id', folder.id)"
            @shared="folder.is_shared = true"
        />

    </fragment>
</template>

<script>
    import Dropdown from "../../ui/Dropdown";
    import FolderPassword from "../../ui/FolderPassword";

    export default {
        props: ['folder', 'index', 'routes'],

        components: {
            Dropdown,
            FolderPassword,
        },

        data() {
            return {
                deleted: false,
                editingFolderName: false,
            }
        },

        watch: {
            sharing: function() {
              this.shareData.shared = false;
              this.shareData.error = null;
              this.shareData.password = '';

              if (this.sharing === true) {
                  this.$nextTick(() => {
                      this.$refs['share-input'].focus();
                  });
              }
            },
        },

        methods: {
            reset() {
                this.deleted = false;
            },

            showFolderSharing() {
              this.$refs.dropdown.close();
              this.$refs.folderSharing.show();
            },

            revokeAccess() {
                axios.patch(this.routes.revoke.replace('__id', this.folder.id))
                    .then(response => {
                        this.folder.is_shared = false;
                    })
                    .catch(error => {
                        let message = error.response.data.message || 'Unable to revoke folder access.';
                        this.error({message: message});
                    })
                ;
            },

            download() {
                window.open(this.routes.download.replace('__id', this.folder.id));
            },

            editFolderName() {
                this.editingFolderName = true;
                this.$nextTick(() => {
                    this.$refs.folderName.focus();
                });
            },

            updateFolderName() {
                const name = this.$refs.folderName.textContent;
                axios.patch(this.routes.edit.replace('__id', this.folder.id), {name})
                    .then(response => {
                        this.folder.name = name;
                        this.editingFolderName = false;
                        this.$refs.folderName.blur();
                    })
                    .catch(error => {
                        let message = error.response.data.message || 'We were unable to update the folder name.';

                        if (error.response.status === 422) {
                            message = error.response.data.errors.name[0];
                        }

                        this.error({message: message});
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
