import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import th from './locales/th.json'

const messages = {
  en,
  th
}

const i18n = createI18n({
  legacy: false, // if using Composition API
  globalInjection: true,
  locale: localStorage.getItem('lang') || 'th',
  fallbackLocale: 'en',
  messages
})

export default i18n
