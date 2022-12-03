<template>
  <div id="static-page">
    <div
      class="container"
      v-html="getContent"
    />
  </div>
</template>
<script>
import { mapGetters } from "vuex"
export default {
    computed: {
        ...mapGetters(['getStoreSettings']),
        getContent() {
            let routeName = this.$route.name
            if (routeName == 'privacy') {
                return this.getStoreSettings.privacy_policy
            } else if (routeName == 'termService') {
                return this.getStoreSettings.term_service
            } else if (routeName == 'notice') {
                return this.getStoreSettings.cancel_return
            } else if (routeName == 'about') {
                return this.getStoreSettings.shipping_info
            }
            return ''
        }
    },
    mounted() {
        window.scrollTo(0,0)
    },
}
</script>

<style lang="scss">
    #static-page {
        padding-top: 20px;
        tbody, td, tfoot, th, thead, tr {
            border-width: 1px;
            padding: 10px;
            border-color: #efefef;
        }
    }
    @media (max-width: 767px) {
        #static-page {
            img {
                max-width: 100%;
                height: auto;
            }
        }
    }
</style>
