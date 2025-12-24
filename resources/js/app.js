// app.js

import '../assets/vendor/libs/jquery/jquery.js';
import './bootstrap'
import '../assets/js/select2.full';
import '../assets/css/dev.css';

import Select2 from "../js/Components/Select2.vue";

import {createInertiaApp, usePage} from '@inertiajs/vue3'
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers"
import {ZiggyVue} from '../../vendor/tightenco/ziggy'
import {createApp, h} from 'vue'
import {registerPlugins} from '@core/utils/plugins'
import App from '@/App.vue'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'
import "vue3-toastify/dist/index.css";
import moment from "moment";

import { resolveStatusVariant } from '@/utils/helpers';

createInertiaApp({
    resolve: name => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
    setup({el, App: InertiaApp, props, plugin}) {

        const app = createApp({
            render: () => h(App, {
                inertiaApp: h(InertiaApp, props)
            })
        }).use(plugin)
            .use(ZiggyVue)

        // Register plugins
        registerPlugins(app)

        app.config.globalProperties.$filters = {
            formatDateTime(value) {
                if (value) {
                    return moment(String(value)).format("DD MMM YYYY, hh:mm A");
                } else {
                    return '-';
                }
            },
            formatDate(value) {
                if (value) {
                    return moment(String(value)).format("DD MMM YYYY");
                } else {
                    return '-';
                }
            },
            formatTime(value) {
                if (value) {
					const [hours, minutes] = value.split(":");
					const formattedTime = `${hours}:${minutes}`;
					return formattedTime;
                } else {
                    return '-';
                }
            },
            capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            },
            limitText(text, maxLength = 200) {
                if (text && text.length > maxLength) {
                    return text.substring(0, maxLength) + "...";
                }
                return text;
            },
			resolveStatusVariant(status) {
				return resolveStatusVariant(status)
			},
			can(permission) {
				const userPermissions = usePage().props.auth.user.permissions
				return userPermissions.includes(permission)
			}
        }

        app.component('Select2', Select2);
        // Mount the app
        app.mount(el)
    },
})
