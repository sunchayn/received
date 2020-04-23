class FormHandler {
    constructor(selector, options = {}) {
        const form = document.querySelector(selector);

        this.init(form);
    }

    reload() {
        this.data.inputs = this.getInputs(this.data.node);
    }

    getInputs(form) {
        const inputs = {};
        const inputsNodeList = form.querySelectorAll('.input, .js-input');

        Array.from(inputsNodeList).forEach(ele => {
            const groupNode = this._getGroupNode(ele.parentNode);
            const attribute = ele.hasAttribute('name') ? 'name' : 'data-name';

            inputs[ele.getAttribute(attribute)] = {
                node: ele,
                groupNode: groupNode,
                errorsHolder: groupNode.querySelector('.input-error'),
            };
        });

        return inputs;
    }

    init(form) {
        if (!form) {
            return true;
        }

        const submitButton = form.querySelector('[type="submit"]');
        const inputs = this.getInputs(form);
        const error = form.querySelector('#js-error-holder');

        this.data = {
            node: form,
            inputs: inputs,
            submit: submitButton,
            error,
        };
    }

    _getGroupNode(node) {
        if (typeof node === "undefined") {
            return null;
        }

        if (node.classList.contains('form-group') || node.classList.contains('js-form-group')) {
            return node;
        }

        return this._getGroupNode(node.parentNode);
    }

    getData() {
        return new FormData(this.data.node);
    }

    showErrors(errors) {
        this.resetErrors();

        if (errors !== undefined) {
            for(const error in errors) {
                const input = this.data.inputs[error];

                if (input) {
                    input.groupNode.classList.add('is-error');
                    if (input.errorsHolder && typeof errors[error] !== 'boolean') {
                        input.errorsHolder.innerHTML = errors[error];
                    }
                }
            }
        } else {
            if (this.data.error) {
                this.data.error.innerHTML = response.data.message;
                this.data.error.classList.add('is-visible')
            }
        }

    }

    resetErrors() {
        Object.values(this.data.inputs).forEach(input => {
            input.groupNode.classList.remove('is-error');
        });

        if (this.data.error) {
            this.data.error.classList.remove('is-visible')
        }
    }
}

export default FormHandler;
