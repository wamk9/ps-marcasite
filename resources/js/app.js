import './bootstrap';
import { createApp, h } from "vue";
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import store from '@/Store';
import VueCookies from 'vue-cookies';

const appName = import.meta.env.VITE_APP_NAME || 'Marcasite Cursos';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: name => {
      const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
      return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
      createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(store)
        .use(VueCookies, { expires: '7d'})
        .use(ZiggyVue)
        .mount(el);
    },
  })
