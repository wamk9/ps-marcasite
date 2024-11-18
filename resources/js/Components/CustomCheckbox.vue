<template>
    <div>
        <div class="inline-flex items-center lg:mt-5">
            <label class="flex items-center relative" :class="disabledClasses" for="check-2">
                <input type="checkbox"
                    :v-model="checked"
                    :value="checked"
                    :checked="checked"
                    class="peer h-5 w-5 transition-all appearance-none rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800"
                    :class="disabledClasses"
                    id="check-2"
                    @change="onChangeProcessed"
                    :disabled="disabled"
                />
                <span class="absolute text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"
                        stroke="currentColor" stroke-width="1">
                        <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"></path>
                    </svg>
                </span>
            </label>
            <label class="ml-2 text-slate-600 text-sm" :class="disabledClasses" for="check-2">
                <slot/>
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: {
      modelValue: {
        type: Boolean,
      },
      id: {
        type:  [Number, String],
        default: Math.floor(Math.random() * 9999),
      },
      disabled: {
        type: Boolean,
        default: false
      },
      accept: {
        type: String,
        default: 'images/*'
      }
    },
    mounted() {
    },
    computed: {
        inputFileElementId() {
            return this.id + "_file_input";
        },
        disabledClasses() {
            return this.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
        },
    },
    methods: {
        onChangeProcessed(event, process) {
            this.checked = event.target.checked
            this.$emit('update:modelValue', this.checked);
        },
    },
    watch: {
        modelValue(newValue) {
            this.checked = newValue ?? false;
        }
    }
  }
</script>
