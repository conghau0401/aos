<template>
  <popup-modal ref="popup">
    <div
      id="style-scrollbar"
      class="popup-content"
    >
      <form
        action=""
        name="payForm"
        method="post"
        @submit.prevent="payment"
      >
        <input
          type="hidden"
          name="PayMethod"
          :value="paymentMethod"
        >
        <input
          type="hidden"
          name="GoodsName"
          value="Credit charge"
        >
        <input
          type="hidden"
          name="Amt"
          :value="amountResult"
        >
        <input
          type="hidden"
          name="MID"
          :value="paymentInfo.mid"
        >
        <input
          type="hidden"
          name="Moid"
          :value="paymentInfo.moid"
        >
        <input
          type="hidden"
          name="BuyerName"
          :value="storeInfos.storeNm"
        >
        <input
          type="hidden"
          name="BuyerEmail"
          :value="storeInfos.email"
        >
        <input
          type="hidden"
          name="BuyerTel"
          :value="storeInfos.mobile"
        >
        <input
          type="hidden"
          name="ReturnURL"
          value="/result-payment"
        >
        <input
          type="hidden"
          name="VbankExpDate"
          :value="paymentInfo.vitural_date"
        >

        <input
          type="hidden"
          name="NpLang"
          value="KO"
        > <!-- EN:English, CN:Chinese, KO:Korean -->
        <input
          type="hidden"
          name="GoodsCl"
          value="1"
        ><!-- products(1), contents(0)) -->
        <input
          type="hidden"
          name="TransType"
          value="0"
        ><!-- USE escrow false(0)/true(1) -->
        <input
          type="hidden"
          name="CharSet"
          value="utf-8"
        ><!-- Return CharSet -->
        <input
          type="hidden"
          name="ReqReserved"
          value=""
        ><!-- mall custom field -->
        <!-- DO NOT CHANGE -->
        <input
          type="hidden"
          name="EdiDate"
          :value="paymentInfo.date"
        ><!-- YYYYMMDDHHMISS -->
        <input
          type="hidden"
          name="SignData"
          :value="paymentInfo.hash"
        ><!-- EncryptData -->
        <div class="wrapper-payment row">
          <div class="payment-left col-md-4">
            <div class="store-info">
              <h2>{{ storeInfos.storeNm }}</h2>
              <div class="store-code">
                Store code: {{ storeInfos.storeNo }}
              </div>
            </div>
            <div class="frm-payment">
              <div class="form-group">
                <div class="balance">
                  <p class="label">
                    Balance
                  </p>
                  <p class="amount">
                    {{ $format.price(creditAmount) }} <span>won</span>
                  </p>
                </div>
              </div>
              <div class="form-group">
                <div class="amount-required">
                  <p class="label">
                    Amount required to recharge
                  </p>
                  <p class="amount">
                    90.000 <span>won</span>
                  </p>
                </div>
              </div>
              <div class="form-group">
                <div class="amount-charge">
                  <p class="label">
                    Charge amount
                  </p>
                  <p class="amount">
                    {{ $format.price(amountResult) }} <span>won</span>
                  </p>
                </div>
              </div>
              <div class="form-group wrap-radio">
                <p>
                  <input
                    id="money1"
                    v-model="amount"
                    type="radio"
                    value="100000"
                    name="amount"
                    checked
                  >
                  <label for="money1">100.000 won</label>
                </p>
                <p>
                  <input
                    id="money2"
                    v-model="amount"
                    type="radio"
                    value="200000"
                    name="amount"
                  >
                  <label for="money2">200.000 won</label>
                </p>
                <p>
                  <input
                    id="money3"
                    v-model="amount"
                    type="radio"
                    value="500000"
                    name="amount"
                  >
                  <label for="money3">500.000 won</label>
                </p>
                <p>
                  <input
                    id="money4"
                    v-model="amount"
                    type="radio"
                    value="1000000"
                    name="amount"
                  >
                  <label for="money4">1.000.000 won</label>
                </p>
                <p>
                  <input
                    id="money5"
                    v-model="amount"
                    type="radio"
                    value="2000000"
                    name="amount"
                  >
                  <label for="money5">2.000.000 won</label>
                </p>
              </div>
            </div>
          </div>
          <div class="payment-right col-md-8">
            <h2>Payment method</h2>
            <div class="choose-method">
              <div class="notice">
                Please select payment method
              </div>
              <div class="form-group wrap-radio">
                <p :class="paymentMethod == 'CARD' ? 'active' : ''">
                  <input
                    id="card"
                    v-model="paymentMethod"
                    type="radio"
                    name="payment_method"
                    value="CARD"
                  >
                  <label
                    for="card"
                    class="text-uppercase"
                  >Card</label>
                </p>
                <p :class="paymentMethod == 'VBANK' ? 'active' : ''">
                  <input
                    id="vitural"
                    v-model="paymentMethod"
                    type="radio"
                    name="payment_method"
                    value="VBANK"
                  >
                  <label
                    for="vitural"
                    class="text-uppercase"
                  >Vitural account</label>
                </p>
              </div>
              <div class="wrapper-description">
                <h3>Payment service fee</h3>
                <div class="description">
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos officia neque aperiam distinctio! Suscipit dolorem tempora corrupti exercitationem numquam assumenda ipsa, libero eum laudantium debitis molestias nobis ducimus perspiciatis. Vero.</p>
                </div>
              </div>
            </div>
            <div class="apply-button">
              <button class="btn btn-primary">
                Apply
              </button>
            </div>
          </div>
        </div>
      </form>
      <LoadingPage
        v-if="loading"
      />
    </div>
    <div
      class="popup-overlay"
      @click="_cancel"
    />
  </popup-modal>
</template>

<script>
import PopupModal from './PopupModal.vue'

import { countNumber } from '../mixins/countNumber'
import LoadingPage from '../components/LoadingPage'

export default {
    name: 'PopupPayment',
    components: {
        PopupModal,
        LoadingPage,
    },
    mixins: [
        countNumber,
    ],
    data: () => ({
        paymentMethod: 'CARD',
        amount: 100000,
        amountResult: 100000,
        okButton: undefined,
        cancelButton: 'Go Back',
        creditAmount: 0,
        storeInfos: {},
        paymentInfo: {},
        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined,
        loading: true,
    }),
    computed: {

    },
    watch: {
        amount(n, o) {
            this.getPaymentInfo(n)
        }
    },
    created() {
        this.getPaymentInfo()
    },
    methods: {
        show(opts = {}) {
            this.title = opts.title
            this.cancelButton = opts.cancelButton
            this.okButton = opts.okButton
            this.creditAmount = opts.creditAmount
            this.storeInfos = opts.storeInfos
            // Once we set our config, we tell the popup modal to open
            this.$refs.popup.open()
            // overlay body
            $('body').addClass('overlay')
            // Return promise so the caller can get results
            return new Promise((resolve, reject) => {
                this.resolvePromise = resolve
                this.rejectPromise = reject
            })
        },

        _confirm() {
            this.$refs.popup.close()
            this.resolvePromise(true)
        },

        _cancel() {
            this.$refs.popup.close()
            this.resolvePromise(false)
            // Or you can throw an error
            // this.rejectPromise(new Error('User cancelled the dialogue'))
        },
        payment() {
            if(checkPlatform(window.navigator.userAgent) == "mobile") {
                document.payForm.action = "https://web.nicepay.co.kr/v3/v3Payment.jsp"
                document.payForm.acceptCharset="euc-kr"
                document.payForm.submit()
            } else {
                goPay(document.payForm)
            }
        },
        getPaymentInfo(amount = this.amount) {
            this.loading = true
            this.amountResult = parseInt(amount) + (parseInt(amount) * 2 / 100)
            axios.post(apiURL + "/order/payment-info", {
                    amount: this.amountResult
                })
                .then(response => {
                    this.loading = false
                    this.paymentInfo = response.data.data
                })
                .catch(err => {
                    console.log(err)
                })
        },
    },
}
</script>

<style lang="scss">
    #style-scrollbar::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #f2f2f2;
    }

    #style-scrollbar::-webkit-scrollbar {
        width: 6px;
        background-color: #D62929;
    }

    #style-scrollbar::-webkit-scrollbar-thumb {
        background-color: #D62929;
    }
    .btns {
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .ok-btn, .cancel-btn {
        padding: 0.4em 2em;
        background-color: #d5eae7;
        color: #35907f;
        border: 1px solid #0ec5a4;
        border-radius: 5px;
        font-weight: bold;
        font-size: 13px;
        text-transform: uppercase;
        cursor: pointer;
    }
    .ok-btn{
        background-color: #e73950;
        color: #fff;
    }
    .popup-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        z-index: 1;
    }
    .popup-content#style-scrollbar {
        background: #fff;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
        width: 860px;
        max-height: 90vh;
        overflow-y: scroll;
        margin: 0 auto;
        position: relative;
        .spinner {
            position: absolute;
        }
        p {
            margin-bottom: 0.5rem;
        }
        z-index: 12;
    }
    .popup-content h3 {
        font-weight: 600;
    }
    .pop-dialog .btns {
        justify-content: center;
    }
    // style wrapper content
    .wrapper-payment {
        margin-left: 0;
        margin-right: 0;
        white-space: initial;
        .payment-left {
            background: #f6f6f7;
            padding: 30px;
            .store-info {
                margin-bottom: 20px;
                h2 {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 0px;
                    color: #000;
                }
                .store-code {
                    font-size: 13px;
                    color: #a9aaad;
                }
            }
            .frm-payment {
                .form-group {
                    div {
                        position: relative;
                        border: 1px solid #eaeaee;
                        border-radius: 5px;
                        margin-bottom: 12px;
                        padding: 5px 10px;
                        background-color: #ffffff;
                        font-size: 12px;
                        &.amount-required {
                            background-color: #ed3f36;
                            color: #ffffff;
                            .label {
                                color: #f2f2f2;
                            }
                            .amount {
                                color: #ffffff;
                            }
                        }
                        &.amount-charge {
                            margin-top: 30px;
                            padding-top: 20px;
                            .label {
                                position: absolute;
                                top: -10px;
                                left: 10px;
                                background-color: #ffffff;
                                padding: 4px 10px;
                                border-radius: 20px;
                                color: #ed3f36;
                            }
                        }
                        p {
                            margin-bottom: 0;
                        }
                        .label {
                            color: rgb(163, 162, 162);
                        }
                        .amount {
                            margin-top: 5px;
                            text-align: right;
                            font-size: 16px;
                            font-weight: bold;
                            color: #000000;
                        }
                    }
                    &.wrap-radio {
                        margin-top: 30px;
                        p {
                            display: inline-block;
                            background: #fff;
                            color: rgb(163, 162, 162);
                            padding: 5px 10px;
                            font-size: 12px;
                            margin-right: 8px;
                            border-radius: 6px;
                            border: 1px solid #eaeaee
                        }
                        input {
                            display: none;
                            &:checked + label {
                                font-weight: bold;
                                color: #000;
                            }
                        }
                    }
                }
            }
        }
        .payment-right {
            padding: 30px;
            h2 {
                font-size: 24px;
                font-weight: bold;
                border-bottom: 3px solid #eeeeee;
                padding-bottom: 10px;
                margin-bottom: 20px;
                color: #000;
            }
            .notice {
                font-weight: bold;
                margin-bottom: 15px;
            }
            .wrap-radio {
                p {
                    display: inline-block;
                    background: #f2f2f2;
                    padding: 8px 25px;
                    font-size: 16px;
                    margin-right: 15px;
                    border-radius: 4px;
                    &.active {
                        font-weight: bold;
                        color: #0e74a6;
                        border: 1px solid #a4ddf6;
                        background: #f2f9ff;
                    }
                }
                [type="radio"]:checked,
                [type="radio"]:not(:checked) {
                    position: absolute;
                    left: -9999px;
                }
                [type="radio"]:checked + label,
                [type="radio"]:not(:checked) + label
                {
                    position: relative;
                    padding-left: 28px;
                    cursor: pointer;
                    line-height: 20px;
                    display: inline-block;
                }
                [type="radio"]:checked + label:before,
                [type="radio"]:not(:checked) + label:before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 18px;
                    height: 18px;
                    border: 1px solid #ccc;
                    border-radius: 100%;
                    background: #fff;
                }
                [type="radio"]:checked + label:after,
                [type="radio"]:not(:checked) + label:after {
                    content: '';
                    width: 12px;
                    height: 12px;
                    background: #0e74a6;
                    position: absolute;
                    top: 3px;
                    left: 3px;
                    border-radius: 100%;
                    -webkit-transition: all 0.2s ease;
                    transition: all 0.2s ease;
                }
                [type="radio"]:not(:checked) + label:after {
                    opacity: 0;
                    -webkit-transform: scale(0);
                    transform: scale(0);
                }
                [type="radio"]:checked + label:after {
                    opacity: 1;
                    -webkit-transform: scale(1);
                    transform: scale(1);
                }
            }
            .wrapper-description {
                margin-top: 20px;
                margin-bottom: 40px;
                h3 {
                    font-size: 16px;
                    font-weight: bold;
                }
                .description {
                    font-size: 13px;
                }
            }
            .apply-button {
                button {
                    min-width: 120px;
                    background: #0e74a6;
                    border-color: #4d9cc1;
                    text-transform: uppercase;
                }
            }
        }
    }
</style>
