message

<script setup>
import Modal from '@/Components/Modal.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TourDelete from '@/Pages/Admin/Tour/Delete.vue';
import Api from '@/Helpers/Api/Api.js';
</script>

<template>
    <div class="text-center p-5">
        <p class="font-bold text-xl">Pague R$ {{ resultRequisition.transaction_amount }} via Pix</p>
        <p>Vencimento: {{ limitDate }}</p>

        <img class="w-full max-w-96 max-h-96 mx-auto" :src="qrCodeImg" alt="QRCode Pix" />
        <p>{{ qrCode }}</p>
    </div>

</template>

<script>
export default {
    name: 'MercadoPagoBricksPix',
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
        this.interval = setInterval(this.checkIfPayed,2000);
    },
    computed:{
        qrCodeImg() {
            return 'data:image/png;base64,' + this.resultRequisition.point_of_interaction?.transaction_data?.qr_code_base64
        },
        qrCode() {
            return this.resultRequisition.point_of_interaction?.transaction_data?.qr_code;
        },
        limitDate() {
            const date = new Date(this.resultRequisition.date_of_expiration);

            // Format the date
            const formattedDate = new Intl.DateTimeFormat('en-GB', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            hourCycle: 'h23',
            //timeZoneName: 'longOffset'
            }).format(date);

            // Replace the comma and adapt the format
            return formattedDate.replace(',', '').replace('GMT', '');
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
        checkIfPayed() {
            (async () => {
                const result = await Api.getAsync(route('api.user.payment.show', {id : this.resultRequisition.id }));
                console.log(result);
                if (result.code == 200) {
                    if (result.response.message.status != 'pending') {
                        clearInterval(this.interval);
                        window.location.href = route(`pages.user.payment.${result.response.message.status}`)
                    }
                }
            })();

        },
    }
}
</script>
