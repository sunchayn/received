<template>
    <div class="form-group" id="css-overpower">
        <VueTelInput
            :enabledCountryCode="true"
            :validCharactersOnly="true"
            placeholder="Enter your phone number"
            v-model="phone_number"
            @onInput="setPhoneNumber"
            @country-changed="entity.country_code = $event.dialCode"
        />

        <input type="hidden" name="phone_number" v-model="entity.phone_number">
        <input type="hidden" name="country_code" v-model="entity.country_code">
        <input type="hidden" name="country" v-model="entity.country">
    </div>
</template>

<script>
    import { VueTelInput } from 'vue-tel-input'
    import AppInput from "../ui/AppInput";

    export default {
        components: {
            VueTelInput,
        },

        data() {
            return {
                value: '',

                entity: {
                    phone_number: '',
                    country_code: '',
                    country: '',
                },

                phone_number: '',
            }
        },

        methods: {
            setPhoneNumber(data) {
                this.entity.country_code = data.country.dialCode;
                this.entity.phone_number = data.number.significant;
                this.entity.country = data.country.iso2;
            },
        }
    }
</script>
