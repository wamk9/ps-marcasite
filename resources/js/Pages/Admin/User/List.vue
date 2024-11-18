<script setup>
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue';
import UserDelete from '@/Pages/Admin/User/Delete.vue';
import UserSave from '@/Pages/Admin/User/Save.vue';
import Api from '@/Helpers/Api/Api.js';
import Store from '@/Store';
</script>

<template>
    <Head title="Usuários" />

    <Modal :show="showDeleteModal" :closeable="true" @close="closeDeleteModal">
        <UserDelete v-model:show="showDeleteModal" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase(filter)"/>
    </Modal>
    <Modal :show="showSaveModal" :closeable="true" @close="closeSaveModal">
        <UserSave v-model:show="showSaveModal" :id="selectedId" @updateDatabase="updateDatabase(filter)"/>
    </Modal>


    <AdminLayout id="admin-user-page">
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
                    Novo usuário
                </PrimaryButton>
            </div>
        </div>
        <table class="table-fixed w-full">
        <thead>
            <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Ativo</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="users.length == 0">
                <td colspan="4">
                    Nenhum usuário cadastrado encontrado
                </td>
            </tr>
            <tr v-else v-for="user in users">
                <td>
                    {{ user.name }}
                </td>
                <td>
                    {{ user.role_name }}
                </td>
                <td>
                    {{ user.activated }}
                </td>
                <td class="flex flex-row">
                    <PrimaryButton
                        @click-ev="openSaveModal(user.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                        :disabled="Store.getters.getUserId == user.id"
                    >
                        <svg class="mx-auto" height="25" width="25" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/>
                        </svg>
                    </PrimaryButton>
                    <PrimaryButton
                        @click-ev="openDeleteModal(user.id)"
                        class="aspect-square mx-1"
                        style="padding: 0 !important; background: none !important;"
                        :disabled="Store.getters.getUserId == user.id"
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
            users: [],
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
                const result = await Api.getAsync(route('api.admin.user.index', { page: page, filter: filter}));

                if (result.code == 200) {
                    this.users = result.response.message.data;
                    this.links = result.response.message.links.slice(1, -1);
                }
            })();
        },
        openSaveModal(selectedId) {
            const user = this.users.find((obj) => obj.id == selectedId );
            this.selectedId = selectedId;
            this.showSaveModal = true;
        },
        closeSaveModal() {
            this.showSaveModal = false;
        },
        openDeleteModal(selectedId) {
            const user = this.users.find((obj) => obj.id == selectedId );
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
        }
    },
    watch: {
        filter(newVal, oldVal) {
            this.updateDatabase(newVal);
        }
    }
}
</script>
