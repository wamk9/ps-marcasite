<script setup>
import Modal from '@/Components/Modal.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
// import PrimaryButton from '@/Components/PrimaryButton.vue';
// import TourDelete from '@/Pages/Admin/Tour/Delete.vue';
import Api from '@/Helpers/Api/Api.js';
import { loadMercadoPago } from "@mercadopago/sdk-js";
import MercadoPagoBricksApproved from './MercadoPagoBricksApproved.vue';
import MercadoPagoBricksPix from './MercadoPagoBricksPix.vue';
import MercadoPagoBricksRejected from './MercadoPagoBricksRejected.vue';
import MercadoPagoBricksInProgress from './MercadoPagoBricksInProgress.vue';
import MercadoPagoBricksPending from './MercadoPagoBricksPending.vue';
import Store from '@/Store'
</script>

<template>
    <Modal :show="showApprovedModal" :closeable="true" @close="closeModals">
        <MercadoPagoBricksApproved v-model:show="showApprovedModal" :resultRequisition="resultRequisition" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase"/>
    </Modal>
    <Modal :show="showPixModal" :closeable="true" @close="closeModals">
        <MercadoPagoBricksPix v-model:show="showPixModal" :resultRequisition="resultRequisition" :name="selectedName" @updateDatabase="updateDatabase"/>
    </Modal>
    <Modal :show="showInProgressModal" :closeable="true" @close="closeModals">
        <MercadoPagoBricksRejected v-model:show="showInProgressModal" :resultRequisition="resultRequisition" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase"/>
    </Modal>
    <Modal :show="showRejectedModal" :closeable="true" @close="closeModals">
        <MercadoPagoBricksInProgress v-model:show="showRejectedModal" :resultRequisition="resultRequisition" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase"/>
    </Modal>
    <Modal :show="showPendingModal" :closeable="true" @close="closeModals">
        <MercadoPagoBricksPending v-model:show="showPendingModal" :resultRequisition="resultRequisition" :id="selectedId" :name="selectedName" @updateDatabase="updateDatabase"/>
    </Modal>

    <div id="paymentBrick_container"></div>

</template>

<script>
export default {
    name: 'MercadoPagoBricks',
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        value: {
            type: String,
            required: true,
        },
        courseId: {
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
            resultRequisition: [],
            showPixModal: false,
            showPendingModal: false,
            showInProcessModal: false,
            showRejectedModal: false,
            showApprovedModal: false,
            accessToken: '',
        };
    },
    async created() {
        const result = await Api.getAsync(route('api.mercadopago.token'));

        if (result.code == 200) {
            this.accessToken = result.response.message;
        }

        const self = this;
        await loadMercadoPago();
        const mp = new MercadoPago(this.accessToken);
        const bricksBuilder = mp.bricks();

        const renderPaymentBrick = async (bricksBuilder) => {
            const settings = {
            initialization: {
                /*
                "amount" é o valor total a ser pago por todos os meios de pagamento
                com exceção da Conta Mercado Pago e Parcelamento sem cartão de crédito, que tem seu valor de processamento determinado no backend através do "preferenceId"
                */
                amount: this.value,
                preferenceId: "<PREFERENCE_ID>",
            },
            customization: {
                paymentMethods: {
                    bankTransfer: ["pix"],
                    creditCard: "all",
                    //debitCard: "all",
                    //mercadoPago: "all",
                },
                visual: {
                texts: {
                    formTitle: "Métodos de Pagamento",
                    emailSectionTitle: "Outras informações",
                    installmentsSectionTitle: "Parcelamento",
                    cardholderName: {
                    label: "Nome do cartão",
                    placeholder: "José Arnaldo da Silva",
                    },
                    email: {
                    label: "Email",
                    placeholder: "jose.arnaldo@gmail.com",
                    },
                    cardholderIdentification: {
                    label: "CPF/CNPJ",
                    },
                    cardNumber: {
                    label: "Número do cartão",
                    placeholder: "1234 1234 1234 1234",
                    },
                    expirationDate: {
                    label: "Vencimento do cartão",
                    placeholder: "MM/AA",
                    },
                    securityCode: {
                    label: "CVV",
                    placeholder: "123",
                    },
                    selectInstallments: "Selecione o seu parcelamento",
                    selectIssuerBank: "Selecione o banco",
                    formSubmit: "string",
                    paymentMethods: {
                    newCreditCardTitle: "Novo cartão de crédito",
                    creditCardTitle: "Cartão de crédito",
                    creditCardValueProp: "Pagamento parcelado",
                    newDebitCardTitle: "Novo cartão de débido",
                    debitCardTitle: "Cartão de débido",
                    debitCardValueProp: "Pagamento à vista",
                    },
                },
                },
            },
            callbacks: {
                onReady: () => {
                /*
                    Callback chamado quando o Brick estiver pronto.
                    Aqui você pode ocultar loadings do seu site, por exemplo.
                */
                },
                onSubmit: ({ selectedPaymentMethod, formData }) => {
                    return new Promise((resolve, reject) => {
                        fetch(route('api.payment.mercadopago', {course_id: self.courseId}), {
                        method: "POST",
                        headers: {
                            Authorization: 'Bearer ' + Store.getters.getToken,
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(formData),
                        })
                        .then((response) => response.json())
                        .then((response) => {
                            // receber o resultado do pagamento
                            resolve();
                            self.resultRequisition = response.message;
                        })
                        .catch((error) => {
                            // lidar com a resposta de erro ao tentar criar o pagamento
                            reject();
                        });
                    });
                },
                onError: (error) => {
                // callback chamado para todos os casos de erro do Brick
                console.error(error);
                },
            },
            };
            window.paymentBrickController = await bricksBuilder.create(
            "payment",
            "paymentBrick_container",
            settings
            );
        };

        renderPaymentBrick(bricksBuilder);
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
        closeModals() {
            this.showPixModal = false;
            this.showPendingModal = false;
            this.showInProcessModal = false;
            this.showRejectedModal = false;
            this.showApprovedModal = false;

            this.resultRequisition = null;
        },
    },
    watch: {
        resultRequisition(newVal, oldVal) {
            if (newVal.payment_method_id == 'pix') {
                this.showPixModal = true;
            } else {
                (async () => {
                    const result = await Api.getAsync(route('api.user.payment.show', {id : newVal.id }));

                    if (result.code == 200) {
                        switch (result.response.message.status) {
                            case 'pending':
                                this.showPendingModal = true;
                                break;
                            case 'in_process':
                                this.showInProcessModal = true;
                                break;
                            case 'rejected':
                                this.showRejectedModal = true;
                                break;
                            case 'approved':
                                this.showApprovedModal = true;
                                break;
                        }


                        // if (result.response.message.status != 'pending') {
                        //     window.location.href = route(`pages.user.payment.${result.response.message.status}`)
                        // }
                    }
                })();
            }
        }
    }
}
</script>
