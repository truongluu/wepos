// import Vue from 'vue'
import store from "@/utils/store/";
import App from "./App.vue";
import router from "./router";

let Vue = wepos_get_lib("Vue");

Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
    el: "#vue-frontend-app",
    router,
    store,
    render: (h) => h(App),
    created() {
        this.setLocaleData(wepos.i18n["wepos"]);
        if (wepos.i18n["wepos-pro"]) {
            this.setLocaleData(wepos.i18n["wepos-pro"]);
        }
    },
});
