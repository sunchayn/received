<template>
    <fragment>
        <h2>Account settings</h2>
        <hr class="my-2">

        <template v-if="! phoneNumber.is_verification">
            <div class="form-group" id="css-overpower">
                <label for="phone_number" class="label mb-0">Phone number</label>
                <p class="text-xs text-gray-700 leading-snug">2FA is enable by default hence a phone number is required.</p>
                <VueTelInput
                    ref="phone_number"
                    :key="key"
                    :enabledCountryCode="true"
                    :validCharactersOnly="true"
                    :defaultCountry="phoneNumber.country"
                    :disabledFetchingCountry="true"
                    placeholder="Enter your phone number"
                    v-model="phoneNumber.phone_number"
                    inputId="phone_number"
                    @onInput="setPhoneNumber"
                    @country-changed="phoneNumber.country_code = $event.dialCode"
                />
                <small class="input-error" :class="{'is-visible': phoneNumber.error}">{{phoneNumber.error}}</small>
            </div>

            <div class="form-group">
                <button :disabled="sameNumber" ref="phoneNumberChangingButton" type="submit" class="button button--secondary" @click.prevent="updatePhone">Change phone number</button>
            </div>
        </template>

        <Verify
            v-if="phoneNumber.is_verification"
            :routes="{
                submit: phoneNumber.verification_route
            }"
            @verified="verified"
        />

        <hr class="my-4">

        <h2>Change Password</h2>
        <form id="js-account-form-hook" @submit.prevent="submitHandler">
            <div class="form-group">
                <AppInput
                    label="Current password"
                    name="current"
                    v-model="entity.current"
                    type="password"
                />
            </div>

            <div class="form-groups">
                <div class="form-group">
                    <AppInput
                        label="New password"
                        name="password"
                        v-model="entity.password"
                        type="password"
                    />
                </div>

                <div class="form-group">
                    <AppInput
                        label="Confirmation"
                        name="confirmation"
                        v-model="entity.confirmation"
                        type="password"
                    />
                </div>

            </div>

            <div class="form-group">
                <button type="submit" class="button button--secondary">Update password</button>
            </div>
        </form>
    </fragment>
</template>

<script>
    import AppInput from '@/ui/AppInput';
    import submitForm from '@/mixins/submitForm';
    import { VueTelInput } from 'vue-tel-input'

    export default {
        props: ['routes', 'phone', 'country_code'],

        mixins: [submitForm],

        components: {
            AppInput,
            VueTelInput
        },

        data() {
            return {
                key: 0,
                phoneNumber: {
                    phone_number: this.phone,
                    country_code: this.country_code,
                    error: null,
                    is_verification: false,
                    country: '',
                },

                skeleton: {
                    current: '',
                    password: '',
                    confirmation: '',
                },
                entity: {},

                formHook: '#js-account-form-hook',
                verb: 'patch',
            }
        },

        mounted() {
            const country = this.$refs.phone_number.allCountries.find(ele => {
                return ele.dialCode === this.phoneNumber.country_code;
            });

            this.phoneNumber.country = country.iso2 || '';
            this.key++;
        },

        computed: {
            sameNumber() {
                return this.phoneNumber.country_code === this.country_code && this.phoneNumber.phone_number === this.phone
            }
        },

        methods: {
            setPhoneNumber(data) {
                this.phoneNumber.country_code = data.country.dialCode;
                this.phoneNumber.phone_number = data.number.significant;
            },

            updatePhone() {
                this.$refs.phoneNumberChangingButton.classList.add('is-submitting');

                const data = {
                    'phone_number': this.phoneNumber.phone_number,
                    'country_code': this.phoneNumber.country_code,
                };

                axios.post(this.routes.change_phone, data)
                    .then(response => {
                        this.phoneNumber.verification_route = response.data.verification_route;
                        this.phoneNumber.is_verification = true;
                    })
                    .catch(error => {
                        this.$refs.phoneNumberChangingButton.classList.remove('is-submitting');

                        if (error.response.status === 422) {
                            this.phoneNumber.error = error.response.data.errors.phone_number[0];
                        } else {
                            const message = error.response.data.message || 'Something went wrong';
                            this.errorNotification({message: message});
                        }
                    })
                ;
            },

            verified() {
                this.country_code = this.phoneNumber.country_code;
                this.phone = this.phoneNumber.phone_number;
                this.phoneNumber.is_verification = false;
                this.phoneNumber.verification_route = '';

                this.verifiedNotification();
            }
        },

        notifications: {
            successNotification: {
                type: 'success',
                title: 'Success',
                message: 'Password changed!'
            },

            verifiedNotification: {
                type: 'success',
                title: 'Success',
                message: 'Your phone number has been verified.'
            },

            errorNotification: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },

            validationNotification: {
                type: 'error',
                title: 'Error',
                message: 'Kindly fix the mentioned errors!'
            },
        }
    }
</script>
