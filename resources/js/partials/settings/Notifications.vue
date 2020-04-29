<template>
    <fragment>
        <h2>Notifications preferences</h2>
        <hr class="my-2">

        <form id="js-notifications-form-hook" @submit.prevent="submitHandler">
            <div class="form-group">
                <label>
                    <input type="checkbox" v-model="entity.notify_by_sms" class="mr-2"> Notify by SMS
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" v-model="entity.notify_by_mail" class="mr-2"> Notify by mail
                </label>
            </div>


            <div class="form-group">
                <button type="submit" class="button button--secondary">Update preferences</button>
            </div>
        </form>
    </fragment>
</template>

<script>
    import submitForm from "@/mixins/submitForm";
    export default {
        mixins: [submitForm],

        data() {
            return {
                entity: {
                    notify_by_SMS: '',
                    notify_by_mail: '',
                },

                formHook: '#js-notifications-form-hook',
                verb: 'patch',
            }
        },

        notifications: {
            successNotification: {
                type: 'success',
                title: 'Success',
                message: 'Preferences updated!'
            },
        }
    }
</script>
