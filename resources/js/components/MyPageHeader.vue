<template>
  <div class="content-header">
    <h2 class="title">
      {{ $t("myPage_header.myPage") }}
    </h2>
    <div class="delivery-status">
      <div class="delivery-status-badge">
        <div class="label">
          {{ $t("myPage_header.same") }}
        </div>
        <div class="badge-noti">
          <router-link to="#">
            {{ $t("myPage_header.ready") }} <span>0</span>
          </router-link>
        </div>
        <div class="badge-noti">
          <router-link to="#">
            {{ $t("myPage_header.shipping") }} <span>1</span>
          </router-link>
        </div>
        <div class="badge-noti">
          <router-link to="#">
            {{ $t("myPage_header.deli") }} <span>0</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "MyPageHeader",
};
</script>
<style lang="scss">
.content-header {
  padding: 0 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #555;
  .delivery-status {
    .badge-noti {
      display: inline-block;
      border: 1px solid gray;
      padding: 4px 15px;
      border-radius: 20px;
      a {
        color: #555;
      }
      &:hover {
        border: 1px solid #ef4130;
        color: #ef4130;
        a {
          color: #ef4130;
        }
      }
    }

    .delivery-status-badge {
      display: flex;
      flex-wrap: wrap;
      font-size: clamp(0.75rem, 3vw, 14px);
    }

    .delivery-status-badge div {
      margin-left: 5px;
    }

    .label {
      padding: 5px;
    }
  }
}
@media only screen and (max-width: 767px) {
  .content-header {
    padding: 0 0 20px;

    h2.title {
      display: none;
    }
    .delivery-status-badge div {
      margin-left: 2px !important;
    }
    .delivery-status {
      .delivery-status-badge {
        .label {
          display: block;
          width: 100%;
          margin-bottom: 10px;
        }
      }
    }
  }
}
</style>
