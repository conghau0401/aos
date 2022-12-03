<template>
  <div class="ar-product">
    <p class="img-product">
      <router-link :to="'/product-detail/' + product.productId">
        <img
          :src="$format.imageUrl(product.image != null ? product.image : product.productImgUrl)"
          alt="Product"
        >
      </router-link>
    </p>
    <div class="ar-product__info">
      <h3 class="name-product">
        <router-link :to="'/product-detail/' + product.productId">
          {{ product.productNm }}
        </router-link>
      </h3>
      <div class="price-product">
        <p class="price-product__current">
          <span class="num">{{ $format.price(getPrice.price) }}</span>
          <span class="currency"> {{ $t("product.won") }}</span>
          <span class="ratio-density">( {{ product.marketableSize }} )</span>
        </p>
      </div>
      <div class="ar-product__option">
        <ul>
          <li
            v-for="(option, idx) in product.options"
            :key="idx"
            :class="option.optionId == compareOptionId ? 'active' : ''"
            @click="chooseOption(option)"
          >
            {{ option.optionName }} {{ option.quantityPerPack }}{{ $t("category.product") }}
          </li>
        </ul>
      </div>
      <div class="ar-product__num">
        <div class="ar-product__number d-flex align-items-center align-items-center flex-wrap justify-content-md-between">
          <span
            class="reduction"
            @click="decreaseNumber"
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
            @click="increaseNumber($event, maxQuantity)"
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
          <p
            class="add-to-cart"
            @click="addToCart($event, product, product.productId, product.productImgUrl, compareOptionId, quantity,
                              shippingMethod)"
          >
            {{ $t("product.addCart") }} <img
              src="/img/header/cart.svg"
              alt=""
            >
          </p>
        </div>
        <p class="noted-num">
          {{ $t("product.max") }} {{ maxQuantity }} {{ $t("product.canbe") }}
        </p>
      </div>
    </div>
  </div>
</template>
<script>
    import { countNumber } from '../mixins/countNumber'
    import { addToCart } from '../mixins/addToCart'
    import { calculatePriceMixin } from '../mixins/calculatePriceMixin'
    import { mapGetters } from "vuex"
    export default {
        mixins: [
            countNumber,
            addToCart,
            calculatePriceMixin,
        ],
        props: {
            product: {
                type: Object,
                default: () => {}
            },
            productInCart: {
                type: Array,
                default: () => {}
            },
        },
        data() {
            return {
                compareOptionId: this.product.options[0].optionId,
                currrentOption: this.product.options[0],
                maxQuantity: this.product.options[0].maxOrder,
            }
        },
        computed: {
            shippingMethod() {
                if (this.getStoreInfos.defaultShippingMethod != null) {
                    return this.getStoreInfos.defaultShippingMethod
                }
                return 2 // default delivery
            },
            ...mapGetters(['getMarginRates', 'getStoreInfos']),
            getPrice() {
                return this.calculatePrice(this.product.productTp, this.currrentOption, this.getMarginRates, this.getStoreInfos)
            },
        },
        watch: {
            productInCart(n, o) {
                for(let i in n) {
                    if (this.product.productId == n[i].ds_product_id && this.compareOptionId == n[i].ds_option_id) {
                        this.quantity = n[i].quantity
                    }
                }
            },
            product(productNew, o) {
                this.compareOptionId = productNew.options[0].optionId
                this.currrentOption = productNew.options[0]
                this.maxQuantity = productNew.options[0].maxOrder
                this.quantity = 0
                for(let i in this.productInCart) {
                    if (productNew.productId == this.productInCart[i].ds_product_id && this.compareOptionId == this.productInCart[i].ds_option_id) {
                        this.quantity = this.productInCart[i].quantity
                    }
                }
            },
        },
        mounted() {
            for(let i in this.productInCart) {
                if (this.product.productId == this.productInCart[i].ds_product_id && this.compareOptionId == this.productInCart[i].ds_option_id) {
                    this.quantity = this.productInCart[i].quantity
                }
            }
        },
        methods: {
            chooseOption(option) {
                this.compareOptionId = option.optionId
                this.currrentOption = option
                this.maxQuantity = option.maxOrder
                let arrProduct = this.productInCart
                let flag = 0
                for(let i in arrProduct) {
                    if (this.product.productId == arrProduct[i].ds_product_id && this.compareOptionId == arrProduct[i].ds_option_id) {
                        flag = 1
                        this.quantity = arrProduct[i].quantity
                    }
                }
                if (flag == 0) {
                    this.quantity = 0
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    @import '/css/category.scss';
    #category {
        .category-main {
            .row {
                &.on {
                    .item-product {
                        .ar-product {
                            display: flex;
                            .img-product {
                                width: 40%;
                            }
                            &__info {
                                width: 60%;
                                padding-left: 25px;
                            }
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        #category {
            .category-main {
                .row {
                    &.on {
                        .item-product {
                            .ar-product {
                                .name-product {
                                    margin: 0 0 10px;
                                }
                                &__option {
                                    margin-bottom: 5px;
                                }
                                &__number {
                                    p.add-to-cart {
                                        width: 100%;
                                        margin-top: 5px;
                                    }
                                    input {
                                        width: calc(60% - 65px);
                                    }
                                }
                                .img-product {
                                    border: none;
                                    a {
                                        border: 1px solid #ededed;
                                    }
                                }
                                &__info {
                                    padding-left: 15px;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
