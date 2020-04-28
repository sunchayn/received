<template>
    <div class="flex mb-5">
        <Sidebar
            :current.sync="current"
        />
        <div class="card mb-0 p-5 flex-1">
            <Profile
                :fill="this.data.profile.fill"
                :username="this.data.profile.username"
                :url="this.data.profile.url"
                :routes="this.routes.profile"
                v-if="current === 'profile'"
            />

            <Account
                :phone="this.data.account.phone"
                :country_code="this.data.account.country_code"
                :routes="this.routes.account"
                v-if="current === 'account'"
            />

            <Notifications
                :fill="this.data.notifications.fill"
                :routes="this.routes.notifications"
                v-if="current === 'notifications'"
            />

            <SharedFolders
                :routes="this.routes.sharedFolders"
                :folders="this.data.sharedFolders.folders"
                v-if="current === 'shared-folders'"
            />

            <Subscription
                :subscription="this.data.subscription.data"
                v-if="current === 'subscription'"
            />
        </div>
    </div>
</template>

<script>
    import Sidebar from '@/partials/settings/Sidebar';
    import Profile from '@/partials/settings/Profile';
    import Account from '@/partials/settings/Account';
    import Notifications from '@/partials/settings/Notifications';
    import SharedFolders from '@/partials/settings/SharedFolders';
    import Subscription from '@/partials/settings/Subscription';

    export default {
        props: ['data', 'routes'],

        components: {
            Sidebar,
            Profile,
            Account,
            Notifications,
            SharedFolders,
            Subscription,
        },

        data() {
            return {
                current: 'profile',
            }
        },

        created() {
            // Load default component from hash
            // --

            let hash = window.location.hash;

            if (! hash) {
                return;
            }

            hash = hash.replace('#', '');

            const component = ['profile', 'notifications', 'account', 'shared-folders', 'subscription'].find(ele => {
                return ele === hash;
            });

            if (component) {
                this.current = component;
            }
        }
    }
</script>
