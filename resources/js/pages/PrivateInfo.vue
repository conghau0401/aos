<template>
  <div class="container">
    <div class="breadcrumbs">
      <ul>
        <li>
          <router-link to="#">
            {{ $t("private_info.home") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("private_info.myPage") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("private_info.personal") }}
          </router-link>
        </li>
      </ul>
    </div>
    <MyPageHeader />
    <div class="row privateInfo mb-5">
      <LeftMypage class="col-lg-2" />
      <div class="col-lg-10 mb-4">
        <div class="d-flex header justify-content-between">
          <h3 class="d-flex align-items-center">
            <span class="img"><img
              src="/img/icon/avatar.png"
              alt=""
              srcset=""
            ></span>
            <span class="text">{{ $t("private_info.personal") }}</span>
          </h3>
          <p>{{ $t("private_info.ifYou") }}</p>
        </div>
        <form
          id="personal-form"
          method="post"
          @submit.prevent="updatePersonal"
        >
          <div class="row">
            <div class="col-lg-8 col-md-12">
              <div class="row">
                <div class="col-lg-6 col-md-12">
                  <div class="input-group-sm">
                    <label
                      for="stt"
                      class="form-label"
                    >{{ $t("private_info.turn") }}</label>
                    <input
                      id="stt"
                      v-model="state.userId"
                      type="number"
                      class="form-control mb-2"
                      readonly
                    >
                    <label
                      for="userName"
                      class="form-label"
                    >{{ $t("private_info.memName") }} </label>
                    <input
                      id="userName"
                      type="text"
                      class="form-control mb-2"
                      readonly
                      :value="state.name"
                    >
                    <p
                      v-for="error of v$.name.$errors"
                      :key="error.$uid"
                      class="error-msg"
                    >
                      {{ error.$message }}
                    </p>

                    <label
                      for="email"
                      class="form-label mb-2"
                    >{{ $t("private_info.email") }}</label>
                    <input
                      id="email"
                      type="text"
                      class="form-control mb-2"
                      readonly
                      :value="state.email"
                    >
                    <p
                      v-for="error of v$.email.$errors"
                      :key="error.$uid"
                      class="error-msg"
                    >
                      {{ error.$message }}
                    </p>

                    <label
                      for="mobileNo"
                      class="form-label mb-2"
                    >
                      {{ $t("private_info.mobileNum") }}
                    </label>
                    <div class="input-group input-group-sm mb-2">
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
                        id="mobileNo"
                        type="text"
                        placeholder="032-1234-5678"
                        class="form-control"
                        aria-label="Text input with dropdown button"
                        readonly
                        :value="state.mobile"
                      >
                    </div>
                    <label
                      for="phoneNo"
                      class="form-label mb-2"
                    > {{ $t("private_info.phoneNum") }} </label>
                    <div class="input-group input-group-sm mb-2">
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
                        id="phoneNo"
                        type="text"
                        placeholder="032-1234-5678"
                        class="form-control"
                        aria-label="Text input with dropdown button"
                        readonly
                        :value="state.phone"
                      >
                    </div>

                    <!-- <label for="bankName" class="form-label">은행 </label>
                    <input type="number" class="form-control mb-2" id="bankName" />

                    <label for="bankOwner" class="form-label">예금주</label>
                    <input type="text" class="form-control mb-2" id="bankOwner" /> -->
                  </div>
                </div>
                <div class="col-lg-6 col-md-12">
                  <div class="imageUpload input-group-sm">
                    <div id="preview">
                      <img
                        v-if="state.avatar"
                        :src="state.avatar"
                      >
                    </div>
                    <input
                      type="file"
                      class="form-control"
                      style="margin-bottom: 32px"
                      @change="onFileChange"
                    >

                    <!-- <label for="stt" class="form-label">계좌번호</label>
                    <input type="number" class="form-control mb-2" id="stt" /> -->
                  </div>
                </div>
              </div>
              <!-- <div class="row">
                <div class="col-lg-4">
                  <label for="zipCode" class="form-label">우편번호</label>
                  <div class="input-group input-group-sm mb-2">
                    <input
                      type="text"
                      class="form-control"
                      id="zipCode"
                    />
                    <button class="zipCode_search">
                      <img src="/img/header/search.svg" /> Search
                    </button>
                  </div>
                </div>
                <div class="col-lg-8 input-group-sm">
                  <label for="stt" class="form-label">기본 주소 </label>
                  <input type="text" class="form-control mb-2" id="stt" />
                  <label for="stt" class="form-label">상세 주소 </label>
                  <input type="text" class="form-control mb-2" id="stt" />
                </div>
              </div> -->
            </div>
            <div class="col-lg-4 col-md-12 text-right">
              <div class="note">
                <div class="mb-5">
                  <div v-html="$t('private_info.memInfo')" />
                </div>
                <p>{{ $t("private_info.consent") }}</p>
                <!-- <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="flexCheckDefault"
                  />
                  <label class="form-check-label" for="flexCheckDefault">
                    휴대전화 (카톡 또는 문자메세지)
                  </label>
                </div> -->
                <!-- <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    value=""
                    id="flexCheckChecked"
                    checked
                  />
                  <label class="form-check-label" for="flexCheckChecked">
                    이메일
                  </label>
                </div> -->
                <p class="mt-5">
                  {{ $t("private_info.weWill") }}
                </p>
              </div>
            </div>
          </div>
          <button class="close text-center rounded border px-5 py-1">
            {{ $t("private_info.close") }}
          </button>
        </form>
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
  name: "PrivateInfo",
  components: {
    MyPageHeader,
    LeftMypage,
    LoadingPage,
  },
  setup() {
    const state = reactive({
      userId: "",
      name: "",
      email: "",
      mobile: "",
      phone: "",
      avatar: "",
    });
    const rules = computed(() => {
      return {
        name: { required },
        email: { required },
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
      optionsRegion: ['+(82)', '+(84)'],
      selectedMobile: '',
      selectedPhone: '',
      loading: true
    }
  },
  async mounted() {
    await this.userInfomation()
  },
  methods: {
    userInfomation() {
      axios.get(apiURL + "/user/my-page")
        .then(res => {
          this.loading = false
          this.filledInfoUser(res.data)
        })
        .catch(error => {
          console.log(error)
        })
    },
    onFileChange(e) {
      const file = e.target.files[0];
      let formData = new FormData();
      formData.append('file', file);
      axios.post(apiURL + '/files/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
      ).then(res => {
        if (res.data.success == true) {
          this.state.avatar = res.data.data.path
        }
      })
      .catch(function(error){
        console.log(error);
      });
    },
    async updatePersonal() {
      const isFormCorrect = await this.v$.$validate();
      if (!isFormCorrect) return;
      this.loading = true
      axios.put(apiURL + "/user/update-info", {
          "name": this.state.name,
          "email": this.state.email,
          "mobile": this.selectedMobile + this.state.mobile,
          "phone": this.selectedPhone + this.state.phone,
          "avatar": this.state.avatar,
        }).then(res => {
          this.loading = false
          if (res.data.status == "200") {
            alert(this.$t("modal.addSuccess"))
          } else {
            this.loading = false
            alert("시스템 오류, 나중에 재설정하십시오");
          }
      }).catch(error => {
          console.log(error)
          this.loading = false
          alert("시스템 오류, 나중에 재설정하십시오");
      })
    },
    filledInfoUser(data) {
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
      this.state.userId = data.userId
      this.state.name = data.name
      this.state.email = data.email
      this.state.avatar = data.avatar
    },
  },
};
</script>
<style lang="scss" scoped>
.privateInfo {
  font-size: 14px;
  color: rgb(109, 106, 106);
  .error-msg {
    margin: 5px 0 0;
    color: #ef4130;
    font-size: 13px;
    font-style: italic;
  }
  #zipCode {
    border-radius: 0.2rem;
  }
  .zipCode_search {
    font-size: 13px;
    img {
      width: 13px;
      margin-right: 3px;
    }
  }
  .header {
    margin-bottom: 20px;
    h3 {
      font-size: clamp(14px, 2.5vw, 20px);
      width: 40%;
      font-weight: bold;
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
  #preview {
    border: 1px solid rgb(185, 182, 182);
    border-radius: 10px;
    padding: 10px;
    height: 216px;
    margin: 30px 0;
    overflow: hidden;
    img {
      width: 100%;
      height: 100%;
      display: block;
      margin: 0 auto;
      object-fit: cover;
    }
  }
  .note {
    margin: 30px 0;
  }
  .zipCode_search {
    width: 100px;
    border-radius: 30px !important;
    margin-left: 10px !important;
    border: 1px solid rgb(185, 182, 182);
    background: none;
  }
  .close {
    display: block;
    margin: 30px auto 0 auto;
    background: #ef4130;
    color: #fff;
    &:active {
      color: rgb(65, 65, 65);
    }
  }
}
</style>
