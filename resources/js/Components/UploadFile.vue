<template>
    <div>
        <label :for="inputFileElementId" :class="inputClass">
            Upload
        </label>
        <input class="hidden"  type="file" :id="inputFileElementId" :accept="accept" @change="uploadByButton($event)" :disabled="disabled"/>
    </div>
</template>

<script>
export default {
    props: {
      modelValue: {
        type: String,
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
        inputClass() {
            const baseClasses = 'inline-flex justify-center items-center btn-marcasite rounded-md font-bold focus:outline-none focus:ring-2 cursor-pointer';
            const disabledClasses = this.disabled ? 'opacity-50 cursor-not-allowed' : '';

            return [
                baseClasses,
                'px-4 py-2',
                disabledClasses,
                this.colorClass
            ].join(' ');
        }
    },
    methods: {
        onInput(event) {
            this.$emit('update:modelValue', event.target.value);
        },
        uploadByButton(event) {
            if (this.disabled) {
                return;
            }

            [...event.target.files].forEach(this.convertToBase64);
        },
        convertToBase64(file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);

            reader.onloadend = () => {
                this.$emit('update:modelValue', reader.result);
            };

            reader.onerror = (error) => {
                console.error(error);
            };
        },
    },
  }
</script>
