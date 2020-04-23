<template>
    <fragment>

        <form @submit.prevent="submitHandler" id="js-form-hook" v-show="! verification">
            <div class="form-group" id="css-overpower">
                <div class="js-input" data-name="phone_number">
                    <VueTelInput
                        :enabledCountryCode="true"
                        :validCharactersOnly="true"
                        :defaultCountry="country"
                        placeholder="Enter your phone number"
                        v-model="phone_number"
                        @onInput="setPhoneNumber"
                        @country-changed="entity.country_code = $event.dialCode"
                    />

                    <div class="input-error"></div>
                </div>
            </div>

            <div class="form-group">
                <app-input
                    :label="false"
                    v-model="entity.password"
                    name="password"
                    type="password"
                    placeholder="*******"
                />
            </div>

            <input type="submit" value="Sign up" class="button w-full uppercase">
        </form>

        <Verify
            v-show="verification"
            :routes="this.routes"
        />

    </fragment>
</template>

<script>
    import submitForm from "../../mixins/submitForm";
    import { VueTelInput } from 'vue-tel-input'
    import AppInput from "../../ui/AppInput";

    export default {
        props: ['oldPhone', 'country'],

        mixins: [submitForm],

        components: {
            AppInput,
            VueTelInput,
        },

        data() {
            return {
                verification: false,
                skeleton: {
                    phone_number: '',
                    country_code: '',
                    password: '',
                },

                entity: {},

                code: '',
                verification_route: '',

                phone_number: '',
            }
        },

        mounted() {
            if (this.oldPhone) {
                this.entity.phone_number = this.oldPhone;
                this.phone_number = this.oldPhone;
            }
        },

        methods: {
            setPhoneNumber(data) {
                this.entity.country_code = data.country.dialCode;
                this.entity.phone_number = data.number.significant;
            },

            successCallback(data) {
                this.verification = true;
                this.routes.submit = data.route;
            },
        },

        notifications: {
            validationNotification: {
                type: 'error',
                title: 'Unable to continue',
                message: 'Kindly fix the mentioned errors.'
            }
        }
    }
</script>
