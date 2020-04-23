import FormHandler from '../utils/FormHandler';

Vue.config.optionMergeStrategies.notifications = function (toVal, fromVal) {
    return {...fromVal, ...toVal};
};

export default {
    props: {
        routes: Object,
        fill: Object,
        preErrors: {
            required: false,
        },
    },

    data() {
        return {
            form: null,
            error: false,
            loading: false,
            formHook: '#js-form-hook',
            verb: 'post',
        }
    },

    mounted() {
        this.form = new FormHandler(this.formHook);

        if (this.fill) {
            this.entity = _.clone(this.fill);
        } else if (this.skeleton) {
            this.entity = _.clone(this.skeleton);
        }

        if (this.preErrors) {
            this.form.showErrors({errors: this.preErrors});
        }
    },

    methods: {
        submitHandler() {
            // Additional code to run before submitting the form
            if (typeof this.beforeSubmit  == 'function') {
                this.beforeSubmit();
            }

            const data = (typeof this.sanitizeData  == 'function') ? this.sanitizeData(this.entity) : this.entity;

            this.form.data.submit.classList.add('is-submitting');

            axios({method: this.verb, url: this.routes.submit, data})
                .then(response => {
                    this.form.resetErrors();

                    // Wherever to redirect or not
                    if (response.data.redirect) {
                        window.location.href = response.data.redirect;
                    }

                    // Wherever to reload or not
                    if (response.data.reload) {
                        window.location.reload();
                    }

                    // Show a success notification if exists
                    if (typeof this.successNotification === 'function') {
                        this.successNotification();
                    }

                    // Run success callback
                    if (typeof this.successCallback === 'function') {
                        this.successCallback(response.data);
                    }

                    // Reset the entity data
                    if (this.skeleton) {
                        this.entity = _.clone(this.skeleton);
                    }
                })
                .catch(error => {
                    console.log(error);

                    this.form.resetErrors();

                    if (error.response && error.response.status == 422) {
                        // Alter the errors array if needed
                        if (typeof this.sanitizeErrors === 'function') {
                            this.sanitizeErrors(error.response);
                        }

                        this.form.showErrors(error.response.data);

                        // Show an error notification is exists
                        if (typeof this.validationNotification === 'function') {
                            this.validationNotification();
                        }
                    } else {
                        const message = error.response.data.message || 'Something went wrong.';

                        // Show an error notification is exists
                        this.somethingWentWrongNotification({message: message});
                    }
                })
                .finally(() => {
                    this.form.data.submit.classList.remove('is-submitting');
                })
            ;
        },

        reloadForm() {
            this.form.reload();
        },
    },

    notifications: {
        somethingWentWrongNotification: {
            type: 'error',
            title: 'Error',
            message: 'Something went wrong.'
        },
    }
}
