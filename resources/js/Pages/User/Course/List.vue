<script setup>
import { Head } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue';
import CourseDelete from '@/Pages/Admin/Course/Delete.vue';
import CourseSave from '@/Pages/Admin/Course/Save.vue';
import Card from '@/Components/Card.vue'
import Api from '@/Helpers/Api/Api.js';
import Store from '@/Store';
</script>

<template>
    <Head title="Cursos" />

    <UserLayout id="user-course-page">
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
        </div>

        <div v-if="courses.length == 0">
            <p colspan="6">
                Nenhum curso encontrado
            </p>
        </div>

        <div v-else>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-center">
                <Card v-for="course in courses"
                :name="course.name"
                :courseId="course.id"
                :bought="!!course.bought"
                :available="(course.vacations_to_fill > 0)"
                :value="course.value"
                :dateAvailable="course.date_available"
                />
            </div>

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
        </div>

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
    },
    computed:{
        searchIcon() {
            return '<svg width="15" height="17" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>';
        }
    },
    methods: {
        updateDatabase(filter = null, page = null) {
            (async () => {
                const result = await Api.getAsync(route('api.user.course.index', { page: page, filter: filter}));

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
        }
    },
    watch: {
        filter(newVal, oldVal) {
            this.updateDatabase(newVal);
        }
    }
}
</script>
