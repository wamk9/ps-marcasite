<script setup>
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue';
import CourseDelete from '@/Pages/Admin/Course/Delete.vue';
import CourseSave from '@/Pages/Admin/Course/Save.vue';
import CourseRegistered from '@/Pages/Admin/Course/Registered.vue';
import Api from '@/Helpers/Api/Api.js';
import Store from '@/Store';
</script>

<template>
    <Head title="Configurações" />

    <AdminLayout id="admin-config-page">
        <div class="grid grid-cols-1 gap-4 items-center">
            <div>
                <h1 class="font-bold text-xl">Configurações</h1>
                <p class="my-5">
                    Para realizar a configuração do sistema e permitir a geração de pagamentos via MercadoPago, você deve preencher a chave pública e privada abaixo:
                </p>
                <CustomInput v-model="form.mpPublicKey"
                    label="Chave pública (MercadoPago)"
                    type="text"

                    :disabled="receivingfromApi"
                    placeholder=""
                />
                <CustomInput v-model="form.mpPrivateKey"
                    label="Chave privada (MercadoPago)"
                    type="text"
                    :disabled="receivingfromApi"
                    placeholder=""
                />
            </div>
            <div>
                <PrimaryButton
                    @click-ev="saveConfig()"
                    class="float-right w-full lg:w-auto"
                    :disabled="receivingfromApi">
                    Salvar configurações
                </PrimaryButton>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
export default {
    props: {
    },
    data() {
        return {
            form: {
                mpPublicKey: '',
                mpPrivateKey: ''
            },
            showDeleteModal: false,
            showSaveModal: false,
            selectedId: null,
            selectedName: null,
            destinationItems: [],
            searchText: '',
            receivingfromApi: false,
            courses: [],
            currentPage: 1,
            links: [],
            filter: null,
            showUsersModal: false,
        };
    },
    created() {
        this.updateDatabase();
    },
    computed:{
        searchIcon() {
            return '<svg width="15" height="17" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>';
        }
    },
    methods: {
        updateDatabase() {
            (async () => {
                const result = await Api.getAsync(route('api.admin.config.index'));

                if (result.code == 200) {
                    this.form.mpPrivateKey = result.response.message.MP_PRIVATE_TOKEN;
                    this.form.mpPublicKey = result.response.message.MP_PUBLIC_TOKEN;
                }
            })();
        },
        async saveConfig()
        {
            this.onComplete = false;

            const dataToSend = {
                MP_PRIVATE_TOKEN: this.form.mpPrivateKey,
                MP_PUBLIC_TOKEN: this.form.mpPublicKey,
            }

            const result = await Api.putAsync(route('api.admin.config.update'), dataToSend);

            if (result.code == 200) {
                window.location.reload();
            }

            this.onComplete = true;


        },
        openSaveModal(selectedId) {
            const user = this.courses.find((obj) => obj.id == selectedId );
            this.selectedId = selectedId;
            this.showSaveModal = true;
        },
        closeSaveModal() {
            this.showSaveModal = false;
        },
        openDeleteModal(selectedId) {
            const user = this.courses.find((obj) => obj.id == selectedId );
            this.selectedName = !!user ? user.name : null;
            this.selectedId = selectedId;
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.selectedName = null;
            this.selectedId = null;
        },
        goToPage(page) {
            this.updateDatabase(this.filter, page);
        },
        openUsersModal(courseId) {
            this.selectedId = courseId;
            this.showUsersModal = true;
        },
        closeUsersModal() {
            this.selectedId = null;
            this.showUsersModal = false;
        }
    },
    watch: {
        filter(newVal, oldVal) {
            this.updateDatabase(newVal);
        }
    }
}
</script>
