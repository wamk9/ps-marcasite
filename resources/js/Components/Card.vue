
<script setup>
import SystemVars from '@/Helpers/General/SystemVars.js';
import PrimaryButton from '@/Components/PrimaryButton.vue';
</script>

<template>
    <div class="card flex flex-col border-solid border-2 ">
        <img class="card-img-to aspect-video " alt="Card image cap" :src="cardImage" @error="(event) => { event.target.src = defaultCardImage }">
        <div class="p-5">
            <h5 class="text-2xl font-bold mt-2">{{ name }}</h5>
            <p class=" mt-2">{{ value }}</p>
            <PrimaryButton v-if="bought" class="float-right"  @click-ev="seeCourse(courseId)">
                Curso já adquirido
            </PrimaryButton>
            <PrimaryButton v-else-if="!dateAvailable" class="float-right" :disabled="true">
                Indisponível
            </PrimaryButton>
            <PrimaryButton v-else-if="!bought && available" class="float-right" @click-ev="seeCourse(courseId)">
                Comprar
            </PrimaryButton>
            <PrimaryButton v-else class="float-right" :disabled="true">
                Sem vagas disponíveis
            </PrimaryButton>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'Card',
    props: {
        name: {
            type: String,
            default: '',
        },
        courseId: {
            type: String,
            default: '',
        },
        bought: {
            type: Boolean,
            default: false
        },
        available: {
            type: Boolean,
            default: false
        },
        dateAvailable: {
            type: Boolean,
            default: false
        },
        description: {
            type: String,
            default: '',
        },
        value: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            cacheTimestamp: Date.now()
        };
    },
    computed: {
        cardImage() {
            return SystemVars.baseUrl + 'storage/course/' + this.courseId + '/thumb.webp?cache=' + this.cacheTimestamp;
        },
        defaultCardImage() {
            return 'https://placehold.co/1270x720/EEE/31343C';//SystemVars.baseUrl + 'storage/course/default/thumb.webp';
        }
    },
    methods: {
        seeCourse(courseId) {
            window.location.href = route('pages.user.course.show', {id : courseId});
        }
    }
  };
  </script>
