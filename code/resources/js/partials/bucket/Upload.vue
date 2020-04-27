<template>
    <div>
        <h1 class="p-2 text-center bg-blue-100 text-blue-900">Available space in this bucket: <b>{{remainingStorage}}</b></h1>
        <FilePond
            ref="pond"

            :maxTotalFileSiz="remainingStorage"
            allow-multiple="true"
            instantUpload="false"
            allowRevert="false"
            dropOnPage="true"

            :server="{
            process: process,
        }"
        />
    </div>
</template>

<script>
    // Filepond Setup
    import vueFilePond from 'vue-filepond';
    import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';

    import 'filepond/dist/filepond.min.css';
    const FilePond = vueFilePond(FilePondPluginFileValidateSize);

    export default {
        props: ['routes', 'password', 'remainingStorage'],

        components: {
            FilePond,
        },

        data() {
            return {
                CSRFToken: window.CSRFToken,
            }
        },

        methods: {
            process(fieldName, file, metadata, load, error, progress, abort, transfer, options){
                const formData = new FormData();
                formData.append('files[]', file, file.name);
                formData.append('password', this.password);

                const request = new XMLHttpRequest();

                request.open('POST', this.routes.upload);

                request.setRequestHeader('X-CSRF-TOKEN', this.CSRFToken);
                request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                request.upload.onprogress = (e) => {
                    progress(e.lengthComputable, e.loaded, e.total);
                };

                request.onload = () => {
                    if (request.status >= 200 && request.status < 300) {
                        load(request.responseText);
                    } else {
                        const e = JSON.parse(request.responseText);
                        const carry = e.errors.password || e.errors.size;
                        this.errorNotification({message: carry[0]});
                    }
                };

                request.send(formData);

                return {
                    abort: () => {
                        // This function is entered if the user has tapped the cancel button
                        request.abort();

                        // Let FilePond know the request has been cancelled
                        abort();
                    }
                };
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

<style>
    .filepond--root {
        height: 40vh;
    }

    .filepond--panel-root {
        background-color: #f9f9f9;
        border: 2px dashed #888;
        border-radius: 5px;
    }

    /*.filepond--drop-label {*/
    /*    height: 100%;*/
    /*}*/

    .filepond--file-wrapper,
    .filepond--panel.filepond--item-panel {
        animation: unset !important;
    }
</style>
