<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Api from '@/Helpers/Api/Api.js'
</script>

<template>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Deletar curso
                </h2>

                <p class="mt-3">Você está prestes a deletar o curso '{{ name }}', deseja continuar?</p>
                <p class="mt-3">Atenção! Está é uma ação irreversível.</p>

                <hr class="my-3" style="border-color: #E5E5E5;">
                <div class="text-right" style="">
                    <PrimaryButton class="mr-2" @click-ev="() => { $emit('update:show', false) }">Cancelar</PrimaryButton>
                    <PrimaryButton class="ml-2" @click-ev="deleteFunction">Deletar</PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        id: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            form: {
                id: '',
            },
        };
    },
    methods: {
        updateItem(e, attr) {
            this.form[attr] = e.target.value;
            this.$emit('update:item', this.form);
        },
        updateShow(e) {
            this.$emit('update:show', e.target.value)
        },
        deleteFunction() {
            (async () => {
                const result = await Api.deleteAsync(route('api.admin.course.delete', this.id));

                if (result.code == 200) {
                    this.$emit('updateDatabase');
                    this.$emit('update:show', false)
                }
            })();
        },
    }
}
</script>
