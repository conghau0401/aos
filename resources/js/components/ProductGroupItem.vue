<template>
  <div>
    <div class="box-product__title">
      <p class="box-product__select">
        <input
          v-if="page=='/shopping-cart'"
          v-model="isCheckAll"
          type="checkbox"
          name="itemCheckAll"
          class="checkAll"
          @click="checkAll(productInfo, 'id'); eventCheckAll()"
        >
        <input
          v-if="page=='/regular-delivery'"
          v-model="isCheckAll"
          type="checkbox"
          name="regularCheckAll"
          class="checkAll"
          @click="checkAllRegular(productInfo, 'productId', 'optionId'); eventCheckAllRegular()"
        >
        <input
          v-if="page!='/shopping-cart' && page!='/regular-delivery'"
          v-model="isCheckAll"
          type="checkbox"
          class="checkAll"
          @click="checkAll(productInfo, 'productId'); eventCheckAllCommon()"
        >
      </p>
      <p :class="['box-product__info', page == '/regular-delivery' ? 'regular' : '']">
        {{ $t("product_group_item.prodName") }}
      </p>
      <p
        v-if="page == '/regular-delivery'"
        class="box-product__date"
      >
        {{ $t("product_group_item.deliDate") }}
      </p>
      <p class="box-product__option">
        {{ $t("product_group_item.option") }}
      </p>
      <p class="box-product__water">
        {{ $t("product_group_item.goWater") }}
      </p>
      <p class="box-product__num">
        {{ $t("product_group_item.subtotal") }}
      </p>
    </div>
    <div
      v-for="(item, index) in productInfo"
      :key="index"
    >
      <ProductItemCart
        v-if="page == '/shopping-cart' || page == '/order-form' "
        :item="item"
        :product-in-cart="productInCart"
        :checkbox-ids="checkboxIds"
        :count-total="countTotal"
        @toggle-check-box="toggleCheckBox"
        @check-all-status="checkAllStatus"
      />
      <ProductItemRegular
        v-else-if="page == '/regular-delivery'"
        :item="item"
        :regular-products="regularProducts"
        :count-total="countTotal"
        :checkbox-ids="checkboxIds"
        @check-all-status="checkAllStatus"
        @toggle-check-box="toggleCheckBox"
        @change-weekday-regular="changeWeekdayRegular"
      />
      <ProductItem
        v-else
        :item="item"
        :product-in-cart="productInCart"
        :checkbox-ids="checkboxIds"
        :count-total="countTotal"
        @check-all-status="checkAllStatus"
        @toggle-check-box="toggleCheckBox"
      />
    </div>
    <!-- <div class="total-price d-lg-flex flex-wrap justify-content-end align-items-center"
            v-if="page != '/shopping-cart' && page != '/order-form'"
        >
            <div class="total-price__total">
                <p class="total-price__title">{{ $t("product_group_item.prodSub") }}</p>
                <p class="total-price__price">
                    {{ $format.price(typePrice) }} <span> {{ $t("product_group_item.won") }}</span>
                </p>
            </div>
            <p class="icon-computed">&#43;</p>
            <div class="total-price__total">
                <p class="total-price__title">{{ $t("product_group_item.deliFee") }}</p>
                <p class="total-price__price">
                    {{ $format.price(shippingFee) }} <span> {{ $t("product_group_item.won") }}</span>
                </p>
            </div>
            <p class="icon-computed">&#61;</p>
            <div class="total-price__total">
                <p class="total-price__title">{{ $t("product_group_item.industProd") }}</p>
                <p class="total-price__price other">
                    {{ $format.price(totalPrice) }} <span> {{ $t("product_group_item.won") }}</span>
                </p>
            </div>
        </div> -->
  </div>
</template>

<script>
    import ProductItem from '../components/ProductItem'
    import ProductItemCart from '../components/ProductItemCart'
    import ProductItemRegular from '../components/ProductItemRegular'
    import { mapGetters } from "vuex"
    import { checkboxMixin } from '../mixins/checkboxMixin'
    export default {
        components: {
            ProductItem,
            ProductItemCart,
            ProductItemRegular
        },
        mixins: [

            checkboxMixin,
        ],
        props: {
            productInfo: {
                type: Array,
                default: () => {}
            },
            page: {
                type: String,
                default: ''
            },
            productTP: {
                type: Number,
                default: 0
            },
            isRemoveChecked: Boolean
        },
        emits: ['toggleCheckboxId', 'toggleCheckboxIdRegular', 'getAllCheckboxIds'],
        data() {
            return {
                productNumber: [],
                shippingFee: 0,
            }
        },
        computed: {
            countTotal() {
                return this.productInfo.length
            },
            productPrice() {
                let result = 0;
                for (let index = 0; index < this.productNumber.length; index++) {
                    const element = this.productNumber[index];
                    for (const [key, value] of Object.entries(element)) {
                        result += value.price * value.quantity
                    }
                }
                return result
            },
            ...mapGetters(['getProducts']),
            productInCart() {
                return this.getProducts.data;
            },
            ...mapGetters(['getRegularProducts']),
            regularProducts() {
                return this.getRegularProducts;
            },
            typePrice() {
                let price = 0;
                let result = [];
                if (this.productInCart != null) {
                    if (this.page == '/regular-delivery') {
                        result = this.groupByRegularType(this.regularProducts, this.productTP)
                    } else {
                        result = this.groupByType(this.productInCart, this.productTP)
                    }
                    price = this.calculatorPrice(result)
                }
                return price
            },
            totalPrice() {
                return this.typePrice + this.shippingFee
            }
        },
        watch: {
            isRemoveChecked(n, o) {
                this.checkboxIds = []
                this.isCheckAll = false
            }
        },
        methods: {
            checkAllStatus(status) {
                this.isCheckAll = status
            },
            toggleCheckBox(result, quantity = null) {
                if (this.checkboxIds.includes(result)) {
                    for(let i = 0; i < this.checkboxIds.length; i++) {
                        if (result == this.checkboxIds[i]) {
                            this.checkboxIds.splice(i, 1)
                        }
                    }
                } else {
                    this.checkboxIds.push(result)
                }
                this.$emit("toggleCheckboxId", result, quantity)
                if (this.checkboxIds.length == this.countTotal) {
                    this.isCheckAll = true
                } else {
                    this.isCheckAll = false
                }
            },
            changeWeekdayRegular(result, quantity = null) {
                if (!this.checkboxIds.includes(result)) {
                    this.checkboxIds.push(result)
                }
                this.$emit("toggleCheckboxIdRegular", result, quantity)
                if (this.checkboxIds.length == this.countTotal) {
                    this.isCheckAll = true
                } else {
                    this.isCheckAll = false
                }
            },
            eventCheckAll() {
                let groupIds = []
                for(let i in this.productInfo) {
                    groupIds.push(this.productInfo[i].id)
                    // check all status
                    if (this.isCheckAll) {
                        this.$store.commit("addIdsChecked", this.productInfo[i].id)
                    } else {
                        this.$store.commit("updateIdsChecked", this.productInfo[i].id)
                    }
                }
                this.$emit("getAllCheckboxIds", groupIds, this.checkboxIds)
            },
            eventCheckAllCommon() {
                let groupIds = []
                for(let i in this.productInfo) {
                    groupIds.push(this.productInfo[i].productId)
                    // check all status
                    if (this.isCheckAll) {
                        this.$store.commit("addIdsChecked", this.productInfo[i].productId)
                    } else {
                        this.$store.commit("updateIdsChecked", this.productInfo[i].productId)
                    }
                }
            },
            eventCheckAllRegular() {
                let groupIds = []
                for(let i in this.productInfo) {
                    let idMerge = this.productInfo[i].productId + "_" + this.productInfo[i].optionId
                    groupIds.push(idMerge)
                    // check all status
                    if (this.isCheckAll) {
                        this.$store.commit("addIdsChecked", idMerge)
                    } else {
                        this.$store.commit("updateIdsChecked", idMerge)
                    }
                }
                // this.$emit("getAllCheckboxIds", groupIds, this.checkboxIds)
            },
            groupByType(obj, type) {
                let result = []
                for(let j = 0; j < this.productInfo.length; j ++) {
                    for(let k = 0; k < this.productInfo[j].options.length; k ++) {
                        for(let i = 0; i < obj.length; i++) {
                            if (this.productInfo[j].options[k].productId == obj[i].ds_product_id && this.productInfo[j].options[k].optionId == obj[i].ds_option_id && parseInt(obj[i].productTp) == parseInt(type)) {
                                result.push(obj[i])
                            }
                        }
                    }
                }
                return result
            },
            groupByRegularType(obj, type) {
                let result = []
                for(let j = 0; j < this.productInfo.length; j ++) {
                    for(let i = 0; i < obj.length; i++) {
                        if (this.productInfo[j].productId == obj[i].productId && this.productInfo[j].optionId == obj[i].optionId && parseInt(obj[i].productTp) == parseInt(type)) {
                            result.push(obj[i])
                        }
                    }
                }
                return result
            },
            calculatorPrice(obj) {
                let result = 0
                for(let i = 0; i < obj.length; i++) {
                    if (this.page != '/regular-delivery') {
                        result += obj[i].price * obj[i].quantity
                    } else {
                        result += obj[i].supplyUnitPrice * obj[i].quantity
                    }
                }
                return result
            },
        }
    }
</script>

<style lang="scss" scoped>
    .box-product {
        &__title {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            font-size: 16px;
            color: #5b5b5b;
            text-align: center;
            background: #f4f4f4;
            padding: 10px 15px;
            font-weight: bold;
            p {
                margin: 0;
            }
        }
        &__select {
            width: 5%;
        }
        &__info {
            width: 55%;
            &.regular {
                width: 40%;
            }
        }
        &__date {
            width: 15%;
        }
        &__option {
            width: 10%;
        }
        &__water {
            width: 10%;
        }
        &__num {
            width: 20%;
            text-align: right;
        }
    }

    .total-price {
        font-weight: bold;
        color: #727272;
        font-size: 16px;
        padding-top: 20px;
        p {
            margin: 0;
        }
        &__total {
            display: flex;
        }
        &__total, .icon-computed {
            margin-left: 20px;
        }
        &__title {
            padding-right: 30px;
        }
        .icon-computed {
            font-size: 20px;
        }
        &__price {
            margin-top: 30px !important;
            font-size: 25px;
            &.other {
                color: #ef4130;
                span {
                    font-size: 14px;
                    color: #999;
                }
            }
            span {
                font-size: 14px;
            }
        }
    }

    @media only screen and (max-width: 767px) {
            .wrapper-regular {
                .box-product {
                    &__info.regular {
                        width: 30%;
                    }
                    &__date {
                        width: 15%;
                    }
                    &__option {
                        width: 10%;
                    }
                    &__water {
                        width: 10%;
                    }
                }
            }
        .total-price {
            &__total {
                margin-left: 0;
                justify-content: space-between;
                align-items: center;
            }
            &__price {
                margin-top: 0 !important;
            }
            .icon-computed {
                display: none;
            }
        }
        .box-product {
            &__title {
                font-size: 12px;
                padding-left: 0;
                padding-right: 0;
            }
            &__info {
                width: 35%;
                &.regular {
                    width: 35%;
                }
            }
            &__option {
                width: 15%;
            }
            &__water {
                width: 15%;
            }
            &__num {
                width: 30%;
                text-align: center;
            }
        }
    }
</style>
