<template>
  <section class="right-bar">
    <div class="right-bar__title">
      {{ $t("right_bar.amount") }}
    </div>
    <dl>
      <dt>{{ $t("right_bar.goods") }}</dt>
      <dd><span>{{ $format.price(getPriceIndustrial) }}</span> {{ $t("right_bar.won") }}</dd>
    </dl>
    <dl>
      <dt>{{ $t("right_bar.fresh") }}</dt>
      <dd><span>{{ $format.price(getPriceMiscellaneous) }}</span> {{ $t("right_bar.won") }}</dd>
    </dl>
    <dl>
      <dt>{{ $t("right_bar.main") }}</dt>
      <dd><span>{{ $format.price(getPriceAlcoholic) }}</span> {{ $t("right_bar.won") }}</dd>
    </dl>
    <dl>
      <dt>{{ $t("right_bar.sub") }}</dt>
      <dd class="black">
        <span>{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + getPriceAlcoholic) }}</span> {{ $t("right_bar.won") }}
      </dd>
    </dl>
    <dl class="trans">
      <dt>{{ $t("right_bar.deli") }}</dt>
      <dd class="black">
        <span class="plus">&#43;</span><span>{{ $format.price(shippingFee.alcohol + shippingFee.normal) }}</span> {{ $t("right_bar.won") }}
      </dd>
    </dl>
    <div class="right-bar__title bottom">
      {{ $t("right_bar.total") }}
    </div>
    <div class="total-price">
      <span>{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + shippingFee.normal) }}</span> {{ $t("right_bar.won") }}
      <p class="total-price__des">
        {{ $t("right_bar.amountAvai") }} {{ $format.price(creditAmount) }} {{ $t("right_bar.won") }}
      </p>
    </div>
    <div class="right-bar__btn">
      <!-- <button type="button" class="btn btn-block btn-primary">
            {{$t("right_bar.place")}}
          </button> -->
      <!-- <button type="button" class="btn btn-block btn-secondary">
            전체구매
          </button> -->
      <button
        v-if="page != '/regular-delivery'"
        type="button"
        class="btn btn-block btn-danger"
      >
        <a href="/shopping-cart">{{ $t("right_bar.cart") }}</a>
      </button>
    </div>
  </section>
</template>

<script>
    import { groupByKeyMixin } from '../mixins/groupByKeyMixin'
    import { getPriceByTypeMixin } from '../mixins/getPriceByTypeMixin'
    import { calculatePriceMixin } from '../mixins/calculatePriceMixin'
    import { mapGetters } from 'vuex'

    export default {
        mixins: [
            groupByKeyMixin,
            getPriceByTypeMixin,
            calculatePriceMixin,
        ],
        props: {
            page: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                creditAmount: 0,
            }
        },
        computed: {
            ...mapGetters(['getProducts', 'getMarginRates', 'getStoreSettings', 'getRegularProducts']),
            productExistInCart() {
                return this.getProducts.data
            },
            allProducts() {
                if (this.$store.state.products.data != null) {
                    return this.$store.state.products.data;
                }
                return []
            },
            regularProducts() {
                return this.getRegularProducts;
            },
            getPriceIndustrial() {
                if (this.page == "/regular-delivery") {
                    return this.getRegularPriceByType(this.regularProducts, 1)
                } else {
                    return this.getPriceByType(this.productExistInCart, 1)
                }
            },
            getPriceMiscellaneous() {
                if (this.page == "/regular-delivery") {
                    return this.getRegularPriceByType(this.regularProducts, 2)
                } else {
                    return this.getPriceByType(this.productExistInCart, 2)
                }
            },
            getPriceAlcoholic() {
                if (this.page == "/regular-delivery") {
                    return this.getRegularPriceByType(this.regularProducts, 3)
                } else {
                    return this.getPriceByType(this.productExistInCart, 3)
                }
            },
            groupByProductTp () {
                if (this.productExistInCart != null) {
                    return this.groupByKey(this.productExistInCart, 'productTp')
                }
                return []
            },
            productsDelivery() {
                return this.shippingMethodProducts(this.allProducts, 2)
            },
            productsSamday() {
                return this.shippingMethodProducts(this.allProducts, 3)
            },
            shippingFee() {
                let alcohol = 0
                let normal = 0
                if (this.productsDelivery != null &&
                    Object.keys(this.getMarginRates).length > 0 && this.page != "/regular-delivery") {
                    for(let i in this.productsDelivery) {
                        let percent = this.getMarginRates[this.productsDelivery[i].productTp].delivery.shipping
                        let getPrice = this.calculatePrice(this.productsDelivery[i].productTp, this.productsDelivery[i], this.getMarginRates, this.getStoreSettings)
                        if (this.productsDelivery[i].productTp == 3) { // ancohol
                            alcohol += parseInt(percent * (getPrice.price * this.productsDelivery[i].quantity) / 100)
                        } else {
                            normal += parseInt(percent * (getPrice.price * this.productsDelivery[i].quantity) / 100)
                        }
                    }
                }
                if (this.productsSamday != null &&
                    Object.keys(this.getMarginRates).length > 0 && this.page != "/regular-delivery") {
                    for(let i in this.productsSamday) {
                        let percent = this.getMarginRates[this.productsSamday[i].productTp].delivery.shipping
                        let getPrice = this.calculatePrice(this.productsSamday[i].productTp, this.productsSamday[i], this.getMarginRates, this.getStoreSettings)
                        if (this.productsSamday[i].productTp == 3) { // ancohol
                            parseInt(alcohol += percent * (getPrice.price * this.productsSamday[i].quantity) / 100)
                        } else {
                            parseInt(normal += percent * (getPrice.price * this.productsSamday[i].quantity) / 100)
                        }
                    }
                }
                return {
                    alcohol: alcohol,
                    normal: normal
                }
            },
        },
        created() {
            this.getStoreInfo()
        },
        methods: {
            getStoreInfo() {
                axios.get(apiURL + "/user/credit")
                .then(response => {
                    this.creditAmount = response.data.data.order_credit_amount
                })
            },
            shippingMethodProducts(products, method) {
                if (products != null) {
                    const pro = products.filter(function(obj){
                        return obj.shipping_method == method;
                    });
                    return pro;
                }
            },
        }
    }
</script>


<style lang="scss" scoped>
.right-bar {
    border: 1px solid #ccc;
    background: #f1f1f1;
    padding: 15px 20px;
    border-radius: 5px;
    color: #555;
    font-size: 16px;
    &__btn {
        margin-top: 20px;
        button {
            width: 100%;
            margin-bottom: 5px;
            a {
                color: #fff;
            }
        }
    }
    &__title {
        font-weight: bold;
        margin-bottom: 20px;
        &.bottom {
            margin: 30px 0 10px;
        }
    }
    dl {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        &.trans {
            dd {
                position: relative;
                span.plus {
                    font-size: 40px;
                    color: #c9c5c5;
                    position: absolute;
                    top: 50%;
                    left: -30px;
                    font-weight: normal;
                    transform: translateY(-50%);
                }
            }
        }
        dt, dd {
            margin: 0;
            padding: 0 5px;
        }
        dt {
            font-weight: normal;
        }
        dd {
            margin-top: 10px;
            color: #888;
            &.black {
                color: #555;
            }
            span {
                font-size: 20px;
                font-weight: bold;
            }
        }
    }
    .total-price {
        text-align: right;
        span {
            font-weight: bold;
            color: #ef4130;
            font-size: 25px;
        }
        &__des {
            font-size: 14px;
            width: 100%;
            margin: 0;
        }
    }
}
</style>
