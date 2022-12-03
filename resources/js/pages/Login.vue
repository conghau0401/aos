<template>
  <div class="container wrap-login">
    <LoadingPage v-if="isLoading" />
    <div id="login">
      <p class="text">
        {{ $t("login.thisShopping") }}
      </p>
      <p class="text">
        {{ $t("login.nonMember") }}
      </p>

      <h2 class="form-title">
        {{ $t("login.memLogin") }}
      </h2>
      <div class="container">
        <div class="row justify-content-md-center">
          <div class="col col-md-8">
            <div
              v-if="showError"
              class="alert alert-danger"
            >
              <ul>
                <li>{{ $t("alert.wrongPassword") }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <form
        class="container login-form"
        @submit.prevent=""
      >
        <div class="form-body">
          <div class="form-group">
            <div class="form-input">
              <span for="email">{{ $t("login.id") }}</span>
              <input
                id="email"
                v-model="state.email"
                type="text"
                class="form-control"
              >
              <div
                v-for="error of v$.email.$errors"
                :key="error.$uid"
                class="error-msg"
              >
                {{ error.$message }}
              </div>
            </div>
            <div class="form-input">
              <span for="password">{{ $t("login.pw") }}</span>
              <input
                id="password"
                v-model="state.password"
                type="password"
                class="form-control"
              >
              <div
                v-for="error of v$.password.$errors"
                :key="error.$uid"
                class="error-msg"
              >
                {{ error.$message }}
              </div>
            </div>
          </div>
          <div class="form-btn">
            <button
              class="btn-submit rounded"
              @click="onSubmit"
            >
              {{ $t("login.login") }}
            </button>
          </div>
        </div>
        <div
          class="
            form-check
            d-flex
            flex-wrap
            align-items-center
            justify-content-center
          "
        >
          <p>
            <input
              id="auto-login"
              v-model="remindID"
              class=""
              type="checkbox"
              name="auto-login"
            >
            <label
              class="space"
              for="auto-login"
            >{{ $t("login.saveId") }}</label>
          </p>
          <p>
            <input
              id="pwd-forget"
              type="checkbox"
              name="pwd-remind"
            >
            <label
              class="space"
              for="pwd-forget"
            >{{ $t("login.autoLogin") }}</label>
          </p>
        </div>
      </form>
      <!-- <p class="text">
        회원으로 가입하시면, 쿠폰. 적립금 등 다향한 혜택을 받으실 수 있습니다.
        회원가입>>
      </p>
      <p class="text">
        아이디와 비밀번호를 잊으셨나요? 아이디 찾기 >> 비밀번호 찾기
      </p> -->
      <div class="wrapper-add-home d-flex
            flex-wrap
            align-items-center
            justify-content-center">
        <button class="add-button">{{ $t("login.addHomeScreen") }}</button>
      </div>
    </div>
  </div>
</template>

<script>
import useValidate from "@vuelidate/core";
import { required, email } from "../i18n/validators";
import { reactive, computed } from "vue";
import LoadingPage from '../components/LoadingPage'

export default {
  name: "Login",
  components: {
    LoadingPage
  },
  setup() {
    const state = reactive({
      email: "",
      password: "",
    });
    const rules = computed(() => {
      return {
        email: { required, email },
        password: { required },
      };
    });

    const v$ = useValidate(rules, state);

    return {
      state,
      v$,
    };
  },
  data() {
    return {
      showError: false,
      remindID: false,
      isLoading: false,
    };
  },
  mounted() {
    // restore remind ID
    let remindID = localStorage.getItem("remindID");
    if (remindID != null) {
      let ojbRemind = JSON.parse(remindID);
      this.state.email = ojbRemind.email;
      this.state.password = ojbRemind.password;
      this.remindID = true;
    }
    let recaptchaScript = document.createElement('script')
    recaptchaScript.setAttribute('src', '/index.js')
    recaptchaScript.setAttribute('defer', 'defer')
    document.head.appendChild(recaptchaScript)
  },
  methods: {
    async onSubmit() {
      const isFormCorrect = await this.v$.$validate();
      if (!isFormCorrect) return;
      //loading icon
      this.isLoading = true;
      // checked remind ID
      if (this.remindID) {
        let ojbRemind = {
          email: this.state.email,
          password: this.state.password,
        };
        localStorage.setItem("remindID", JSON.stringify(ojbRemind));
      } else {
        localStorage.removeItem("remindID");
      }
      axios
        .post(apiURL + "/login", {
          username: this.state.email,
          password: this.state.password,
        })
        .then((res) => {
          this.isLoading = false;
          if (!res.data.error) {
            // save access token
            localStorage.setItem("app_token", res.data.access_token);
            this.$i18n.locale = res.data.language
            localStorage.setItem('language', res.data.language)
            window.location.href = "/";
          } else {
            this.showError = true;
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
</script>

<style lang="scss" scope>
#aos-main {
  height: 100vh;
}
.wrap-login {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
#login {
  padding: 40px 0;
  font-size: 14px;
  .form-body {
    display: flex;
    justify-content: center;
    font-size: 14px;
  }
  .form-input {
    display: flex;
    align-items: center;
    padding: 0 25px;
    margin: 0 0 15px 0;
    width: 100%;
    position: relative;
  }
  h2.form-title {
    margin: 30px 0 50px;
    font-size: 25px;
    font-weight: bold;
  }
  .error-msg {
    position: absolute;
    bottom: -20px;
    right: 25px;
    color: #f00;
    font-size: 12px;
    font-style: italic;
  }
  .form-input input:focus {
    box-shadow: none;
    outline: none;
    border: 2px solid #a7a4a4;
  }
  span {
    margin-right: 45px;
    width: 30%;
  }
  .form-check {
    text-align: center;
    padding-left: 0;
    p {
      margin: 0 12px;
    }
  }
  .space {
    margin-left: 5px;
  }
  .add-button,
  button.btn-submit {
    outline: 0;
    border: none;
    background: #ef4130;
    padding: 35px 40px;
    color: #fff;
  }
  .add-button {
    background: #ef4130;
    padding: 8px 40px;
    border-radius: 6px;
  }
  .form-group.invalid input {
    border: 1px solid red;
  }
  p.text {
    margin-bottom: 15px;
  }
  .alert > ul {
    margin-bottom: 0;
  }
  .login-form {
    margin-bottom: 50px;
  }
}
@media only screen and (max-width: 767px) {
  #login {
    padding: 30px 0;
    form.container.login-form {
      padding: 0;
      font-size: 14px;
    }
    .form-body {
      display: block;
    }
    .form-input {
      padding: 0;
    }

    button.btn-submit.rounded {
      margin-bottom: 20px;
      padding: 5px;
      width: 100%;
      height: 38px;
      background: #ef4130;
      color: #fff;
    }
    p.text {
      margin-bottom: 5px;
    }
    h2.form-title {
      margin: 20px 0 30px;
      font-size: 20px;
    }
    .login-form {
      margin-bottom: 30px;
      .form-input {
        span {
          margin-right: 20px;
        }
      }
    }
  }
}
</style>
