<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Api from '@/Helpers/Api/Api.js';
import MercadoPagoBricks from '@/Components/MercadoPagoBricks.vue';
import SystemVars from '@/Helpers/General/SystemVars.js';
</script>

<template>
    <Head title="Dashboard" />
    <UserLayout id="user-dashboard-page">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <img class="card-img-to aspect-video " alt="Imagem de apresentação do curso" :src="courseImage" @error="(event) => { event.target.src = defaultCourseImage }">
            </div>
            <div>
                <h1 class="text-2xl font-bold">
                    {{ course.name }}
                </h1>
                <p>{{ course.description }}</p>
                <p class="mt-5">Valor de investimento: R${{ course.value }}</p>
            </div>
        </div>

        <MercadoPagoBricks
            v-if="(course.date_available || course.available) && !course.bought"
            class="mt-10"
            :value="course.value"
            :description="course.name"
            :courseId="course.id"
        />

        <div v-if="course.bought" class="mt-5">
            <h2 class="text-xl">
                Materiais
            </h2>
            <li v-for="filename in course.files">
                <a :href="SystemVars.baseUrl + 'storage/course/' + this.course.id + '/' + filename" >
                    {{ filename }}
                </a>
            </li>
        </div>
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
            course: {},
            cacheTimestamp: Date.now()
        };
    },
    created() {
        (async () => {
            const result = await Api.getAsync(route('api.user.course.show', {id : route().params.id }));

            if (result.code == 200) {
                this.course = result.response.message;
            }
        })();

    },
    mounted() {
    },
    computed:{
        courseImage() {
            return SystemVars.baseUrl + 'storage/course/' + this.course.id + '/thumb.webp?cache=' + this.cacheTimestamp;
        },
        defaultCourseImage() {
            return 'https://placehold.co/1270x720/EEE/31343C';//SystemVars.baseUrl + 'storage/course/default/thumb.webp';
        }
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
