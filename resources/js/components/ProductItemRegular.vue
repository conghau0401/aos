<template>
  <div class="content-product__content ar-product">
    <div class="content-product__select">
      <input
        v-model="checkboxIds"
        type="checkbox"
        :data-product="JSON.stringify(item)"
        :data-quantity="quantity"
        :data-weekday="weekdayChecked"
        name="checkboxItem"
        :value="item.productId + '_' + item.optionId"
        @click="toggleCheckBox(item.productId, item.optionId)"
      >
    </div>
    <div class="content-product__info">
      <p class="content-product__image img-product">
        <router-link :to="'/product-detail/' + item.productId">
          <img
            :src="$format.imageUrl(item.productImg)"
            alt="Product"
          >
        </router-link>
      </p>
      <div class="content-product__name">
        <h3>
          <router-link :to="'/product-detail/' + item.productId">
            {{ item.productName }}
          </router-link>
        </h3>
        <p class="price">
          <span class="number">{{ $format.price(getPrice.price) }}</span> <span class="unit">{{ $t("product_item.won") }}</span> ({{ item.marketableSize }})
        </p>
      </div>
    </div>
    <p class="content-product__date">
      <span
        v-for="(weekday, index) in weekdays"
        :key="index"
      >
        <input
          :id="weekday + item.productId + item.optionName"
          v-model="weekdayChecked"
          type="checkbox"
          :value="weekday"
          @change="chooseWeekday(weekday, item)"
        >
        <label :for="weekday + item.productId + item.optionName">{{ weekday }}</label>
      </span>
    </p>
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
          @click="addToRegular(item)"
        >
          수정
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
            regularProducts: {
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
        data() {
            return {
                compareOptionId: this.item.optionId,
                quantity: this.item.quantity,
                shippingMethod: 2,
                weekdayChecked: [],
                weekdays: [
                    "월", "화", "수", "목", "금", "보류"
                ],
            }
        },
        watch: {
            item (n, o) {
                this.compareOptionId = n.optionId
            },
        },
        computed: {
            ...mapGetters(['getMarginRates', 'getStoreInfos']),
            itemPrice() {
                return this.getPrice.price * this.quantity
            },
            getPrice() {
                return this.calculatePrice(this.item.productTp, this.item, this.getMarginRates, this.getStoreInfos)
            },
        },
        emits: [
            "toggleCheckBox",
            "changeWeekdayRegular",
            "checkAllStatus",
        ],
        mounted() {
            if (this.item.monday == true) {
                this.weekdayChecked.push("월")
            }
            if (this.item.tuesday == true) {
                this.weekdayChecked.push("화")
            }
            if (this.item.wednesday == true) {
                this.weekdayChecked.push("수")
            }
            if (this.item.thursday == true) {
                this.weekdayChecked.push("목")
            }
            if (this.item.friday == true) {
                this.weekdayChecked.push("금")
            }
            if (this.item.hold == true) {
                this.weekdayChecked.push("보류")
            }
        },
        methods: {
            toggleCheckBox(productId, optionId) {
                let id = productId + "_" + optionId
                this.$store.commit("updateIdsChecked", id)
                this.$emit("toggleCheckBox", id)
            },
            chooseWeekday(value, product) {
                let id = product.productId + "_" + product.optionId
                this.$emit("changeWeekdayRegular", id)
                if (value == "보류") {
                    this.weekdayChecked = []
                    this.weekdayChecked.push("보류")
                } else {
                    if (this.weekdayChecked.length > 1 && this.weekdayChecked.indexOf("보류") != -1) {
                        this.weekdayChecked.splice(this.weekdayChecked.indexOf("보류"), 1)
                    } else if (this.weekdayChecked.length == 0) {
                        this.weekdayChecked.push("보류")
                    }
                }
                // commit to vuex
                let routeName = this.$route.name
                this.commitRegular(routeName)
            },
            addToRegular(product) {
                if (this.quantity == 0) {
                    alert(this.$t("modal.quantityZero"))
                } else {
                    alert(this.$t("modal.addSuccess"))
                    let params = {
                        "regularDeliveryProductAdd": [
                            {
                                "productId": product.productId,
                                "quantityPerPack": product.quantityPerPack,
                                "quantity": this.quantity,
                                "optionId": product.optionId,
                                "monday": this.weekdayChecked.indexOf("월") != -1 ? true : false,
                                "tuesday": this.weekdayChecked.indexOf("화") != -1 ? true : false,
                                "wednesday": this.weekdayChecked.indexOf("수") != -1 ? true : false,
                                "thursday": this.weekdayChecked.indexOf("목") != -1 ? true : false,
                                "friday": this.weekdayChecked.indexOf("금") != -1 ? true : false,
                                "hold": this.weekdayChecked.indexOf("보류") != -1 ? true : false,
                            }
                        ],
                    }
                    let flag = 0;
                    for (let i=0; i < this.regularProducts.length; i++) {
                        if (product.productId == this.regularProducts[i].productId && product.optionId == this.regularProducts[i].optionId) {
                            flag = 1
                        }
                    }
                    if (flag == 0) {
                        //add new product
                        this.$store.commit('addRegularProduct', params.regularDeliveryProductAdd[0])
                    } else {
                        //update product
                        this.$store.commit('updateRegularProduct', params.regularDeliveryProductAdd[0])
                    }
                    //add product to Regular API
                    axios.put(apiURL + "/product/regular-delivery", params)
                    .then((res) => {
                        console.log(res)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
    .plan-title {
        color: #5b5b5b;
        margin-bottom: 30px;
        &__name {
            font-size: 30px;
            font-weight: bold;
            margin: 0;
        }
        ul {
            margin: 0;
            li {
                font-size: 14px;
                padding: 0 7px;
                position: relative;
                &:last-child {
                    &:after {
                        display: none;
                    }
                }
                &:after {
                    content: "";
                    position: absolute;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 1px;
                    height: 10px;
                    background: #b5b5b5;
                }
                a {
                    color: #555555;
                }
                &:hover {
                    font-size: 18px;
                    font-weight: bold;
                }
            }
        }
    }

    .container-box {
        margin-bottom: 30px;
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
            width: 40%;
            text-align: left;
            display: flex;
            align-items: center;
        }
        &__date {
            width: 15%;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            input[type="checkbox"] {
                + label {
                    display: block;
                    padding: 5px 0;
                    cursor: pointer;
                }
                display: none;
                &:checked {
                    + label {
                        background: #f8e25b;
                    }
                }
            }
            span {
                display: block;
                border: 1px solid #ddd;
                width: 32%;
                font-size: 12px;
                margin: 0 1%;
                cursor: pointer;
                margin-bottom: 4px;
                &.active {
                    background: #f8e25b;
                }
            }
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
        .plan-title {
            &__name {
                margin-bottom: 20px;
            }
            ul {
                padding: 0;
                li {
                    margin-bottom: 10px;
                }
            }
        }
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
        .wrapper-regular {
            .content-product {
                &__date {
                    width: 20%;
                    span {
                        font-size: 9px;
                        white-space: nowrap;
                    }
                }
                &__option {
                    width: 10%;
                }
                &__water {
                    width: 10%;
                }
                &__info {
                    width: 30%;
                    padding: 0 5px;
                }
            }
        }
        #plan-product {
            padding-bottom: 15px;
        }
        .plan-title {
            &__name {
                font-size: 22px;
            }
            ul {
                margin: 0 0 0 -7px;
                li {
                    margin-bottom: 5px;
                    &.other {
                        font-size: 16px;
                    }
                }
            }
        }
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
                width: 35%;
            }
            &__option, &__water {
                width: 15%;
            }
            &__num {
                width: 30%;
            }
        }
        .content-product {
            &__number {
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
                    white-space: nowrap;
                    display: flex;
                    align-items: center;
                    font-size: 9px;
                }
            }
            &__content {
                font-size: 12px;
                padding: 15px 5px;
            }
            &__info {
                display: block;
                width: 35%;
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
                span {
                    font-size: 8px;
                }
            }
            &__num {
                width: 30%;
                p.noted-num {
                    font-size: 8px;
                }
                p.num {
                    font-size: 14px;
                }
            }
        }
    }
</style>
