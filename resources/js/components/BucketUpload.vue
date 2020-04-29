<template>
    <fragment>
        <Unlock
            v-if="locked"
            :routes="routes"
            @unlocked="unlocked"
        />

        <Upload
            v-else
            :routes="routes"
            :password="password"
            :remainingStorage="remainingStorage"
        />
    </fragment>
</template>

<script>
    import Unlock from '@/partials/bucket/Unlock';
    import Upload from '@/partials/bucket/Upload';

    export default {
        props: ['routes'],

        components: {
            Unlock,
            Upload,
        },

        data() {
            return {
                locked: true,
                remainingStorage: null,
                password: null,
            }
        },

        methods: {
            unlocked(password, remainingStorage) {
                this.locked = false;
                this.password = password;
                this.remainingStorage = remainingStorage;
            }
        }
    }
</script>
