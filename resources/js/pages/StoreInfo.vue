<template>
  <div class="container">
    <div class="breadcrumbs">
      <ul>
        <li>
          <router-link to="#">
            {{ $t("store_info.home") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("store_info.myPage") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("store_info.personal") }}
          </router-link>
        </li>
      </ul>
    </div>
    <MyPageHeader />
    <div class="row storeInfo">
      <LeftMypage class="col-lg-2" />
      <div class="col-lg-10">
        <div class="d-flex header justify-content-between align-items-center">
          <h3 class="d-flex align-items-center">
            <span class="img"><img src="/img/icon/print.png"></span>
            <span class="text">{{ $t("store_info.storeInfo") }}</span>
          </h3>
          <p>{{ $t("store_info.ifYou") }}</p>
        </div>
        <div class="body mb-5 my-2">
          <form
            class="row g-3 mb-3"
            @submit.prevent="updateStore"
          >
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="stt"
                class="form-label"
              >{{ $t("store_info.turn") }}</label>
              <input
                id="stt"
                v-model="state.storeNo"
                class="form-control"
                readonly
              >
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="storeType"
                class="form-label"
              >{{ $t("store_info.storeType") }} </label>
              <select
                v-model="state.storeTp"
                class="form-control"
                disabled
                aria-label=""
              >
                <option
                  v-for="item, i in storeType"
                  :key="i"
                  :value="item.code"
                >
                  {{ item.name }}
                </option>
              </select>
              <!-- <input type="text" class="form-control" readonly v-model="state.storeTpName" id="storeType" /> -->
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="storeName"
                class="form-label"
              >{{ $t("store_info.storeName") }} </label>
              <input
                id="storeName"
                v-model="state.storeNm"
                type="text"
                class="form-control"
                readonly
              >
              <p
                v-for="error of v$.storeNm.$errors"
                :key="error.$uid"
                class="error-msg"
              >
                {{ error.$message }}
              </p>
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="shortName"
                class="form-label"
              >
                {{ $t("store_info.storeShortName") }}
              </label>
              <input
                id="shortName"
                v-model="state.storeShortNm"
                type="text"
                class="form-control"
                readonly
              >
              <p
                v-for="error of v$.storeShortNm.$errors"
                :key="error.$uid"
                class="error-msg"
              >
                {{ error.$message }}
              </p>
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <!-- <label for="GroupPrice" class="form-label">
                공급 단가 그룹
              </label>
              <input type="text" class="form-control" v-model="state.unitPriceCode" id="GroupPrice" /> -->
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <!-- <label for="orderPay" class="form-label">
                반품 금액 설정 (%)
              </label>
              <div class="input-group input-group-sm">
                <button
                  class="btn btn-outline-secondary dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  선택
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#">Action</a></li>
                </ul>
                <input
                  id="orderPay"
                  type="text"
                  class="form-control"
                  aria-label="Text input with dropdown button"
                />
              </div> -->
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <div class="row">
                <div class="col-md-6 col-sm-12 input-group-sm date">
                  <label
                    for="startDate"
                    class="form-label"
                  >{{ $t("store_info.startDate") }}
                  </label>
                  <input
                    id="startDate"
                    v-model="state.dealStrDt"
                    type="date"
                    class="form-control"
                    readonly
                  >
                </div>
                <div class="col-md-6 col-sm-12 input-group-sm date">
                  <label
                    for="endDate"
                    class="form-label"
                  >{{ $t("store_info.endDate") }} </label>
                  <input
                    id="endDate"
                    v-model="state.dealEndDt"
                    type="date"
                    class="form-control"
                    readonly
                  >
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="represent"
                class="form-label"
              >{{ $t("store_info.representative") }}</label>
              <input
                id="represent"
                v-model="state.representative"
                type="text"
                class="form-control"
                readonly
              >
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="country"
                class="form-label"
              >{{ $t("store_info.country") }}</label>
              <select
                v-model="state.country"
                class="form-control"
                disabled
                aria-label=""
              >
                <option
                  v-for="item, i in coutries"
                  :key="i"
                  :value="item.code"
                >
                  {{ item.name }}
                </option>
              </select>
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="phoneNo"
                class="form-label"
              > {{ $t("store_info.phoneNum") }} </label>
              <div class="input-group input-group-sm">
                <select
                  v-model="selectedPhone"
                  class="btn btn-outline-secondary"
                  disabled
                  aria-label=""
                >
                  <option
                    v-for="item, i in optionsRegion"
                    :key="i"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </select>
                <input
                  id="phoneNo"
                  v-model="state.phone"
                  type="text"
                  readonly
                  placeholder="032-1234-5678"
                  class="form-control"
                  aria-label="Text input with dropdown button"
                >
              </div>
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="email"
                class="form-label"
              >{{ $t("store_info.email") }}</label>
              <input
                id="email"
                v-model="state.email"
                type="text"
                class="form-control"
                readonly
              >
            </div>
            <div class="col-md-6 col-sm-12 input-group-sm">
              <label
                for="faxNo"
                class="form-label"
              > {{ $t("store_info.faxNo") }} </label>
              <div class="input-group input-group-sm">
                <select
                  v-model="selectedFax"
                  class="btn btn-outline-secondary"
                  disabled
                  aria-label=""
                >
                  <option
                    v-for="item, i in optionsRegion"
                    :key="i"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </select>
                <input
                  id="faxNo"
                  v-model="state.fax"
                  type="text"
                  readonly
                  placeholder="032-1234-5678"
                  class="form-control"
                  aria-label="Text input with dropdown button"
                >
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <label
                for="mobileNo"
                class="form-label"
              > {{ $t("store_info.mobiNum") }}</label>
              <div class="input-group input-group-sm">
                <select
                  v-model="selectedMobile"
                  class="btn btn-outline-secondary"
                  disabled
                  aria-label=""
                >
                  <option
                    v-for="item, i in optionsRegion"
                    :key="i"
                    :value="item"
                  >
                    {{ item }}
                  </option>
                </select>
                <input
                  id="mobileNo"
                  v-model="state.mobile"
                  readonly
                  type="text"
                  placeholder="032-1234-5678"
                  class="form-control"
                  aria-label="Text input with dropdown button"
                >
              </div>
            </div>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <label
                    for="code"
                    class="form-label"
                  >{{ $t("store_info.zipCode") }}</label>
                  <div class="input-group-sm d-flex mb-2">
                    <input
                      id="code"
                      v-model="state.storePostCd"
                      type="text"
                      class="form-control"
                      readonly
                      style="width: 80%"
                    >
                    <button
                      class="zipCode_search"
                      type="button"
                      @click="goPopup();"
                    >
                      <img src="/img/header/search.svg"> {{ $t("store_info.search") }}
                    </button>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="input-group-sm">
                    <label
                      for="address01"
                      class="form-label"
                    >{{ $t("store_info.baseAddr") }} </label>
                    <input
                      id="address01"
                      v-model="state.storeAddressBasic"
                      type="text"
                      class="form-control"
                      readonly
                    >
                    <label
                      for="address02"
                      class="form-label mt-3"
                    >{{ $t("store_info.detailAddr") }} </label>
                    <input
                      id="address02"
                      v-model="state.storeAddressDetail"
                      type="text"
                      class="form-control"
                      readonly
                    >
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="input-group-sm">
                <label
                  for="note"
                  class="form-label"
                >{{ $t("store_info.note") }} </label>
                <textarea
                  id="note"
                  v-model="state.storeNt"
                  type="text"
                  class="form-control textarea"
                  readonly
                  role="textbox"
                  contenteditable
                  style="min-height: 80px"
                />
              </div>
              <div class="text-center">
                <button class=" rounded border bg-light px-5 py-1 mt-3">
                  {{ $t("store_info.close") }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <LoadingPage v-if="loading" />
  </div>
</template>
<script>
import MyPageHeader from "../components/MyPageHeader";
import LeftMypage from "../components/LeftMypage";
import useVuelidate from '@vuelidate/core'
import { required } from "../i18n/validators";
import LoadingPage from '../components/LoadingPage'
import { reactive, computed } from "vue";

export default {
  name: "StoreInfo",
  components: {
    MyPageHeader,
    LeftMypage,
    LoadingPage,
  },
  mixins: [

  ],
  setup() {
    const state = reactive({
      storeNo: "",
      storeNm: "",
      unitPriceCode: "",
      dealStrDt: "",
      dealEndDt: "",
      countryName: "",
      email: "",
      mobile: "",
      storeNt: "",
      storeTp: "",
      storeTpName: "",
      storeShortNm: "",
      representative: "",
      phone: "",
      fax: "",
      storeAddressBasic: "",
      storeAddressDetail: "",
      storePostCd: "",
    });
    const rules = computed(() => {
      return {
        storeNm: { required },
        storeShortNm: { required },
      };
    });
    const v$ = useVuelidate(rules, state);
    return {
      state,
      v$,
    };
  },
  data() {
    return {
      loading: false,
      selectedMobile: '',
      selectedPhone: '',
      selectedFax: '',
      regionPhone: '',
      optionsRegion: ['+(82)', '+(84)'],
      store: {},
      storeType: [],
      coutries: [],
    }
  },
  async mounted() {
    await this.storeInfomation()
  },
  methods: {
    storeInfomation() {
      this.loading = true
      axios.get(apiURL + "/user/store")
        .then(res => {
          this.loading = false
          this.store = res.data.data
          this.filledInfoStire(res.data.data)
          this.getCoutries()
          this.getStoreType()
        })
        .catch(error => {
          console.log(error)
        })
    },
    getCoutries() {
      axios.post(apiURL + "/core/common-code/type", {
        codeType: "CU"
      })
        .then(res => {
          this.coutries = res.data.data
        }).catch(error => {
          console.log(error)
        })
    },
    getStoreType() {
      axios.post(apiURL + "/core/common-code/type", {
        codeType: "ST"
      })
        .then(res => {
          this.storeType = res.data.data
        }).catch(error => {
          console.log(error)
        })
    },
    filledInfoStire(data) {
      if (data.mobile != '' && data.mobile != null) {
        let mobileSplit = data.mobile.split(')')
        this.selectedMobile = mobileSplit[0] + ')'
        this.state.mobile = mobileSplit[1]
      }
      if (data.phone != '' && data.phone != null) {
        let mobileSplit = data.phone.split(')')
        this.selectedPhone = mobileSplit[0] + ')'
        this.state.phone = mobileSplit[1]
      }
      if (data.fax != '' && data.fax != null) {
        let mobileSplit = data.fax.split(')')
        this.selectedFax = mobileSplit[0] + ')'
        this.state.fax = mobileSplit[1]
      }
      this.state.storeNo = data.storeNo
      this.state.storeNm = data.storeNm
      this.state.unitPriceCode = data.unitPriceCode
      this.state.dealStrDt = this.$format.timeToStr(data.dealStrDt, '-')
      this.state.dealEndDt = this.$format.timeToStr(data.dealEndDt, '-')
      this.state.country = data.country
      this.state.countryName = data.countryName
      this.state.email = data.email
      this.state.storeNt = data.storeNt
      this.state.storeTp = data.storeTp
      this.state.storeTpName = data.storeTpName
      this.state.storeShortNm = data.storeShortNm
      this.state.representative = data.representative
      this.state.storeAddressBasic = data.storeAddressBasic
      this.state.storeAddressDetail = data.storeAddressDetail
      this.state.storePostCd = data.storePostCd
    },
    goPopup(){
      window.open("/popup-address","pop","width=570,height=420, scrollbars=yes, resizable=yes");
    },
    async updateStore() {
      const isFormCorrect = await this.v$.$validate();
      return
      if (!isFormCorrect) return;
      this.loading = true
      let params = {
        storeNm: this.state.storeNm,
        unitPrice: this.state.unitPriceCode,
        dealStrDt: this.state.dealStrDt,
        dealEndDt: this.state.dealEndDt,
        countryName: this.state.countryName,
        email: this.state.email,
        storeNt: this.state.storeNt,
        storeTp: this.state.storeTp,
        storeShortNm: this.state.storeShortNm,
        representative: this.state.representative,
        phone: this.selectedPhone + this.state.phone,
        fax: this.selectedFax + this.state.fax,
        mobile: this.selectedMobile + this.state.mobile,
        storeAddressBasic: this.state.storeAddressBasic,
        storeAddressDetail: this.state.storeAddressDetail,
        storePostCd: this.state.storePostCd,
        country: this.state.country,

        // not show front
        storeCd: this.store.storeCd,
        loanLimitAmount: this.store.loanLimitAmount,
        accoutingcd: this.store.accoutingcd,
        oldCd: this.store.oldCd,
        bussinessNumber: this.store.bussinessNumber,
        comporationNumber: this.store.comporationNumber,
        storeBussiness: this.store.storeBussiness,
        businessIndustry: this.store.businessIndustry,
        paymentAccountNum: this.store.paymentAccountNum,
        paymentDepositor: this.store.paymentDepositor,
        storeManager: this.store.storeManager,
        managerNt: this.store.managerNt,
        deliveryReceiver: this.store.deliveryReceiver,
        deliveryPostCd: this.store.deliveryPostCd,
        deliveryAddressBasic: this.store.deliveryAddressBasic,
        deliveryAddressDetail: this.store.deliveryAddressDetail,
        paymentBank: this.store.paymentBank,
        officeManager: this.store.officeManager,
        salesManager: this.store.salesManager,
      }
      axios.put(apiURL + "/user/store", params).then(res => {
        this.loading = false
        if (res.data.status == "201") {
          alert(this.$t("modal.addSuccess"))
        } else {
          alert("시스템 오류, 나중에 재설정하십시오");
        }
      }).catch(error => {
        console.log(error)
        alert("시스템 오류, 나중에 재설정하십시오");
      })
    }
  }
};
</script>
<style lang="scss" scoped>
.storeInfo {
  font-size: 14px;
  .error-msg {
    margin: 5px 0 0;
    color: #ef4130;
    font-size: 13px;
    font-style: italic;
  }
  .header {
    margin-bottom: 20px;
    h3 {
      font-size: clamp(14px, 2.5vw, 20px);
      width: 40%;
      margin: 0;
      span {
        display: block;
        &.img {
          margin-right: 10px;
        }
      }
    }
    p {
      font-size: clamp(10px, 1.5vw, 14px);
      color: red;
      width: 60%;
      text-align: end;
      padding: 5px 0;
      margin: 0;
    }
  }
  .body {
    position: relative;
    div {
      margin-bottom: 0px;
    }
    .close {
      display: block;
      margin: 0 auto;
    }
    .zipCode_search {
      width: 100px;
      border-radius: 30px !important;
      margin-left: 10px !important;
      border: 1px solid rgb(185, 182, 182);
      background: none;
      font-size: 13px;
      img {
        width: 13px;
        margin-right: 3px;
      }
    }
  }
}
@media only screen and (max-width: 767px) {
  .storeInfo {
    .date {
      margin-bottom: 15px !important;
      &:last-child {
        margin-bottom: 0 !important;
      }
    }
  }
}
</style>
