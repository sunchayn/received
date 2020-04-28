<template>
    <fragment>
        <header class="browser_toolbar block text-right leading-normal" v-if="deleted">
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
                <div class="flex items-center" style="max-width: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-12 mr-2 icon-folder"><g><path class="secondary" d="M22 10H2V6c0-1.1.9-2 2-2h7l2 2h7a2 2 0 0 1 2 2v2z"/><rect width="20" height="12" x="2" y="8" class="primary" rx="2"/></g></svg>
                    <div class="flex items-start">
                        <div class="mr-2">
                            <h1
                                :key="1"
                                v-if="! editingFolderName"
                                class="mb-1 select-none"
                                @click="editFolderName"
                                title="Edit folder name."
                            >{{folder.name}}</h1>

                            <h1
                                v-if="editingFolderName"
                                :key="2"
                                ref="folderName"
                                contenteditable
                                class="mb-1 outline-none"
                                @blur="editingFolderName = false"
                                @keydown.enter.prevent="updateFolderName"
                            >{{folder.name}}</h1>

                            <small class="text-gray-700">{{folder.files.length}} files in this folder</small>
                        </div>
                        <span class="badge" v-if="folder.is_shared">shared</span>
                    </div>
                </div>
                <div class="ml-auto flex items-center">
                    <div class="mr-2 relative">

                        <button class="button button--outline button--slim" @click="sharing = !sharing" v-if="folder.is_shared === false">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-6 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                            <div>Share folder</div>
                        </button>

                        <button class="button button--outline button--slim" @click="revokeAccess" v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 mr-2 icon-lock"><g><path class="secondary" d="M12 10v3a2 2 0 0 0-1 3.73V18a1 1 0 0 0 1 1v3H5a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h7z"/><path class="primary" d="M12 19a1 1 0 0 0 1-1v-1.27A2 2 0 0 0 12 13v-3h3V7a3 3 0 0 0-6 0v3H7V7a5 5 0 1 1 10 0v3h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7v-3z"/></g></svg>
                            Revoke access
                        </button>

                        <div
                            v-show="sharing"
                            class="absolute border border-gray-200 shadow mt-2 right-0 bg-white p-5 z-20 max-w-full" style="min-width: 320px;"
                        >
                            <template v-if="! shareData.shared">
                                <h1>Sharing a folder</h1>
                                <p class="text-gray-700 text-sm leading-snug">Passwords are not saved in plain format, make sure you've copied it before saving.</p>
                                <div class="form-group">
                                    <input ref="share-input" type="text" class="input" placeholder="password" v-model="shareData.password" />
                                    <small class="input-error" :class="{'is-visible' : shareData.error }">
                                        {{shareData.error}}
                                    </small>
                                </div>

                                <div class="flex">
                                    <button ref="shareButton" type="submit" class="button mr-2 flex-1" @click="shareFolder">Share</button>
                                    <button type="submit" class="button button--outline button--slim flex-1" @click="sharing = false">Dismiss</button>
                                </div>
                            </template>

                            <template v-else>
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-check w-12 mx-auto">
                                        <circle cx="12" cy="12" r="10" class="fill-current text-green-200"/>
                                        <path class="fill-current text-green-900" d="M10 14.59l6.3-6.3a1 1 0 0 1 1.4 1.42l-7 7a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l2.3 2.3z"/>
                                    </svg>
                                    <h2 class="text-gray-700 leading-snug my-2">Public access created!</h2>
                                    <p class="text-gray-700 text-sm leading-snug">In order to start receiving files forward your public bucket URL and this folder password to the interested parties.</p>
                                    <button type="submit" class="button button--outline button--slim w-full" @click="sharing = false">Dismiss</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <button class="button button--outline button--slim mr-2" @click="download">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cloud-download w-6 mr-2"><path class="primary" d="M15 15v-3a3 3 0 0 0-6 0v3H6a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4.5 4.5H15z"/><path class="secondary" d="M11 18.59V12a1 1 0 0 1 2 0v6.59l1.3-1.3a1 1 0 0 1 1.4 1.42l-3 3a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l1.3 1.3z"/></svg>
                        <div>Download</div>
                    </button>

                    <button class="button button--outline button--slim button--red" @click="deleted=true">
                        <span class="h-6"></span>Delete
                    </button>
                </div>
            </header>

            <header class="browser_toolbar" v-else>
                <h1 class="text-gray-700 my-2">Select a folder to browse its content</h1>
            </header>
        </template>
    </fragment>
</template>

<script>
    export default {
        props: ['folder', 'index', 'routes'],

        data() {
            return {
                deleted: false,
                sharing: false,

                shareData: {
                    password: '',
                    shared: false,
                    error: null,
                },
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

            shareFolder() {
                this.$refs.shareButton.classList.add('is-submitting');

                axios.patch(this.routes.share.replace('__id', this.folder.id), {password: this.shareData.password})
                    .then(response => {
                        this.shareData.shared = true;
                        this.folder.is_shared = true;
                    })
                    .catch(error => {
                        this.$refs.shareButton.classList.remove('is-submitting');

                        if (error.response.status === 422) {
                            this.shareData.error = error.response.data.errors.password[0];
                        } else {
                            let message = error.response.data.message || 'Unable to share the folder.';
                            this.error({message: message});
                        }
                    })
                ;
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
