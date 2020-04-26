<template>
    <div class="flex-1 flex">

        <Sidebar
            :folders="folders"
            :storage="storage"
            :current="folder"
            :routes="routes"

            @folderCreated="addNewFolder"
            @showFolder="folder=$event"
        />

        <div class="flex flex-col w-4/5">
            <Toolbar
                :folder="folder"
            />

            <Files
                :folder="folder"
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
                    if (!isNaN(response.data.total_storage)) {
                        this.storage.data.total_storage = response.data.total_storage;
                    }

                    if (!isNaN(response.data.used_storage)) {
                        this.storage.data.used_storage = response.data.used_storage;
                    }
                })
                .catch(error => {})
                .finally(() => {
                    this.storage.loaded = true;
                })
            ;
        },

        methods: {
            addNewFolder(folder) {
                this.folders.data.push(folder);
            }
        },
    }

</script>
