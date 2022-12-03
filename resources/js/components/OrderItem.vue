<template>
  <div class="ar-data d-flex align-items-stretch justify-content-between text-center">
    <p class="order-history__content--checkbox">
      <input
        v-model="checkboxIds"
        type="checkbox"
        :value="order.orderId"
        @click="toggleCheckBox(order.orderId)"
      >
    </p>
    <p class="order-history__content--no">
      {{ order.orderNumber }}
    </p>
    <p class="order-history__content--name">
      {{ order.productName }}
    </p>
    <p class="order-history__content--shipping">
      <span
        v-if="order.n10 > 0"
        class="prepare-product"
      >{{ $t("order_item.n10") }}({{ order.n10 }})</span>
      <span
        v-if="order.n20 > 0"
        class="prepare-ship"
      >{{ $t("order_item.n20") }}({{ order.n20 }})</span>
      <span
        v-if="order.n30 > 0"
        class="shipping"
      >{{ $t("order_item.n30") }}({{ order.n30 }})</span>
      <span
        v-if="order.n40 > 0"
        class="shipped"
      >{{ $t("order_item.n40") }}({{ order.n40 }})</span>
      <span
        v-if="order.cancelQuantity > 0"
        class="canceled"
      >{{ $t("order_item.c40") }}({{ order.cancelQuantity }})</span>
      <span
        v-if="order.holderQuantity > 0"
        class="holded"
      >{{ $t("order_item.hold") }}({{ order.holderQuantity }})</span>
    </p>
    <p class="order-history__content--amount">
      {{ order.productTotal }}
    </p>
    <p class="order-history__content--payment">
      {{ $format.price(order.paymentAmount) }}
    </p>
    <p class="order-history__content--status">
      {{ statusText(order.paymentStatus) }}
    </p>
    <p class="order-history__content--date">
      {{ $format.timeToStr(order.orderDate, '-') }}
    </p>
    <p class="order-history__content--view">
      <router-link :to="'/order-history/' + order.orderId">
        <span class="view-btn">{{ $t("order_item.orderDetail") }}</span>
      </router-link>
    </p>
  </div>
</template>
<script>

    import { statusTextMixin } from '../mixins/statusTextMixin'

    export default {
        mixins: [
            statusTextMixin,
        ],
        props: {
            order: {
                type: Object
            },
            checkboxIds: {
                type: Array
            },
            countTotal: {
                type: Number
            },
        },
        emits: ['toggleCheckBox'],
        methods: {
            toggleCheckBox(orderId) {
                this.$store.commit("updateIdsChecked", orderId)
                this.$emit("toggleCheckBox", orderId)
            },
        }
    }
</script>

<style lang="scss">
    .order-history {
        padding: 0 0 50px;
        color: #555;
        font-size: 14px;
        &__content {
            width: 100%;
        }
        p, ul, h2 {
            margin: 0;
            padding: 0;
        }
        ul {
            li {
                transition: all 0.3s;
                &:hover {
                    border: 1px solid #ef4130;
                    color: #ef4130;
                    a {
                        color: #ef4130;
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
        &__search {
            margin: 30px 0 10px;
            input[type="date"] {
                border: 1px solid #ccc;
                height: 35px;
                padding: 0 10px 0 15px;
                outline: none;
                color: #555555;
                background: #fff;
                border-radius: 5px;
            }
            .between-icon {
                margin: 0 10px 0;
            }
            #searchSubmit {
                border: 1px solid #ccc;
                height: 35px;
                outline: none;
                color: #555555;
                background: #f5f5f5;
                border-radius: 20px;
                padding: 0 15px;
                margin-left: 10px;
                cursor: pointer;
                transition: all 0.3s;
                &:hover {
                    opacity: 0.8;
                }
                img {
                    height: 15px;
                    width: auto;
                    margin-right: 5px;
                }
            }
        }
        &__filter {
            &--button {
                margin-right: 20px;
                ul {
                    li {
                        display: inline-block;
                        border: 1px solid #ccc;
                        padding: 5px 15px;
                        margin-right: 10px;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: all 0.3s;
                        &:hover {
                            opacity: 0.8;
                        }
                    }
                }
            }
            &--items {
                ul {
                    li {
                        display: inline-block;
                        border: 1px solid #ccc;
                        padding: 5px 20px;
                        margin-right: 5px;
                        border-radius: 20px;
                        cursor: pointer;
                        transition: all 0.3s;
                    }
                }
            }
        }
        &__content {
            &--checkbox {
                width: 5%;
            }
            &--no {
                width: 15%;
            }
            &--name {
                width: 30%;
            }
            &--shipping {
                width: 14%;
                span {
                    display: inline-block;
                    padding: 2px 12px;
                    border-radius: 4px;
                    margin: 0 2px 4px 2px;
                    border: 1px solid #ccc;
                    &.prepare-product {
                        background: #F6CAEC;
                        color: #E290CF;
                        border-color: #ECA0DA;
                    }
                    &.prepare-ship {
                        background: #F6E4BC;
                        color: #C8B38F;
                        border-color: #E4CD9B;
                    }
                    &.shipping {
                        background: #D1E9F2;
                        color: #709DAE;
                        border-color: #A5D0DF;
                    }
                    &.shipped {
                        background: #D1F2D1;
                        color: #54AB54;
                        border-color: #8CD18C;
                    }
                    &.holded {
                        background: #E8E8E8;
                        color: #B7B7B7;
                        border-color: #D5D5D5;
                    }
                    &.canceled {
                        background: #FF9E9E;
                        color: #AB5454;
                        border-color: #F58181;
                    }
                }

            }
            &--amount {
                width: 8%;
                color: #1d7ac6;
            }
            &--payment {
                width: 8%;
                color: #bc093f;
            }
            &--status {
                width: 8%;
            }
            &--date {
                width: 10%;
            }
            &--view {
                width: 10%;
                span {
                    border: 1px solid #ccc;
                    padding: 5px 10px;
                    cursor: pointer;
                    transition: all 0.3s;
                    font-size: 13px;
                    border-radius: 3px;
                    display: inline-block;
                    &:hover {
                        opacity: 0.8;
                    }
                    a {
                        color: #555;
                    }
                }
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
                        padding: 15px 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        &.order-history__content--shipping {
                            display: block;
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 991px) {
        .order-history {
            &__content {
                &--amount,
                &--payment,
                &--status,
                &--date {
                    display: none !important;
                }
                &--no {
                    width: 25% !important;
                }
                &--name {
                    width: 40% !important;
                }
                &--shipping {
                    width: 15% !important;
                }
                &--view {
                    width: 15% !important;
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .order-history {
            padding-bottom: 30px;
            &__content {
                &--data {
                    .ar-data {
                        &:last-child {
                            p {
                                border: none;
                            }
                        }
                    }
                }
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
                &::-webkit-scrollbar{
                    height: 6px;
                    width: 6px;
                    background: #f1f1f1;
                }
                &::-webkit-scrollbar-thumb {
                    background: #ef4130;
                    border-radius: 5px;
                }
                &--data {
                    .ar-data {
                        width: 100%;
                    }
                }
                p {
                    overflow: hidden;
                    white-space: initial;
                    text-overflow: ellipsis;
                }
            }
            &__title {
                h2 {
                    font-size: 25px;
                }
                &--right {
                    ul {
                        padding-left: 0;
                    }
                }
            }
            &__search {
                margin: 10px 0;
                input {
                    width: 35%;
                }
                .between-icon {
                    width: 5%;
                    text-align: center;
                    margin: 0;
                }
                #searchSubmit {
                    width: 15%;
                    border-radius: 5px;
                    img {
                        margin: 0;
                    }
                    span.text {
                        display: none;
                    }
                }
            }
            &__filter {
                margin-bottom: 10px;
                &--button {
                    width: 100%;
                    margin: 0 0 10px;
                }
                &--items {
                    ul {
                        li {
                            margin-bottom: 5px;
                        }
                    }
                }
            }
        }
    }
</style>
