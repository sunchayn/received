<template>
    <div class="card p-10 md:mx-12">
        <form @submit.prevent="unlock">
            <h1 class="text-2xl font-bold text-black w-2/3 mx-auto text-center">
                Kindly enter the password to unlock the bucket.
            </h1>

            <div class="form-group">
                <input type="text" placeholder="Password" class="input" v-model="password">
                <small class="input-error" :class="{'is-visible': error}">{{error}}</small>
            </div>

            <input ref="button" type="submit" value="unlock the bucket" class="button w-full">
        </form>
    </div>
</template>

<script>
    export default {
        props: ['routes'],

        data() {
            return {
                error: null,
                password: '',
            }
        },

        methods: {
            unlock() {
                this.error = null;
                this.$refs.button.classList.add('is-submitting');

                axios.post(this.routes.unlock, {password: this.password})
                    .then(response => {
                        this.$emit('unlocked', this.password, response.data.remainingStorage);
                    })
                    .catch(error => {
                        this.$refs.button.classList.remove('is-submitting');

                        if (error.response.status === 422) {
                            const carry = error.response.data.errors.password || error.response.data.errors.size;
                            this.error = carry[0];
                        } else {
                            let message = error.response.data.message || 'Something went wrong';
                            this.errorNotification({message: message});
                        }
                    })
                ;
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

