require("./bootstrap");
require("/css/breadcrumb.scss");
require("/css/arrows.scss");
require("/css/rating.scss");
require("/css/pagination.scss");

window.Vue = require("vue");
import App from "./components/App.vue";
import router from "./router/index";
import store from "./store/index";
import i18n from "./i18n/index";
import BootstrapVue from 'bootstrap-vue-3'
import printer from './plugins/printer'
import { format } from './plugins/format'
import "./dbr"
window.$ = window.jQuery = require("jquery");
window.apiURL = process.env.MIX_API_URL;

// Create and mount the root instance.
const app = Vue.createApp({
    components: { App },
});

app.use(BootstrapVue);
app.use(i18n);
app.use(router);
app.use(store);
app.use(printer)
router.isReady().then(() => {
    app.mount("#app");
})

// setup global properties
app.config.globalProperties.$format = format
