<template>
  <section id="discount-product">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("discount_product.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("discount_product.specialOffer") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="discount-title">
        <div class="row">
          <div class="col-lg-9 col-12">
            <div
              v-if="Object.keys(listDiscount).length"
              class="discount-title__left d-lg-flex flex-wrap align-items-center justify-content-between"
            >
              <p class="price">
                <span>{{ listDiscount[discountId].productDealName }}</span>
                <span class="small">{{ dealStartDate }} ~ {{ dealEndDate }} &ensp; {{ $t("discount_product.numberItem") }} {{ listDiscount[discountId].countDealDetail }}</span>
              </p>
              <p v-if="checkDueExpireStatus">
                {{ $t("discount_product.proceeding") }}
              </p>
              <p v-else>
                {{ $t("discount_product.deadline") }}
              </p>
            </div>
            <div class="add-all-cart d-flex flex-wrap">
              <p
                class="btn-add"
                @click="addCartMultiple"
              >
                {{ $t("cart.addAll") }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-9">
          <div
            v-for="(item, index) in products"
            :key="index"
            class="container-box"
          >
            <div class="container-box__title d-lg-flex flex-wrap justify-content-between align-items-center">
              <div class="title-left d-flex align-items-center">
                <p class="container-box__icon">
                  <img
                    :src="item.icon"
                    alt="icon"
                  >
                </p>
                <p class="container-box__name">
                  {{ item.productType }}
                </p>
              </div>
            </div>
            <div class="container-box__product box-product">
              <div class="box-product__ar content-product">
                <ProductGroupItem
                  :product-info="item.products"
                  :is-remove-checked="isRemoveChecked"
                  :product-t-p="parseInt(item.productTypeCode)"
                />
              </div>
            </div>
          </div>
          <div class="special-sale first">
            <h3 class="special-sale__title">
              {{ $t("discount_product.onGoing") }}
            </h3>
            <div
              v-for="(item, idx) in discountDueList"
              :key="idx"
              class="special-sale__box d-lg-flex flex-wrap align-items-center justify-content-between"
            >
              <p class="price">
                <span>
                  <router-link :to="'/discount-product/' + item.productDealId">
                    {{ item.productDealName }}
                  </router-link>
                </span>
                <span class="small">{{ convertTimeStamp(item.saleStartDate) }} ~ {{ convertTimeStamp(item.saleEndDate) }}  상품 수 : {{ item.countDealDetail }}</span>
              </p>
              <p>{{ $t("discount_product.goingAhead") }}</p>
            </div>
          </div>
          <div class="special-sale">
            <h3 class="special-sale__title">
              {{ $t("discount_product.closeSpec") }}
            </h3>
            <div
              v-for="(item, idx) in discountExpireList"
              :key="idx"
              class="special-sale__box d-lg-flex flex-wrap align-items-center justify-content-between"
            >
              <p class="price">
                <span>
                  <router-link :to="'/discount-product/' + item.productDealId">
                    {{ item.productDealName }}
                  </router-link>
                </span>
                <span class="small">{{ convertTimeStamp(item.saleStartDate) }} ~ {{ convertTimeStamp(item.saleStartDate) }}  상품 수 : {{ item.countDealDetail }}</span>
              </p>
              <p>{{ $t("discount_product.deadline") }}</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <RightBar :shipping-fee="shippingFee" />
        </div>
      </div>
    </div>
    <ConfirmDialogue ref="ConfirmDialogue" />
    <LoadingPage
      v-if="loading"
      :size="sizeLoading"
      :color="colorLoading"
    />
  </section>
</template>

<script>
    import RightBar from '../components/RightBar'
    import ProductGroupItem from '../components/ProductGroupItem'
    import LoadingPage from '../components/LoadingPage'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    export default {
        components: {
            RightBar,
            ProductGroupItem,
            LoadingPage,
            ConfirmDialogue,
        },
        data() {
            return {
                products: [],
                discountDueList: [],
                discountExpireList: [],
                listDiscount: {},
                discountId: "",
                discountFirstId: "",
                loading: true,
                sizeLoading: 50,
                colorLoading: "#41b883",
                isRemoveChecked: false,
                currentPage: this.$route.name,
            }
        },
        computed: {
            dealStartDate() {
                return this.convertTimeStamp(this.listDiscount[this.discountId].saleStartDate)
            },
            dealEndDate() {
                return this.convertTimeStamp(this.listDiscount[this.discountId].saleEndDate)
            },
            getCurrentDate() {
                return + new Date();
            },
            checkDueExpireStatus() {
                if (this.getCurrentDate < this.listDiscount[this.discountId].saleEndDate) {
                    return true
                }
                return false
            },
            shippingFee() {
                return this.$store.state.shippingFee
            },
            getCheckedItem() {
                return this.$store.state.checkedItem;
            },
            getIdsChecked() {
                return this.$store.state.idsChecked;
            },
        },
        watch: {
            $route (to){
                if (to.name != this.currentPage) return
                if (to.params.id != "") {
                    this.discountId = to.params.id
                    this.getDiscountDetail(this.discountId)
                    this.compareDateListDiscount(this.discountId);
                } else {
                    this.discountId = ""
                    this.products = []
                    this.listDiscount = {}
                    this.discountDueList = []
                    this.discountExpireList = []
                    this.getListDiscount()
                }
            },
        },
        mounted() {
            // reset checked item
            this.$store.state.checkedItem = []
            this.$store.state.idsChecked = []
            this.getListDiscount()
        },
        methods: {
            getListDiscount() {
                this.loading = true
                axios.get(apiURL + "/account/product-deal")
                .then(response => {
                    let res = response.data.data;
                    if (res[0] != undefined) {
                        for (let i in res) {
                            this.listDiscount[res[i].productDealId] = res[i]
                        }
                        //get first discount id (condition: datetime happening)
                        if(this.discountId == "") {
                            let newArr = [];
                            for(let i in res) {
                                if (res[i].saleEndDate > this.getCurrentDate && res[i].saleStartDate < this.getCurrentDate || res[i].saleStartDate < this.getCurrentDate && res[i].saleEndDate < this.getCurrentDate) {
                                    newArr.push(res[i])
                                }
                            }
                            if (newArr[0] != undefined) {
                                this.discountId = this.discountFirstId = newArr[0].productDealId
                            } else {
                                this.discountId = this.discountFirstId = 0
                            }
                        } else {
                            this.discountFirstId = this.discountId
                        }
                        this.getDiscountDetail(this.discountId)
                        this.compareDateListDiscount(this.discountFirstId);
                    } else {
                        this.loading = false
                    }
                }).catch(err => {
                    console.log(err)
                })
            },
            getDiscountDetail(id) {
                this.loading = true
                axios
                    .get(apiURL + '/account/product-deal/' + id)
                    .then(response => {
                        this.products = response.data.data
                        this.loading = false
                    }).catch(error => {
                        console.log(error)
                    })
            },
            // convert datetime to yy.dd.mm
            convertTimeStamp(timestamp) {
                let date = new Date(timestamp)
                let day = date.getUTCDate()
                let month = date.getUTCMonth() + 1
                let year = date.getUTCFullYear()
                let newDate = year + "." + day + "." + month
                return newDate
            },
            //filter type discount
            compareDateListDiscount(id) {
                this.discountDueList = []
                this.discountExpireList = []
                let currentDate = this.getCurrentDate
                let newArr = {}
                //remove current discount detail
                for (let i in this.listDiscount) {
                    if (this.listDiscount[i].productDealId != id) {
                        newArr[this.listDiscount[i].productDealId] = this.listDiscount[i]
                    }
                }
                //filter Due time and Expire time
                for(let i in newArr) {
                    if (newArr[i].saleEndDate > currentDate && newArr[i].saleStartDate < currentDate) {
                        this.discountDueList.push(newArr[i])
                    } else if (newArr[i].saleEndDate < currentDate) {
                        this.discountExpireList.push(newArr[i])
                    }
                }
            },
            addCartMultiple() {
                let itemChecked = []
                for (let i in this.getCheckedItem) {
                    for (let j in this.getIdsChecked) {
                        if (this.getCheckedItem[i].productId == this.getIdsChecked[j]) {
                            let item = {
                                "ds_product_id": this.getCheckedItem[i].productId,
                                "ds_option_id": this.getCheckedItem[i].compareOptionId,
                                "shipping_method": 2,
                                "quantity": this.getCheckedItem[i].quantityUpdate,
                            }
                            itemChecked.push(item)
                        }
                    }
                }
                if (itemChecked.length == 0) {
                    //show notice dialogue
                    this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.nothingUpdated'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                } else {
                    //show notice dialogue
                    this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.addAllSuccess'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    let params = {
                        "listRequest": itemChecked
                    }
                    // reset checked item
                    this.isRemoveChecked = !this.isRemoveChecked
                    this.$store.state.checkedItem = []
                    this.$store.state.idsChecked = []
                    axios.post(apiURL + "/carts/create-multiple", params)
                    .then((res) => {
                        for (const index in res.data.data) {
                            //update product
                            this.$store.commit('updateProduct', res.data.data[index]);
                        }
                    }).catch(err => {
                        console.log(err)
                    })
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
#discount-product {
    padding: 0 0 50px;
}
.discount-title {
    font-size: 14px;
    color: #555;
    .add-all-cart {
        margin-top: 10px;
        justify-content: right;
        p {
            border: 1px solid #ccc;
            padding: 4px 12px;
            border-radius: 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
    }
    p {
        margin: 0;
    }
    &__status {
        font-size: 14px;
    }
    &__right {
        height: 50px;
        border: 1px solid #ddd;
        padding: 0 15px;
        position: relative;
        border-left: 0;
        margin: 0;
        &:after {
            position: absolute;
            width: 20px;
            height: 50px;
            transform: skew(20deg);
            background: transparent;
            content: "";
            top: -1px;
            left: -10px;
            border-left: 1px solid #ddd;
            border-top: 1px solid #ddd;
        }
        li {
            padding: 0 15px;
            position: relative;
            a {
                color: #555;
                &:hover {
                    color: #ef4130;
                }
            }
            &:first-child {
                &:after {
                    display: none;
                }
            }
            &:after {
                position: absolute;
                content: "•";
                left: -3px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 20px;
            }
        }
    }
    &__left {
        border: 1px solid #ddd;
        padding: 0 15px;
        position: relative;
        height: 50px;
        p {
            &.price {
                font-size: 20px;
                font-weight: bold;
                display: flex;
                align-items: center;
                span.small {
                    font-size: 14px;
                    padding-left: 20px;
                    font-weight: normal;
                }
            }
            &:last-child {
                color: #ef4130;
                font-size: 14px;
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
            width: auto;
            height: 35px;
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
        width: 130px;
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
        p.old-price {
            font-size: 14px;
            text-decoration: line-through;
            display: block;
            width: 100%;
            margin: 10px 0 -3px 0;
        }
        p.discount {
            color: #ef4130;
            font-size: 14px;
            font-weight: bold;
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

.special-sale {
    margin-bottom: 40px;
    &.first {
        margin-top: 40px;
    }
    &__title {
        font-size: 20px;
        margin-bottom: 15px;
    }
    &__box {
        border: 1px solid #ddd;
        padding: 0 15px;
        position: relative;
        height: 50px;
        color: #727272;
        margin-bottom: 10px;
        p {
            margin: 0;
            &.price {
                font-size: 20px;
                font-weight: bold;
                display: flex;
                align-items: center;
                a {
                    color: #727272;
                }
                span.small {
                    font-size: 14px;
                    padding-left: 20px;
                    font-weight: normal;
                }
            }
            &:last-child {
                font-size: 14px;
                font-weight: bold;
            }
        }
    }
}

@media only screen and (max-width: 991px) {
    .container-box {
        &__title {
            .title-right {
                margin-top: 10px;
                .btn-delete {
                    display: inline-flex;
                    margin: 0 0 0 10px;
                    &:first-child {
                        margin-left: 0;
                    }
                }
                p.noted {
                    margin: 10px 0;
                    font-size: 14px;
                }
            }
        }
    }
    .discount-title {
        margin-bottom: 20px;
        &__left {
            border: 1px solid #ddd;
            p.price {
                display: block;
                span {
                    display: block;
                    &.small {
                        padding-left: 0;
                        margin: 5px 0;
                    }
                }
            }
            &:after {
                display: none;
            }
        }
        &__left {
            padding: 10px 15px;
            height: auto;
        }
    }
}

@media only screen and (max-width: 767px) {
    #discount-product {
        padding-bottom: 15px;
    }
    .container-box {
        margin-bottom: 15px;
        &__name {
            font-size: 18px;
            font-weight: bold;
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
            p.discount {
                font-size: 12px;
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
    .special-sale {
        margin-bottom: 20px;
        &__box {
            height: auto;
            padding: 5px 10px;
            p.price {
                font-size: 16px;
                display: block;
                span {
                    display: block;
                    &.small {
                        padding-left: 0;
                        margin: 5px 0;
                    }
                }
            }
        }
    }
    .total-price {
        border-bottom: 1px solid #ddd;
        padding: 20px 0;
    }
}
</style>
