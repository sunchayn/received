<template>
    <fragment>
        <form @submit.prevent="submitHandler" id="js-form-hook">
            <div class="form-group" id="css-overpower">
                <div class="js-input" data-name="phone_number">
                    <VueTelInput
                        :enabledCountryCode="true"
                        :validCharactersOnly="true"
                        placeholder="Enter your phone number"
                        v-model="phone_number"
                        @onInput="setPhoneNumber"
                        @country-changed="entity.country_code = $event.dialCode"
                    />

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

                <small class="input-error" id="js-error-holder"></small>
            </div>


            <input type="submit" value="Sign in" class="button w-full uppercase">
        </form>
    </fragment>
</template>

<script>
    import submitForm from "../../mixins/submitForm";
    import { VueTelInput } from 'vue-tel-input'
    import AppInput from "../../ui/AppInput";

    export default {
        props: [],

        mixins: [submitForm],

        components: {
            AppInput,
            VueTelInput,
        },

        data() {
            return {
                skeleton: {
                    phone_number: '',
                    password: '',
                },

                entity: {},

                phone_number: '',
            }
        },

        methods: {
            setPhoneNumber(data) {
                this.entity.country_code = data.country.dialCode;
                this.entity.phone_number = data.number.significant;
            },
        },
    }
</script>
