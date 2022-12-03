<template>
  <section id="right-bar">
    <div class="rightbar-content">
      <div
        v-if="getOrderType == 'sameDay' || getOrderType == null"
        class="upperPart"
      >
        <div class="title">
          <p>{{ $t("cart.sameDay") }}</p>
          <p
            v-show="$route.name != 'OrderForm'"
            class="buy"
            @click="checkoutByType('sameDay')"
          >
            {{ $t("rightbar_old.purchase") }}
          </p>
        </div>
        <div class="content">
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.goods") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceSameDay.industrial) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.new") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceSameDay.miscellaneous) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.main") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceSameDay.alcoholic) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.shipfree") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(shippingFeeSameDay.normal + shippingFeeSameDay.alcohol) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.subtotal") }}
            </div>
            <div class="item-content">
              <span class="amount total">{{ $format.price(priceSameDay.all + shippingFeeSameDay.normal + shippingFeeSameDay.alcohol) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="getOrderType == 'pickup' || getOrderType == null"
        class="upperPart"
      >
        <div class="title">
          <p>{{ $t("rightbar_old.pickup") }}</p>
          <p
            v-show="$route.name != 'OrderForm'"
            class="buy"
            @click="checkoutByType('pickup')"
          >
            {{ $t("rightbar_old.purchase") }}
          </p>
        </div>
        <div class="content">
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.goods") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(pricePickUpCenter.industrial) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.new") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(pricePickUpCenter.miscellaneous) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.main") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(pricePickUpCenter.alcoholic) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.subtotal") }}
            </div>
            <div class="item-content">
              <span class="amount total">{{ $format.price(pricePickUpCenter.all) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="getOrderType == 'delivery' || getOrderType == null"
        class="lowerPart"
      >
        <div class="title">
          <p>{{ $t("rightbar_old.direct") }}</p>
          <p
            v-show="$route.name != 'OrderForm'"
            class="buy"
            @click="checkoutByType('delivery')"
          >
            {{ $t("rightbar_old.purchase") }}
          </p>
        </div>
        <div class="content">
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.goods") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceDelivery.industrial) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.new") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceDelivery.miscellaneous) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.main") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(priceDelivery.alcoholic) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-label">
              {{ $t("rightbar_old.shipfree") }}
            </div>
            <div class="item-content">
              <span class="amount">{{ $format.price(shippingFeeDelivery.normal + shippingFeeDelivery.alcohol) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
          <div class="item">
            <div class="item-wrap">
              <div class="item-label">
                {{ $t("rightbar_old.shipdisc") }}
              </div>
              <div class="item-content">
                <span class="amount total">{{ $format.price(discountShippingFee) }}</span>
                <span class="note">{{ $t("rightbar_old.won") }}</span>
              </div>
            </div>
            <span class="deliveryMsg">{{ $t("rightbar_old.discount") }}</span>
          </div>
          <div class="item noBorder">
            <div class="item-label">
              {{ $t("rightbar_old.subtotal") }}
            </div>
            <div class="item-content">
              <span class="amount total">{{ $format.price(priceDelivery.all + shippingFeeDelivery.normal + shippingFeeDelivery.alcohol) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="lastPart">
        <p class="title">
          {{ $t("rightbar_old.total") }}
        </p>
        <div class="content">
          <div class="item-wrap noBorder">
            <div class="item-content">
              <span class="amount total">{{ $format.price(totalPrice) }}</span>
              <span class="note">{{ $t("rightbar_old.won") }}</span>
            </div>
            <div class="moneyInText">
              <p class="text">
                {{ $t("rightbar_old.amount") }} <span> {{ $format.price(creditAmount) }}</span> {{ $t("rightbar_old.won") }}
              </p>
            </div>
          </div>
        </div>
        <div class="funcBtn">
          <!-- <button type="button" @click="showPopupPayment" class="btn btn-block btn-primary">
            {{$t("rightbar_old.charge")}}
          </button> -->
          <div v-if="currentPage == 'OrderForm'">
            <div class="checkbox-balance mb-1 d-flex justify-content-center align-items-center">
              <label>{{ $t('rightbar_old.useDeposit') }}</label>
              <div class="balance-amount">
                <input
                  v-model="balance"
                  type="text"
                  class=""
                  :readonly="useBalance"
                  @keyup="changeBalance"
                >
                <span class="won">{{ $t("rightbar_old.won") }}</span>
              </div>
              <button
                v-if="!useBalance"
                type="button"
                class="btn-use"
                @click="calculate"
              >
                {{ $t('rightbar_old.apply') }}
              </button>
              <button
                v-if="useBalance"
                type="button"
                class="btn-use not-use"
                @click="cancelCalculate"
              >
                {{ $t('rightbar_old.cancel') }}
              </button>
            </div>
            <div class="deposit-calculate text-end mb-2">
              ( {{ $t('rightbar_old.calculatedDeposit') }} <span>{{ $format.price(depsitCalculate) }}</span> {{ $t('rightbar_old.one') }} )
            </div>
            <div class="order-calculate text-end mb-3">
              <p class="text">
                <span> {{ $format.price(totalPriceCalculate) }}</span> {{ $t("rightbar_old.won") }}
              </p>
            </div>
          </div>
          <button
            type="submit"
            class="btn btn-block btn-danger"
            @click="orderFormSubmit"
          >
            {{ $t("rightbar_old.place") }}
          </button>
        </div>
      </div>
    </div>
  </section>
  <PopupPayment ref="PopupPayment" />
  <ConfirmDialogue ref="ConfirmDialogue" />
</template>

<script>
  import ConfirmDialogue from '../components/ConfirmDialogue'
  import { calculatePriceMixin } from '../mixins/calculatePriceMixin'
  import PopupPayment from '../components/PopupPayment'
  import { mapGetters } from "vuex"
  export default {
    components: {
      ConfirmDialogue,
      PopupPayment,
    },
    mixins: [

      calculatePriceMixin,
    ],
    props: {
      page: {
        type: String,
        default: ''
      },
    },
    emits: ['totalOrder'],
    data() {
      return {
        creditAmount: null,
        balance: 0,
        useBalance: false,
        canOrder: true,
      }
    },
    computed: {
      // all products in cart
      ...mapGetters(['getProducts', 'getMarginRates', 'getStoreSettings', 'getStoreInfos']),
      currentPage() {
        return this.$route.name
      },
      getOrderType() {
        return this.$route.query.type
      },
      productsInCart() {
          return this.getProducts.data;
      },
      productsDelivery() {
        if (this.productsInCart != null) {
          return this.productsInCart.filter( (obj) => {
            return obj.shipping_method == 2
          })
        }
        return []
      },
      productsCenterPickup() {
        if (this.productsInCart != null) {
          return this.productsInCart.filter( (obj) => {
            return obj.shipping_method == 1
          })
        }
        return []
      },
      productsSameDay() {
        if (this.productsInCart != null) {
          return this.productsInCart.filter( (obj) => {
            return obj.shipping_method == 3
          })
        }
        return []
      },
      //group by product follow Shipping Method
      groupByShippingMethod() {
        if (this.productsInCart != null) {
          return this.groupByObject(this.productsInCart, "shipping_method");
        }
        return []
      },
      // price same day
      priceSameDay() {
        let objectPrice = {}
        if (this.groupByShippingMethod != null) {
          objectPrice = {
            industrial: this.caculationPrice(this.groupByShippingMethod, 3, 1),
            miscellaneous: this.caculationPrice(this.groupByShippingMethod, 3, 2),
            alcoholic: this.caculationPrice(this.groupByShippingMethod, 3, 3),
          }
          objectPrice.subtotal = objectPrice.industrial + objectPrice.miscellaneous
          objectPrice.all = objectPrice.industrial + objectPrice.miscellaneous + objectPrice.alcoholic
        }
        return objectPrice
      },
      //price Pick Up Center
      pricePickUpCenter() {
        let objectPrice = {}
        if (this.groupByShippingMethod != null) {
          objectPrice = {
            industrial: this.caculationPrice(this.groupByShippingMethod, 1, 1),
            miscellaneous: this.caculationPrice(this.groupByShippingMethod, 1, 2),
            alcoholic: this.caculationPrice(this.groupByShippingMethod, 1, 3),
          }
          objectPrice.subtotal = objectPrice.industrial + objectPrice.miscellaneous
          objectPrice.all = objectPrice.industrial + objectPrice.miscellaneous + objectPrice.alcoholic
        }
        return objectPrice
      },
      //price Delivery
      priceDelivery() {
        let objectPrice = {}
        if (this.groupByShippingMethod != null) {
          objectPrice = {
            industrial: this.caculationPrice(this.groupByShippingMethod, 2, 1),
            miscellaneous: this.caculationPrice(this.groupByShippingMethod, 2, 2),
            alcoholic: this.caculationPrice(this.groupByShippingMethod, 2, 3),
          }
          objectPrice.subtotal = objectPrice.industrial + objectPrice.miscellaneous,
          objectPrice.all = objectPrice.industrial + objectPrice.miscellaneous + objectPrice.alcoholic
        }
        return objectPrice
      },
      shippingFeeDelivery() {
        let alcohol = 0
        let normal = 0
        if (this.productsDelivery != null && Object.keys(this.getMarginRates).length > 0) {
          for(let i in this.productsDelivery) {
            let percent = this.getMarginRates[this.productsDelivery[i].productTp].delivery.shipping
            let getPrice = this.calculatePrice(this.productsDelivery[i].productTp, this.productsDelivery[i], this.getMarginRates, this.getStoreSettings)
            if ( this.productsDelivery[i].productTp == 3) { // 3 = alcohol
              alcohol += parseInt(percent * (getPrice.price * this.productsDelivery[i].quantity) / 100)
            } else {
              normal += parseInt(percent * (getPrice.price * this.productsDelivery[i].quantity) / 100)
            }
          }
        }
        return {
          alcohol: alcohol,
          normal: normal
        }
      },
      shippingFeeSameDay() {
        let alcohol = 0
        let normal = 0
        if (this.productsSameDay != null && Object.keys(this.getMarginRates).length > 0) {
          for(let i in this.productsSameDay) {
            let percent = this.getMarginRates[this.productsSameDay[i].productTp].supplier.shipping
            let getPrice = this.calculatePrice(this.productsSameDay[i].productTp, this.productsSameDay[i], this.getMarginRates, this.getStoreSettings)
            if (this.productsSameDay[i].productTp == 3) { // 3 = alcohol
              alcohol += parseInt(percent * (getPrice.price * this.productsSameDay[i].quantity) / 100)
            } else {
              normal += parseInt(percent * (getPrice.price * this.productsSameDay[i].quantity) / 100)
            }
          }
        }
        return {
          alcohol: alcohol,
          normal: normal
        }
      },
      discountShippingFee() {
        return 0
      },
      // Total price in cart
      totalPrice() {
        let total = 0
        if (this.getOrderType == null) {
          total = this.pricePickUpCenter.subtotal + this.priceDelivery.subtotal + this.priceSameDay.subtotal + this.shippingFeeDelivery.normal + this.shippingFeeSameDay.normal
        } else {
          if (this.getOrderType == 'pickup') {
            total = this.pricePickUpCenter.subtotal
          } else if (this.getOrderType == 'delivery') {
            total = this.priceDelivery.subtotal + this.shippingFeeDelivery.normal
          } else if (this.getOrderType == 'sameDay') {
            total = this.priceSameDay.subtotal + this.shippingFeeSameDay.normal
          }
        }
        // redirect page order form if not enough money
        // if (this.$route.name == 'OrderForm' && total > this.creditAmount && this.creditAmount != null) {
        //   this.$router.push('/shopping-cart')
        // }
        total = parseInt(total)
        this.$emit('totalOrder', {
          totalPrice: total,
          credit: this.creditAmount,
          balance: this.balance,
          useBalance: this.useBalance,
        })
        return total
      },
      totalPriceCalculate() {
        return this.totalPrice - this.balance
      },
      depsitCalculate() {
        return this.creditAmount - this.balance
      },
      comparePrice() {
        if (this.totalPrice < this.creditAmount) {
          return true
        } else {
          return false
        }
      }
    },
    created() {
      this.getStoreInfo()
    },
    methods: {
      getStoreInfo() {
        axios.get(apiURL + "/user/credit")
          .then(response => {
            this.creditAmount = response.data.data.order_credit_amount
          })
      },
      groupByObject (object, key) {
          return object.reduce(function(type, obj) {
              (type[obj[key]] = type[obj[key]] || []).push(obj);
              return type;
          }, {});
      },
      caculationPrice(obj, shippingMethod, productType) {
        let result = 0;
        Object.entries(obj).forEach(value => {
          if (value[0] == shippingMethod) {
            let newObj = value[1];
            for (let i = 0; i < newObj.length; i++) {
              if (newObj[i].productTp == productType) {
                let getPrice = this.calculatePrice(newObj[i].productTp, newObj[i], this.getMarginRates, this.getStoreSettings)
                result += getPrice.price * newObj[i].quantity;
              }
            }
          }
        });
        return result
      },
      orderFormSubmit() {
        if(this.productsInCart.length > 0) {
          for (let i = 0; i < this.productsInCart.length; i++) {
            if (this.productsInCart[i].limited == true && this.productsInCart[i].quantity > this.productsInCart[i].stockQty) {
              alert("제품 " + this.productsInCart[i].productNm + " 재고보다 수량이 많습니다.")
              this.$router.push('/shopping-cart')
              return
            }
          }
        }
        if(this.page == "/shopping-cart") {
          if (this.productsInCart.length != 0) {
            if (this.comparePrice == true) {
              this.$router.push('/order-form')
            } else {
              this.$refs.ConfirmDialogue.show({
                title: this.$t('modal.titleDialog'),
                message: this.$t('modal.dipositMoney'),
                cancelButton: this.$t('modal.btnOk'),
                isDialog: true,
              })
            }
          } else {
            alert("장바구니에 담긴 상품이 없습니다")
          }
        }
      },
      checkoutByType(checkoutType) {
        let priceCheck = 0
        let listProducts = []
        if(checkoutType == 'pickup') {
          priceCheck = this.pricePickUpCenter.subtotal
          listProducts = this.productsCenterPickup
        } else if(checkoutType == 'delivery') {
          priceCheck = this.priceDelivery.subtotal
          listProducts = this.productsDelivery
        } else if(checkoutType == 'sameDay') {
          priceCheck = this.priceSameDay.subtotal
          listProducts = this.productsSameDay
        }
        // disable empty product and if page current = order form
        if (listProducts.length == 0 || this.$route.name == 'OrderForm') {
          return
        }
        // check can order
        if(listProducts.length > 0) {
          for (let i = 0; i < listProducts.length; i++) {
            if (listProducts[i].limited == true && listProducts[i].quantity > listProducts[i].stockQty) {
              alert("제품 " + listProducts[i].productNm + " 재고보다 수량이 많습니다.")
              this.$router.push('/shopping-cart')
              return
            }
          }
        }

        // dialog notice not enough money
        if (priceCheck > this.creditAmount) {
          console.log(priceCheck)
          this.$refs.ConfirmDialogue.show({
            title: this.$t('modal.titleDialog'),
            message: this.$t('modal.dipositMoney'),
            cancelButton: this.$t('modal.btnOk'),
            isDialog: true,
          })
        } else {
          this.$router.push({ path: '/order-form', query: { type: checkoutType } })
        }
      },
      async showPopupPayment() {
        await this.$refs.PopupPayment.show({
          cancelButton: this.$t('modal.btnClose'),
          storeInfos: this.getStoreInfos,
          storeInfos: this.getStoreInfos,
          creditAmount: this.creditAmount,
        })
      },
      calculate() {
        if (this.balance > 0) {
          if (this.balance > this.creditAmount) {
            this.$refs.ConfirmDialogue.show({
              title: this.$t('modal.titleDialog'),
              message: this.$t('modal.dipositMoney'),
              cancelButton: this.$t('modal.btnOk'),
              isDialog: true,
            })
            this.balance = 0
          } else {
            this.useBalance = true
          }
        }
      },
      cancelCalculate() {
        this.balance = 0
        this.useBalance = false
      },
      changeBalance(event) {
        let val = event.target.value.trim()
        // It can only be positive integer or empty, and the rule can be modified according to the requirement.
        if (/^[1-9]\d*$|^$/.test(val)) {
          if (val > this.totalPrice) {
            this.balance = 0
          }
        } else {
          this.balance = 0
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rightbar-content {
    padding: 20px;
    margin: 0 auto;
    width: 100%;
    height: auto;
    border-radius: 10px;
    border: 1px solid #ddd;
    color: rgb(110, 107, 107);
    background: #f4f4f4;
    .title {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    p {
      font-size: 14px;
      font-weight: 600;
      margin: 0;
    }
    p.buy {
      display: inline-block;
      outline: none;
      border: 1px solid red;
      border-radius: 30px;
      font-size: 13px;
      color: red;
      font-weight: 500;
      height: fit-content;
      padding: 3px 25px;
      text-align: center;
      cursor: pointer;
    }
    .item {
      flex-wrap: wrap;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #ddd;
      font-size: 14px;
      padding: 5px 0;
    }
    .content {
      padding: 10px 0;
    }
    span.amount {
      font-weight: 600;
      font-size: 20px;
      &.total {
        color: rgb(228, 23, 23);
      }
    }
    .noBorder {
      border-bottom: none;
    }
    button.btn.btn-block {
      display: block;
      width: 100%;
      margin-bottom: 5px;
      padding: 10px 0;
      &:last-child {
        margin-bottom: 0;
      }
    }

    .lowerPart {
      margin-bottom: 20px;
      .flex_col {
        display: flex;
        flex-direction: column;
      }
      .item-wrap {
        display: flex;
        justify-content: space-between;
        width: 100%;
      }
      span.deliveryMsg {
        margin: 0 0 5px;
        text-align: right;
        display: block;
        width: 100%;
      }
    }
    .lastPart {
      .content {
        display: block;
        text-align: right;
        p.text {
          font-weight: 500;
        }
        span.amount.total {
          font-size: 30px;
        }
      }
    }
    .checkbox-balance input[type=checkbox] + label {
      display: block;
      margin: 0.2em;
      cursor: pointer;
      padding: 0.2em;
      font-family: 'Arial'
    }

    .checkbox-balance {
      font-size: 12px;
      label {
        width: 30%;
      }
      .balance-amount {
        width: 45%;
        padding-right: 6px;
        position: relative;
        input {
          width: 100%;
          border-radius: 4px;
          border: 1px solid #e2e2e2;
          padding: 4px;
          padding-left: 6px;
          padding-right: 15px;
          &[readonly] {
            background-color: #e9ecef;
            opacity: 1;
          }
        }
        .won {
          position: absolute;
          right: 15px;
          top: 5px;
          color: #222;
        }
      }
      .btn-use {
        width: 25%;
        border: 1px solid #d3d3d3;
        border-radius: 4px;
        padding: 4px;
        background: #dbdbdb;
        &.not-use {
          background: #dc3545;
          color: #fff;
        }
      }
    }
    .deposit-calculate {
      font-size: 12px;
    }
    .order-calculate {
      span {
        color: rgb(228, 23, 23);
        font-size: 30px;
        font-weight: bold;
      }
    }
  }

  @media only screen and (max-width: 767px) {
    .rightbar-content .lowerPart {
      margin-bottom: 0;
    }
  }
</style>
