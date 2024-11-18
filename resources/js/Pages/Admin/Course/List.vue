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
    <Head title="Cursos" />

    <Modal :show="showDeleteModal" :closeable="true" @close="closeDeleteModal">
        <CourseDelete v-model:show="showDeleteModal" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase(filter)"/>
    </Modal>
    <Modal :show="showUsersModal" :closeable="true" @close="closeUsersModal">
        <CourseRegistered v-model:show="showUsersModal" :courseId="selectedId" :name="selectedName" @updateDatabase="updateDatabase(filter)"/>
    </Modal>
    <Modal :show="showSaveModal" :closeable="true" @close="closeSaveModal">
        <CourseSave v-model:show="showSaveModal" :courseId="selectedId" @updateDatabase="updateDatabase(filter)"/>
    </Modal>


    <AdminLayout id="admin-course-page">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
            <div>
                <CustomInput v-model="filter"
                    :iconSvg="searchIcon"
                    iconPosition="right"
                    type="email"
                    :disabled="receivingfromApi"
                    placeholder=""
                />
            </div>
            <div>
                <PrimaryButton
                    @click-ev="openSaveModal(null)"
                    class="float-right w-full lg:w-auto"
                    :disabled="receivingfromApi">
                    Novo curso
                </PrimaryButton>
            </div>
        </div>
        <table class="table-fixed w-full">
        <thead>
            <tr>
            <th>Nome</th>
            <th>Valor</th>
            <th>Ativo</th>
            <th>Período de inscrição</th>
            <th>Vagas restantes</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="courses.length == 0">
                <td colspan="6">
                    Nenhum curso encontrado
                </td>
            </tr>
            <tr v-else v-for="course in courses">
                <td>
                    {{ course.name }}
                </td>
                <td>
                    {{ course.value }}
                </td>
                <td>
                    {{ course.active }}
                </td>
                <td>
                    {{ course.registration_period }}
                </td>
                <td>
                    {{ course.vacations_to_fill }}
                </td>
                <td class="flex flex-row">
                    <PrimaryButton
                        @click-ev="openUsersModal(course.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                    >
                        <svg class="mx-auto" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                            <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM609.3 512l-137.8 0c5.4-9.4 8.6-20.3 8.6-32l0-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2l61.4 0C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z"/>
                        </svg>
                    </PrimaryButton>
                    <PrimaryButton
                        @click-ev="openSaveModal(course.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                    >
                        <svg class="mx-auto" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/>
                        </svg>
                    </PrimaryButton>
                    <PrimaryButton
                        @click-ev="openDeleteModal(course.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                    >
                        <svg class="mx-auto" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                        </svg>
                    </PrimaryButton>
                </td>
            </tr>
        </tbody>
        </table>
        <div class="mt-10 text-center">
            <PrimaryButton
                v-for="page in links"
                @click-ev="goToPage(page.label)"
                class="aspect-square mx-1"
                :disabled="page.active"
            >
                {{ page.label }}
            </PrimaryButton>
        </div>
        <!-- <div class="flex justify-between items-center">
            <h1 class="text-3xl md:text-6xl font-bold">
                Destinos
            </h1>
            <PrimaryButton
            @click-ev="goToCreatePage"
            class=""
            >
            Cadastrar destino
        </PrimaryButton>
    </div>

    <div id="container" class="rounded-md">
        <DataTable :options="tableOptions" ref="datatable" :data="destinationItems" class="display"/>
    </div> -->
    </AdminLayout>
</template>

<script>
export default {
    props: {
    },
    data() {
        return {
            form: {
                email: '',
                password: ''
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
        updateDatabase(filter = null, page = null) {
            (async () => {
                const result = await Api.getAsync(route('api.admin.course.index', { page: page, filter: filter}));

                if (result.code == 200) {
                    this.courses = result.response.message.data;
                    this.links = result.response.message.links.slice(1, -1);
                }
            })();
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
