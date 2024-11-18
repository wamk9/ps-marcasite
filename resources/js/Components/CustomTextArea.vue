<template>
    <div>
      <p v-if="label" class="font-bold">{{ label }}</p>
      <div class="relative">
        <textarea
            ref="textarea"
            :class="inputClass"
            :placeholder="placeholder"
            v-model="internalValue"
            @input="callInputOut"
            :maxlength="limit"
            :disabled="disabled"
        />

      </div>
      <p v-if="limit > 0" class="text-sm textarea-marcasite-message mt-2">
        {{ messageToShow }}
        <span v-if="!!errorMessage" :class="messageClass"> ({{ errorMessage }})</span>
      </p>
      <span v-else-if="!!errorMessage" :class="messageClass"> {{ errorMessage }}</span>
    </div>
  </template>

  <script>
  export default {
    name: 'CustomTextArea',
    props: {
      label:  {
        type: String,
        default: '',
      },
      placeholder: {
        type: String,
        default: '',
      },
      limit: {
        type: Number,
        default: null,
      },
      type: {
        type: String,
        default: 'text',
      },
      modelValue: {
        type: String,
      },
      successMessage: {
        type: [String, Boolean],
        default: false
      },
      errorMessage:  {
        type: [String, Boolean],
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        internalValue: this.modelValue,
      };
    },
    watch: {
      modelValue(newVal) {
        this.internalValue = newVal;
      },
      internalValue(newVal) {
        this.$emit('update:modelValue', newVal);
      },
    },
    methods: {
      callInputOut(event) {
        this.$emit('checkValue', event.target.value);
      },
    },
    computed: {
        messageClass() {
        return [
          'text-sm textarea-marcasite-message',
          this.successMessage ? 'success' : 'error',
        ].join(' ');
      },
      messageToShow() {
        const actualLength = (this.internalValue != null ? this.internalValue.length : 0);
        return actualLength + ' / ' + this.limit;
      },
      inputClass() {
        const statusClass = this.successMessage && !this.errorMessage
          ? 'ring-2 success'
          : this.errorMessage && !this.successMessage
          ? 'ring-2 error'
          : '';

        const disabledClasses = this.disabled ? 'opacity-50 cursor-not-allowed' : '';

        return [
          statusClass,
          disabledClasses,
          'text-sm textarea-marcasite resize-none rounded-md p-4',
        ].join(' ');
      },
    },
  };
  </script>
