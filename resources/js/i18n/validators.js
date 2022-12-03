
import * as validators from '@vuelidate/validators'
import i18n from './index';

// or import { createI18nMessage } from '@vuelidate/validators'
const { createI18nMessage } = validators

const messagePath = ({ $validator }) => `validators.${$validator}`

// Create your i18n message instance. Used for vue-i18n@9
const withI18nMessage = createI18nMessage({ t: i18n.global.t.bind(i18n), messagePath })

// wrap each validator.
export const required = withI18nMessage(validators.required)
export const email = withI18nMessage(validators.email)
