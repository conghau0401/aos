<template>
  <popup-modal ref="popup">
    <div class="popup-content">
      <div class="delivery-window">
        <div class="container">
          <div class="delivery-window__content">
            <div class="delivery-window__top">
              <p class="delivery-window__img">
                <img
                  :src="$format.imageUrl(product.image)"
                  alt=""
                >
              </p>
              <p class="delivery-window__name">
                {{ product.productNm }}
              </p>
            </div>
            <div class="delivery-window__bottom">
              <div class="delivery-window__choose">
                <p class="delivery-window__bottom--title">
                  {{ $t('popupAddCart.option') }}
                </p>
                <ul>
                  <li
                    v-for="(option, index2) in product.options"
                    :key="index2"
                    :class="option.optionId == compareOptionId ? 'active' : ''"
                    @click="chooseOption(option)"
                  >
                    {{ option.optionName }}
                  </li>
                </ul>
              </div>
              <div class="delivery-window__water">
                <p class="delivery-window__bottom--title">
                  {{ $t('popupAddCart.perPack') }}
                </p>
                <ul>
                  <li
                    v-for="(option, index2) in product.options"
                    :key="index2"
                  >
                    {{ option.quantityPerPack }}{{ $t('popupAddCart.unit') }}
                  </li>
                </ul>
              </div>
              <div class="delivery-window__num">
                <p class="delivery-window__bottom--title">
                  {{ $t('popupAddCart.subQuantity') }}
                </p>
                <div class="delivery-window__num--box">
                  <p class="num">
                    {{ $format.price(getPrice.price) }}<span> {{ $t('popupAddCart.won') }}</span>
                  </p>
                  <div class="delivery-window__number">
                    <span
                      class="reduction"
                      @click="decreaseNumber($event)"
                    >
                      <svg
                        viewBox="0 0 24 24"
                        width="24px"
                        height="24px"
                        xmlns="http://www.w3.org/2000/svg"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                      ><path
                        fill-rule="evenodd"
                        d="M0 12v1h23v-1h-1h11z"
                      /></svg>
                    </span>
                    <input
                      v-model="quantity"
                      type="number"
                      min="0"
                      class="no"
                    >
                    <span
                      class="increment"
                      @click="increaseNumber($event, maxOrder)"
                    >
                      <svg
                        viewBox="0 0 24 24"
                        width="24px"
                        height="24px"
                        xmlns="http://www.w3.org/2000/svg"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                      ><path
                        fill-rule="evenodd"
                        d="M11 11v-11h1v11h11v1h-11v11h-1v-11h-11v-1h11z"
                      /></svg>
                    </span>
                  </div>
                  <p class="noted-num">
                    {{ $t('popupAddCart.max') }} {{ maxOrder }}{{ $t('popupAddCart.canBuy') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="delivery-window__btn">
              <p
                class="save"
                @click="addToCart($event, product, product.productId, product.productImgUrl, compareOptionId, quantity, 2, 'popup')"
              >
                <img
                  src="/img/icon/save.svg"
                  alt="{{ $t('popupAddCart.save') }}"
                >{{ $t('popupAddCart.save') }}
              </p>
              <p
                class="close"
                @click="_cancel"
              >
                <img
                  src="/img/icon/close.svg"
                  alt="{{ $t('popupAddCart.close') }}"
                >{{ $t('popupAddCart.close') }}
              </p>
            </div>
          </div>
        </div>
        <LoadingPage
          v-if="loading"
        />
      </div>
    </div>
  </popup-modal>
</template>

<script>
import PopupModal from './PopupModal.vue'
import { countNumber } from '../mixins/countNumber'
import LoadingPage from '../components/LoadingPage'
import { addToCart } from '../mixins/addToCart'
import { mapGetters } from "vuex"
import { calculatePriceMixin } from '../mixins/calculatePriceMixin'

export default {
    name: 'PopupAddCart',
    components: { PopupModal, LoadingPage },
    mixins: [
        addToCart,
        countNumber,
        calculatePriceMixin,
    ],
    data: () => ({
        okButton: undefined,
        cancelButton: 'Go Back',
        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined,
        product: {},
        currentOption: {},
        compareOptionId: 0,
        price: 0,
        maxOrder: 0,
        loading: false,
        weekdayInt: 0,
        weekdays: [
            "월", "화", "수", "목", "금", "보류"
        ],
    }),
    computed: {
        ...mapGetters(['getProducts', 'getMarginRates', 'getStoreInfos']),
        productInCart() {
            return this.getProducts.data;
        },
        getPrice() {
            return this.calculatePrice(this.product.productTp, this.currentOption, this.getMarginRates, this.getStoreInfos)
        },
    },
    methods: {
        getProductInfo(productId) {
            axios.get(apiURL + "/product/product/" + productId)
            .then((res) => {
                this.loading = false
                this.product = res.data.data;
                this.price = this.product.options[0].supplyUnitPrice;
                this.compareOptionId = this.product.options[0].optionId
                this.maxOrder = this.product.options[0].maxOrder
                this.currentOption = this.product.options[0]
                this.setQuantity()
            })
            .catch(error => {
                console.log(error)
            })
        },
        chooseOption(option) {
            this.compareOptionId = option.optionId
            this.price = option.supplyUnitPrice
            this.maxOrder = option.maxOrder
            this.currentOption = option
            this.setQuantity()
        },
        chooseWeekday(idx) {
            this.weekdayInt = idx
        },
        setQuantity() {
            let arrProduct = this.productInCart
            let flag = 0
            for(let i in arrProduct) {
                if (this.product.productId == arrProduct[i].ds_productId && this.compareOptionId == arrProduct[i].ds_option_id) {
                    flag = 1
                    this.quantity = arrProduct[i].quantity
                }
            }
            if (flag == 0) {
                this.quantity = 0
            }
        },
        show(opts = {}) {
            this.loading = true
            this.title = opts.title
            this.productId = opts.product.productId
            this.cancelButton = opts.cancelButton
            this.okButton = opts.okButton
            this.getProductInfo(this.productId)
            // Once we set our config, we tell the popup modal to open
            this.$refs.popup.open()
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

<style lang="scss" scoped>
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

.popup-content {
    text-align: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
    width: 600px;
    margin: 0 auto;
    padding: 1.5rem;
    position: relative;
    .spinner {
        position: absolute;
    }
}
.popup-content h3 {
    font-weight: 600;
}
.pop-dialog .btns {
    justify-content: center;
}
// product info
.delivery-window {
    max-width: 500px;
    margin: 0 auto;
    padding: 25px 10px;
    &__top {
        display: flex;
        align-items: center;
    }
    &__img {
        width: 25%;
        border: 1px solid #e8e8e8;
        img {
            width: 100%;
        }
    }
    &__name {
        width: 75%;
        font-size: 14px;
        padding-left: 20px;
        text-align: left;
    }
    &__bottom {
        display: flex;
        justify-content: center;
        &--title {
            font-size: 14px;
            text-align: center;
        }
    }
    &__date {
        width: 25%;
        ul {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 0 10%;
            margin: 0;
            li {
                display: block;
                text-align: center;
                border: 1px solid #ddd;
                width: 48%;
                font-size: 12px;
                padding: 5px 0;
                cursor: pointer;
                margin-bottom: 4px;
                &.active {
                    background: #f8e25b;
                }
            }
        }
    }
    &__choose {
        width: 25%;
        ul {
            padding: 0 10%;
            margin: 0;
            li {
                display: block;
                text-align: center;
                border: 1px solid #ddd;
                width: 100%;
                font-size: 12px;
                padding: 5px 0;
                cursor: pointer;
                margin-bottom: 4px;
                &:last-child {
                    margin-bottom: 0;
                }
                &.active {
                    background: #f8e25b;
                }
            }
        }
    }
    &__water {
        width: 15%;
        ul {
            padding: 0;
            margin: 0;
            text-align: center;
            li {
                padding: 5px 0;
                font-size: 14px;
                margin-bottom: 4px;
                &:last-child {
                    margin-bottom: 0;
                }
            }
        }
    }
    &__num {
        width: 35%;
        text-align: center;
        &--box {
            border: 1px solid #e8e8e8;
            padding: 6px 10px;
            margin-bottom: 4px;
            border-radius: 2px;
        }
        p.num {
            margin: 0;
            font-weight: bold;
            font-size: 20px;
            span {
                font-weight: normal;
                font-size: 14px;
            }
        }
        p.noted-num {
            margin: 0;
            font-size: 12px;
        }
    }
    &__number {
        margin: 5px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        input {
            width: 40px;
            text-align: center;
            outline: none;
            border: 1px solid #ccc;
            border-radius: 0;
            height: 25px;
            margin: 0 1px;
            font-size: 14px;
            &::-webkit-outer-spin-button,
            &::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            &[type=number] {
                -moz-appearance:textfield;
            }
        }
        span {
            display: block;
            height: 25px;
            width: 25px;
            border: 1px solid #ccc;
            cursor: pointer;
            text-align: center;
            background: #fff;
        }
        p.noted-num {
            font-size: 11px;
            margin-bottom: 0;
            margin-top: 7px;
        }
    }
    &__btn {
        text-align: center;
        margin-top: 20px;
        p {
            display: inline-block;
            padding: 3px 15px;
            font-size: 13px;
            border: 1px solid #e8e8e8;
            border-radius: 25px;
            margin: 0 10px;
            cursor: pointer;
            img {
                width: 12px;
                margin-right: 5px;
            }
        }
    }
}
@media only screen and (max-width: 480px) {
    .delivery-window {
        &__date {
            ul {
                padding: 0;
            }
        }
        &__num {
            &--box {
                p.num {
                    font-size: 16px;
                }
            }
        }
        &__bottom {
            &--title {
                font-size: 12px;
            }
        }
        &__btn {
            p {
                margin: 0 5px;
            }
        }
    }
}
</style>
