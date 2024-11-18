<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue'
import CustomSelect from '@/Components/CustomSelect.vue'
import CustomCheckbox from '@/Components/CustomCheckbox.vue'
import CustomTextArea from '@/Components/CustomTextArea.vue'
import UploadFile from '@/Components/UploadFile.vue'
import MultipleUploadFile from '@/Components/MultipleUploadFile.vue'
import Api from '@/Helpers/Api/Api.js'
import SystemVars from '@/Helpers/General/SystemVars.js'
import Store from '@/Store'
</script>

<template>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            Inscrições do curso
                        </h2>
                    </div>
                    <div>
                        <a href="#" v-on:click="$emit('update:show', false)" class="float-right">
                            x
                        </a>
                    </div>
                </div>


                <div class="grid grid-cols-1 mt-10">
                    <table class="table-fixed w-full">
                        <thead>
                            <tr>
                                <th>Inscrição Cód.</th>
                                <th>Nome do aluno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="users.length == 0">
                                <td colspan="2">
                                    Nenhum aluno inscrito encontrado
                                </td>
                            </tr>
                            <tr v-else v-for="user in users">
                                <td>
                                    {{ user.register_num }}
                                </td>
                                <td>
                                    {{ user.name }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr class="mt-10 mb-5">

                    <div class="flex flex-row-reverse">
                        <PrimaryButton
                            class="w-full mt-2 lg:ml-2 lg:w-auto"
                            @click-ev="exportExcel()"
                        >
                            Exportar para Excel
                        </PrimaryButton>

                        <PrimaryButton
                            class="w-full mt-2 lg:ml-2 lg:w-auto"
                            @click-ev="exportPdf()"
                        >
                            Exportar para PDF
                        </PrimaryButton>
                    </div>
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
        courseId: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            form: {
                id: '',
                name: '',
                category_id: '',
                value: '',
                vacations: '',
                registration_at: '',
                registration_until: '',
                description: '',
                active: false,
                thumb_image: '',
                files: [],

            },
            onComplete: false,
            users: [],
            cacheTimestamp: Date.now()
        };
    },
    async created() {
        const result = await Api.getAsync(route('api.admin.course.registered', { id: this.courseId }));

        if (result.code == 200) {
            this.users = result.response.message;
        }

        this.onComplete = true;
    },
    methods: {
        updateShow(e) {
            this.$emit('update:show', e.target.value)
        },
        async exportExcel() {
            this.onComplete = false;

            const result = await Api.getAsync(route('api.admin.course.export.excel', { id: this.courseId }));

            if (result.code == 200) {
                window.location.href = SystemVars.baseUrl + result.response.message;
            }

            this.onComplete = true;
        },
        async exportPdf() {
            this.onComplete = false;

            const result = await Api.getAsync(route('api.admin.course.export.pdf', { id: this.courseId }));

            if (result.code == 200) {
                window.location.href = SystemVars.baseUrl + result.response.message;
            }

            this.onComplete = true;
        }
    },
    computed: {
    }
}
</script>
