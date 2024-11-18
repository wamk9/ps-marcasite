<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import CustomInput from '@/Components/CustomInput.vue'
import Api from '@/Helpers/Api/Api.js'
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
                        <a href="#" v-on:click="$emit('update:show', false)">
                            x
                        </a>
                    </div>
                </div>


                <div class="grid md grid-cols-1 lg:grid-cols-2 gap-4 mt-3">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800 leading-tight">Detalhes de usuário</h3>
                        <div id="form" class="w-full">
                            <CustomInput v-model="form.email"
                                label="Usuário"
                                :iconSvg="userIcon"
                                iconPosition="left"
                                type="email"
                                :disabled="sendingToApi"
                                placeholder="usuario@email.com"
                            />

                            <CustomInput v-model="form.password"
                                label="Senha"
                                :iconSvg="passIcon"
                                iconPosition="left"
                                :disabled="sendingToApi"
                                type="password"
                            />

                            <span v-if="hasError" class="text-center w-full input-tourbinou-message error">
                                Login ou senha inválidos, tente novamente
                            </span>

                            <PrimaryButton
                                @click-ev="login"
                                class="mt-4 mx-auto"
                                :disabled="sendingToApi">
                                Login
                            </PrimaryButton>

                            <a href="#" class="mt-4 mx-auto" v-on:click="showRegistrationModal">
                                Não possui cadastro? Clique aqui!
                            </a>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800 leading-tight">Foto de perfil</h3>

                            <img class="rounded-xl w-full aspect-square" src="">
                        </div>
                    </div>
                </div>
                <PrimaryButton class="mr-2" @click-ev="() => { $emit('update:show', false) }">Cancelar</PrimaryButton>
                <PrimaryButton class="ml-2" @click-ev="deleteDestination">Deletar</PrimaryButton>
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
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            form: {
                id: '',
            },
        };
    },
    methods: {
        updateItem(e, attr) {
            this.form[attr] = e.target.value;
            this.$emit('update:item', this.form);
        },
        updateShow(e) {
            this.$emit('update:show', e.target.value)
        },
        deleteDestination() {
            (async () => {
                const result = await Api.deleteAsync(route('api.admin.destination.delete', this.id));

                if (result.code == 200) {
                    this.$emit('updateDatabase');
                    this.$emit('update:show', false)
                }
            })();
        },
    }
}
</script>
