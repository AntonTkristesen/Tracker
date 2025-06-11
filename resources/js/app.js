import './bootstrap';
import '../css/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';


import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Layout from './Layouts/Layout.vue';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Button from "primevue/button"
import InputText from 'primevue/inputtext';
import Card from 'primevue/card';
import Avatar from 'primevue/avatar';
import Textarea from 'primevue/textarea';
import Tree from 'primevue/tree'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Calendar from 'primevue/calendar'
import ToastService from 'primevue/toastservice'
import Sidebar from 'primevue/sidebar';
import Drawer from 'primevue/drawer';

createInertiaApp({
  title: (title) => `My App - ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`];
    page.default.layout = page.default.layout || Layout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
    .use(PrimeVue, { theme: { preset: Aura }})
    .component('Button', Button)
    .component('InputText', InputText)
    .component('Avatar', Avatar)
    .component('Textarea', Textarea)
    .component('Tree', Tree)
    .component('DataTable', DataTable)
    .component('Column', Column)
    .component('Calendar', Calendar)
    .component('Toast', ToastService)
    .component('Sidebar', Sidebar)
    .component('Drawer', Drawer)
      .use(plugin)
      .use(ZiggyVue)
      .use(ToastService)
      .component('Head', Head)
      .component('Link', Link)
      .component('Card', Card)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
    includeCSS: true,
    showSpinner: true,
  },
})