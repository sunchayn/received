<!--suppress CheckTagEmptyBody -->
<template>
    <fragment>
        <label class="label"
               :class="{'mb-0': description}"
               :for="$data._id" v-if="label !== false">{{ $data._label | capitalize }}</label>

        <p class="text-xs text-gray-700 leading-snug" v-if="description">{{description}}</p>

        <input
            v-if="type != 'textarea'"
            v-bind="$attrs"
            :name="prefix ? $data._id + '_' + name : name"
            :type="type"
            class="input"
            :id="$data._id"
            :placeholder="$data._placeholder | capitalize"
            :value="value"
            @input="$emit('input', $event.target.value)"
        >

        <textarea
            v-else
            v-bind="$attrs"
            :name="prefix ? $data._id + '_' + name : name"
            :type="type"
            class="input"
            :id="$data._id"
            :placeholder="$data._placeholder | capitalize"
            :value="value"
            @input="$emit('input', $event.target.value)"
        ></textarea>
        <span class="input-error"></span>
    </fragment>
</template>

<script>
    export default {
        props: {
            name: String,

            value: {
                required: true,
            },

            label: {
                required: false,
            },

            description: {
                required: false,
            },

            placeholder: {
                required: false,
            },

            prefix: {
                required: false,
                default: false,
            },

            type: {
                default: 'text',
            },

            id: {
                required: false,
            }
        },

        data() {
            return {
                _label: this.label || this.name,
                _id: this.id || 'i' + Math.random().toString(36).substr(2, 9),
                _placeholder: this.placeholder || this.label || this.name,
            }
        },
    }
</script>
