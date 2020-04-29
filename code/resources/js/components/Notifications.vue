<template>
    <div class="relative">
        <button
            class="flex items-center outline-none active:outline-none focus:outline-none"
            @click="showNotifications()"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-notification w-6 mr-2"><circle cx="12" cy="19" r="3" class="secondary"/><path class="primary" d="M10.02 4.28L10 4a2 2 0 1 1 3.98.28A7 7 0 0 1 19 11v5a1 1 0 0 0 1 1 1 1 0 0 1 0 2H4a1 1 0 0 1 0-2 1 1 0 0 0 1-1v-5a7 7 0 0 1 5.02-6.72z"/></svg>
            <span
                class="py-1 px-2 rounded inline-block leading-none text-xs"
                :class="{
                'bg-blue-400 text-white': unread,
                'bg-gray-300': !unread
            }"
            >{{unreadCount}}</span>
        </button>

        <div
            v-show="showing"
            class="absolute right-0 border-gray-200 border border-b-0 shadow mt-2 bg-white z-20"
            style="width: 210px;"
        >
            <template v-if="notifications.length > 0">
                <div
                    class="py-2 px-4 hover:bg-teal-100 border-b border-gray-200"
                    v-for="notification in notifications" :key="notification.id"
                >
                    <h2 class="mb-0 text-sm">{{notification.title}}</h2>
                    <p class="mb-0 text-sm text-gray-700">{{notification.content}}</p>
                    <small>{{notification.created_at}}</small>
                </div>
            </template>

            <template v-else>
                <div class="p-4">
                    <h1 class="text-center text-gray-700 mb-0 text-sm">There's no new notification.</h1>
                </div>
            </template>


        </div>
    </div>

</template>

<script>
    export default {
        props: ['routes'],

        data() {
            return {
                showing: false,
                notifications: [],
                unread: false,
                unreadCount: 0,
            }
        },

        created() {
            this.pull();
            setInterval(this.pull, 60 * 1000);
        },

        methods: {
            pull() {
                axios.get(this.routes.pull)
                    .then(response => {
                        this.notifications = response.data;
                        this.unreadCount = this.notifications.length;
                        this.unread = this.unreadCount > 0;
                    })
                ;
            },

            showNotifications() {
                if (this.showing) {
                    this.showing = false;
                    return false;
                }

                this.showing = true;
                this.unread = false;
                this.unreadCount = 0;

                axios.patch(this.routes.read, {ids: this.notifications.map(ele => ele.id)});
            }
        }
    }
</script>
