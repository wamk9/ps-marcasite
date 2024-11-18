<template>
    <div>
      <p v-if="label" class="font-bold">{{ label }}</p>
        <select
            :class="inputClass"
            v-model="internalValue"
            @change="callChangeEvent"
            :disabled="disabled"
        >
            <option
                v-if="!!placeholder"
                value=""
                class=""
                disabled
                :selected="modelValue == ''"
                hidden
            >
                {{ placeholder }}
            </option>
            <option
                v-for="onlyOne in data"
                :value="onlyOne.value"
                :key="onlyOne.value"
                :selected="onlyOne.value == modelValue"
                class=""
            >
                {{ onlyOne.title }}
            </option>
        </select>
        <p :class="messageClass">
            {{ messageToShow }}
        </p>
    </div>
  </template>

  <script>
  export default {
    name: 'CustomSelect',
    props: {
      label: {
        type: String,
        default: ''
      },
      placeholder: {
        type: String,
        default: ''
      },
      data: {
        type: Array,
        default: [],
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
      callChangeEvent(event) {
        this.internalValue = event.target.value;
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
    },
  };
  </script>
