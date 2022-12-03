<template>
  <div class="best-product__box">
    <div class="best-product__box--top d-flex flex-wrap align-items-end">
      <p class="best-product__box--no">
        <span>{{ idx + 1 }}</span> {{ $t("product_item_best.unit") }}
      </p>
      <ul class="best-product__box--tick d-flex align-items-center flex-wrap">
        <li
          :class="['wishlist', isWishlist ? 'active' : '']"
          @click="addToWishlist(product, isWishlist)"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 12.5 16.39"
          ><g id="a" /><g id="b"><g id="c"><g><path
            class="d"
            d="M4.43,12.47l-1.64-3.82c-.11-.27,0-.58,.27-.69,.27-.11,.58,0,.69,.27l.97,2.25,4.1-4.89c.19-.22,.52-.25,.74-.06,.22,.19,.25,.52,.06,.74l-5.2,6.19Z"
          /><path
            class="d"
            d="M12.5,16.39H0V0H9.49l3.01,2.98v13.41ZM1.5,14.89H11V3.98h-2.51V1.5H1.5V14.89Z"
          /></g></g></g></svg>{{ $t("product_item_best.interestItem") }}
        </li>
        <li
          :class="[isRegular ? 'active' : '']"
          @click="showPopupRegular(product)"
        >
          <router-link to="#">
            {{ $t("product_monthly_purchase.regularDeli") }}
          </router-link>
        </li>
      </ul>
    </div>
    <div class="best-product__info">
      <p class="best-product__image">
        <router-link :to="'/product-detail/' + product.productId">
          <img
            :src="$format.imageUrl(product.productImg)"
            :alt="product.productNm"
          >
        </router-link>
      </p>
      <div class="best-product__name">
        <h3>
          <router-link :to="'/product-detail/' + product.productId">
            {{ product.productNm }}
          </router-link>
        </h3>
        <!-- <p class="density">4겹 25M*30롤</p> -->
        <p class="price">
          <span class="number">{{ $format.price(getPrice.price) }}</span> <span class="unit"> {{ $t("product_item_best.won") }}</span> ({{ product.marketableSize }})
        </p>
      </div>
    </div>
  </div>
  <ConfirmDialogue ref="confirmDialogue" />
  <PopupRegular
    ref="showPopupRegular"
    @isProductInRegular="isProductInRegular"
  />
  <LoadingPage
    v-if="loading"
  />
</template>

<script>
    import { addToWishlist } from '../mixins/addToWishlist'
    import PopupRegular from '../components/PopupRegular'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    import LoadingPage from '../components/LoadingPage'
    import { calculatePriceMixin } from '../mixins/calculatePriceMixin'
    import { mapGetters } from "vuex"

    export default {
        components: {
            PopupRegular,
            ConfirmDialogue,
            LoadingPage,
        },
        mixins: [
            addToWishlist,
            calculatePriceMixin,
        ],
        props: {
            product: {
                type: Object,
                default: () => {}
            },
            idx: {
                type: Number,
                default: 0
            }
        },
        data() {
            return {
                loading: false,
                isWishlist: false,
                isRegular: false,
            }
        },
        computed: {
            ...mapGetters(['getMarginRates', 'getStoreInfos', 'getWishlistProducts', 'getRegularProducts']),
            getPrice() {
                return this.calculatePrice(this.product.productTp, this.product, this.getMarginRates, this.getStoreInfos)
            },
            wishlistProducts() {
                return this.getWishlistProducts.data;
            },
            productInRegular() {
                return this.getRegularProducts;
            },
        },
        watch: {
            wishlistProducts(n, o) {
                if (n) {
                    this.addOrDeleteProductWishList(n)
                }
            },
            productInCart(n, o) {
                if (n) {
                    this.activeProductInCart(n)
                }
            },
            productInRegular(n, o) {
                if (n) {
                    this.activeProductInRegular(n)
                }
            },
        },
        mounted() {
            if (this.wishlistProducts != null) {
                this.addOrDeleteProductWishList(this.wishlistProducts)
            }
            if (this.productInRegular != null) {
                this.activeProductInRegular(this.productInRegular)
            }
        },
        methods: {
            addOrDeleteProductWishList(prd) {
                for(let i in prd) {
                    if (prd[i].productId == this.product.productId) {
                        this.isWishlist = true
                    }
                }
            },
            activeProductInRegular(prd) {
                for(let i in prd) {
                    if (prd[i].productId == this.product.productId) {
                        this.isRegular = true
                    }
                }
            },
            async showPopupRegular(product) {
                if (this.isRegular) {
                    this.isRegular = false
                    let itemOptionId = []
                    this.$refs.confirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.deleteSuccessRegular'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    axios
                        .get(apiURL + "/product/product/" + product.productId)
                        .then(res => {
                            let data = res.data.data.options
                            for(let i in data) {
                                for(let j in this.productInRegular) {
                                    if (data[i].optionId == this.productInRegular[j].optionId && data[i].productId == this.productInRegular[j].productId) {
                                        let params = {
                                            "productId": data[i].productId,
                                            "quantityPerPack": data[i].quantityPerPack,
                                            "quantity": 1,
                                            "optionId": data[i].optionId,
                                        }
                                        itemOptionId.push(params)
                                    }
                                }
                            }
                            let resultItems = {
                                regularDeliveryProductDelete: itemOptionId,
                                regularDeliveryProductAdd: []
                            }

                            axios.put(apiURL + "/product/regular-delivery", resultItems)
                                .then((res) => {
                                    console.log(res)
                                })
                        })
                        .catch(err => {
                            console.log(err)
                        })
                } else {
                    await this.$refs.showPopupRegular.show({
                        title: this.$t('modal.titleDialog'),
                        product: product,
                        cancelButton: this.$t('modal.btnOk'),
                    })
                }
            },
            isProductInRegular(result) {
                this.isRegular = result
            },
        }
    }
</script>

<style lang="scss">
    .best-product {
        &__box {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
            &--top {
                margin-bottom: 10px;
            }
            &--no {
                font-size: 18px;
                font-weight: bold;
                color: #ef4130;
                border-bottom: 2px solid #ef4130;
                display: inline-block;
                margin-bottom: 0;
                span {
                    font-size: 30px;
                }
            }
            &--tick {
                margin: 0;
                padding: 0;
                padding-left: 95px;
                li {
                    border: 1px solid #ccc;
                    padding: 3px 10px;
                    color: #999;
                    cursor: pointer;
                    margin-left: 10px;
                    transition: all 0.3s;
                    a {
                        color: #999;
                    }
                    &:hover {
                        opacity: 0.8;
                    }
                    &.active {
                        border: 1px solid #ef4130;
                        color: #ef4130;
                        svg {
                            fill: #ef4130;
                        }
                        a {
                            color: #ef4130;
                        }
                    }
                    svg {
                        width: 10px;
                        margin-right: 5px;
                        margin-top: -2px;
                        color: #ccc;
                    }
                    img {
                        width: 10px;
                        filter: grayscale(1);
                        margin-right: 5px;
                    }
                }
            }
        }
    }
</style>
