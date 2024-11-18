<script setup>
import Auth from '@/Helpers/Api/Auth.js'
import Loading from '@/Components/Loading.vue'
import DropDown from '@/Components/DropDown.vue'
import Store from '@/Store'
import SystemVars from '@/Helpers/General/SystemVars.js'
</script>


<template>
    <Loading v-if="!onComplete"/>
    <div class="flex grow">
        <nav id="user-sidebar" ref="navLinks" class="flex flex-col items-center h-full overflow-hidden text-gray-700 bg-gray-100 rounded toggled">
            <div class="w-full px-2">
                <div class="flex flex-col items-center w-full mt-3">
                    <a class="flex items-center w-full h-12 px-3 mt-2 rounded" href="#">
                        <svg class="w-6 h-6 stroke-current nav-icon close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" v-on:click="toggleMenu">
                            <path d="M376.6 84.5c11.3-13.6 9.5-33.8-4.1-45.1s-33.8-9.5-45.1 4.1L192 206 56.6 43.5C45.3 29.9 25.1 28.1 11.5 39.4S-3.9 70.9 7.4 84.5L150.3 256 7.4 427.5c-11.3 13.6-9.5 33.8 4.1 45.1s33.8 9.5 45.1-4.1L192 306 327.4 468.5c11.3 13.6 31.5 15.4 45.1 4.1s15.4-31.5 4.1-45.1L233.7 256 376.6 84.5z"/>
                        </svg>
                        <svg class="w-6 h-6 stroke-current nav-icon bars" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" v-on:click="toggleMenu">
                            <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z"/>
                        </svg>
                        <svg class="ml-5 w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path d="M256 64C150 64 64 150 64 256s86 192 192 192c17.7 0 32 14.3 32 32s-14.3 32-32 32C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256l0 32c0 53-43 96-96 96c-29.3 0-55.6-13.2-73.2-33.9C320 371.1 289.5 384 256 384c-70.7 0-128-57.3-128-128s57.3-128 128-128c27.9 0 53.7 8.9 74.7 24.1c5.7-5 13.1-8.1 21.3-8.1c17.7 0 32 14.3 32 32l0 80 0 32c0 17.7 14.3 32 32 32s32-14.3 32-32l0-32c0-106-86-192-192-192zm64 192a64 64 0 1 0 -128 0 64 64 0 1 0 128 0z"/>
                            </svg>
                        <span class="ml-2 text-sm font-medium" v-on:click="goToDashboard">

                            Marcasite
                        </span>
                    </a>
                </div>
            </div>
            <div class="w-full px-2">
                <div class="flex flex-col items-center w-full mt-3">
                    <a class="flex items-center w-full h-12 px-3 mt-2 rounded nav-item" :class="route().current('pages.user.dashboard.*') ? 'active' : ''" :href="route('pages.user.dashboard.index')">
                        <svg class="w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z"/>
                        </svg>
                        <span class="ml-2 text-sm font-medium nav-title">Dashboard</span>
                    </a>
                    <a class="flex items-center w-full h-12 px-3 mt-2 rounded nav-item" :class="route().current('pages.user.my-course.*') ? 'active' : ''" :href="route('pages.user.my-course.index')">
                        <svg class="w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm80 64c-8.8 0-16 7.2-16 16l0 96c0 8.8 7.2 16 16 16l96 0c8.8 0 16-7.2 16-16l0-96c0-8.8-7.2-16-16-16l-96 0z"/>
                        </svg>
                        <span class="ml-2 text-sm font-medium nav-title">Meus Cursos</span>
                    </a>
                    <a class="flex items-center w-full h-12 px-3 mt-2 nav-item rounded" :class="route().current('pages.user.course.*') ? 'active' : ''" :href="route('pages.user.course.index')">
                        <svg class="w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M160 96a96 96 0 1 1 192 0A96 96 0 1 1 160 96zm80 152l0 264-48.4-24.2c-20.9-10.4-43.5-17-66.8-19.3l-96-9.6C12.5 457.2 0 443.5 0 427L0 224c0-17.7 14.3-32 32-32l30.3 0c63.6 0 125.6 19.6 177.7 56zm32 264l0-264c52.1-36.4 114.1-56 177.7-56l30.3 0c17.7 0 32 14.3 32 32l0 203c0 16.4-12.5 30.2-28.8 31.8l-96 9.6c-23.2 2.3-45.9 8.9-66.8 19.3L272 512z"/>
                        </svg>
                        <span class="ml-2 text-sm font-medium nav-title">Vitrine de Cursos</span>
                    </a>
                    <a class="flex items-center w-full h-12 px-3 mt-2 rounded nav-item" :class="route().current('pages.user.config.*') ? 'active' : ''" :href="route('pages.user.config.index')">
                        <svg class="w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                        </svg>
                        <span class="ml-2 text-sm font-medium nav-title">Configurações</span>
                    </a>
                </div>
            </div>
        </nav>

        <div class="flex w-full grow-0 flex-row">
            <nav id="user-navbar" class="flex-row-reverse flex">
                <DropDown id="user-dropdown" :items="userDropdown" class="flex items-center h-12 pl-3 mt-2">
                    {{ userInfo.name }}
                    <img class="aspect-square flex h-8 rounded-full ml-5 object-contain" :src="profileImage" @error="(event) => { event.target.src = defaultProfileImage }">
                </DropDown>

                <DropDown id="user-dropdown" :items="alertDropdown" class="flex items-center h-12 pl-3 mt-2">
                    <svg class="w-6 h-6 stroke-current nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path d="M224 0c-17.7 0-32 14.3-32 32l0 19.2C119 66 64 130.6 64 208l0 18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416l384 0c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8l0-18.8c0-77.4-55-142-128-156.8L256 32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3l-64 0-64 0c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z"/>
                    </svg>
                    <span v-if="numberAlerts > 0" class="rounded-full text-xs mb-5 p-1 flex alert-label">
                        {{ numberAlerts }}
                    </span>
                </DropDown>

            </nav>
            <div id="menu-spacer" ref="menuSpacer" class="toggled h-full"></div>
            <main :id="id" ref="main" class="toggled">
                <slot/>
            </main>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'UserLayout',
        props:{
          id: {
            type: String,
            default: 'page'
          }
        },
        data() {
            return {
                onComplete: false,
                userDropdown: [
                    {
                        title: 'Logout',
                        link: route('pages.logout')
                    }
                ],
                numberAlerts: 0,
                alertDropdown: [
                    {
                        title: 'Sem notificações no momento...',
                        link: '#'
                    }
                ],
                userInfo: {
                    name: 'João',
                    picture: 'https://placehold.co/800'
                },
                cacheTimestamp: Date.now()
            }
        },
        async created() {
            this.onComplete = await Auth.checkAuthentication();
            this.userInfo.name = Store.getters.getUserName;
            //this.userInfo.picture = Store.getters.getUserId;
        },
        computed: {
            profileImage() {
                return SystemVars.baseUrl + 'storage/user/' + Store.getters.getUserId + '/profile.webp?cache=' + this.cacheTimestamp;
            },
            defaultProfileImage() {
                return 'https://placehold.co/800/EEE/31343C';//SystemVars.baseUrl + 'storage/user/default/profile/profile.webp';
            }
        },
        methods: {
            toggleMenu() {
                this.$refs.navLinks.classList.toggle('toggled');
                this.$refs.main.classList.toggle('toggled');
                this.$refs.menuSpacer.classList.toggle('toggled');
            },
            goToDashboard() {
                window.location.href = route(`pages.${Store.getters.getRole}.dashboard.index`)
            },
            logout() {
                this.onComplete = false;
                Auth.logout();
            },
        }
    };
</script>
