<template>
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
          <div class="delivery-window__date">
            <p class="delivery-window__bottom--title">
              배송일
            </p>
            <ul>
              <li
                v-for="(item, index) in weekdays"
                :key="index"
                :class="{'active': weekdayInt == index}"
                @click="chooseWeekday(index)"
              >
                {{ item }}
              </li>
            </ul>
          </div>
          <div class="delivery-window__choose">
            <p class="delivery-window__bottom--title">
              옵션
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
              입수
            </p>
            <ul>
              <li
                v-for="(option, index2) in product.options"
                :key="index2"
              >
                {{ option.quantityPerPack }}개
              </li>
            </ul>
          </div>
          <div class="delivery-window__num">
            <p class="delivery-window__bottom--title">
              소계 / 수량
            </p>
            <div class="delivery-window__num--box">
              <p class="num">
                {{ $format.price(price) }}<span> 원</span>
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
                최대 {{ maxOrder }}개 구매 가능
              </p>
            </div>
          </div>
        </div>
        <div class="delivery-window__btn">
          <p
            class="save"
            @click="saveRegular"
          >
            <img
              :src="'/img/icon/save.svg'"
              alt=""
            >저장
          </p>
          <p
            class="close"
            @click="closeWindowPortal"
          >
            <img
              :src="'/img/icon/close.svg'"
              alt=""
            >닫기
          </p>
        </div>
      </div>
    </div>
    <LoadingPage
      v-if="loading"
    />
  </div>
</template>

<script>

import { countNumber } from '../mixins/countNumber'
import LoadingPage from '../components/LoadingPage'
import ConfirmDialogue from '../components/ConfirmDialogue.vue'

export default {
    components: {
        LoadingPage,
        ConfirmDialogue
    },
    mixins: [
        countNumber,

        formatPrice
    ],
    data() {
        return {
            product: {},
            weekdayInt: 0,
            weekdays: [
                "월", "화", "수", "목", "금", "보류"
            ],
            currentOption: {},
            compareOptionId: 0,
            price: 0,
            maxOrder: 0,
            loading: true,
        }
    },
    created() {
        this.getProductInfo()
    },
    methods: {
        getProductInfo() {
            let productId = this.$route.query.product
            axios.get(apiURL + "/product/product/" + productId)
            .then((res) => {
                this.loading = false
                this.product = res.data.data;
                this.price = this.product.options[0].supplyUnitPrice;
                this.compareOptionId = this.product.options[0].optionId
                this.maxOrder = this.product.options[0].maxOrder
                this.currentOption = this.product.options[0]
                console.log(this.product)
            })
            .catch(error => {
                console.log(error)
            })
        },
        closeWindowPortal() {
            let newWindow = open(location, '_self');
            newWindow.close();
            return false;
        },
        async saveRegular() {
            if (this.quantity == 0) {
                await this.$refs.confirmDialogue.show({
                    title: this.$t('modal.titleDialog'),
                    message: this.$t('modal.quantityZero'),
                    cancelButton: this.$t('modal.btnOk'),
                    isDialog: true,
                })
            } else {
                this.loading = true
                // prepare params
                let params = {
                    "regularDeliveryProductAdd": [
                        {
                            "productId": this.product.productId,
                            "quantityPerPack": this.currentOption.quantityPerPack,
                            "quantity": this.quantity,
                            "optionId": this.compareOptionId
                        }
                    ],
                }
                axios.put(apiURL + "/product/regular-delivery", params)
                .then((res) => {
                    this.loading = false
                    if(res.data.status == '201') {
                        let newWindow = open(location, '_self');
                        newWindow.close();
                        return false;
                    }
                })
            }
        },
        chooseWeekday(idx) {
            this.weekdayInt = idx
        },
        chooseOption(option) {
            this.compareOptionId = option.optionId
            this.price = option.supplyUnitPrice
            this.maxOrder = option.maxOrder
        },
    }
}
</script>

<style lang="scss" scoped>
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
