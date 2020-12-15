import Vue from 'vue'
import VueYandexMetrika from 'vue-yandex-metrika'  

Vue.use(VueYandexMetrika, {
    id: 67456357,
    env: 'production',
    debug: false,
    scriptSrc: "https://mc.yandex.ru/metrika/tag.js",
    options: {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    }
})