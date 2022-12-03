<template>
  <div
    v-if="orders.length > 0"
    :class="['group-order', orderType != 1 ? 'hide' : '']"
  >
    <div class="order-history__content--title">
      <div>
        <div class="d-flex align-items-center justify-content-between text-center">
          <p class="order-history__content--checkbox">
            <input
              id="checkAll"
              v-model="isCheckAll"
              type="checkbox"
              @click="checkAll(orders, 'orderId'); eventCheckAll()"
            >
          </p>
          <p class="order-history__content--no">
            {{ $t("group_order_list.orderNum") }}
          </p>
          <p class="order-history__content--name">
            {{ $t("group_order_list.purchaseHis") }}
          </p>
          <p class="order-history__content--shipping">
            {{ $t("group_order_list.shippingStatus") }}
          </p>
          <p class="order-history__content--amount">
            {{ $t("group_order_list.orderAmount") }}
          </p>
          <p class="order-history__content--payment">
            {{ $t("group_order_list.amountPay") }}
          </p>
          <p class="order-history__content--status">
            {{ $t("group_order_list.orderStatus") }}
          </p>
          <p class="order-history__content--date">
            {{ $t("group_order_list.orderDate") }}
          </p>
          <p class="order-history__content--view">
        &nbsp;
          </p>
        </div>
      </div>
    </div>
    <div class="order-history__content--data">
      <div
        v-for="(order, index) in orders"
        :key="index"
        class=""
      >
        <OrderItem
          :checkbox-ids="checkboxIds"
          :order="order"
          @toggle-check-box="toggleCheckBox"
        />
      </div>
    </div>
  </div>
</template>
<script>
    import {checkboxMixin} from '../mixins/checkboxMixin'
    import OrderItem from '../components/OrderItem'

    export default {
        components: {
            OrderItem,
        },
        mixins: [checkboxMixin],
        props: {
            orders: Array,
            orderType: Number,
            isRemoveChecked: Boolean,
        },
        data() {
            return {
                hasOrder: false
            }
        },
        computed: {
            countTotal() {
                return this.orders.length
            },
        },
        watch: {
            isRemoveChecked(n, o) {
                this.checkboxIds = []
                this.isCheckAll = false
            }
        },
        methods: {
            eventCheckAll() {
                for(let i in this.orders) {
                    // check all status
                    if (this.isCheckAll) {
                        this.$store.commit("addIdsChecked", this.orders[i].orderId)
                    } else {
                        this.$store.commit("updateIdsChecked", this.orders[i].orderId)
                    }
                }
            },
            toggleCheckBox(orderId) {
                if (this.checkboxIds.includes(orderId)) {
                    for(let i = 0; i < this.checkboxIds.length; i++) {
                        if (orderId == this.checkboxIds[i]) {
                            this.checkboxIds.splice(i, 1)
                        }
                    }
                } else {
                    this.checkboxIds.push(orderId)
                }
                if (this.checkboxIds.length == this.countTotal) {
                    this.isCheckAll = true
                } else {
                    this.isCheckAll = false
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
    .group-order.hide {
        display: none;
    }
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
            &--wrap-title {
                margin-bottom: 20px;
                border: 1px solid #ccc;
                padding: 8px 20px;
                box-shadow: rgb(0 0 0 / 35%) 0px 3px 10px;
                color: #ff0000;
                cursor: pointer;
                position: relative;
                img {
                    width: 13px;
                    transform: rotate(90deg);
                    position: absolute;
                    right: 20px;
                    top: 50%;
                    margin-top: -7px;
                }
            }
            &--checkbox {
                width: 5%;
            }
            &--no {
                width: 15%;
            }
            &--name {
                width: 30%;
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

    @media only screen and (max-width: 991px) {

    }

    @media only screen and (max-width: 767px) {
        .group-order {
            overflow-x: scroll;
        }
        .order-history__content--data,
        .order-history__content--title {
            width: 150%;
        }
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
                p.title {
                    width: 100%;
                    margin: 10px 0;
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
