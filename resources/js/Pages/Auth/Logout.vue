<script setup>
import Auth from '@/Helpers/Api/Auth.js'
import Loading from '@/Components/Loading.vue'
</script>


<template>
    <Loading v-if="!onComplete"/>
</template>

<script>
    export default {
        name: 'AdminLayout',
        props:{
          id: {
            type: String,
            default: 'page'
          }
        },
        data() {
            return {
            }
        },
        async created() {
            this.onComplete = false;
            Auth.logout();
        },
        methods: {
        }
    };
</script>
