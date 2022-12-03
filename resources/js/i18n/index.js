import { createI18n } from 'vue-i18n'
import en from './en.js'
import vi from './vi.js'
import ko from './ko.js'

const messages = {
    en,
    vi,
    ko,
}

const i18n = createI18n({
    locale: 'ko', // set locale
    fallbackLocale: 'ko', // set fallback locale
    messages, // set locale messages
})

if (localStorage.getItem('language') != null) {
    i18n.global.locale = localStorage.getItem('language')
}

export default i18n;
