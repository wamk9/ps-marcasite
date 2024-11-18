<script setup>
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue';
import CourseDelete from '@/Pages/Admin/Course/Delete.vue';
import CourseSave from '@/Pages/Admin/Course/Save.vue';
import Api from '@/Helpers/Api/Api.js';
import Store from '@/Store';
</script>

<template>
    <Head title="Meus cursos" />

    <Modal :show="showDeleteModal" :closeable="true" @close="closeDeleteModal">
        <CourseDelete v-model:show="showDeleteModal" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase(filter)"/>
    </Modal>
    <Modal :show="showSaveModal" :closeable="true" @close="closeSaveModal">
        <CourseSave v-model:show="showSaveModal" :id="selectedId" @updateDatabase="updateDatabase(filter)"/>
    </Modal>


    <UserLayout id="user-mycourse-page">
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

            </div>
        </div>
        <table class="table-fixed w-full">
        <thead>
            <tr>
            <th>Nome</th>
            <th>Data de inscrição</th>
            <th>Status</th>
            <th>Valor Pago</th>
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
                    {{ course.registered_at }}
                </td>
                <td :class="course.status">
                    {{ course.status_message }}
                </td>
                <td>
                    {{ course.value_payed }}
                </td>
                <td class="flex flex-row">
                    <PrimaryButton
                        @click-ev="goToCourse(course.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                        :disabled="course.status != 'approved'"
                    >
                        <svg class="mx-auto" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/>
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
    </UserLayout>
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
            filter: null
        };
    },
    created() {
        this.updateDatabase();

        // const self = this;
        // this.tableOptions = {
        //     searching: false,
        //     lengthChange: false,
        //     pageLength: 30,
        //     language: {
        //         url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/pt-BR.json",
        //         info: "Mostrando destinos: _START_ ao _END_ (_TOTAL_ items ao todo)",
        //         infoEmpty: "Lista de destinos vazia (_TOTAL_ items ao todo)",
        //         infoFiltered: "(filtrado _MAX_ items ao todo)",
        //         entries: {
        //             _: 'destinos',
        //             1: 'destino'
        //         }
        //     },
        //     responsive: true,
        //     order: [[ 0, 'desc' ]],
        //     aoColumns: [
        //         {bSorteable: true},
        //         {bSorteable: true},
        //         {bSorteable: true},
        //         {bSorteable: false},
        //     ],
        //     columnDefs: [
        //         {
        //             width: '25%',
        //             targets: [ 0 ],
        //             data: 'name',
        //             title: 'Cidade',
        //         },
        //         {
        //             width: '25%',
        //             targets: [ 1 ],
        //             data: 'state',
        //             title: 'Estado',
        //         },
        //         {
        //             width: '25%',
        //             targets: [ 2 ],
        //             data: 'tour_qtd',
        //             title: 'Passeios',
        //         },
        //         {
        //             width: '25%',
        //             data: null,
        //             defaultContent: '',
        //             targets: [ 3 ],
        //             title: '',
        //             orderable: false,
        //             searchable: false,
        //             render: function ( data, type, row, meta ) {
        //                 const fontAwesomePenSvg = '<svg class="pen" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.3103 6.90307L17.1216 2.71339C16.9823 2.57406 16.8169 2.46354 16.6349 2.38814C16.4529 2.31274 16.2578 2.27393 16.0608 2.27393C15.8638 2.27393 15.6687 2.31274 15.4867 2.38814C15.3047 2.46354 15.1393 2.57406 15 2.71339L3.43969 14.2746C3.2998 14.4134 3.18889 14.5786 3.11341 14.7607C3.03792 14.9427 2.99938 15.1379 3.00001 15.3349V19.5246C3.00001 19.9225 3.15804 20.304 3.43935 20.5853C3.72065 20.8666 4.10218 21.0246 4.50001 21.0246H8.6897C8.88675 21.0253 9.08197 20.9867 9.26399 20.9112C9.44602 20.8358 9.61122 20.7248 9.75001 20.5849L21.3103 9.02464C21.4496 8.88534 21.5602 8.71997 21.6356 8.53796C21.711 8.35595 21.7498 8.16087 21.7498 7.96385C21.7498 7.76684 21.711 7.57176 21.6356 7.38975C21.5602 7.20774 21.4496 7.04237 21.3103 6.90307ZM8.6897 19.5246H4.50001V15.3349L12.75 7.08495L16.9397 11.2746L8.6897 19.5246ZM18 10.2134L13.8103 6.02464L16.0603 3.77464L20.25 7.96339L18 10.2134Z" fill="#171717"/></svg>';
        //                 const fontAwesomeTrashSvg = '<svg class="trash" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.25 4.52466H3.75C3.55109 4.52466 3.36032 4.60368 3.21967 4.74433C3.07902 4.88498 3 5.07575 3 5.27466C3 5.47357 3.07902 5.66434 3.21967 5.80499C3.36032 5.94564 3.55109 6.02466 3.75 6.02466H4.5V19.5247C4.5 19.9225 4.65804 20.304 4.93934 20.5853C5.22064 20.8666 5.60218 21.0247 6 21.0247H18C18.3978 21.0247 18.7794 20.8666 19.0607 20.5853C19.342 20.304 19.5 19.9225 19.5 19.5247V6.02466H20.25C20.4489 6.02466 20.6397 5.94564 20.7803 5.80499C20.921 5.66434 21 5.47357 21 5.27466C21 5.07575 20.921 4.88498 20.7803 4.74433C20.6397 4.60368 20.4489 4.52466 20.25 4.52466ZM18 19.5247H6V6.02466H18V19.5247ZM7.5 2.27466C7.5 2.07575 7.57902 1.88498 7.71967 1.74433C7.86032 1.60368 8.05109 1.52466 8.25 1.52466H15.75C15.9489 1.52466 16.1397 1.60368 16.2803 1.74433C16.421 1.88498 16.5 2.07575 16.5 2.27466C16.5 2.47357 16.421 2.66434 16.2803 2.80499C16.1397 2.94564 15.9489 3.02466 15.75 3.02466H8.25C8.05109 3.02466 7.86032 2.94564 7.71967 2.80499C7.57902 2.66434 7.5 2.47357 7.5 2.27466Z" fill="#171717"/></svg>';

        //                 return '<button class="btn-edit mx-2 float-right">' + fontAwesomePenSvg + '</button>' +
        //                 '<button class="btn-delete mx-2 float-right">' + fontAwesomeTrashSvg + '</button>';
        //             },
        //             createdCell(td, items, rowData, row, col) {
        //                 td.addEventListener('click', (e) => {
        //                     if (e.target.parentNode.classList.contains('btn-edit') || e.target.classList.contains('pen-icon'))
        //                         self.goToEditPage(rowData.id);
        //                     else if (e.target.parentNode.classList.contains('btn-delete') || e.target.classList.contains('trash-icon'))
        //                         self.openDeleteModal(rowData.id);
        //                 });
        //             }
        //         },
        //     ]
        // };
    },
    computed:{
        searchIcon() {
            return '<svg width="15" height="17" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>';
        }
    },
    methods: {
        updateDatabase(filter = null, page = null) {
            (async () => {
                const result = await Api.getAsync(route('api.user.my-course.index', { page: page, filter: filter}));

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
        goToCourse(courseId) {
            window.location.href = route('pages.user.course.show', {id : courseId});
        }
    },
    watch: {
        filter(newVal, oldVal) {
            this.updateDatabase(newVal);
        }
    }
}
</script>
