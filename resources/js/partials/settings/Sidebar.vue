<template>
    <aside class="settings-sidebar w-full mb-6 md:mb-0 md:w-1/4">

        <div class="flex items-center justify-between md:hidden">
            <h2 class="mb-0">Settings</h2>
            <div class="button button--outline button--slim" @click="active=true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-cog w-6"><path class="primary" d="M6.8 3.45c.87-.52 1.82-.92 2.83-1.17a2.5 2.5 0 0 0 4.74 0c1.01.25 1.96.65 2.82 1.17a2.5 2.5 0 0 0 3.36 3.36c.52.86.92 1.8 1.17 2.82a2.5 2.5 0 0 0 0 4.74c-.25 1.01-.65 1.96-1.17 2.82a2.5 2.5 0 0 0-3.36 3.36c-.86.52-1.8.92-2.82 1.17a2.5 2.5 0 0 0-4.74 0c-1.01-.25-1.96-.65-2.82-1.17a2.5 2.5 0 0 0-3.36-3.36 9.94 9.94 0 0 1-1.17-2.82 2.5 2.5 0 0 0 0-4.74c.25-1.01.65-1.96 1.17-2.82a2.5 2.5 0 0 0 3.36-3.36zM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/><circle cx="12" cy="12" r="2" class="secondary"/></svg>
            </div>
        </div>

        <nav class="settings-sidebar_nav" :class="{'is-active': active}">
            <button class="settings-sidebar_item ml-auto md:hidden" @click="active = false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-close w-6"><path class="secondary" fill-rule="evenodd" d="M15.78 14.36a1 1 0 0 1-1.42 1.42l-2.82-2.83-2.83 2.83a1 1 0 1 1-1.42-1.42l2.83-2.82L7.3 8.7a1 1 0 0 1 1.42-1.42l2.83 2.83 2.82-2.83a1 1 0 0 1 1.42 1.42l-2.83 2.83 2.83 2.82z"/></svg>
            </button>

            <a
                href="#profile"
                class="settings-sidebar_item"
                :class="{'is-active': current === 'profile'}"
                @click="active=false,$emit('update:current', 'profile')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 mr-2 icon-user"><path class="primary" d="M12 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/><path class="secondary" d="M21 20v-1a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v1c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2z"/></svg>
                Profile
            </a>

            <a
                href="#account"
                class="settings-sidebar_item"
                :class="{'is-active': current === 'account'}"
                @click="active=false,$emit('update:current', 'account')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-lock w-6 mr-2"><g><path class="secondary" d="M12 10v3a2 2 0 0 0-1 3.73V18a1 1 0 0 0 1 1v3H5a2 2 0 0 1-2-2v-8c0-1.1.9-2 2-2h7z"/><path class="primary" d="M12 19a1 1 0 0 0 1-1v-1.27A2 2 0 0 0 12 13v-3h3V7a3 3 0 0 0-6 0v3H7V7a5 5 0 1 1 10 0v3h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-7v-3z"/></g></svg>
                Account
            </a>

            <a
                href="#notifications"
                class="settings-sidebar_item"
                :class="{'is-active': current === 'notifications'}"
                @click="active=false,$emit('update:current', 'notifications')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-notification w-6 mr-2"><circle cx="12" cy="19" r="3" class="secondary"/><path class="primary" d="M10.02 4.28L10 4a2 2 0 1 1 3.98.28A7 7 0 0 1 19 11v5a1 1 0 0 0 1 1 1 1 0 0 1 0 2H4a1 1 0 0 1 0-2 1 1 0 0 0 1-1v-5a7 7 0 0 1 5.02-6.72z"/></svg>
                Notifications
            </a>

            <a
                href="#shared-folders"
                class="settings-sidebar_item"
                :class="{'is-active': current === 'shared-folders'}"
                @click="active=false,$emit('update:current', 'shared-folders')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-inbox-upload w-6 mr-2"><path class="primary" d="M8 4a1 1 0 0 1-1 1H5v10h2a2 2 0 0 1 2 2c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2c0-1.1.9-2 2-2h2V5h-2a1 1 0 0 1 0-2h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h2a1 1 0 0 1 1 1z"/><path class="secondary" d="M11 6.41V13a1 1 0 0 0 2 0V6.41l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42L11 6.4z"/></svg>
                Shared folders
            </a>
            <a
                href="#subscription"
                class="settings-sidebar_item"
                :class="{'is-active': current === 'subscription'}"
                @click="active=false,$emit('update:current', 'subscription')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-identification w-6 mr-2"><path class="primary" d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2zm13 3a1 1 0 0 0 0 2h2a1 1 0 0 0 0-2h-2zm-2 4a1 1 0 0 0 0 2h4a1 1 0 0 0 0-2h-4zm1 4a1 1 0 0 0 0 2h3a1 1 0 0 0 0-2h-3z"/><path class="secondary" d="M8 12a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm-2 2h4a2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1c0-1.1.9-2 2-2z"/></svg>
                Subscription
            </a>
        </nav>
    </aside>
</template>

<script>
    export default {
        props: ['current'],

        data() {
            return {
                active: false,
            }
        }
    }
</script>
