<template>
  <div
    class="box-detail__type--header d-flex align-items-center text-center"
  >
    <p
      class="box-detail__type--name d-flex align-items-center"
    >
      <input
        id="checkAll"
        v-model="isCheckAll"
        type="checkbox"
        @click="checkAll(products, 'orderItemCode'); eventCheckAll()"
      >
      <span class="img">
        <img
          v-if="productTp == 1"
          src="/img/icon/box1.png"
          :alt="productTp"
        >
        <img
          v-else-if="productTp == 2"
          src="/img/icon/box2.png"
          :alt="productTp"
        >
        <img
          v-else
          src="/img/icon/both.svg"
          :alt="productTp"
        >
      </span>
      <span class="text">
        <b>{{ products[0].productType }}</b>
      </span>
    </p>
    <p class="box-detail__type--option">
      {{ $t('order_bill.option') }}
    </p>
    <p class="box-detail__type--water">
      {{ $t('order_bill.perPack') }}
    </p>
    <p class="box-detail__type--price">
      {{ $t('order_bill.price') }}
    </p>
    <p class="box-detail__type--quantity">
      {{ $t('order_bill.quantity') }}
    </p>
    <p class="box-detail__type--total">
      {{ $t('order_bill.amount') }}
    </p>
    <p class="box-detail__type--status">
      {{ $t('order_bill.status') }}
    </p>
  </div>
  <div
    v-for="product in products"
    :key="product.productCode"
    class="box-detail__type--body d-flex align-items-center text-center"
  >
    <p class="box-detail__type--name d-flex align-items-center">
      <span class="checkbox"><input
        v-model="checkboxIds"
        type="checkbox"
        :value="product.orderItemCode"
        @click="toggleCheckBox(product.orderItemCode)"
      ></span>
      <span class="img-product"><router-link :to="'/product-detail/' + product.productId"><img
        :src="$format.imageUrl(product.productImg)"
        alt=""
      ></router-link></span>
      <router-link :to="'/product-detail/' + product.productId">
        <span class="name">{{ product.productName }}</span>
      </router-link>
    </p>
    <p class="box-detail__type--option">
      {{ product.option }}
    </p>
    <p class="box-detail__type--water">
      {{ product.quantityPerPack }}
    </p>
    <p class="box-detail__type--price">
      <span>{{ $format.price(product.unitPrice) }}</span>{{ $t('order_bill.won') }}
    </p>
    <p class="box-detail__type--quantity">
      {{ product.quantity }}
    </p>
    <p class="box-detail__type--total">
      <span>{{ $format.price(product.unitPrice * product.quantity) }}</span>{{ $t('order_bill.won') }}
    </p>
    <p :class="['box-detail__type--status', product.quantity <= 0 ? 'color-red' : '']">
      {{ statusDelivery(product.statusId) }}
    </p>
  </div>
</template>
<script>
    import { checkboxMixin } from '../mixins/checkboxMixin'
    import { statusTextMixin } from '../mixins/statusTextMixin'

    export default {
        components: {
        },
        mixins: [
            checkboxMixin,
            statusTextMixin,
        ],
        props: {
            products: {
                type: Array,
                default: () => {}
            },
            productTp: {
                type: Number,
                default: 1
            },
        },
        emits: ["getAllCheckboxIds", "toggleCheckboxId"],
        data() {
            return {
            }
        },
        computed: {
            countTotal() {
                return this.products.length
            },
        },
        methods: {
            eventCheckAll() {
                let groupIds = []
                for(let i in this.products) {
                    groupIds.push(this.products[i].orderItemCode)
                }
                this.$emit("getAllCheckboxIds", groupIds, this.checkboxIds)
            },
            toggleCheckBox(idCode) {
                if (this.checkboxIds.includes(idCode)) {
                    for(let i = 0; i < this.checkboxIds.length; i++) {
                        if (idCode == this.checkboxIds[i]) {
                            this.checkboxIds.splice(i, 1)
                        }
                    }
                } else {
                    this.checkboxIds.push(idCode)
                }
                this.$emit("toggleCheckboxId", idCode)
                if (this.checkboxIds.length == this.countTotal) {
                    this.isCheckAll = true
                } else {
                    this.isCheckAll = false
                }
            },
        }
    }
</script>

<style lang="scss" scoped>
.color-red {
    color: #ef4130;
}
.order-detail {
    padding: 0 0 50px;
    color: #555;
    font-size: 14px;
    &__content {
        width: 100%;
    }
    p,
    ul,
    h2 {
        margin: 0;
        padding: 0;
    }
    &__right {
        &--button {
            .btn-right {
                background: #fff;
                border: 1px solid #ccc;
                padding: 13.5px 12px;
                transition: all 0.3s;
                color: #555;
                img {
                    width: 15px;
                }
                &:hover {
                    opacity: 0.8;
                    background: #f1f1f1;
                }
            }
        }
        &--box-price {
            background: #f1f1f1;
            padding: 15px 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-top: 10px;
            .ar-box {
                margin-bottom: 15px;
                p.title {
                    font-weight: bold;
                }
                .total-price {
                    text-align: right;
                    span {
                        color: #ef4130;
                        font-weight: bold;
                        font-size: 25px;
                    }
                }
                dl {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid #ccc;
                    margin: 0;
                    padding: 6px 0;
                    dt, dd {
                        margin: 0;
                        padding: 0;
                    }
                    dt {
                        font-weight: 500;
                        width: 30%;
                    }
                    dd {
                        width: 70%;
                        text-align: right;
                        span {
                            font-weight: bold;
                            font-size: 17px;
                            &.note {
                                display: block;
                                font-size: 13px;
                                font-weight: 500;
                            }
                        }
                    }
                }
                &--red {
                    span {
                        color: #ef4130;
                    }
                }
            }
        }
    }
    &__table {
        table {
            text-align: center;
            width: 100%;
            thead {
                background: #f1f1f1;
                font-size: 16px;
                tr {
                    th {
                        padding: 10px 0;
                    }
                }
            }
            tbody {
                font-size: 14px;
                tr {
                    border-bottom: 1px solid #ccc;
                    td {
                        padding: 10px 0;
                    }
                }
            }
        }
    }
    &__btn {
        background: #fff;
        border: 1px solid #ccc;
        padding: 5px 15px;
        transition: all 0.3s;
        color: #555;
        img {
            width: 15px;
        }
        &:hover {
            opacity: 0.8;
            background: #f1f1f1;
        }
    }
    &__button {
        margin-top: 30px;
        .last {
            margin-left: 10px;
        }
    }
    &__infomation {
        margin: 30px 0;
        &--title {
            font-size: 16px;
            font-weight: bold;
        }
        &--box {
            border: 1px solid #ccc;
            padding: 10px 15px;
            margin-top: 5px;
            font-size: 13px;
            border-radius: 5px;
            ul {
                li {
                    width: 49%;
                    min-height: 45px;
                    display: flex;
                    align-items: center;
                    span {
                        display: block;
                        &.title {
                            min-width: 20%;
                        }
                        &.data {
                            letter-spacing: -0.7px;
                            line-height: 22px;
                        }
                    }
                }
            }
        }
    }
    &__title {
        h2 {
            font-size: 30px;
            font-weight: bold;
        }
        &--right {
            .title {
                font-size: 16px;
            }
            ul {
                padding-left: 20px;
                li {
                    border: 1px solid #ccc;
                    border-radius: 20px;
                    padding: 3px 20px;
                    margin-left: 10px;
                    cursor: pointer;
                    transition: all 0.3s;
                    &:hover {
                        border: 1px solid #ef4130;
                        color: #ef4130;
                        a {
                            color: #ef4130;
                        }
                    }
                    &.on {
                        border: 1px solid #ef4130;
                        a {
                            color: #ef4130;
                        }
                    }
                    a {
                        color: #555;
                    }
                }
            }
        }
    }
    &__content {
        margin: 30px 0;
        &--no {
            width: 15%;
        }
        &--name {
            width: 35%;
        }
        &--amount {
            width: 15%;
            color: #1d7ac6;
        }
        &--payment {
            width: 15%;
            color: #bc093f;
        }
        &--status {
            width: 10%;
        }
        &--date {
            width: 10%;
        }
        &--title {
            font-weight: bold;
            font-size: 15px;
            background: #f1f1f1;
            p {
                background: #f1f1f1;
                padding: 15px 10px;
            }
        }
        &--data {
            .ar-data {
                p {
                    border-bottom: 1px solid #ccc;
                    padding: 15px 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            }
        }
    }
}
.box-detail {
    font-size: 14px;
    &__title {
        background: #f1f1f1;
        padding: 10px 15px;
        &--left {
            span {
                display: block;
                &.img {
                    width: 30px;
                    margin-right: 10px;
                }
                &.text {
                    font-size: 18px;
                    padding-top: 3px;
                }
            }
        }
        &--right {
            span {
                display: block;
                margin-left: 20px;
            }
        }
    }
    &__type {
        p {
            padding: 0 10px;
        }
        &--header {
            padding: 15px 0 5px;
        }
        &--body {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        &--name {
            width: 40%;
            span {
                display: block;
                &.checkbox {
                    width: 20px;
                }
                &.img {
                    width: 50px;
                    img {
                        width: 100%;
                        height: 35px;
                        width: auto;
                    }
                }
                &.img-product {
                    width: 70px;
                    margin: 0 10px;
                    border: 1px solid #f1f1f1;
                    padding: 10px;
                    img {
                        width: 100%;
                    }
                }
                &.text {
                    font-weight: bold;
                    font-size: 16px;
                    padding-left: 10px;
                }
                &.name {
                    text-align: left;
                    color: #555;
                    // width: calc(100% - (80px + 20px));
                    a {
                        color: #555;
                        display: block;
                    }
                    b {
                        color: #fff;
                        background: #6db2e0;
                        border-radius: 5px 0 5px 0;
                        padding: 2px 5px;
                        font-size: 11px;
                        font-weight: 500;
                        margin-right: 5px;
                        &.green {
                            background: #27b538;
                        }
                    }
                }
            }
        }
        &--option {
            width: 7%;
        }
        &--water {
            width: 7%;
        }
        &--price {
            width: 15%;
            span {
                font-size: 20px;
            }
        }
        &--quantity {
            width: 8%;
        }
        &--total {
            width: 15%;
            span {
                font-size: 20px;
                font-weight: bold;
            }
        }
        &--status {
            width: 10%;
        }
    }
}

@media only screen and (max-width: 991px) {
    .order-detail {
        &__right {
            &--button {
                button {
                    width: 100%;
                    margin-bottom: 5px;
                }
            }
        }
        &__infomation {
            &--box {
                margin-bottom: 15px;
            }
        }
    }
    .box-detail {
        &__title {
            &--left {
                width: 100%;
            }
            &--right {
                margin-top: 10px !important;
                width: 100%;
                span {
                    margin-left: 0;
                    margin-right: 20px;
                }
            }
        }
        &__type {
            &--price span,
            &--total span{
                font-size: 16px;
            }
            p {
                font-size: 12px;
            }
        }
    }
}

@media only screen and (max-width: 767px) {
    .order-detail {
        &__btn {
            padding: 5px 5px;
            width: 100%;
            &.last {
                width: 45%;
                &:first-child {
                    margin-left: 0;
                }
            }
        }
        &__right {
            &--button {
                .btn-right {
                    width: 32%;
                    padding: 5px 10px;
                    text-align: center;
                }
            }
        }
        &__infomation {
            margin: 30px 0 0;
            &--box {
                margin-bottom: 15px;
                ul {
                    li {
                        width: 100%;
                        min-height: auto;
                        padding: 5px 0;
                    }
                }
            }
        }
        &__table {
            margin-top: 20px;
            &--box {
                overflow-x: auto;
                &::-webkit-scrollbar{
                    height: 3px;
                    width: 3px;
                    background: #f1f1f1;
                    border-radius: 0;
                }
                &::-webkit-scrollbar-thumb {
                    background: #ef4130;
                    border-radius: 5px;
                    border-radius: 0;
                }
                table {
                    width: 200%;
                }
            }
        }
        &__title {
            h2 {
                width: 100%;
            }
            p.title {
                margin: 10px 0;
            }
            &--right {
                ul {
                    padding-left: 0;
                    li {
                        margin: 0 10px 5px 0;
                    }
                }
            }
        }
        &__content {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            margin: 10px 0 20px;
            &::-webkit-scrollbar{
                height: 3px;
                width: 3px;
                background: #f1f1f1;
                border-radius: 0;
            }
            &::-webkit-scrollbar-thumb {
                background: #ef4130;
                border-radius: 5px;
                border-radius: 0;
            }
            p {
                overflow: hidden;
                white-space: initial;
                text-overflow: ellipsis;
            }
            &--title {
                p {
                    padding: 10px;
                }
            }
            &--no {
                min-width: 50%;
            }
            &--name {
                min-width: 50%;
            }
            &--amount {
                min-width: 30%;
                color: #1d7ac6;
            }
            &--payment {
                min-width: 30%;
                color: #bc093f;
            }
            &--status {
                min-width: 30%;
            }
            &--date {
                min-width: 30%;
            }
        }
    }
    .box-detail {
        &__type {
            width: 250%;
            p {
                overflow: hidden;
                white-space: initial;
                text-overflow: ellipsis;
            }
        }
        &__ar {
            width: 100%;
            overflow-x: auto;
            white-space: nowrap;
            &::-webkit-scrollbar{
                height: 5px;
                width: 5px;
                background: #f1f1f1;
                border-radius: 0;
            }
            &::-webkit-scrollbar-thumb {
                background: #ef4130;
                border-radius: 0;
            }
        }
        &__title {
            &--left {
                width: 100%;
            }
            &--right {
                margin-top: 10px !important;
                width: 100%;
                span {
                    margin-left: 0;
                    margin-right: 20px;
                }
            }
        }
    }
}
</style>
