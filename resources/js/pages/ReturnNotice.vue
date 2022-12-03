<template>
  <div class="container">
    <div class="breadcrumbs">
      <ul>
        <li>
          <router-link to="#">
            {{ $t("return_notice.home") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("return_notice.myPage") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("return_notice.returnNoti") }}
          </router-link>
        </li>
      </ul>
    </div>
    <MyPageHeader />
    <div class="row return-notice">
      <LeftMypage class="col-lg-2" />
      <div class="col-lg-10">
        <p class="mr-3">
          <img src="/img/icon/return.png">
          {{ $t("return_notice.returnNoti") }}
        </p>
        <div class="table-responsive-md">
          <table class="table mt-1">
            <thead>
              <tr>
                <th scope="col">
                  {{ $t("return_notice.notiNum") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.period") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.amount") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.rate") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.returnAmount") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.cumulative") }}
                </th>
                <th scope="col">
                  {{ $t("return_notice.remaining") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td scope="row">
                  A11211-15
                </td>
                <td>2021.10.01 ~ 2021.12.11</td>
                <td>15,200,000</td>
                <td>15%</td>
                <td>3,000,000</td>
                <td>1,250,000 [ 이력 조회 ]</td>
                <td>17,500,000</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import MyPageHeader from "../components/MyPageHeader";
import LeftMypage from "../components/LeftMypage";
export default {
  name: "ReturnNotice",
  components: {
    MyPageHeader,
    LeftMypage,
  },
};
</script>
<style lang="scss" scoped>
.return-notice {
  p {
    font-size: clamp(18px, 3vw, 23px);
    font-weight: 600;
  }
  table {
    color: #6d6969 !important;

    :not(:first-child) {
      border-top: none !important;
    }
  }
  thead {
    background: #e7e8e8;
    font-size: 13px;
  }
  tbody {
    font-size: 13px;
  }
}
</style>
