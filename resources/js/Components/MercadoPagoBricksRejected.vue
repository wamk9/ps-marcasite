<script setup>
</script>

<template>
    <div class="text-center p-5">
        <p class="font-bold text-xl">Pagamento de R${{ resultRequisition.transaction_amount }} rejeitado!</p>
        <p class="mt-5">Contate sua operadora para saber o motivo do pagamento ter sido rejeitado</p>

        <p class="mt-10">Você será redirecionado dentro de 10 segundos para a página do seu curso</p>
    </div>

</template>

<script>
export default {
    name: 'MercadoPagoBricksRejected',
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        resultRequisition: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            showDeleteModal: false,
            selectedId: null,
            selectedName: null,
            tourItems: [],
            interval: null
        };
    },
    async created() {
        this.interval = setInterval(this.refreshPage, 10000);
    },
    computed:{
    },
    methods: {
        refreshPage() {
            clearInterval(this.interval);
            window.location.reload();
        },
    }
}
</script>
