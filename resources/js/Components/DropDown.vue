<template>
    <div class="relative">
        <div class="relative inline-block text-left h-full">
            <div class="h-full flex align-center">
                <button
                    type="button"
                    class="inline-flex w-full justify-center items-center gap-x-1.5 rounded-md px-3 py-2"
                    id="filter-button"
                    aria-expanded="false"
                    aria-haspopup="true"
                    v-on:click="toggleMenu"
                    :class="disabled ? 'opacity-50 cursor-not-allowed' : ''"
                    >
                    <slot/>
                </button>
            </div>

            <div ref="menu" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                role="menu" aria-orientation="vertical" aria-labelledby="filter-button" tabindex="-1" id="filter-dropdown">
                <div class="py-1" role="none">
                    <a class="inline-flex items-center px-4 py-2 text-sm text-gray-700" v-for="item in items" :href="item.link">
                        <span class="ml-2">{{ item.title }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
  </template>

  <script>
  export default {
    name: 'DropDown',
    props: {
      id:  {
        type: String,
        default: '',
      },
      disabled: {
        type: Boolean,
        default: false
      },
      items:{
        type: Array,
        default: []
      },
      pivot: {
        type: String,
        default: 'right'
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
      toggleMenu() {
        if (this.disabled) {
            return;
        }

        this.$refs.menu.classList.toggle("hidden");
      }
    },
    computed: {
    },
  };
  </script>
