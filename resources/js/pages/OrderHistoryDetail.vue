<template>
  <div class="order-detail">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("order_his_detail.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_his_detail.myPage") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_his_detail.orderInquiry") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div
        class="order-detail__title d-flex flex-wrap align-items-center justify-content-between"
      >
        <h2>{{ $t("order_his_detail.orderInquiry") }}</h2>
        <div
          class="order-detail__title--right d-md-flex flex-wrap align-items-center justify-content-end"
        >
          <p class="title">
            {{ $t("order_his_detail.sameDay") }}
          </p>
          <ul class="d-flex flex-wrap items-center">
            <li>
              <router-link to="#">
                {{ $t("order_history.preparingProduct") }} {{ n10 }}
              </router-link>
            </li>
            <li>
              <router-link to="#">
                {{ $t("order_history.preparing") }} {{ n20 }}
              </router-link>
            </li>
            <li class="on">
              <router-link to="#">
                {{ $t("order_history.shipping") }} {{ n30 }}
              </router-link>
            </li>
            <li>
              <router-link to="#">
                {{ $t("order_history.deliComp") }} {{ n40 }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>
      <div class="order-detail__content responsive-wrapper">
        <div class="responsive-content">
          <div
            class="order-detail__content--title d-flex align-items-center justify-content-between text-center"
          >
            <p class="order-detail__content--no">
              {{ $t("order_his_detail.orderNum") }}
            </p>
            <p class="order-detail__content--name">
              {{ $t("order_his_detail.purchase") }}
            </p>
            <p class="order-detail__content--amount">
              {{ $t("order_his_detail.orderAmount") }}
            </p>
            <p class="order-detail__content--payment">
              {{ $t("order_his_detail.amountPay") }}
            </p>
            <p class="order-detail__content--status">
              {{ $t("order_his_detail.orderStatus") }}
            </p>
            <p class="order-detail__content--date">
              {{ $t("order_his_detail.orderDate") }}
            </p>
          </div>
          <div class="order-detail__content--data">
            <div
              class="ar-data d-flex align-items-stretch justify-content-between text-center"
            >
              <p class="order-detail__content--no">
                {{ order.orderNumber }}
              </p>
              <p class="order-detail__content--name">
                {{ getProductNames }}
              </p>
              <p class="order-detail__content--amount">
                {{ getQuantity }}
              </p>
              <p class="order-detail__content--payment">
                {{ $format.price(order.paymentAmount) }}
              </p>
              <p class="order-detail__content--status">
                {{ statusText(order.paymentStatus) }}
              </p>
              <p class="order-detail__content--date">
                {{ $format.timeToStr(order.orderDate, '-') }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-9">
          <div class="order-detail__product">
            <div class="box-detail">
              <div
                class="box-detail__title d-flex flex-wrap align-items-center justify-content-between"
              >
                <p
                  class="box-detail__title--left d-flex flex-wrap align-items-center"
                >
                  <span class="img"><img
                    src="/img/icon/icon_de_detail.svg"
                    alt=""
                  ></span>
                  <span class="text">{{ order.shippingMethod }}</span>
                </p>
                <p
                  class="box-detail__title--right d-flex flex-wrap align-items-center"
                >
                  <span>{{ $t("order_his_detail.orderNum") }} {{ order.orderNumber }}</span>
                </p>
              </div>
              <div class="box-detail__ar responsive-wrapper">
                <div
                  v-for="(products, index) in groupByProductTp"
                  :key="index"
                  class="box-detail__type responsive-content"
                >
                  <ProductGroupOrderItem
                    :products="products"
                    :product-tp="parseInt(index)"
                    @toggle-checkbox-id="toggleCheckboxId"
                    @get-all-checkbox-ids="getAllCheckboxIds"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="order-detail__infomation">
            <div class="row">
              <div class="col-lg-6">
                <p class="order-detail__infomation--title">
                  {{ $t("order_his_detail.orderInfo") }}
                </p>
                <div class="order-detail__infomation--box">
                  <ul
                    class="d-flex flex-wrap align-items-center justify-content-between"
                  >
                    <li>{{ $t("order_his_detail.toOrder") }} {{ order.buyerName }}</li>
                    <li>{{ $t("order_his_detail.orderDate") }} : {{ $format.timeToStr(order.orderDate, '-') }}</li>
                    <li>{{ $t("order_his_detail.phoneNum") }} {{ order.buyerPhoneNo }}</li>
                    <li>{{ $t("order_his_detail.cellPhone") }} {{ order.buyerMobileNo }}</li>
                  </ul>
                </div>
                <p class="order-detail__infomation--title mt-2 mb-1">
                  {{ $t("order_his_detail.tracking") }}
                </p>
                <div class="order-detail__table">
                  <div class="order-detail__table--box">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ $t("order_his_detail.tracking") }}</th>
                          <th>{{ $t("order_his_detail.date") }}</th>
                          <th>{{ $t("order_his_detail.detail") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, index) in productsByTracking"
                          :key="index"
                        >
                          <td>{{ index }}</td>
                          <td>{{ $format.timeToStr(order.orderDate, '-') }}</td>
                          <td>
                            <a
                              href=""
                              @click="showTrackingDetail($event, item)"
                            >{{ $t("order_his_detail.readMore") }}</a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <p class="order-detail__infomation--title">
                  {{ $t("order_his_detail.shipping") }}
                </p>
                <div class="order-detail__infomation--box">
                  <ul
                    class="d-flex flex-wrap align-items-center justify-content-between"
                  >
                    <li>{{ $t("order_his_detail.receiver") }} {{ order.receiverName }}</li>
                    <li>
                      <span class="title">{{ $t("order_his_detail.addr") }} </span>
                      <span class="data">{{ order.receiveAddress1 }}<br>{{ order.receiveAddress2 }}</span>
                    </li>
                    <li>{{ $t("order_his_detail.phoneNum") }} {{ order.receiverPhoneNo }}</li>
                    <li>{{ $t("order_his_detail.cellPhone") }} {{ order.receiverMobileNo }}</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="order-detail__right">
            <div class="order-detail__right--button d-flex align-items-center justify-content-between flex-wrap">
              <button
                class="btn-right"
                @click="cancelRequestOrder"
              >
                {{ $t("order_his_detail.withDraw") }}
              </button>
              <!-- <button class="btn-right">{{$t("order_his_detail.holdOrder")}}</button> -->
              <button
                class="btn-right print-bill"
                @click="popupOrderBill"
              >
                <img
                  src="/img/icon/print.svg"
                  alt=""
                > receipt
              </button>
            </div>
            <div class="order-detail__right--box-price">
              <div
                v-if="order.shippingMethod == 'Center Pickup'"
                class="ar-box"
              >
                <p class="title">
                  {{ $t("order_his_detail.storePick") }}
                </p>
                <dl>
                  <dt>{{ $t("order_his_detail.indust") }}</dt>
                  <dd><span>{{ $format.price(getPriceIndustrial) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.fresh") }}</dt>
                  <dd><span>{{ $format.price(getPriceMiscellaneous) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.main") }}</dt>
                  <dd><span>{{ $format.price(getPriceAlcoholic) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.indust") }}</dt>
                  <dd class="ar-box--red">
                    <span>{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + getPriceAlcoholic) }}</span> {{ $t("order_his_detail.won") }}
                  </dd>
                </dl>
              </div>
              <div
                v-else
                class="ar-box"
              >
                <p class="title">
                  {{ $t("order_his_detail.direct") }}
                </p>
                <dl>
                  <dt>{{ $t("order_his_detail.indust") }}</dt>
                  <dd><span>{{ $format.price(getPriceIndustrial) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.fresh") }}</dt>
                  <dd><span>{{ $format.price(getPriceMiscellaneous) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.main") }}</dt>
                  <dd><span>{{ $format.price(getPriceAlcoholic) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_his_detail.shipFee") }}</dt>
                  <dd><span>{{ $format.price(order.shippingFee) }}</span> {{ $t("order_his_detail.won") }}</dd>
                </dl>
                <!-- <dl>
                                    <dt>배송할인</dt>
                                    <dd>
                                        <span>- 6,000</span> {{$t("order_his_detail.won")}}
                                        <span class="note">50만{{$t("order_his_detail.won")}} 이상 배송비 할인</span>
                                    </dd>
                                </dl> -->
                <dl>
                  <dt>{{ $t("order_his_detail.subtotal") }}</dt>
                  <dd class="ar-box--red">
                    <span>{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + getPriceAlcoholic + order.shippingFee) }}</span> {{ $t("order_his_detail.won") }}
                  </dd>
                </dl>
              </div>
              <div class="ar-box">
                <p class="title">
                  {{ $t("order_his_detail.amountOf") }}
                </p>
                <p class="total-price">
                  <span>{{ $format.price(getPriceIndustrial + getPriceMiscellaneous + getPriceAlcoholic + order.shippingFee) }}</span> {{ $t("order_his_detail.won") }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="order-detail__table">
        <!-- <div class="order-detail__table--box">
                    <table>
                        <thead>
                            <tr>
                                <th>번호</th>
                                <th>분류</th>
                                <th>주문 문의</th>
                                <th>답변</th>
                                <th>입력일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in 1" :key="index">
                                <td>{{index + 1}}</td>
                                <td>1:1문의</td>
                                <td>{{ getProductNames }}</td>
                                <td>답변</td>
                                <td>{{ $format.timeToStr(order.orderDate, '-') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div> -->
        <div class="order-detail__button">
          <div class="row">
            <div class="col-lg-6 col-4 d-flex flex-wrap justify-content-lg-end">
              <div class="order-detail__btn">
                <router-link
                  :to="{
                    path: '/order-history',
                    query: {
                      fromDate: getConditionsSearchOrder.fromDate,
                      endDate: getConditionsSearchOrder.endDate,
                    }
                  }"
                >
                  {{ $t("order_his_detail.orderList") }}
                </router-link>
              </div>
            </div>
            <!-- <div class="col-lg-6 col-8 d-flex flex-wrap justify-content-end">
                            <button class="order-detail__btn last hard-code">1:1문의</button>
                            <button class="order-detail__btn  last hard-code">취소 반품</button>
                        </div> -->
          </div>
        </div>
      </div>
    </div>
    <PopupOrderItem ref="PopupOrderItem" />
    <PopupOrderBill ref="PopupOrderBill" />
    <ConfirmDialogue ref="ConfirmDialogue" />
    <LoadingPage
      v-if="loading"
    />
  </div>
</template>

<script>
    import LoadingPage from '../components/LoadingPage'
    import { groupByKeyMixin } from '../mixins/groupByKeyMixin'
    import { getPriceByTypeMixin } from '../mixins/getPriceByTypeMixin'
    import { statusTextMixin } from '../mixins/statusTextMixin'
    import PopupOrderItem from '../components/PopupOrderItem'
    import PopupOrderBill from '../components/PopupOrderBill'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    import ProductGroupOrderItem from '../components/ProductGroupOrderItem'
    import { mapGetters } from "vuex"

    export default {
        components: {
            LoadingPage,
            PopupOrderItem,
            ConfirmDialogue,
            ProductGroupOrderItem,
            PopupOrderBill,
        },
        mixins: [


            groupByKeyMixin,
            getPriceByTypeMixin,
            statusTextMixin,
        ],
        data() {
            return {
                checkedOrderIds: [],
                order: {},
                centerInfo: {},
                loading: true,
                orderId: '',
                n10: 0,
                n20: 0,
                n30: 0,
                n40: 0,
                activeStatus: '',
                productsByTracking: [],
                isRemoveChecked: false,
            }
        },
        computed: {
            ...mapGetters(['getConditionsSearchOrder', 'getStoreSettings']),
            groupByProductTp () {
                if (this.order.products != null) {
                    return this.groupByKey(this.order.products, 'productTp')
                }
                return []
            },
            getProductNames() {
                if (this.order.products != null) {
                    if (this.order.products.length == 1) {
                        return this.order.products[0].productName
                    } else {
                        let numProducts = this.order.products.length - 1
                        return this.order.products[0].productName + '...(more ' + numProducts + ')'
                    }
                }
                return ''
            },
            getQuantity() {
                if (this.order.products != null) {
                    let quantity = 0
                    for (const index in this.order.products) {
                        quantity += this.order.products[index].quantity
                    }
                    // set freeship if cancel all
                    if (quantity == 0) {
                        this.order.shippingFee = 0
                    }
                    return quantity
                }
                return 0
            },
            getPriceIndustrial() {
                return this.getPriceByType(1)
            },
            getPriceMiscellaneous() {
                return this.getPriceByType(2)
            },
            getPriceAlcoholic() {
                return this.getPriceByType(3)
            },
        },
        created() {
            this.getOrderDetail()
            this.getCenterInfo()
        },
        methods: {
            getOrderDetail() {
                this.orderId = this.$route.params.id;
                axios.get(apiURL + "/order/detail/" + this.orderId)
                .then(response => {
                    this.order = response.data.data
                    this.loading = false
                    console.log(this.order)
                    // count status
                    for (const i in this.order.products) {
                        if (this.order.products[i].statusId == "N10") {
                            this.n10++
                        }
                        if (this.order.products[i].statusId == "N20") {
                            this.n20++
                        }
                        if (this.order.products[i].statusId == "N30") {
                            this.n30++
                        }
                        if (this.order.products[i].statusId == "N40") {
                            this.n40++
                        }
                    }
                    // groupby tracking no
                    this.productsByTracking = this.groupByKey(this.order.products, 'trackingNo')
                }).catch(error => {
                    console.log(error)
                });
            },
            getCenterInfo() {
                axios.get(apiURL + "/core/center-info")
                    .then(response => {
                        this.centerInfo = response.data.data
                    }).catch(error => {
                        console.log(error)
                    });
            },
            async cancelRequestOrder() {
                if (this.checkedOrderIds.length == 0) {
                    await this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.chooseOrderItem'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                } else {
                    // check status possible cancel order
                    let flag = 0
                    for (const index in this.checkedOrderIds) {
                        for (const i in this.order.products) {
                            // N10 => posible cancel order
                            if (this.order.products[i].orderItemCode == this.checkedOrderIds[index] && this.order.products[i].statusId != "N10") {
                                flag = 1
                            }
                        }
                    }
                    if (flag == 1) {
                        this.$refs.ConfirmDialogue.show({
                            title: this.$t('modal.titleConfirm'),
                            message: this.$t('modal.cannotCancelOrder'),
                            cancelButton: this.$t('modal.btnOk'),
                            isDialog: true,
                        })
                        return
                    }

                    const confirm = await this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleConfirm'),
                        message: this.$t('modal.confirmDelete'),
                        okButton: this.$t('modal.btnYes'),
                        cancelButton: this.$t('modal.btnCancel'),
                    })
                    // If you throw an error, the method will terminate here unless you surround it wil try/catch
                    if (confirm) {
                        let listItems = [];
                        for (const index in this.checkedOrderIds) {
                            for (const i in this.order.products) {
                                if (this.order.products[i].orderItemCode == this.checkedOrderIds[index]) {
                                    listItems.push({
                                        order_item_code: this.checkedOrderIds[index],
                                        quantity: this.order.products[i].quantity < 0 ? 0 : this.order.products[i].quantity,
                                    })
                                }
                            }
                        }
                        // prepare request data
                        const requestData = {
                            "order_id": this.order.orderId,
                            "status" : "canceled",
                            "reason": "Do not like",
                            "items": listItems
                        }
                        // send request data
                        axios.post(apiURL + '/order/cancel', requestData)
                            .then(res => {
                                console.log(res)
                                this.$router.go()
                            })
                    }
                }
            },
            getPriceByType(type = 1) {
                if (this.order.products != null) {
                    let price1 = 0
                    let price2 = 0
                    let price3 = 0
                    for (const index in this.order.products) {
                        if (this.order.products[index].productTp == 1) {
                            price1 = price1 + (this.order.products[index].unitPrice * this.order.products[index].quantity)
                        } else if (this.order.products[index].productTp == 2) {
                            price2 = price2 + (this.order.products[index].unitPrice * this.order.products[index].quantity)
                        } else if (this.order.products[index].productTp == 3) {
                            price3 = price3 + (this.order.products[index].unitPrice * this.order.products[index].quantity)
                        }
                    }
                    if (type == 1) {
                        return price1
                    } else if (type == 2) {
                        return price2
                    } else if (type == 3) {
                        return price3
                    }
                }
            },
            async showTrackingDetail(event, listProduct) {
                event.preventDefault();
                let groupByProductTp = this.groupByKey(listProduct, 'productTp')
                await this.$refs.PopupOrderItem.show({
                    cancelButton: this.$t('modal.btnClose'),
                    isDialog: true,
                    hasProducts: true,
                    groupByProductTp: groupByProductTp,
                })
            },
            getAllCheckboxIds(allId, mixinIds) {
                if (this.checkedOrderIds.length == 0) {
                    for(let j in allId) {
                        this.checkedOrderIds.push(allId[j])
                    }
                } else {
                    if (mixinIds.length == 0) {
                        for(let i = 0; i < allId.length; i++) {
                            this.checkedOrderIds.splice(this.checkedOrderIds.indexOf(allId[i]), 1)
                        }
                    } else {
                        for(let j in allId) {
                            this.checkedOrderIds.push(allId[j])
                        }
                    }
                }
            },
            toggleCheckboxId(id) {
                if (this.checkedOrderIds.includes(id)) {
                    for(let i = 0; i < this.checkedOrderIds.length; i++) {
                        if (id == this.checkedOrderIds[i]) {
                            this.checkedOrderIds.splice(i, 1)
                        }
                    }
                } else {
                    this.checkedOrderIds.push(id)
                }
            },
            async popupOrderBill() {
                await this.$refs.PopupOrderBill.show({
                    cancelButton: this.$t('modal.btnClose'),
                    order: this.order,
                    groupByProductTp: this.groupByProductTp,
                    getStoreSettings: this.getStoreSettings,
                    centerInfo: this.centerInfo,
                    getPriceIndustrial: this.getPriceIndustrial,
                    getPriceMiscellaneous: this.getPriceMiscellaneous,
                    getPriceAlcoholic: this.getPriceAlcoholic,
                })
            }
        },
    }
</script>

<style lang="scss" scoped>
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
                width: 48%;
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
                    width: calc(100% - (80px + 20px));
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
        .responsive-wrapper {
            overflow-x: scroll;
            .responsive-content {
                width: 120%;
                &.box-detail__type {
                    width: 150%;
                }
            }
        }
        &__right {
            &--button {
                button {
                    font-size: 13px;
                    margin-bottom: 5px;
                    padding: 10px 6px !important;
                }
            }
            &--box-price {
                padding: 15px 10px;
                .ar-box dl {
                    dt {
                        font-size: 13px;
                        width: 35%;
                    }
                    dd {
                        width: 65%;
                        span {
                            font-size: 15px;
                        }
                    }
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
    }
}

@media only screen and (max-width: 767px) {
    .order-detail {
        .responsive-wrapper {
            .responsive-content {
                &.box-detail__type {
                    width: 200%;
                }
            }
        }
        &__btn {
            padding: 5px 5px;
            width: 100%;
            text-align: center;
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
                        padding: 3px 8px;
                        font-size: 3.5vw;
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
            p {
                overflow: hidden;
                white-space: initial;
                text-overflow: ellipsis;
            }
            &--name {
                width: 40%;
            }
            &--option {
                width: 15%;
            }
            &--water {
                width: 15%;
            }
            &--price {
                width: 15%;
            }
            &--quantity {
                width: 10%;
            }
            &--total {
                width: 15%;
            }
            &--status {
                width: 10%;
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
