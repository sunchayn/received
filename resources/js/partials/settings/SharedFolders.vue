<template>
    <fragment>
        <h2>Shared folders</h2>
        <hr class="my-2">

        <template v-if="folders.length > 0">
            <div
                class="border-b border-gray-1 py-4 flex items-center"
                v-for="(folder, index) in folders"
                :key="folder.id"
            >
                <div>
                    <p class="mb-0"><u>{{folder.name}}</u></p>
                    <p class="mb-0">files uploaded: {{folder.files.length}} files</p>
                    <p class="mb-0 text-gray-700 text-sm">shared on: {{folder.shared_at}}</p>
                </div>
                <div class="ml-auto pl-2 flex flex-col">
                    <div class="relative">
                        <button class="button px-3 py-1 mb-2" @click="changePassword(folder.id)">Change password</button>
                        <FolderPassword
                            :new="false"
                            :ref="'password-' + folder.id"
                            :key="folder.id"
                            :route="routes.changePassword.replace('__id', folder.id)"
                        />
                    </div>
                    <button class="hover:underline" @click="revokeAccess(folder.id, index)">revoke access</button>
                </div>
            </div>
        </template>

        <template v-else>
            <h2 class="text-gray-700 mb-0">You don't have a shared folders.</h2>
            <p class="text-sm">Create and share folder withing your <a :href="routes.bucket">Bucket</a></p>
        </template>

    </fragment>
</template>

<script>
    import FolderPassword from "@/ui/FolderPassword";

    export default {
        props: ['routes', 'folders'],

        components: {
            FolderPassword,
        },

        data() {
            return {
            }
        },

        methods: {
            revokeAccess(id, index) {
                if (confirm('Are you sure?')) {
                    axios.patch(this.routes.revoke.replace('__id', id))
                        .then(response => {
                            this.folders.splice(index, 1);
                            this.$forceUpdate();
                        })
                        .catch(error => {
                            this.errorNotification();
                        })
                    ;
                }
            },

            changePassword(id) {
                this.$refs['password-' + id][0].show();
            }
        },

        notifications: {
            accessRevoked: {
                type: 'success',
                title: 'Success',
                message: 'Folder access has been revoked.'
            },

            passwordChanged: {
                type: 'success',
                title: 'Success',
                message: 'Folder sharing password has been changed.'
            },

            errorNotification: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },
        }
    }
</script>
