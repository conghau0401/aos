<template>
  <div class="content-product__content ar-product">
    <div class="content-product__select">
      <input
        v-model="checkboxIds"
        type="checkbox"
        name="checkboxItem"
        :value="item.id"
        @click="toggleCheckBox(item.id)"
      >
    </div>
    <div class="content-product__info">
      <p class="content-product__image img-product">
        <router-link :to="'/product-detail/' + item.ds_product_id">
          <img
            :src="$format.imageUrl(item.product_image)"
            alt="Product"
          >
        </router-link>
      </p>
      <div class="content-product__name">
        <h3>
          <router-link :to="'/product-detail/' + item.ds_product_id">
            {{ item.productNm }}
          </router-link>
        </h3>
        <p class="price">
          <span class="number">{{ $format.price(getPrice.price) }}</span> <span class="unit">{{ $t("product_item.won") }}</span> ({{ item.marketableSize }})
        </p>
      </div>
    </div>
    <p class="content-product__option">
      <span class="active">{{ item.optionName }}</span>
    </p>
    <p class="content-product__water">
      <span>{{ item.quantityPerPack }}{{ $t("category.product") }}</span>
    </p>
    <div class="content-product__num">
      <p class="num">
        {{ $format.price(itemPrice) }}<span>{{ $t("product_item.won") }}</span>
      </p>
      <div class="content-product__number d-flex align-items-center align-items-center justify-content-end">
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
          @change="updateInputQuantity($event, item.maxOrder)"
        >
        <span
          class="increment"
          @click="increaseNumber($event, item.maxOrder)"
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
          class="modify"
          @click="addToCart($event, item, item.ds_product_id, item.product_image, compareOptionId, quantity, item.shipping_method)"
        >
          {{ $t('cart.update') }}
        </p>
      </div>
      <p class="noted-num">
        {{ $t("product_item.max") }} {{ item.maxOrder }}{{ $t("product_item.canbe") }}
      </p>
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
            item: {
                type: Object,
                default: () => {}
            },
            productInCart: {
                type: Array,
                default: () => {}
            },
            checkboxIds: {
                type: Array,
                default: () => {}
            },
            countTotal: {
                type: Number,
                default: 0
            },
        },
        emits: ['toggleCheckBox'],
        data() {
            return {
                compareOptionId: this.item.ds_option_id,
                quantity: this.item.quantity,
            }
        },
        computed: {
            ...mapGetters(['getMarginRates', 'getStoreInfos']),
            getPrice() {
                return this.calculatePrice(this.item.productTp, this.item, this.getMarginRates, this.getStoreInfos)
            },
            itemPrice() {
                return this.quantity * this.getPrice.price
            },
        },
        watch: {
            item (n, o) {
                this.compareOptionId = n.ds_option_id
                this.quantity = n.quantity
            },
        },
        methods: {
            toggleCheckBox(id) {
                this.$store.commit("updateIdsChecked", id)
                this.$emit("toggleCheckBox", id)
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container-box {
        margin-bottom: 30px;
        padding-bottom: 30px;
        &__icon {
            width: 50px;
            margin-right: 15px;
            margin-bottom: 0;
            img {
                width: 100%;
            }
        }
        &__name {
            font-size: 20px;
            margin-bottom: 0;
            span {
                font-size: 12px;
            }
        }
        &__title {
            margin-bottom: 20px;
            color: #555555;
            .title-right {
                font-size: 14px;
                p.noted {
                    margin: 0;
                }
                .btn-delete {
                    border: 1px solid #ccc;
                    padding: 3px 12px;
                    margin: 0 0 0 15px;
                    border-radius: 2px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    transition: all 0.3s;
                    &:hover {
                        opacity: 0.8;
                    }
                    img {
                        height: 15px;
                        width: auto;
                        margin-right: 5px;
                        margin-top: -2px;
                    }
                }
            }
        }
    }

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

    .content-product {
        &__content {
            display: flex;
            align-items: center;
            text-align: center;
            padding: 15px 15px;
            color: #555555;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            position: relative;
        }
        &__select {
            width: 5%;
        }
        &__info {
            width: 55%;
            text-align: left;
            display: flex;
            align-items: center;
        }
        &__option {
            width: 10%;
            margin: 0;
            span {
                display: block;
                border: 1px solid #ddd;
                border-bottom: 0;
                width: 100%;
                font-size: 12px;
                padding: 5px 0;
                cursor: pointer;
                &:last-child {
                    border-bottom: 1px solid #ddd;
                }
                &.active {
                    background: #f8e25b;
                }
            }
        }
        &__water {
            width: 10%;
            margin: 0;
            span {
                display: block;
                width: 100%;
                font-size: 12px;
                padding: 5px 0;
                cursor: pointer;
            }
        }
        &__num {
            width: 20%;
            text-align: right;
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
        &__image {
            width: 100px;
            margin: 0 15px 0 0;
            img {
                width: 100%;
                border: 1px solid #ededed;
                padding: 5px;
            }
        }
        &__name {
            width: calc(100% - 100px);
            p {
                margin-bottom: 0;
            }
            p.old-price {
                font-size: 14px;
                text-decoration: line-through;
                display: block;
                width: 100%;
                margin: 10px 0 -3px 0;
            }
            p.price {
                font-size: 12px;
                margin: 0 0 5px;
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

        &__number {
            margin: 5px 0;
            input {
                width: 40px;
                text-align: center;
                outline: none;
                border: 1px solid #ccc;
                border-radius: 0;
                height: 25px;
                margin: 0 1px;
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
            p.modify {
                margin: 0;
                font-size: 11px;
                border: 1px solid #ccc;
                background: #e0f6ff;
                height: 25px;
                text-align: center;
                margin-left: 1px;
                padding: 2px 5px;
                box-sizing: border-box;
                cursor: pointer;
                img {
                    width: 15px;
                }
            }
        }
    }

    @media only screen and (max-width: 991px) {
        .container-box {
            &__title {
                .title-right {
                    .btn-delete {
                        margin: 0;
                        margin-right: 10px;
                        display: inline-flex;
                    }
                    p.noted {
                        margin: 10px 0;
                        font-size: 14px;
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .container-box {
            &__name {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }
        }
        .box-product {
            &__title {
                padding: 10px 5px;
                font-size: 13px;
            }
            &__info {
                width: 40%;
            }
            &__option, &__water {
                width: 15%;
            }
            &__num {
                width: 25%;
            }
        }
        .content-product {
            &__number {
                flex-wrap: wrap;
                span {
                    width: 30%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                input {
                    width: calc(40% - 2px);
                }
                p.modify {
                    margin-left: 0;
                    margin-top: 2px;
                }
            }
            &__content {
                font-size: 12px;
                padding: 15px 5px;
            }
            &__info {
                display: block;
                width: 40%;
                padding: 0 10px;
            }
            &__name {
                width: 100%;
                margin-top: 5px;
                h3 {
                    font-size: 12px;
                    font-weight: bold;
                    margin-top: 10px;
                }
                p.price {
                    span.number {
                        font-size: 15px;
                    }
                }
            }
            &__image {
                width: 100%;
                margin: 0;
            }
            &__option, &__water {
                width: 15%;
            }
            &__num {
                width: 25%;
            }
        }
    }
</style>
