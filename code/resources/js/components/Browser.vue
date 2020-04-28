<template>
    <div class="flex-1 flex">

        <Sidebar
            :folders="folders"
            :storage="storage"
            :current="folder"
            :routes="routes"

            @folderCreated="addNewFolder"
            @showFolder="showFolder"
        />

        <div class="flex flex-col w-4/5">
            <Toolbar
                ref="toolbar"
                :folder="folder"
                :index="index"
                :routes="routes"
                @delete="deleteFolder"
            />

            <Files
                :folder="folder"
                @download="downloadFile"
                @delete="deleteFile"
            />
        </div>
    </div>
</template>

<script>
    import Sidebar from '@/partials/browser/sidebar';
    import Toolbar from '@/partials/browser/Toolbar';
    import Files from '@/partials/browser/Files';

    export default {
        props: ['routes'],

        components: {
            Sidebar,
            Toolbar,
            Files,
        },

        data() {
            return {
                folder: null,
                index: null,

                folders: {
                    loaded: false,
                    data: [],
                },

                storage: {
                    loaded: false,
                    data: {
                        used_storage: 'n/a',
                        total_storage: 'n/a',
                    }
                },
            }
        },

        beforeMount() {
            axios.get(this.routes.folders)
                .then(response => {
                    this.folders.loaded = true;
                    this.folders.data = response.data;
                })
                .catch(error => {
                    alert('Unable to fetch folders. Kindly refresh the page!')
                })
            ;

            axios.get(this.routes.storage_info)
                .then(response => {
                    if (response.data.total_storage !== null) {
                        this.storage.data.total_storage = response.data.total_storage;
                    }

                    if (response.data.used_storage !== null) {
                        this.storage.data.used_storage = response.data.used_storage;
                    }

                    this.storage.data.percentage = response.data.percentage;
                })
                .catch(error => {})
                .finally(() => {
                    this.storage.loaded = true;
                })
            ;
        },

        methods: {
            showFolder(folder, index) {
                this.folder = folder;
                this.index = index;
            },

            addNewFolder(folder) {
                folder.files = [];
                this.folders.data.unshift(folder);
            },

            deleteFolder(id, index) {
                axios.delete(this.routes.delete.replace('__id', id))
                    .then(response => {
                        this.folders.data.splice(index, 1);
                        this.folder = null;
                        this.index = null;
                        this.$refs.toolbar.reset();
                    })
                    .catch(error => {
                        alert('Unable to delete the folder! Kindly reload the page.')
                    })
                ;
            },

            downloadFile(file) {
                window.open(this.routes.file_download.replace('__id', file.id))
            },

            deleteFile(file, index) {
                if (confirm('Are you sure?')) {
                    axios.delete(this.routes.file_delete.replace('__id', file.id))
                        .then(response => {
                            this.folder.files.splice(index, 1);
                        })
                        .catch(error => {
                            let message = error.response.data.message || 'We were unable to delete the file.';
                            this.error({message: message});
                        })
                    ;
                }
            }
        },


        notifications: {
            errorNotification: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },
        }
    }

</script>
