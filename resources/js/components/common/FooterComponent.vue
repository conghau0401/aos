<template>
  <footer>
    <div class="footer-head">
      <div class="container d-flex align-items-center flex-wrap">
        <router-link to="/about">
          {{ $t("footer.about") }}
        </router-link>
        <router-link to="/notice">
          {{ $t("footer.notice") }}
        </router-link>
        <router-link to="/terms-of-service">
          {{ $t("footer.term") }}
        </router-link>
        <router-link to="/privacy">
          {{ $t("footer.policy") }}
        </router-link>
      </div>
    </div>
    <div class="container">
      <div class="footer-body">
        <div
          class="content"
          v-html="getStoreSettings.bottom_info"
        />
      </div>
    </div>
  </footer>
</template>

<script>
import { mapGetters } from "vuex"
export default {
  computed: {
    ...mapGetters(['getStoreSettings']),
  },
}
</script>


<style scoped>
.footer-head {
  margin-bottom: 20px;
  border-bottom: 2px solid #ccc;
  border-top: 2px solid #ccc;
  padding: 20px 0;
}
.footer-head a {
  margin-right: 50px;
  position: relative;
  color: #444;
  text-decoration: none;
}
.footer-head a:not(:last-child)::before {
  content: "|";
  position: absolute;
  top: 0;
  right: -25px;
}

.footer-body {
  padding: 20px 0;
  font-size: clamp(10px, 1vw, 12px);
}
.footer-body a {
  text-decoration: underline;
  color: #444;
}
.footer-body-left {
  padding-right: 100px;
}
.footer-body-left .space-top {
  margin-top: 15px;
}
.footer-body-right {
  border-left: 2px solid #ccc;
  padding-left: 20px;
}
.footer-body p {
  margin-bottom: 10px;
  /* font-size: 10px; */
}
footer h1 {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0;
  color: var(--kim-ig-yong);
}
.footer-body .space span,
.footer-body .space b {
  margin-right: 25px;
}
.footer-body .space span.verticalBar::after {
  content: "|";
  margin-left: 25px;
}

@media only screen and (max-width: 767px) {
  .footer-body-left {
    padding-right: 0;
  }
  .footer-body-right {
    border: none;
    padding-left: 0;
    margin-bottom: 25px;
  }
  .footer-head a {
    margin-right: 20px;
    font-size: 14px;
  }
  .footer-head a:not(:last-child)::before {
    right: -25%;
  }
  .footer-body .space span,
  .footer-body .space b {
    margin-right: 5px;
  }
  .footer-body .space span.verticalBar::after {
    content: "|";
    margin-left: 5px;
  }
  footer .info1 {
    order: 2;
  }
  footer .info2 {
    order: 1;
  }
  .footer-body {
    padding-bottom: 0;
  }
}
</style>
