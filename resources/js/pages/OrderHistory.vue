<template>
  <div class="order-history">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("order_history.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_history.myPage") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_history.orderInqui") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="order-history__title d-flex flex-wrap align-items-center justify-content-between">
        <h2>{{ $t("order_history.orderInqui") }}</h2>
        <div class="order-history__title--right d-flex flex-wrap align-items-center justify-content-end">
          <p class="title">
            {{ $t("order_history.deliStatus") }}
          </p>
          <ul class="d-flex flex-wrap items-center">
            <li>
              <router-link to="#">
                {{ $t("order_history.preparingProduct") }} {{ getNumberStatus.n10 }}
              </router-link>
            </li>
            <li>
              <router-link to="#">
                {{ $t("order_history.preparing") }} {{ getNumberStatus.n20 }}
              </router-link>
            </li>
            <li class="on">
              <router-link to="#">
                {{ $t("order_history.shipping") }} {{ getNumberStatus.n30 }}
              </router-link>
            </li>
            <li>
              <router-link to="#">
                {{ $t("order_history.deliComp") }} {{ getNumberStatus.n40 }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>
      <div class="order-history__search">
        <form
          id="search-order"
          action=""
          @submit.prevent="searchOrders(1)"
        >
          <div class="d-lg-flex align-items-start justify-content-between">
            <div class="order-history__filter mb-2">
              <div class="d-flex flex-wrap align-items-center">
                <div class="order-history__filter--button">
                  <ul class="d-flex align-items-center hidden-xs">
                    <li @click="cancelRequestOrders">
                      {{ $t("order_history.withDraw") }}
                    </li>
                    <!-- <li class="hard-code">{{$t("order_history.orderHold")}}</li> -->
                  </ul>
                </div>
                <div class="order-history__filter--items">
                  <ul class="d-flex align-items-center flex-wrap">
                    <li
                      :class="daySearch == 0 ? 'actived' : ''"
                      @click="updateDate(0)"
                    >
                      {{ $t("order_history.today") }}
                    </li>
                    <li
                      :class="daySearch == 3 ? 'actived' : ''"
                      @click="updateDate(3)"
                    >
                      {{ $t("order_history.threeDay") }}
                    </li>
                    <li
                      :class="daySearch == 7 ? 'actived' : ''"
                      @click="updateDate(7)"
                    >
                      {{ $t("order_history.sevenDay") }}
                    </li>
                    <li
                      :class="daySearch == 30 ? 'actived' : ''"
                      @click="updateDate(30)"
                    >
                      {{ $t("order_history.oneMonth") }}
                    </li>
                    <li
                      :class="daySearch == 90 ? 'actived' : ''"
                      @click="updateDate(90)"
                    >
                      {{ $t("order_history.threeMonth") }}
                    </li>
                    <li
                      :class="daySearch == 180 ? 'actived' : ''"
                      @click="updateDate(180)"
                    >
                      {{ $t("order_history.sixMonth") }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-4">
              <input
                id="fromDate"
                v-model="fromDate"
                type="date"
              >
              <p class="between-icon">
                ~
              </p>
              <input
                id="endDate"
                v-model="endDate"
                type="date"
              >
              <button
                id="searchSubmit"
                type="submit"
              >
                <img
                  src="/img/header/search.svg"
                  alt=""
                > <span class="text">{{ $t("order_history.search") }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="order-history__content">
        <div
          :class="'order-history__content--wrap-title tab-title actived'"
          @click="toggleDeliveryContent"
        >
          AOS <span class="icon-down"><img
            src="/img/icon/arrow_right_thin.svg"
            alt=""
          ></span>
        </div>
        <GroupOrderList
          :is-remove-checked="isRemoveChecked"
          :orders="groupAOS"
          :order-type="1"
        />

        <div
          :class="'order-history__content--wrap-title tab-title'"
          @click="toggleDeliveryContent"
        >
          {{ $t("order_history.regularDeli") }} <span class="icon-down"><img
            src="/img/icon/arrow_right_thin.svg"
            alt=""
          ></span>
        </div>
        <GroupOrderList
          :is-remove-checked="isRemoveChecked"
          :orders="groupRegular"
          :order-type="2"
        />

        <div
          :class="'order-history__content--wrap-title tab-title'"
          @click="toggleDeliveryContent"
        >
          {{ $t("order_history.sameDay") }} <span class="icon-down"><img
            src="/img/icon/arrow_right_thin.svg"
            alt=""
          ></span>
        </div>
        <GroupOrderList
          :is-remove-checked="isRemoveChecked"
          :orders="groupSameDay"
          :order-type="5"
        />

        <div
          :class="'order-history__content--wrap-title tab-title'"
          @click="toggleDeliveryContent"
        >
          {{ $t("order_history.excelUpload") }} <span class="icon-down"><img
            src="/img/icon/arrow_right_thin.svg"
            alt=""
          ></span>
        </div>
        <GroupOrderList
          :is-remove-checked="isRemoveChecked"
          :orders="groupExcel"
          :order-type="3"
        />

        <div
          :class="'order-history__content--wrap-title tab-title'"
          @click="toggleDeliveryContent"
        >
          {{ $t("order_history.phoneOrder") }} <span class="icon-down"><img
            src="/img/icon/arrow_right_thin.svg"
            alt=""
          ></span>
        </div>
        <GroupOrderList
          :is-remove-checked="isRemoveChecked"
          :orders="groupByPhone"
          :order-type="4"
        />
      </div>
    </div>
    <ConfirmDialogue ref="ConfirmDialogue" />
    <LoadingPage v-if="loading" />
  </div>
</template>

<script>
    import LoadingPage from '../components/LoadingPage'
    import GroupOrderList from '../components/GroupOrderList'
    import moment from 'moment-timezone'
    import ConfirmDialogue from '../components/ConfirmDialogue'

    export default {
        components: {
            LoadingPage,
            GroupOrderList,
            ConfirmDialogue,
        },
        data() {
            return {
                fromDate: null,
                endDate: null,
                orders: [],
                loading: false,
                groupAOS: [],
                groupRegular: [],
                groupExcel: [],
                groupByPhone: [],
                groupSameDay: [],
                moment: null,
                isRemoveChecked: false,
            }
        },
        computed: {
            daySearch() {
                if (this.fromDate == null || this.endDate == null) {
                    return 0
                }
                let from = moment(this.fromDate, "YYYY-MM-DD")
                let to = moment(this.endDate, "YYYY-MM-DD")
                return moment.duration(to.diff(from)).asDays();
            },
            getIdsChecked() {
                return this.$store.state.idsChecked;
            },
            getNumberStatus() {
                let result = {
                    n10: 0,
                    n20: 0,
                    n30: 0,
                    n40: 0,
                }
                let orderCheck = []
                if (this.groupAOS.length > 0) {
                    for (const key in this.groupAOS) {
                        if (!orderCheck.includes(this.groupAOS[key].orderId)) {
                            result.n10 += this.groupAOS[key].n10
                            result.n20 += this.groupAOS[key].n20
                            result.n30 += this.groupAOS[key].n30
                            result.n40 += this.groupAOS[key].n40
                            orderCheck.push(this.groupAOS[key].orderId)
                        }
                    }
                }
                if (this.groupRegular.length > 0) {
                    for (const key in this.groupRegular) {
                        if (!orderCheck.includes(this.groupRegular[key].orderId)) {
                            result.n10 += this.groupRegular[key].n10
                            result.n20 += this.groupRegular[key].n20
                            result.n30 += this.groupRegular[key].n30
                            result.n40 += this.groupRegular[key].n40
                            orderCheck.push(this.groupRegular[key].orderId)
                        }
                    }
                }
                if (this.groupExcel.length > 0) {
                    for (const key in this.groupExcel) {
                        if (!orderCheck.includes(this.groupExcel[key].orderId)) {
                            result.n10 += this.groupExcel[key].n10
                            result.n20 += this.groupExcel[key].n20
                            result.n30 += this.groupExcel[key].n30
                            result.n40 += this.groupExcel[key].n40
                            orderCheck.push(this.groupExcel[key].orderId)
                        }
                    }
                }
                if (this.groupByPhone.length > 0) {
                    for (const key in this.groupByPhone) {
                        if (!orderCheck.includes(this.groupByPhone[key].orderId)) {
                            result.n10 += this.groupByPhone[key].n10
                            result.n20 += this.groupByPhone[key].n20
                            result.n30 += this.groupByPhone[key].n30
                            result.n40 += this.groupByPhone[key].n40
                            orderCheck.push(this.groupByPhone[key].orderId)
                        }
                    }
                }
                return result
            }
        },
        created() {
            this.fromDate = this.$route.query.fromDate
            this.endDate = this.$route.query.endDate
        },
        mounted() {
            this.searchOrders(1)
            // reset checked item
            this.$store.state.idsChecked = []
        },
        methods: {
            updateDate(day) {
                this.endDate = moment().tz('Asia/Seoul').format('YYYY-MM-DD')
                this.fromDate = moment().tz('Asia/Seoul').subtract(day, 'days').format('YYYY-MM-DD')
            },
            searchOrders(type) {
                this.loading = true
                let params = {
                    fromDate: this.fromDate,
                    endDate: this.endDate,
                };
                this.$store.state.conditionsSearchOrder = params
                this.$router.push({ name: "OrderHistory", query: params })
                if (type == 1) {
                    params.mallCodes = ["AOS"]
                } else if (type == 2) {
                    params.orderType = '2043.3'
                } else if (type == 3) {
                    params.orderType = '2043.2'
                } else if (type == 4) {
                    params.orderType = '2043.4'
                } else if (type == 5) {
                    params.shippingMethod = '3'
                }
                axios.get(apiURL + "/order/list", { params: params })
                .then(response => {
                    if (type == 1) {
                        this.groupAOS = response.data.data
                        this.searchOrders(2)
                        this.searchOrders(3)
                        this.searchOrders(4)
                        this.searchOrders(5)
                    } else if (type == 2) {
                        this.groupRegular = response.data.data
                    } else if (type == 3) {
                        this.groupExcel = response.data.data
                    } else if (type == 4) {
                        this.groupByPhone = response.data.data
                    } else if (type == 5) {
                        this.groupSameDay = response.data.data
                    }
                    this.orders.push(...response.data.data)
                    this.loading = false
                }).catch(error => {
                    console.log(error)
                });
            },
            toggleDeliveryContent(e) {
                e.stopPropagation();
                let _this = $(e.target);
                _this.next(".group-order").slideToggle();
                _this.toggleClass("actived");
            },
            async cancelRequestOrders() {
                if (this.getIdsChecked.length == 0) {
                    await this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.chooseOrderItem'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                } else {
                    // check possible cancel
                    let flag = 0
                    for (const i in this.orders) {
                        for (const j in this.getIdsChecked) {
                            if (this.orders[i].orderId == this.getIdsChecked[j]) {
                                // check has status n20, n30, n40
                                if (this.orders[i].n20 > 0 || this.orders[i].n30 > 0 || this.orders[i].n40 > 0) {
                                    flag = 1
                                }
                            }
                        }
                    }
                    if (flag == 1) {
                        // can't cancel
                        await this.$refs.ConfirmDialogue.show({
                            title: this.$t('modal.titleDialog'),
                            message: this.$t('modal.cannotCancelOrder'),
                            cancelButton: this.$t('modal.btnOk'),
                            isDialog: true,
                        })
                    } else {
                        const confirm = await this.$refs.ConfirmDialogue.show({
                            title: this.$t('modal.titleConfirm'),
                            message: this.$t('modal.confirmDelete'),
                            okButton: this.$t('modal.btnYes'),
                            cancelButton: this.$t('modal.btnCancel'),
                        })
                        if (confirm) {
                            // call api cancel orders by ids
                            let requestData = {
                                orderIds: this.getIdsChecked
                            }
                            // reset checked item
                            this.isRemoveChecked = !this.isRemoveChecked
                            this.$store.state.idsChecked = []
                            axios.post(apiURL + '/order/cancel-by-ids', requestData)
                                .then(res => {
                                    console.log(res)
                                    this.$router.go()
                                })
                        }
                    }
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
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
            margin: 20px 0 10px;
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
                        &.actived {
                            border-color: #ff0000;
                            color: #ff0000;
                        }
                    }
                }
            }
        }
        &__content {
            &--wrap-title {
                margin-bottom: 20px;
                border: 1px solid #e2e2e2;
                padding: 8px 20px;
                cursor: pointer;
                position: relative;
                font-weight: bold;
                img {
                    width: 13px;
                    transform: rotate(90deg);
                    position: absolute;
                    right: 20px;
                    top: 50%;
                    margin-top: -7px;
                }
                &.actived {
                    background: #f5f5f5;
                    color: #ff0000;
                    img {
                        transform: rotate(-90deg);
                    }
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
                width: 10%;
                color: #1d7ac6;
            }
            &--payment {
                width: 10%;
                color: #bc093f;
            }
            &--status {
                width: 10%;
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

    @media only screen and (max-width: 767px) {
        .order-history {
            padding-bottom: 30px;
            .hidden-xs {
                display: none !important;
            }
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
                    font-size: 5vw;
                    margin-bottom: 5px;
                }
                p.title {
                    margin: 0;
                    margin-right: 10px;
                }
                &--right {
                    ul {
                        padding-left: 0;
                        li {
                            margin: 0 5px 0px 0;
                            font-size: 2.8vw;
                            padding: 3px 8px;
                        }
                    }
                }
            }
            &__search {
                margin: 5px 0px 10px 0;
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
                            font-size: 3.5vw;
                            padding: 3px 10px;
                        }
                    }
                }
            }
        }
    }
</style>
