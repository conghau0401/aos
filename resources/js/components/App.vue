<template>
  <div>
    <title>{{ titlePage }}</title>
    <HeaderComponent v-if="hasLayout" />
    <div id="aos-main">
      <router-view />
    </div>
    <FooterComponent v-if="hasLayout" />
  </div>
</template>

<script>
import "bootstrap/dist/css/bootstrap.min.css";
import HeaderComponent from "./common/HeaderComponent.vue"
import FooterComponent from "./common/FooterComponent.vue"
export default {
    components: {
        HeaderComponent,
        FooterComponent,
    },
    data() {
        return {
            hasLayout: true,
        }
    },
    computed: {
        titlePage() {
            if (this.$route.meta.title != 'undefined') {
                document.title = this.$i18n.t(this.$route.meta.title)
            }
            return this.$i18n.t(this.$route.meta.title)
        }
    },
    created() {
        if (this.$route.query.isBlank == 1 || this.$route.path == '/login') {
            this.hasLayout = false
        }
    }
}
</script>

<style lang="scss">
    @font-face {
        font-family: "Cafe24Simplehae";
        src: local("Cafe24Simplehae"),
        url(/fonts/Cafe24Simplehae.ttf) format("truetype");
    }
    #app {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #444;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: "Cafe24Simplehae", Helvetica, Arial, sans-serif;
    }
    img {
        max-width: 100%;
    }
    .hard-code, .hard-code *{
        color: #ff0000 !important;
    }
    /* reset font icon */
    input[type="password"],
    ul.pagination .page-link {
        font-family: Arial, Helvetica, sans-serif
    }
    ul,
    li {
        list-style: none;
    }
    a {
        text-decoration: none;
    }
    .b-flying-img {
        position: absolute;
        width: 200px;
        height: 200px;
        top: 0;
        left: 0;
        z-index: 12;
        animation:fly 0.8s 1;
        -webkit-animation:fly 0.8s 1;
        -webkit-backface-visibility: hidden;
    }
    .increment, .reduction {
        svg {
            width: 10px;
            height: 10px;
            fill: #555;
        }
    }

    @keyframes fly {
        0% {
            -moz-transform: scale(0);
            -ms-transform: scale(0);
            -o-transform: scale(0);
            -webkit-transform: scale(0);
            transform: scale(0);
        }
        100% {
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            -o-transform: scale(1);
            -webkit-transform: scale(1);
            transform: scale(1);
        }
    }

    @-webkit-keyframes fly {
        0% {
            -webkit-transform: scale(1);
        }
        100% {
            -webkit-transform: scale(1);
        }
    }

    @media (min-width: 1400px) {
        .container {
            max-width: 1200px !important;
        }
    }
</style>
