<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue'
import CustomSelect from '@/Components/CustomSelect.vue'
import CustomCheckbox from '@/Components/CustomCheckbox.vue'
import CustomTextArea from '@/Components/CustomTextArea.vue'
import UploadFile from '@/Components/UploadFile.vue'
import MultipleUploadFile from '@/Components/MultipleUploadFile.vue'
import Api from '@/Helpers/Api/Api.js'
import SystemVars from '@/Helpers/General/SystemVars.js'
import Store from '@/Store'
</script>

<template>
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ courseSaveTitle }}
                        </h2>
                    </div>
                    <div>
                        <a href="#" v-on:click="$emit('update:show', false)" class="float-right">
                            x
                        </a>
                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-10">
                    <div class="grid col-span-2">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">Detalhes do curso</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:mt-5">
                            <CustomInput v-model="form.name"
                                label="Nome*"
                                type="text"
                                :disabled="!onComplete"
                                placeholder=""
                                :errorMessage="nameInputError"
                                :successMessage="nameInputSuccess"
                                @checkValue="checkName"
                            />
                            <CustomSelect v-model="form.category_id"
                                placeholder=""
                                label="Categoria*"
                                :data="categoryItems"
                                :errorMessage="categoryInputError"
                                :successMessage="categoryInputSuccess"
                                @checkValue="checkCategory"
                                :disabled="!onComplete"
                            />

                            <CustomInput v-model="form.value"
                                label="Valor*"
                                type="decimal"
                                :disabled="!onComplete"
                                placeholder="R$"
                                :errorMessage="valueInputError"
                                :successMessage="valueInputSuccess"
                                @checkValue="checkValue"
                            />
                            <CustomInput v-model="form.vacations"
                                label="Vagas*"
                                type="integer"
                                :disabled="!onComplete"
                                placeholder="300"
                                :errorMessage="vacationInputError"
                                :successMessage="vacationInputSuccess"
                                @checkValue="checkVacation"
                            />

                            <CustomInput v-model="form.registration_at"
                                label="Inscrições de:"
                                type="date"
                                :disabled="!onComplete"
                                placeholder="08/05/2024"
                                :errorMessage="registrationAtInputError"
                                :successMessage="registrationAtInputSuccess"
                                @checkValue="checkRegistrationAt"
                            />
                            <CustomInput v-model="form.registration_until"
                                label="Até:"
                                type="date"
                                :disabled="!onComplete"
                                placeholder="10/05/2024"
                                :errorMessage="registrationUntilInputError"
                                :successMessage="registrationUntilInputSuccess"
                                @checkValue="checkRegistrationUntil"
                            />
                        </div>

                        <div class="grid md grid-cols-1 gap-4 lg:mt-5">
                            <CustomTextArea v-model="form.description"
                                label="Descrição*"
                                :disabled="!onComplete"
                                placeholder=""
                                :errorMessage="descriptionInputError"
                                :successMessage="descriptionInputSuccess"
                                @checkValue="checkDescription"
                            />
                        </div>

                        <CustomCheckbox
                            v-model="form.active"
                            :disabled="!onComplete"
                        >
                            Ativo
                        </CustomCheckbox>
                    </div>
                    <div class="grid text-center">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight ">Thumbnail</h3>

                            <img class="rounded-xl w-40 aspect-square mx-auto object-contain mt-10"  :src="thumbImage" @error="(event) => { event.target.src = defaultThumbImage }">

                            <UploadFile
                                id="thumb_image"
                                accept="image/*"
                                v-model="form.thumb_image"
                                :disabled="!onComplete"
                                class="mt-10"
                            />

                        </div>

                    </div>

                    <div class="col-span-3">
                        <MultipleUploadFile
                            id="materials"
                            accept="*/*"
                            v-model="form.files"
                            :disabled="!onComplete"
                            class="mt-10"
                        />
                    </div>
                </div>

                <div class="mt-10">
                    <PrimaryButton class="mr-2" @click-ev="saveCourse" :disabled="!onComplete">{{ courseSaveButtonTitle }}</PrimaryButton>
                    <PrimaryButton class="ml-2" @click-ev="() => { $emit('update:show', false) }">Cancelar</PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        courseId: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            form: {
                id: '',
                name: '',
                category_id: '',
                value: '',
                vacations: '',
                registration_at: '',
                registration_until: '',
                description: '',
                active: false,
                thumb_image: '',
                files: [],

            },
            onComplete: false,
            nameInputError: null,
            nameInputSuccess: null,
            categoryInputError: null,
            categoryInputSuccess: null,
            valueInputError: null,
            valueInputSuccess: null,
            vacationInputSuccess: null,
            vacationInputError: null,
            registrationAtInputSuccess: null,
            registrationAtInputError: null,
            registrationUntilInputSuccess: null,
            registrationUntilInputError: null,
            descriptionInputError: null,
            descriptionInputSuccess: null,
            categoryItems: [],
            cacheTimestamp: Date.now()
        };
    },
    async created() {
        const result = await Api.getAsync(route('api.admin.category.index'));

        if (result.code == 200) {
            this.categoryItems = result.response.message;
        }

        if (this.courseId != null) {
            this.form.id = this.courseId;
            await this.getData();
        }

        this.onComplete = true;
    },
    methods: {
        async getData() {
            const result = await Api.getAsync(route('api.admin.course.show', this.courseId));

            if (result.code == 200) {
                this.form.id = result.response.message.id;
                this.form.name = result.response.message.name;
                this.form.category_id = result.response.message.category_id;
                this.form.value = result.response.message.value;
                this.form.vacations = result.response.message.vacations;
                this.form.registration_at = result.response.message.registration_at;
                this.form.registration_until = result.response.message.registration_until;
                this.form.description = result.response.message.description;
                this.form.active = result.response.message.active;
                this.form.thumb_image = result.response.message.thumb_image;
                this.form.files = result.response.message.files;
            }
        },
        updateItem(e, attr) {
            this.form[attr] = e.target.value;
            this.$emit('update:item', this.form);
        },
        updateShow(e) {
            this.$emit('update:show', e.target.value)
        },
        checkName(value) {
            const result = (value.length > 0);
            this.form.name = value;
            this.nameInputError = null;
            this.nameInputSuccess = null;

            if (!result) {
                this.nameInputError = "Nome deve ser preenchido.";
            } else {
                this.nameInputSuccess = true;
            }

            return this.nameInputSuccess ?? false;
        },
        checkCategory(value) {
            const result = (value.length > 0);
            this.form.category_id = value;
            this.categoryInputError = null;
            this.categoryInputSuccess = null;

            if (!result) {
                this.categoryInputError = "Categoria deve ser preenchido.";
            } else {
                this.categoryInputSuccess = true;
            }

            return this.categoryInputSuccess ?? false;
        },
        checkValue(value) {
            const result = (value.length > 0);
            this.form.email = value;
            this.valueInputError = null;
            this.valueInputSuccess = null;

            if (!result) {
                this.valueInputError = "Valor da inscrição deve ser preenchido.";
            } else {
                this.valueInputSuccess = true;
            }

            return this.valueInputSuccess ?? false;
        },
        checkVacation(value) {
            const result = parseInt(value) > 0;
            this.form.vacations = value;
            this.vacationInputError = null;
            this.vacationInputSuccess = null;

            if (!result) {
                this.vacationInputError = "Vagas deve ser preenchido.";
            } else {
                this.vacationInputSuccess = true;
            }

            return this.vacationInputSuccess ?? false;
        },
        checkRegistrationAt(value) {
            const result = (value.length > 0);
            this.form.registration_at = value;
            this.registrationAtInputError = null;
            this.registrationAtInputSuccess = null;

            if (!result) {
                this.registrationAtInputError = "Data de início das inscrições deve ser preenchido.";
            } else {
                this.registrationAtInputSuccess = true;
            }

            return this.registrationAtInputSuccess ?? false;
        },
        checkRegistrationUntil(value) {
            const result = (value.length > 0);
            this.form.registration_until = value;
            this.registrationUntilInputError = null;
            this.registrationUntilInputSuccess = null;

            if (!result) {
                this.registrationUntilInputError = "Data final das inscrições deve ser preenchido.";
            } else {
                this.registrationUntilInputSuccess = true;
            }

            return this.registrationUntilInputSuccess ?? false;
        },

        checkDescription(value) {
            const result = (value.length > 0);
            this.form.description = value;
            this.descriptionInputError = null;
            this.descriptionInputSuccess = null;

            if (!result) {
                this.descriptionInputError = "Descrição deve ser preenchido.";
            } else {
                this.descriptionInputSuccess = true;
            }

            return this.descriptionInputSuccess ?? false;
        },
        async saveCourse() {
            this.onComplete = false;
            const nameResult = this.checkName(this.form.name);
            const roleResult = this.checkCategory(this.form.category_id);
            const valueResult = this.checkValue(this.form.value);
            const vacationResult = this.checkVacation(this.form.vacations);
            const registrationAtResult = this.checkRegistrationAt(this.form.registration_at);
            const registrationUntilResult = this.checkRegistrationUntil(this.form.registration_until);
            const descriptionResult = this.checkDescription(this.form.description);
            if (!(nameResult && roleResult && valueResult && vacationResult && registrationAtResult && registrationUntilResult && descriptionResult)) {
                this.onComplete = true;
                return;
            }

            const dataToSend = {
                id: this.form.id,
                name: this.form.name,
                category_id: this.form.category_id,
                value: this.form.value,
                vacations: this.form.vacations,
                registration_at: this.form.registration_at,
                registration_until: this.form.registration_until,
                description: this.form.description,
                active: this.form.active,
                thumb_image: this.form.thumb_image,
                files: this.form.files,
            }

            const result = !!this.form.id ?
                await Api.putAsync(route('api.admin.course.update', this.form.id), dataToSend) :
                await Api.postAsync(route('api.admin.course.store'), dataToSend);

            if (result.code == 200) {
                this.$emit('updateDatabase');
                this.$emit('update:show', false);
            } else if (result.code == 422) { // Validation error
                if (result.response.message.registration_until != null) {

                }

                Object.keys(result.response.message).forEach((key) => {
                    switch (key) {
                        case 'registration_until':
                            this.registrationUntilInputSuccess = null;
                            this.registrationUntilInputError = 'Verifique se a data é posterior do campo \'Inscrições de\'';
                            break;
                        default:
                            break;
                    }
                })
            }
            this.onComplete = true;
        },
        convertDateFormat(dateStr) {
            // Regular expression to validate the format dd/mm/yyyy
            const datePattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
            const match = dateStr.match(datePattern);

            if (match) {
                const day = parseInt(match[1], 10);
                const month = parseInt(match[2], 10);
                const year = parseInt(match[3], 10);

                // Basic validation for day, month, and year ranges
                if (day > 0 && day <= 31 && month > 0 && month <= 12) {
                    // Adjust month for JavaScript Date (0-based index)
                    return `${year.toString().padStart(4, '0')}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                } else {
                    console.error('Invalid day or month.');
                    return null;
                }
            } else {
                console.error('Invalid date format. Expected dd/mm/yyyy.');
                return null;
            }
        }
    },
    computed: {
        thumbImage() {
            return !this.form.thumb_image ? SystemVars.baseUrl + 'storage/course/' + this.form.id + '/thumb.webp?cache=' + this.cacheTimestamp : this.form.thumb_image;
        },
        defaultThumbImage() {
            return 'https://placehold.co/800/EEE/31343C';//SystemVars.baseUrl + 'storage/league/default/logo.webp';
        },
        courseSaveTitle() {
            return (this.courseId == null ? 'Novo curso' : 'Editando curso')
        },
        courseSaveButtonTitle() {
            return (this.courseId == null ? 'Adicionar' : 'Salvar')
        }

    }
}
</script>
