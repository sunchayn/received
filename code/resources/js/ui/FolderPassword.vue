<template>
    <div
        v-show="sharing"
        class="absolute border border-gray-200 shadow mt-2 right-0 bg-white p-5 z-20 max-w-full" style="min-width: 320px;"
    >
        <template v-if="! shared">
            <h1>Sharing a folder</h1>
            <p class="text-gray-700 text-sm leading-snug">Passwords are not saved in plain format, make sure you've copied it before saving.</p>
            <div class="form-group">
                <input ref="share-input" type="text" class="input" placeholder="password" v-model="password" />
                <small class="input-error" :class="{'is-visible' : error }">{{error}}</small>
            </div>

            <div class="flex">
                <button ref="shareButton" type="submit" class="button button--slim mr-2 flex-1" @click="shareFolder">
                    {{buttonText}}
                </button>
                <button type="submit" class="button button--outline button--slim flex-1" @click="sharing = false">Dismiss</button>
            </div>
        </template>

        <template v-else>
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-check w-12 mx-auto">
                    <circle cx="12" cy="12" r="10" class="fill-current text-green-200"/>
                    <path class="fill-current text-green-900" d="M10 14.59l6.3-6.3a1 1 0 0 1 1.4 1.42l-7 7a1 1 0 0 1-1.4 0l-3-3a1 1 0 0 1 1.4-1.42l2.3 2.3z"/>
                </svg>
                <h2 class="text-gray-700 leading-snug my-2">Public access created!</h2>
                <p class="text-gray-700 text-sm leading-snug">In order to start receiving files forward your public bucket URL and this folder password to the interested parties.</p>
                <button type="submit" class="button button--outline button--slim w-full" @click="sharing = false">Dismiss</button>
            </div>
        </template>
    </div>
</template>

<script>

    export default {
        props: {
            new: {
                required: false,
                default: true,
            },

            route: {
                required: true,
            },
        },

        data() {
            return {
                sharing: false,
                shared: false,
                password: '',
                error: null,
            }
        },

        watch: {
            sharing: function () {
                this.shared = false;
                this.error = null;
                this.password = '';

                if (this.sharing === true) {
                    this.$nextTick(() => {
                        this.$refs['share-input'].focus();
                    });
                }
            },
        },

        computed: {
            buttonText: function() {
                return this.new ? 'share' : 'change password';
            }
        },

        methods: {
            shareFolder() {
                this.$refs.shareButton.classList.add('is-submitting');

                axios.patch(this.route, {password: this.password})
                    .then(response => {
                        this.shared = true;
                        this.folder.is_shared = true;
                    })
                    .catch(error => {
                        this.$refs.shareButton.classList.remove('is-submitting');

                        if (error.response.status === 422) {
                            this.error = error.response.data.errors.password[0];
                        } else {
                            let message = error.response.data.message || 'Unable to save the password.';
                            this.errorNotification({message: message});
                        }
                    })
                ;
            },

            show() {
                this.sharing = true;
            }
        },

        notifications: {
            errorNotification: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },
        }
    }
</script>
