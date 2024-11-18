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
    <Head title="Configurações" />

    <UserLayout id="user-config-page">
        <p class="font-bold text-xl text-center">
            Nenhuma configuração disponível no momento
        </p>
    </UserLayout>
</template>

<script>
export default {
    props: {
    },
    data() {
        return {

        };
    },
    created() {
        //this.updateDatabase();
    },
    computed:{
    },
    methods: {
        updateDatabase() {
            (async () => {
                const result = await Api.getAsync(route('api.user.config.index'));

                if (result.code == 200) {
                    // this.courses = result.response.message.data;
                    // this.links = result.response.message.links.slice(1, -1);
                }
            })();
        },
    },
}
</script>
