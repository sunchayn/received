<template>
    <form action="#" @submit.prevent="submitHandler" id="js-code-form-hook">
        <div class="form-group">
            <small>A 6 digit code has been sent to your number, kindly enter it below to verify your identity.</small>
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

            <div class="form-group flex mb-0">
                <input type="submit" value="Verify" class="button shadow-none w-full uppercase">
            </div>
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
                entity: {
                    code: '',
                }
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
        }
    }
</script>
