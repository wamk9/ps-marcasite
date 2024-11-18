<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue'
import CustomSelect from '@/Components/CustomSelect.vue'
import CustomCheckbox from '@/Components/CustomCheckbox.vue'
import UploadFile from '@/Components/UploadFile.vue'
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
                            Novo usuário
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
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">Detalhes de usuário</h3>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:mt-5">
                            <CustomInput v-model="form.name"
                                label="Nome *"
                                type="text"
                                :disabled="!onComplete"
                                placeholder=""
                                :errorMessage="nameInputError"
                                :successMessage="nameInputSuccess"
                                @checkValue="checkName"
                            />
                            <CustomSelect v-model="form.role_id"
                                placeholder=""
                                label="Tipo de usuário *"
                                :data="roleItems"
                                :errorMessage="userRoleInputError"
                                :successMessage="userRoleInputSuccess"
                                @checkValue="checkUserRole"
                                :disabled="!onComplete"
                            />
                        </div>
                        <div class="lg:mt-5">
                            <CustomInput v-model="form.email"
                                label="Email *"
                                type="email"
                                :disabled="!onComplete"
                                :errorMessage="emailInputError"
                                :successMessage="emailInputSuccess"
                                @checkValue="checkEmail"
                                placeholder="email@address.com"
                            />
                        </div>
                        <div class="grid md grid-cols-1 lg:grid-cols-2 gap-4 lg:mt-5">
                            <CustomInput v-model="form.password"
                                label="Senha"
                                type="password"
                                :disabled="!onComplete"
                                placeholder="******"
                                :errorMessage="passwordInputError"
                                :successMessage="passwordInputSuccess"
                                @checkValue="checkPassword(form.password, form.password_confirmation)"
                            />

                            <CustomInput v-model="form.password_confirmation"
                                label="Confirmação de Senha"
                                type="password"
                                :disabled="!onComplete"
                                placeholder="******"
                                :errorMessage="passwordInputError != null"
                                :successMessage="passwordInputSuccess"
                                @checkValue="checkPassword(form.password, form.password_confirmation)"
                            />
                        </div>

                        <CustomCheckbox
                            v-model="form.send_confirmation"
                            :disabled="!onComplete"
                        >
                            Enviar confirmação por e-mail
                        </CustomCheckbox>
                    </div>
                    <div class="grid text-center">
                        <h3 class="font-semibold text-lg text-gray-800 leading-tight">Foto de perfil</h3>

                        <img class="rounded-xl w-40 aspect-square mx-auto object-contain"  :src="profileImage" @error="(event) => { event.target.src = defaultProfileImage }">

                        <UploadFile
                            id="profile_image"
                            accept="image/*"
                            v-model="form.profile_image"
                            :disabled="!onComplete"
                        />

                    </div>
                </div>

                <div class="mt-10">
                    <PrimaryButton class="mr-2" @click-ev="saveUser" :disabled="!onComplete">Adicionar</PrimaryButton>
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
        id: {
            type: String,
            default: ''
        },
    },
    data() {
        return {
            form: {
                id: '',
                name: '',
                role_id: '',
                email: '',
                password: '',
                password_confirmation: '',
                send_confirmation: false,
                profile_image: '',
            },
            onComplete: false,
            nameInputError: null,
            nameInputSuccess: null,
            userRoleError: null,
            userRoleInputSuccess: null,
            emailInputError: null,
            emailInputSuccess: null,
            passwordInputError: null,
            passwordInputSuccess: null,
            roleItems: [],
            cacheTimestamp: Date.now()
        };
    },
    async created() {
        const result = await Api.getAsync(route('api.admin.role.index'));

        if (result.code == 200) {
            this.roleItems = result.response.message;
        }

        if (this.id != null) {
            this.form.id = this.id;
            await this.getData();
        }

        this.onComplete = true;
    },
    methods: {
        async getData() {
            const result = await Api.getAsync(route('api.admin.user.show', this.id));

            if (result.code == 200) {
                this.form.id = result.response.message.id;
                this.form.name = result.response.message.name;
                this.form.role_id = result.response.message.role_id;
                this.form.email = result.response.message.email;
                this.form.password = result.response.message.password;
                this.form.password_confirmation = result.response.message.password_confirmation;
                this.form.send_confirmation = false;
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
        checkUserRole(value) {
            const result = (value.length > 0);
            this.form.role_id = value;
            this.userRoleInputError = null;
            this.userRoleInputSuccess = null;

            if (!result) {
                this.userRoleInputError = "Tipo de usuário deve ser preenchido.";
            } else {
                this.userRoleInputSuccess = true;
            }

            return this.userRoleInputSuccess ?? false;
        },
        checkEmail(value) {
            const result = (value.length > 0);
            this.form.email = value;
            this.emailInputError = null;
            this.emailInputSuccess = null;

            if (!result) {
                this.emailInputError = "Email deve ser preenchido.";
            } else {
                this.emailInputSuccess = true;
            }

            return this.emailInputSuccess ?? false;
        },
        checkPassword(password, password_confirmation) {
            if (this.form.id != null && password == null && password_confirmation == null) {
                return true;
            }

            const result = (password?.length >= 6 && password == password_confirmation);
            this.form.password = password;
            this.form.password_confirmation = password_confirmation;
            this.passwordInputError = null;
            this.passwordInputSuccess = null;

            if (!result) {
                this.passwordInputError = "Senha deve conter mais de 6 dígitos e ter a confirmação igual.";
            } else {
                this.passwordInputSuccess = true;
            }

            return this.passwordInputSuccess ?? false;
        },
        async saveUser() {
            this.onComplete = false;
            const nameResult = this.checkName(this.form.name);
            const roleResult = this.checkUserRole(this.form.role_id);
            const emailResult = this.checkEmail(this.form.email);
            const passwordResult = this.checkPassword(this.form.password, this.form.password_confirmation);

            if (!(nameResult && roleResult && emailResult && passwordResult)) {
                this.onComplete = true;
                return;
            }

            const dataToSend = {
                id: this.form.id,
                name: this.form.name,
                role_id: this.form.role_id,
                email: this.form.email,
                password: this.form.password,
                password_confirmation: this.form.password_confirmation,
                send_confirmation: this.form.send_confirmation,
                profile_image: this.form.profile_image
            }

            const result = !!this.form.id ?
                await Api.putAsync(route('api.admin.user.update', this.form.id), dataToSend) :
                await Api.postAsync(route('api.admin.user.store'), dataToSend);

            if (result.code == 200) {
                this.$emit('updateDatabase');
                this.$emit('update:show', false);
            }

            this.onComplete = true;
        },
    },
    computed: {
        profileImage() {
            return !this.form.profile_image ? SystemVars.baseUrl + 'storage/user/' + this.form.id + '/profile.webp?cache=' + this.cacheTimestamp : this.form.profile_image;
        },
        defaultProfileImage() {
            return 'https://placehold.co/800/EEE/31343C';//SystemVars.baseUrl + 'storage/league/default/logo.webp';
        }

    }
}
</script>
