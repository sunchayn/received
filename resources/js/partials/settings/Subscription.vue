<template>
    <fragment>
        <h2>Subscription</h2>
        <hr class="my-2">

        <small>current plan</small>

        <template v-if="subscription">
            <div class="leading-snug py-2">
                <h1 class="mb-0">{{subscription.plan.title}}</h1>
                <p class="mb-0">{{subscription.plan.description}}</p>
                <p class="text-sm text-gray-700">subscribed at: {{subscription.plan.created_at}}</p>
            </div>
        </template>
        <template v-else>
            <h1>n/a</h1>
        </template>
    </fragment>
</template>

<script>
    export default {
        props: ['subscription'],
        data() {
            return {}
        }
    }
</script>
