<template>
  <div class="container">
    <div class="breadcrumbs">
      <ul>
        <li>
          <router-link to="#">
            {{ $t("deposit.home") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("deposit.myPage") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("deposit.account") }}
          </router-link>
        </li>
      </ul>
    </div>
    <div class="deposit-page">
      <MyPageHeader />
      <div class="content row">
        <LeftMypage class="col-lg-2" />
        <div class="col-lg-6">
          <div class="deposit-title py-2">
            <div class="d-flex">
              <img src="/img/icon/Picture2.png">
              <h3>{{ $t("deposit.account") }}</h3>
            </div>
            <div class="contact">
              <p>{{ $t("deposit.bank") }} {{ store.paymentBankName }} {{ store.paymentAccountNum }} {{ store.paymentDepositor }}</p>
              <p>{{ $t("deposit.infomation") }}</p>
            </div>
          </div>
          <div class="deposit-table table-responsive">
            <table class="table table-hover">
              <thead class="thead-bg-gray">
                <tr>
                  <th scope="col">
                    {{ $t("deposit.number") }}
                  </th>
                  <th scope="col">
                    {{ $t("deposit.history") }}
                  </th>
                  <th scope="col">
                    {{ $t("deposit.aDeposit") }}
                  </th>
                  <!-- <th scope="col">구매금액</th> -->
                  <th scope="col">
                    {{ $t("deposit.dateOf") }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="item, index in paginatedItems"
                  :key="index"
                >
                  <th scope="row">
                    {{ index + 1 }}
                  </th>
                  <td>{{ item.receivableManagementGroup }}</td>
                  <td><span v-if="item.type=='Deposit'">{{ $format.price(item.amount) }}</span></td>
                  <!-- <td><span v-if="item.type=='Deduction'">-{{ $format.price(item.amount) }}</span></td> -->
                  <td>{{ $format.timeToStr(item.date, '-') }}</td>
                </tr>
              </tbody>
            </table>
            <div v-if="rows > perPage">
              <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                align="center"
                @page-click="onPageChanged"
              />
            </div>
          </div>
        </div>

        <div class="col-lg-4 deposit-page__title">
          <div
            class="
              d-flex
              justify-content-between
              align-items-center
              border border-gray-600
              p-2
              mb-3
            "
          >
            <p>
              <img src="/img/icon/Picture3.png">
              {{ $t("deposit.theDeposit") }}
            </p>
            <p><span>{{ $format.price(depositNumber) }}</span> {{ $t("deposit.won") }}</p>
            <p>{{ $t("deposit.credit") }}</p>
            <p><span>{{ $format.price(creditBalance) }}</span> {{ $t("deposit.won") }}</p>
          </div>
          <div class="d-flex justify-content-end align-item-center">
            <!-- <div class="border transfer">
              <img src="/img/icon/Picture4.png">
              {{ $t("deposit.toCharge") }}
            </div> -->
            <div
              class="border transfer"
              @click="isShow = !isShow"
            >
              <img src="/img/icon/Picture5.png">
              {{ $t("deposit.certificate") }}
            </div>
          </div>
          <div class="cerDate-table mt-3">
            <div
              class="
                header
                d-flex
                justify-content-between
                align-item-center
                border-0
                p-2
              "
            >
              <p>{{ $t("deposit.issuance") }}</p>
              <p>{{ $t("deposit.issueDate") }}</p>
            </div>
            <div class="body py-2">
              <ul
                v-for="item, index in listCertificate"
                :key="index"
                class="d-flex justify-content-between border-bottom py-1 mb-1"
              >
                <li class="">
                  {{ item.certificate }}
                </li>
                <li
                  class=""
                  style="width: 40%"
                >
                  <ul class="">
                    <li class="border-bottom">
                      <ul class="d-flex justify-content-between">
                        <li>{{ $t("deposit.theDeposit") }}</li>
                        <li>{{ item.creditAmount >= 0 ? $format.price(item.creditAmount) : 0 }}{{ $t("deposit.won") }}</li>
                      </ul>
                    </li>
                    <li class="">
                      <ul class="d-flex justify-content-between px-0">
                        <li>{{ $t("deposit.credit") }}</li>
                        <li>{{ item.creditAmount < 0 ? $format.price(item.creditAmount) : 0 }}{{ $t("deposit.won") }}</li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <li class="">
                  {{ $format.timeToStr(item.createdDate, '-') }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div
        v-show="isShow"
        id="print-content"
        class="billPrt row py-3"
      >
        <div class="p-2 border">
          <div class="float-end pb-2">
            <img
              onerror="this.src='/img/icon/ponang.png'"
              :src="getStoreSettings.logo_url"
            >
          </div>
          <div class="clearfix" />
          <div class="wrapper-issued">
            <div
              v-if="!isShowDetail"
              class="wrap-btn-issued d-flex justify-content-center mb-3"
            >
              <button
                type="button"
                class="btn btn-danger"
                @click="isShowDetail = !isShowDetail"
              >
                {{ $t("deposit.btnIssued") }}
              </button>
            </div>
            <div
              v-show="isShowDetail"
              class="detail-issued"
            >
              <h2 class="py-3">
                {{ $t("deposit.certificate") }}
              </h2>
              <p>{{ $t("deposit.issuance") }} : {{ certificate }}</p>
              <div class="headerBar d-flex">
                <p class="title">
                  {{ $t("deposit.balance") }}
                </p>
                <p class="contact">
                  {{ store.storeNm }} {{ store.storeNo }}
                </p>
              </div>
              <div class="pt-5">
                <p>
                  {{ $t("deposit.amount") }} {{ todayYear }}{{ $t("deposit.year") }} {{ todayMonth }}{{ $t("deposit.month") }} {{ todayDay }}{{ $t("deposit.who") }} {{ centerInfo.centerName }} {{ $t("deposit.who2") }}
                  {{ $t("deposit.theName") }}
                </p>
                <div class="row g-1">
                  <div class="col-6">
                    <div
                      class="
                        px-2
                        py-3
                        rounded
                        border border-primary
                        text-center text-primary
                      "
                    >
                      <p>{{ $t("deposit.balanceD") }}</p>
                      <p>{{ $format.price(depositNumber) }}<span>{{ $t("deposit.won") }}</span></p>
                    </div>
                  </div>
                  <div class="col-6">
                    <div
                      class="
                        px-2
                        py-3
                        rounded
                        border border-danger
                        text-center text-danger
                      "
                    >
                      <p>{{ $t("deposit.credit") }}</p>
                      <p>{{ $format.price(creditBalance) }}<span>{{ $t("deposit.won") }}</span></p>
                    </div>
                  </div>
                </div>
                <p class="pt-3 mb-1">
                  {{ $t("deposit.purpose") }}
                </p>
                <p>
                  {{ $t("deposit.check") }} (<a
                    href="https://staging.aos.smdc-dev.com"
                    class="text-secondary"
                    title="staging.aos.smdc-dev.com"
                    target="_blank"
                  >staging.aos.smdc-dev.com</a>)
                  {{ $t("deposit.youCan") }}
                </p>
                <p class="mt-5 text-center">
                  {{ todayYear }}{{ $t("deposit.year") }} {{ todayMonth }}{{ $t("deposit.month") }} {{ todayDay }}{{ $t("deposit.day") }}
                </p>
                <div class="footer mb-2 d-flex justify-content-between">
                  <div class="info">
                    <h2>{{ centerInfo.centerName }}</h2>
                    <p>{{ $t("deposit.ceo") }} {{ centerInfo.centerManager }}</p>
                    <p>{{ $t("deposit.company") }} {{ centerInfo.centerCorporateNumber }}</p>
                    <p>{{ $t("deposit.addr") }} {{ centerInfo.centerAddressBasic }}</p>
                    <p>포항 {{ centerInfo.centerRepresentativePhone }}</p>
                  </div>
                  <div class="stamp">
                    <p>{{ $t("deposit.proof") }}</p>
                    <div class="rounded border text-center px-2 py-4">
                      <img
                        onerror="this.src='/img/icon/stamp.png'"
                        :src="centerOptions.stampImageUrl"
                        width="56"
                      >
                    </div>
                  </div>
                </div>
                <div class="wrapper-btn-certificate d-flex align-items-center justify-content-center mb-3">
                  <button
                    type="button"
                    class="btn btn-danger mx-3"
                    @click="getStoreCertificate"
                  >
                    확인
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger mx-3"
                    @click="print"
                  >
                    출력
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <LoadingPage v-if="loading" />
  </div>
</template>
<script>
import MyPageHeader from "../components/MyPageHeader";
import LeftMypage from "../components/LeftMypage";
import LoadingPage from '../components/LoadingPage'
import { mapGetters } from "vuex"
import moment from 'moment-timezone'

export default {
  name: "Deposit",
  components: {
    LeftMypage,
    MyPageHeader,
    LoadingPage,
  },
  mixins: [],
  data() {
    return {
      isShow: false,
      isShowDetail: false,
      listDeposit: [],
      paginatedItems: [],
      currentPage: 1,
      perPage: 10,
      store: {},
      storeCredit: {},
      centerInfo: {},
      centerOptions: {},
      depositNumber: 0,
      creditBalance: 0,
      todayDay: "01",
      todayMonth: "01",
      todayYear: "2022",
      loading: false,
      certificate: "",
      listCertificate: [],
    };
  },
  computed: {
    ...mapGetters(['getStoreSettings']),
    rows() {
      return this.listDeposit.length
    },
  },
  created() {
    this.getListDeposit()
    this.getStoreInfo()
    this.getStoreCredit()
    this.getCenterInfo()
    this.getListCertificate()
  },
  mounted() {
    this.todayDay = moment().tz('Asia/Seoul').format('DD')
    this.todayMonth = moment().tz('Asia/Seoul').format('MM')
    this.todayYear = moment().tz('Asia/Seoul').format('YYYY')
  },
  methods: {
    onPageChanged(event, page) {
      this.paginate(this.perPage, page - 1);
    },
    paginate(pageSize, pageNumber) {
      let itemsToParse = this.listDeposit
      this.paginatedItems = itemsToParse.slice(
        pageNumber * pageSize,
        (pageNumber + 1) * pageSize
      );
    },
    getListDeposit() {
      this.loading = true
      axios.get(apiURL + "/user/store-deposit-deduction")
      .then(response => {
        this.loading = false
        this.listDeposit = response.data.data
        console.log(this.listDeposit)
        this.paginate(this.perPage, 0)
      }).catch(error => {
        console.log(error)
      });
    },
    getCenterInfo() {
      axios.get(apiURL + "/core/center-info")
      .then(response => {
        this.centerInfo = response.data.data
        this.getStamp(this.centerInfo.centerCode)
      }).catch(error => {
        console.log(error)
      });
    },
    getStamp(centerCode) {
      axios.get(apiURL + "/core/center-option/" + centerCode)
      .then(response => {
        this.centerOptions = response.data.data
      }).catch(error => {
        console.log(error)
      });
    },
    getStoreCredit() {
      axios.get(apiURL + "/user/credit")
      .then(response => {
        this.storeCredit = response.data.data
        if (this.storeCredit.credit_amount >= 0) {
          this.depositNumber = this.storeCredit.credit_amount
          this.creditBalance = 0
        } else {
          this.depositNumber = 0
          this.creditBalance = this.storeCredit.credit_amount
        }
      }).catch(error => {
        console.log(error)
      });
    },
    getStoreInfo() {
      axios.get(apiURL + "/user/store")
      .then(response => {
        this.store = response.data.data
      }).catch(error => {
        console.log(error)
      });
    },
    getStoreCertificate() {
      this.loading = true
      axios.get(apiURL + "/user/store-certificate")
      .then(response => {
        this.loading = false
        this.certificate = response.data.data
      }).catch(error => {
        console.log(error)
      });
    },
    getListCertificate() {
      axios.get(apiURL + "/user/list-certificate")
      .then(response => {
        this.listCertificate = response.data.data
        if (this.listCertificate.length > 0) {
            this.certificate = this.listCertificate[0].certificate
        }
      }).catch(error => {
        console.log(error)
      });
    },
    print() {
      this.$printHtml('print-content', {
        styles: [
          'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
          process.env.MIX_APP_URL + 'css/print.css',
        ]
      });
    }
  }
};
</script>
<style lang="scss">
$text-color: #575555;

.deposit-page {
  font-size: 14px;
  table {
    color: #6d6969;

    :not(:first-child) {
      border-top: none;
    }
  }
  tbody {
    td:nth-child(3) {
      color: #00a6dc;
    }
    td:nth-child(4) {
      color: #d33bcc;
    }
  }
  thead {
    background: #e7e8e8;
  }

  &__title {
    p {
      margin: 0;
    }
    .border {
      border: 1px solid #dee2e6 !important;
      padding: 8px 10px;
      &.transfer {
        cursor: pointer;

        &:active {
          color: red;
        }
      }
    }
    .cerDate-table {
      .header {
        background: #e7e8e8;
        font-weight: 600;
        color: $text-color;
      }
      .body {
        font-size: 12px;

        ul {
          padding-left: 0;
          width: 100%;
        }
      }
    }
  }

  .deposit-title {
    display: flex;
    justify-content: space-evenly;
    img {
      height: fit-content;
      margin: 0 5px 15px 0;
    }
  }

  h3 {
    font-size: clamp(16px, 1vw, 24px);
    line-height: 1.3;
    color: $text-color;
    font-weight: bold;
    padding-top: 5px;
  }

  .contact {
    font-size: clamp(11px, 3vw, 14px);
    margin: 0 0 0 10px;
    line-height: 0.8;
  }
  .billPrt {
    font-size: clamp(10px, 3vw, 11px);
    width: 50%;
    margin: 0 auto;
    .float-end {
      img {
        max-height: 40px;
      }
    }
    h2 {
      font-size: clamp(16px, 3vw, 20px);
      font-weight: bold;
    }

    .headerBar {
      border-bottom: 2px solid #ef4130;
      p.title {
        position: relative;
        background: #ef4130;
        color: #fff;
        height: 45px;
        line-height: 47px;
        text-align: center;

        margin-bottom: 0;
        border-radius: 10px 12px 0 0;
        width: 35%;
      }
      p.contact {
        padding-left: 5px;
        width: 65%;
        line-height: 45px;
      }
    }
    .wrap-btn-issued {
      button {
        min-width: 120px;
        background-color: #ed3f36;
        border-radius: 0;
      }
    }
    .wrapper-btn-certificate {
      button {
        font-size: inherit;
        background: #ef4130;
        padding: 6px;
        min-width: 110px;
        color: #fff;
        border-radius: 0;
        &:hover {
          background: #c72e26;
        }
        &:focus {
          outline: none !important;
        }
      }
    }
  }
}
@media screen and (max-width: 767px) {
  .deposit-page {
    .deposit-title {
      display: inline-block;
      margin-top: 10px;
    }
    h3 {
      line-height: 1;

      padding-top: 10px;
      margin-right: 10px;
    }

    .contact {
      line-height: 1;
      margin: 0;
    }
  }
  .billPrt {
    width: 100% !important;
  }
}
</style>
