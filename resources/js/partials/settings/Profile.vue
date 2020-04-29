<template>
    <fragment>
        <h2>Profile settings</h2>
        <hr class="my-2">
        <div class="form-group">
            <AppInput
                label="username"
                placeholder="Username"
                description="Usernames are what identify all your buckets. Easy to remember usernames are recommended."
                v-model="usernameEntity.value"
            />
            <small class="input-error" :class="{'is-visible': usernameEntity.error}">{{usernameEntity.error}}</small>
            <small>{{url}}/<b>{{usernameEntity.value}}</b></small>
        </div>

        <div class="form-group">
            <button
                :disabled="usernameEntity.value === username"
                ref="usernameSaveButton" class="button button--secondary" @click.prevent="updateUsername"
            >Update username</button>
        </div>

        <form id="js-profile-form-hook" @submit.prevent="submitHandler">
            <div class="form-group">
                <AppInput
                    label="name"
                    name="name"
                    v-model="entity.name"
                />
            </div>

            <div class="form-group">
                <AppInput
                    label="email"
                    name="email"
                    placeholder="Email address"
                    description="You will received updates about your account on your email."
                    v-model="entity.email"
                />
            </div>

            <div class="form-group">
                <button type="submit" class="button button--secondary">Save</button>
            </div>
        </form>
    </fragment>
</template>

<script>
    import AppInput from '@/ui/AppInput';
    import submitForm from '@/mixins/submitForm';

    export default {
        props: ['routes', 'username', 'url'],

        mixins: [submitForm],

        components: {
            AppInput,
        },

        data() {
            return {
                usernameEntity: {
                    value:  this.username,
                    error: null,
                },

                entity: {
                    name: '',
                    email: '',
                },

                formHook: '#js-profile-form-hook',
                verb: 'patch',
            }
        },

        methods: {
            updateUsername() {
                this.$refs.usernameSaveButton.classList.add('is-submitting');

                axios.patch(this.routes.username, {username: this.usernameEntity.value})
                    .then(response => {
                        this.successNotification({
                            message: 'Username updated.',
                        });

                        this.username = this.usernameEntity.value;
                        this.usernameEntity.error = null;
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.usernameEntity.error = error.response.data.errors.username[0];
                        } else {
                            const message = error.response.data.message || 'Something went wrong';
                            this.errorNotification({message: message});
                        }
                    })
                    .finally(() => {
                        this.$refs.usernameSaveButton.classList.remove('is-submitting');
                    })
                ;
            }
        },

        notifications: {
            successNotification: {
                type: 'success',
                title: 'Success',
                message: ''
            },

            errorNotification: {
                type: 'error',
                title: 'Error',
                message: 'Something went wrong.'
            },
        }
    }
</script>
