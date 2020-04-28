<template>
    <section class="flex-1 files relative">
        <div class="absolute bg-white opacity-75 top-0 right-0 left-0 bottom-0 z-20"
             v-if="!folder"></div>
        <header class="files_header" ref="table-header">
            <span class="files_column w-4/12">File</span>
            <span class="files_column w-1/12">Extension</span>
            <span class="files_column w-1/12">Size</span>
            <span class="files_column w-2/12">Type</span>
            <span class="files_column w-2/12">Received at</span>
            <span class="files_column w-2/12">Action</span>
        </header>
        <div class="files_content" @scroll="moveTableHeader">
            <template v-if="folder">
                <div class="files_row"
                     v-for="(file, index) in folder.files"
                     :key="file.id"
                >
                    <span class="files_column w-4/12">{{file.filename}}</span>
                    <span class="files_column w-1/12">{{file.extension}}</span>
                    <span class="files_column w-1/12">{{file.size}}</span>
                    <span class="files_column w-2/12">{{file.type}}</span>
                    <span class="files_column w-2/12">{{file.sent_on}}</span>
                    <span class="files_column w-2/12">
                    <button class="mr-2 hover:underline" @click="$emit('download', file)">download</button>
                    <button class="hover:underline" @click="$emit('delete', file, index)">delete</button>
                </span>
                </div>
            </template>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['folder'],

        data() {
            return {

            }
        },

        methods: {
            moveTableHeader(e) {
                this.$refs['table-header'].style.left = -e.currentTarget.scrollLeft + 'px';
            }
        }
    }
</script>
