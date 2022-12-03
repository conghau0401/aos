<template>
  <div class="row">
    <div class="col-lg-8 col-md-6 col-6">
      <div class="purchase-history__info">
        <p class="purchase-history__image">
          <router-link :to="'/product-detail/' + product.productId">
            <img
              :src="$format.imageUrl(product.productImg)"
              alt="Product"
            >
          </router-link>
        </p>
        <div class="purchase-history__name">
          <h3>
            <router-link :to="'/product-detail/' + product.productId">
              {{ product.productNm }}
            </router-link>
          </h3>
          <p class="price">
            <span class="number">{{ $format.price(getPrice.price) }}</span> <span class="unit">{{ $t("product_monthly_purchase.won") }}</span> ({{ product.marketableSize }})
          </p>
        </div>
        <div class="purchase-history__option">
          <p>{{ product.optionNm }}, {{ product.quantityPerPack }}{{ $t("category.product") }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6">
      <div class="purchase-history__right">
        <div class="purchase-history__right--info">
          <dl>
            <dt>{{ $t("product_monthly_purchase.payDate") }}</dt>
            <dd>{{ $format.timeToStr(product.forwardDt) }}</dd>
          </dl>
          <dl>
            <dt>{{ $t("product_monthly_purchase.Quan") }} </dt>
            <dd>{{ product.forwardEa }}{{ $t("product_monthly_purchase.product") }}</dd>
          </dl>
          <dl>
            <dt>{{ $t("product_monthly_purchase.monthlyOrder") }}</dt>
            <dd>{{ product.forwardCount }} {{ $t("product_monthly_purchase.timeMonth") }}</dd>
          </dl>
        </div>
        <ul class="purchase-history__right--btn">
          <li
            :class="[isCart ? 'active' : '']"
            @click="popupAddCart(product)"
          >
            {{ $t("product_monthly_purchase.shoppingCart") }}
          </li>
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
            /></g></g></g></svg>{{ $t("product_monthly_purchase.interestItem") }}
          </li>
          <li
            :class="[isRegular ? 'active' : '']"
            @click="showPopupRegular(product)"
          >
            {{ $t("product_monthly_purchase.regularDeli") }}
          </li>
        </ul>
      </div>
    </div>
  </div>
  <PopupAddCart
    ref="popupAddCart"
    @is-product-in-cart="isProductInCart"
  />
  <ConfirmDialogue ref="confirmDialogue" />
  <PopupRegular
    ref="showPopupRegular"
    @is-product-in-regular="isProductInRegular"
  />
</template>

<script>
import PopupAddCart from '../components/PopupAddCart'
import ConfirmDialogue from '../components/ConfirmDialogue'
import PopupRegular from '../components/PopupRegular'
import { addToWishlist } from '../mixins/addToWishlist'
import { calculatePriceMixin } from '../mixins/calculatePriceMixin'
import { mapGetters } from "vuex"

export default {
    components: {
        PopupAddCart,
        ConfirmDialogue,
        PopupRegular,
    },
    mixins: [
        addToWishlist,
        calculatePriceMixin,
    ],
    props: {
        product: {
            type: Object,
            default: () => {}
        }
    },
    data() {
        return {
            isWishlist: false,
            isCart: false,
            isRegular: false
        }
    },
    computed: {
        ...mapGetters(['getProducts', 'getRegularProducts', 'getMarginRates', 'getStoreInfos', 'getWishlistProducts']),
        wishlistProducts() {
            return this.getWishlistProducts.data;
        },
        productInCart() {
            return this.getProducts.data;
        },
        productInRegular() {
            return this.getRegularProducts;
        },
        getPrice() {
            return this.calculatePrice(this.product.productTp, this.product, this.getMarginRates, this.getStoreInfos)
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
        if (this.productInCart != null) {
            this.activeProductInCart(this.productInCart)
        }
        if (this.productInRegular != null) {
            this.activeProductInRegular(this.productInRegular)
        }
    },
    methods: {
        addOrDeleteProductWishList(prd) {
            let result = false
            for(let i in prd) {
                if (prd[i].productId == this.product.productId) {
                    result = true
                }
            }
            this.isWishlist = result
        },
        activeProductInCart(prd) {
            let result = false
            for(let i in prd) {
                if (prd[i].ds_product_id == this.product.productId) {
                    result = true
                }
            }
            this.isCart = result
        },
        activeProductInRegular(prd) {
            let result = false
            for(let i in prd) {
                if (prd[i].productId == this.product.productId) {
                    result = true
                }
            }
            this.isRegular = result
        },
        async popupAddCart(product) {
            if (this.isCart) {
                this.isCart = false
                let getId = [];
                for(let i in this.productInCart) {
                    if (this.productInCart[i].ds_product_id == this.product.productId) {
                        getId.push(this.productInCart[i].id)
                    }
                }
                this.$store.commit("deleteProductInCart", getId)
                this.$refs.confirmDialogue.show({
                    title: this.$t('modal.titleDialog'),
                    message: this.$t('modal.addSuccess'),
                    cancelButton: this.$t('modal.btnOk'),
                    isDialog: true,
                })
                axios
                    .delete(apiURL + '/carts/delete/' + getId.join(","))
                    .then(res => {
                        this.$emit("deleteProductInCart", product.productId)
                    })
                    .catch(error => {
                        console.log(error)
                    })
            } else {
                await this.$refs.popupAddCart.show({
                    title: this.$t('modal.titleDialog'),
                    product: product,
                    cancelButton: this.$t('modal.btnOk'),
                })
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
        isProductInCart(result) {
            this.isCart = result
        },
        isProductInRegular(result) {
            this.isRegular = result
        }
    }
}
</script>

<style lang="scss" scoped>
    .purchase-history {
        padding: 40px 0;
        &__filter {
            margin-bottom: 30px;
            &--submit {
                border: none;
                background: #999999;
                height: 35px;
                padding: 0 30px;
                color: #fff;
                margin-left: 10px;
                font-size: 14px;
                border-radius: 5px;
                transition: all 0.3s;
                &:hover {
                    opacity: 0.8;
                }
            }
            &--title {
                margin: 0;
                font-weight: bold;
                text-transform: uppercase;
                padding-right: 10px;
            }
            .between-icon {
                margin: 0 10px 0;
            }
            &--box {
                input {
                    border: 1px solid #ccc;
                    height: 35px;
                    padding: 0 10px 0 15px;
                    margin-left: 5px;
                    outline: none;
                    color: #555555;
                    background: #fff;
                    border-radius: 5px;
                }
            }
        }
        &__ar {
            padding: 15px 0;
            border-bottom: 1px solid #e8e8e8;
        }
        &__content {
            margin-bottom: 30px;
        }
        &__right {
            display: flex;
            align-items: center;
            justify-content: space-between;
            &--info {
                width: 75%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                dl {
                    width: 48%;
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    font-size: 14px;
                    word-break: break-all;
                    margin-bottom: 10px;
                    dt, dd {
                        margin: 0;
                        font-weight: normal;
                    }
                    dt {
                        width: 40%;
                    }
                    dd {
                        width: 60%;
                    }
                }
            }
            &--btn {
                width: 22%;
                padding: 0;
                margin: 0;
                li {
                    list-style: none;
                    width: 100%;
                    border: 1px solid #ccc;
                    padding: 5px;
                    font-size: 12px;
                    text-align: center;
                    border-bottom: 0;
                    color: #555;
                    &.wishlist {
                        svg {
                            width: 10px;
                            margin-right: 5px;
                            margin-top: -2px;
                            fill: #ccc;
                        }
                    }
                    &.active {
                        border: 1px solid #ef4130;
                        color: #ef4130;
                        font-weight: bold;
                        margin-bottom: -1px;
                        position: relative;
                        svg {
                            fill: #ef4130;
                        }
                        &:last-child {
                            border-bottom: 1px solid #ef4130;
                        }
                    }
                    cursor: pointer;
                    a {
                        color: #555;
                    }
                    &:last-child {
                        border-bottom: 1px solid #ccc;
                    }
                    &:hover {
                        border-left: 2px solid #ef4130;
                        border-right: 2px solid #ef4130;
                        color: #ef4130;
                        svg {
                            fill: #ef4130;
                        }
                        a {
                            color: #ef4130;
                        }
                        img {
                            filter: grayscale(0%);
                            opacity: 1;
                        }
                    }
                    img {
                        width: 10px;
                        filter: grayscale(100%);
                        opacity: 0.6;
                    }
                }
            }
        }
        &__info {
            width: 60%;
            text-align: left;
            display: flex;
            align-items: center;
        }
        &__image {
            width: 100px;
            margin: 0 15px 0 0;
            img {
                width: 100%;
                border: 1px solid #ededed;
            }
        }
        &__name {
            width: calc(100% - 100px);
            p {
                margin-bottom: 0;
            }
            p.price {
                font-size: 12px;
                margin: 5px 0;
                span {
                    &.number {
                        font-size: 20px;
                        font-weight: bold;
                    }
                    &.unit {
                        margin: 0 5px 0 0;
                    }
                }
            }
            p.rating {
                display: flex;
                align-items: center;
                font-size: 12px;
                color: #999999;
                img {
                    width: 15px;
                    margin-top: -3px;
                    margin-right: 5px;
                }
            }
            h3 {
                font-size: 16px;
                margin: 0;
                a {
                    color: #555555;
                }
            }
        }
        &__option {
            width: 110px;
        }
        &__title {
            font-size: 30px;
            text-align: center;
            margin-bottom: 40px;
        }
        &__content {
            &--title {
                font-weight: bold;
                position: relative;
                &.other {
                    font-size: 14px;
                    font-weight: normal;
                    p {
                        margin-left: 0;
                        padding-left: 0;
                    }
                }
                p {
                    display: inline-block;
                    background: #fff;
                    margin: 0 0 0 100px;
                    position: relative;
                    padding: 0 15px;
                    max-width: 80%;
                }
                &:before {
                    position: absolute;
                    content: "";
                    width: 100%;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    height: 5px;
                    background: #e8e8e8;
                }
                span {
                    font-size: 20px;
                }
            }
        }
    }

    @media only screen and (max-width: 991px) {
        .purchase-history {
            &__info {
                width: 100%;
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .purchase-history {
            padding: 25px 0;
            &__filter {
                margin-bottom: 30px;
                &--submit {
                    margin-left: 0;

                }
                &--title {
                    margin: 0;
                    font-weight: bold;
                    text-transform: uppercase;
                    padding-right: 10px;
                }
                .between-icon {
                    display: none;
                }
                &--box {
                    input {
                        margin: 5px 0;
                        width: 100%;
                    }
                }
            }
            &__content {
                &:last-child {
                    margin-bottom: 0;
                }
            }
            &__title {
                font-size: 25px;
                margin-bottom: 25px;
            }
            &__content {
                &--title {
                    p {
                        padding-left: 0;
                        margin-left: 0;
                    }
                }
            }
            &__info {
                width: 100%;
                display: block;
            }
            &__name {
                width: 100%;
                h3 {
                    font-size: 14px;
                    margin: 10px 0 5px;
                }
                p.price {
                    margin-bottom: 0;
                }
            }
            &__image{
                width: 50%;
            }
            &__right {
                &--info {
                    width: 56%;
                    dl {
                        width: 100%;
                        margin-bottom: 5px;
                        display: block;
                        dt, dd {
                            width: 100%;
                        }
                        dt {
                            font-weight: bold;
                        }
                        dd {
                            font-size: 13px;
                        }
                    }
                }
                &--btn {
                    width: 42%;
                }
            }
        }
    }
</style>
