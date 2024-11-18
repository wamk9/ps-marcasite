<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
</script>

<template>
    <div v-for="(item, index) in items" :key="index">
        <div v-if="item.name !== null" class="mt-5">
            <label>
                {{ item.name }}
            </label>
            <PrimaryButton class="ml-2" @click="removeInput(index)" :disabled="disabled">-</PrimaryButton>
        </div>
        <div v-else class="mt-5">
            <label :for="inputFileElementId" :class="inputClass">
                Upload
            </label>
            <input class="hidden" type="file" :id="inputFileElementId" :accept="accept" @change="uploadByButton($event, index)" :disabled="disabled" />
            <PrimaryButton class="ml-2" @click="addInput" :disabled="disabled">+</PrimaryButton>
        </div>
    </div>
    <div class="mt-5">
        <label :for="inputFileElementId" :class="inputClass">
            Upload
        </label>
        <input class="hidden" type="file" :id="inputFileElementId" :accept="accept" @change="uploadByButton($event)" :disabled="disabled" />
        <PrimaryButton class="ml-2" :class="inputClass" @click="addInput" :disabled="disabled">+</PrimaryButton>
    </div>
</template>

<script>
export default {
    props: {
        modelValue: {
            type: Array,
            default: () => [] // Ensures `modelValue` is an array by default
        },
        id: {
            type: [Number, String],
            default: () => Math.floor(Math.random() * 9999)
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
    data() {
        return {
            items: this.modelValue,
            removedItems: [],
            cacheTimestamp: Date.now()
        };
    },
    created() {
    },
    computed: {
        inputFileElementId() {
            return `${this.id}_file_input`;
        },
        inputClass() {
            const baseClasses = 'inline-flex justify-center items-center btn-marcasite rounded-md font-bold focus:outline-none focus:ring-2 cursor-pointer';
            const disabledClasses = this.disabled ? 'opacity-50 cursor-not-allowed' : '';
            return [baseClasses, 'px-4 py-2', disabledClasses].join(' ');
        }
    },
    methods: {
        onInput(event) {
            this.$emit('update:modelValue', event.target.value);
        },
        uploadByButton(event, index) {
            if (this.disabled) return;
            [...event.target.files].forEach((file) => this.convertToBase64(file, index));
        },
        convertToBase64(file, index) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = () => {
                const fileData = {
                    name: file.name,
                    data: reader.result,
                    new_file: true,
                    remove: false
                };
                if (typeof index === 'undefined') {
                    this.items.push(fileData);
                } else {
                    this.$set(this.items, index, { ...this.items[index], ...fileData });
                }
                this.$emit('update:modelValue', [...this.items, ...this.removedItems]);
            };
            reader.onerror = (error) => {
                console.error('Error reading file:', error);
            };
        },
        addInput() {
            this.items.push({
                name: null,
                data: null,
                new_file: true,
                remove: false
            });
        },
        removeInput(index) {
            if (!this.items[index].new_file) {
                this.items[index].remove = true;
                this.removedItems.push(this.items[index]);
            }
            this.items.splice(index, 1); // Properly remove the item at `index`
        }
    },
    watch: {
        modelValue(newValue) {
            if (this.items.length == 0) {
                this.items = Array.isArray(newValue) ? [...newValue] : [];
            }
        }
    }
};
</script>
