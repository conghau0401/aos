<template>
  <popup-modal ref="popup">
    <div
      id="style-scrollbar"
      class="popup-content"
    >
      <div class="order-bill">
        <div class="wrapper-header">
          <div class="header-bill d-md-flex justify-content-between">
            <div class="header-left d-flex align-items-center">
              <div class="header-bar">
                <p class="title-bar">
                  {{ order.shippingMethod }}
                </p>
              </div>
              <div class="title-bill">
                <p>{{ order.store }}</p>
              </div>
            </div>
            <div class="header-right d-flex align-items-center justify-content-between">
              <div class="info-bill d-flex text-start">
                <div class="info-bill-left">
                  <p>{{ $t('order_bill.receiverDate') }} : {{ $format.timeToStr(order.orderDate, '-') }}</p>
                  <p>{{ $t('order_bill.orderNumber') }} : {{ order.orderNumber }}</p>
                </div>
                <div class="info-bill-right">
                  <p>{{ $t('order_bill.orderPhone') }} : {{ order.buyerPhoneNo }}</p>
                  <p>{{ $t('order_bill.orderMobile') }} : {{ order.buyerMobileNo }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="content-bill">
          <div class="bill-domain text-end pt-1">
            <p>
              <a
                href="https://www.pohang.snackisland.kr/"
                class="text-danger"
                target="_blank"
              >https://www.pohang.snackisland.kr/</a>
            </p>
          </div>
          <div class="info-bill-title">
            <p>{{ $t('order_bill.receiverInfo') }}</p>
          </div>
          <div class="info-bill-content d-md-flex justify-content-between">
            <div class="bill-name">
              <p>{{ $t('order_bill.receiverName') }} : {{ order.receiverName }}</p>
            </div>
            <div class="bill-address">
              <p>{{ $t('order_bill.receiveAddress') }} : {{ order.receiveAddress1 }} {{ order.receiveAddress2 }}</p>
            </div>
            <div class="bill-phone">
              <p>{{ $t('order_bill.receiverPhoneNo') }} : {{ order.receiverPhoneNo }}</p>
              <p>{{ $t('order_bill.receiverMobileNo') }} : {{ order.receiverMobileNo }}</p>
            </div>
          </div>
          <div class="bill-shipping-method mt-2">
            <img
              src="/img/icon/arrow-right.svg"
              alt=""
            >
            <h3>{{ order.shippingMethod }}</h3>
          </div>
          <div class="wrapper-content">
            <div
              v-for="(item, index) in groupByProductTp"
              :key="index"
              class="item-tab"
            >
              <div class="tab-title">
                <img
                  v-if="index == 1"
                  src="/img/icon/square.svg"
                  alt=""
                >
                <img
                  v-else-if="index == 2"
                  src="/img/icon/heart.svg"
                  class="type-2"
                  alt=""
                >
                <img
                  v-else-if="index == 3"
                  src="/img/icon/triangle.svg"
                  class="type-3"
                  alt=""
                >
                <p>{{ item[0].productType }}</p>
              </div>
              <div class="product-table">
                <div class="table-title d-flex">
                  <p class="name">
                    {{ $t('order_bill.name') }}
                  </p>
                  <p class="option">
                    {{ $t('order_bill.option') }}
                  </p>
                  <p class="per-pack">
                    {{ $t('order_bill.perPack') }}
                  </p>
                  <p class="price">
                    {{ $t('order_bill.price') }}
                  </p>
                  <p class="quantity">
                    {{ $t('order_bill.quantity') }}
                  </p>
                  <p class="amount">
                    {{ $t('order_bill.status') }}
                  </p>
                  <p class="status">
                    {{ $t('order_bill.status') }}
                  </p>
                </div>
                <div class="content-table">
                  <div
                    v-for="product, i in item"
                    :key="i"
                    class="table-tr d-flex"
                  >
                    <p class="name text-start">
                      {{ product.productName }}
                    </p>
                    <p class="option">
                      {{ product.option }}
                    </p>
                    <p class="per-pack">
                      {{ product.quantityPerPack }}
                    </p>
                    <p class="price">
                      {{ $format.price(product.unitPrice) }}{{ $t('order_bill.won') }}
                    </p>
                    <p class="quantity">
                      {{ product.quantity }}
                    </p>
                    <p class="amount">
                      {{ $format.price(product.unitPrice * product.quantity) }}{{ $t('order_bill.won') }}
                    </p>
                    <p class="status">
                      {{ product.status }}
                    </p>
                  </div>
                  <div class="table-tr d-flex">
                    <div class="sub-total-left" />
                    <div class="sub-total">
                      <span v-if="index == 1">{{ $format.price(getPriceIndustrial) }}{{ $t('order_bill.won') }}</span>
                      <span v-else-if="index == 2">{{ $format.price(getPriceMiscellaneous) }}{{ $t('order_bill.won') }}</span>
                      <span v-else-if="index == 3">{{ $format.price(getPriceAlcoholic) }}{{ $t('order_bill.won') }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bill-total d-flex justify-content-between align-items-center">
          <div class="title-total text-center px-3 py-1">
            <p>{{ $t('order_bill.total') }}</p>
          </div>
          <div class="count-total text-center px-3 py-1">
            <p><span class="total">{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + getPriceAlcoholic + order.shippingFee) }}</span> {{ $t('order_bill.won') }}</p>
          </div>
        </div>
        <div class="footer-bill d-md-flex justify-content-between align-items-center">
          <div class="footer-left">
            <div class="logo mb-3">
              <img
                onerror="this.src='/img/header/logo.svg'"
                :src="getStoreSettings.logo_url"
              >
            </div>
            <div class="footer-title">
              <p>{{ centerInfo.centerName }}</p>
            </div>
          </div>
          <div class="footer-right">
            <p><span class="name">{{ $t('order_bill.ceo') }} : {{ centerInfo.centerManager }}</span> <span class="company-number">{{ $t('order_bill.companyNumber') }} : {{ centerInfo.centerCorporateNumber }}</span></p>
            <p>{{ $t('order_bill.orderEmail') }} : {{ centerInfo.representativeMobile }}</p>
            <p>{{ $t('order_bill.receiveAddress') }} : {{ centerInfo.centerAddressBasic }} {{ centerInfo.centerAddressDetail }}</p>
          </div>
        </div>
      </div>
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

export default {
    name: 'PopupOrderBill',
    components: {
        PopupModal,
    },
    mixins: [
        countNumber,
    ],
    data: () => ({
        okButton: undefined,
        cancelButton: 'Go Back',
        order: {},
        groupByProductTp: {},
        getStoreSettings: {},
        centerInfo: {},
        getPriceIndustrial: 0,
        getPriceMiscellaneous: 0,
        getPriceAlcoholic: 0,
        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined,
    }),
    computed: {

    },
    methods: {
        show(opts = {}) {
            this.title = opts.title
            this.order = opts.order
            this.groupByProductTp = opts.groupByProductTp
            this.getStoreSettings = opts.getStoreSettings
            this.getPriceIndustrial = opts.getPriceIndustrial,
            this.getPriceMiscellaneous = opts.getPriceMiscellaneous,
            this.getPriceAlcoholic = opts.getPriceAlcoholic,
            this.centerInfo = opts.centerInfo
            this.cancelButton = opts.cancelButton
            this.okButton = opts.okButton
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
    },
}
</script>

<style lang="scss">
#style-scrollbar::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
	background-color: #f2f2f2;
}

#style-scrollbar::-webkit-scrollbar
{
	width: 6px;
	background-color: #D62929;
}

#style-scrollbar::-webkit-scrollbar-thumb
{
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
    border-radius: 5px;
    box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
    width: 850px;
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
// bill
.wrapper-header {
    border-bottom: 3px solid #ef4130;
    .header-bill {
        padding: 20px 20px 0 20px;
        p {
            margin: 0;
        }
        .header-left {
            .header-bar {
                .title-bar {
                    font-size: 20px;
                    position: relative;
                    background: #ef4130;
                    color: #fff;
                    height: 42px;
                    line-height: 45px;
                    text-align: center;
                    padding: 0 30px;
                    margin-bottom: 0;
                    border-radius: 10px 12px 0 0;
                    cursor: pointer;
                    &::before {
                        content: "";
                        position: absolute;
                        background-color: inherit;
                        width: 42px;
                        height: 43px;
                        border-top-right-radius: 30%;
                        transform: rotate(-16deg) skewY(16deg) scale(0.6, 1.05) translate(227%);
                        right: 30%;
                    }
                }
            }
            .title-bill {
                padding-left: 30px;
                font-size: 18px;
                font-weight: bold;
            }
        }
        .info-bill-right {
            padding-left: 30px;
        }
    }
}
.content-bill {
    padding: 0 25px 25px 25px;
    .info-bill-title {
        font-weight: bold;
    }
    .info-bill-content {
        > div {
            white-space: break-spaces;
            &.bill-name {
                width: 25%;
            }
            &.bill-address {
                width: 50%;
            }
            &.bill-phone {
                width: 25%;
            }
        }
    }
    .item-tab {
        border-bottom: 2px solid #f8e1e9;
        margin-bottom: 10px;
        .tab-title {
            position: relative;
            img {
                width: 12px;
                position: absolute;
                top: 4px;
                &.type-2 {
                    top: 1px;
                }
            }
            p {
                padding-left: 20px;
            }
        }
    }
    .bill-shipping-method {
        position: relative;
        img {
            position: absolute;
            width: 22px;
            top: -3px;
        }
        h3 {
            font-size: 18px;
            position: relative;
            color: #e73950;
            font-weight: normal;
            padding-left: 35px;
            position: relative;
        }
    }
    .product-table {
        p {
            margin: 0;
            padding: 4px 5px;
            text-align: center;
        }
        .table-title {
            background: #f8e1e9;
            p {
                border-right: 1px solid #f2f2f2;
            }
        }
        .name {
            width: 40%;
        }
        .option {
            width: 8%;
        }
        .per-pack {
            width: 8%;
        }
        .price {
            width: 12%;
        }
        .quantity {
            width: 8%;
        }
        .amount {
            width: 12%;
        }
        .status {
            width: 12%;
        }
        .sub-total-left {
            width: 76%;
        }
        .sub-total {
            width: 12%;
            text-align: center;
            font-weight: bold;
        }
    }
}
.bill-total {
    background: #f8e1e9;
    &>div {
        width: 50%;
        text-align: center;
    }
    p {
        margin: 0;
    }
    .total {
        font-size: 20px;
        color: #e73950;
        font-weight: bold;
    }
}
.footer-bill {
    padding: 25px;
    border-top: 3px solid #e73950;
    .logo img {
        max-height: 36px;
    }
    .footer-title {
        font-size: 26px;
        font-weight: bold;
    }
}

@media only screen and (max-width: 991px) {
    .header-left {
        margin-bottom: 10px;
        border-bottom: 2px solid #e73950;
    }
    .header-right {
        margin-bottom: 10px;
    }
}
@media only screen and (max-width: 767px) {
    .content-bill {
        .info-bill-content > div {
            width: 100% !important;
        }
        .item-tab {
            overflow-x: scroll;
            .product-table {
                width: 200%;
            }
            .product-table {
                .name {
                    width: 80%;
                }
                .option {
                    width: 16%;
                }
                .per-pack {
                    width: 16%;
                }
                .price {
                    width: 24%;
                }
                .quantity {
                    width: 16%;
                }
                .amount {
                    width: 24%;
                }
                .status {
                    width: 24%;
                }
            }
        }
    }
    .footer-bill {
        p {
            word-break: break-all;
            white-space: break-spaces;
        }
    }
}

</style>
