<template>
    <form action="#" @submit.prevent="submitHandler" id="js-code-form-hook">
        <div class="form-group">
            <small>A 6 digit code has been sent to the number you provided, kindly enter it below to verify your phone number.</small>
        </div>

        <div class="form-groups">
            <div class="form-group mb-0">
                <input
                    type="text"
                    class="input px-2"
                    name="code"
                    maxlength="6"
                    placeholder="# # # # # #"
                    v-model="entity.code"
                    @keypress="checkInput"
                >

                <div class="input-error"></div>
            </div>

            <div class="form-group mb-0">
                <input type="submit" value="Verify" class="button w-full uppercase shadow-none">
            </div>
        </div>

        <div v-if="this.routes.resend" class="mt-2">
            <template v-if="! canResend">
                <small class="text-gray-700" v-if="! smsResent">You can request a new SMS after {{resendTimer}}.</small>
            </template>

            <template v-else>
                <small v-if="smsStatus === 'not-sent'"><button class="text-gray-900" @click="resend">Request a new code</button></small>

                <small class="text-gray-700" v-if="smsStatus === 'sending'">Sending...</small>
                <small class="text-gray-700 text-red-600" v-if="smsStatus === 'error'">We were unable to delivery a new sms</small>
                <small class="text-gray-700" v-if="smsStatus === 'sent'">A new SMS has been sent!</small>
            </template>
        </div>
    </form>
</template>

<script>
    import submitForm from "../../mixins/submitForm";

    export default {
        mixins: [submitForm],

        data() {
            return {
                formHook: '#js-code-form-hook',
                smsStatus: 'not-sent',
                canResend: false,
                timeToResend: 60,
                entity: {
                    code: '',
                }
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

        computed: {
            resendTimer() {
                return this.timeToResend + ' seconds';
            }
        },

        methods: {
            checkInput(evt) {
                evt = (evt) ? evt : window.event;
                const charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) || charCode === 46) {
                    evt.preventDefault();
                } else {
                    return true;
                }
            },

            successCallback() {
                this.$emit('verified', true);
            },

            resend() {
                this.smsStatus = 'sending';

                axios.post(this.routes.resend)
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
