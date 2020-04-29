<template>
    <div class="leading-none">
        <template v-if="! canResend">
            <small class="text-gray-700">You can request a new code after {{resendTimer}}.</small>
        </template>

        <template v-else>
            <small v-if="smsStatus === 'not-sent'">
                <button class="text-gray-900" @click="resend">Request a new code</button>
            </small>

            <small class="text-gray-700" v-if="smsStatus === 'sending'">Sending...</small>
            <small class="text-gray-700 text-red-600" v-if="smsStatus === 'error'">
                We were unable to delivery a new code
            </small>
            <small class="text-gray-700" v-if="smsStatus === 'sent'">A new code has been sent!</small>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['route'],

        data() {
            return {
                smsStatus: 'not-sent',
                canResend: false,
                timeToResend: 60,
            }
        },

        computed: {
            resendTimer() {
                return this.timeToResend + ' seconds';
            }
        },

        mounted() {
            let timer = setInterval(() => {
                if (this.timeToResend === 0) {
                    clearInterval(timer);
                    this.canResend = true;
                    return;
                }

                this.timeToResend--;
            }, 1000);
        },

        methods: {
            resend() {
                this.smsStatus = 'sending';

                axios.post(this.route)
                    .then(response => {
                        this.smsStatus = 'sent';
                    })
                    .catch(error => {
                        this.smsStatus = 'error';
                    })
                ;
            }
        }
    }
</script>
