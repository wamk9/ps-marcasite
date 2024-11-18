<template>
    <div>
      <p v-if="label" class="font-bold">{{ label }}</p>
      <div class="relative">
        <input
          :type="type"
          :class="inputClass"
          :placeholder="placeholder"
          v-model="internalValue"
          @input="callInputOut"
          :disabled="disabled"
        />
        <div v-if="iconCssClass || iconSvg" :class="iconContainerClass">
          <i v-if="iconCssClass" :class="iconClass"></i>
          <span v-else-if="iconSvg" v-html="iconSvg"></span>
        </div>
      </div>
      <p :class="messageClass">
        {{ messageToShow }}
      </p>
    </div>
  </template>

  <script>
  export default {
    name: 'CustomInput',
    props: {
      label: String,
      placeholder: String,
      iconCssClass: String,
      iconSvg: String,
      iconPosition: {
        type: String,
        default: 'right',
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
      messageToShow() {
        return typeof this.successMessage == 'string' ? this.successMessage :
            typeof this.errorMessage == 'string' ? this.errorMessage : '';
      },
      messageClass() {
        return [
          'text-sm input-marcasite-message mt-2',
          this.successMessage ? 'success' : 'error',
        ].join(' ');
      },
      iconClass() {
        return `${this.iconCssClass} text-gray-400`;
      },
      inputClass() {
        const statusClass = this.successMessage && !this.errorMessage
          ? 'ring-2 success'
          : this.errorMessage && !this.successMessage
          ? 'ring-2 error'
          : '';

        const padding = this.iconCssClass || this.iconSvg
          ? this.iconPosition === 'right'
            ? 'pl-10 pr-4'
            : 'pl-4 pr-10'
          : 'pl-4 pr-4';

        const disabledClasses = this.disabled ? 'opacity-50 cursor-not-allowed' : '';

        return [
          padding,
          statusClass,
          disabledClasses,
          'py-2 border rounded-lg w-full input-marcasite',
        ].join(' ');
      },
      iconContainerClass() {
        return [
          this.iconPosition === 'right' ? 'left-0 pl-4' : 'right-0 pr-4',
          'absolute inset-y-0 flex items-center pointer-events-none',
        ].join(' ');
      },
    },
  };
  </script>
