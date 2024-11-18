<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Api from '@/Helpers/Api/Api.js';
import MercadoPagoBricks from '@/Components/MercadoPagoBricks.vue';
import Store from '@/Store'
</script>

<template>
    <Head title="Dashboard" />

    <UserLayout id="user-dashboard-page">
        <h1 class="font-bold text-center text-xl">
            Bem vindo, {{ Store.getters.getUserName }}!
        </h1>
        <p class="text-center mt-3">
            Adquira cursos na aba "Vitrine de Cursos" e veja os que você possui na aba "Meus Cursos"
        </p>
    </UserLayout>
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
            showDeleteModal: false,
            selectedId: null,
            selectedName: null,
            tourItems: [],
        };
    },
    created() {

    },
    computed:{
    },
    methods: {
        updateDatabase() {
            (async () => {
                const result = await Api.getAsync(route('api.admin.tour.index'));

                if (result.code == 200) {
                    this.tourItems = result.response.message;
                    this.$refs.datatable.dt.clear().draw();
                }
            })();
        },
        goToCreatePage() {
            // window.location.href = route('pages.admin.tour.store')
        },
        goToEditPage(selectedId) {
            // window.location.href = route('pages.admin.tour.update', [ selectedId ])
        },
        openDeleteModal(selectedId) {
            const tour = this.tourItems.find((obj) => obj.id == selectedId );
            this.selectedName = !!tour ? tour.name : null;
            this.selectedId = selectedId;
            this.showDeleteModal = true;
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
            this.selectedName = null;
            this.selectedId = null;
        },
    }
}
</script>
