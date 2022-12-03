<template>
  <section id="wishlist-product">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("wishlist_product.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("wishlist_product.myPage") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("wishlist_product.wishList") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="wishlist-title">
        <div class="row">
          <div class="col-lg-9 col-12">
            <div class="wishlist-title__left d-flex flex-wrap align-items-center">
              <p>{{ $t("wishlist_product.wishList") }} </p>
              <!-- <p class="status">수량을 변경하신 후에는 반드시 ‘수정’ 버튼을 클릭해주시기 바랍니다.</p> -->
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
      </div>
      <div class="row">
        <div class="col-lg-9">
          <div
            v-for="(item, index) in products"
            :key="index"
            class="container-box"
          >
            <div class="container-box__title d-flex flex-wrap justify-content-between align-items-center">
              <div class="title-left d-flex align-items-center">
                <p class="container-box__icon">
                  <img
                    v-if="index == 1"
                    src="/img/icon/box1.png"
                    class="icon"
                    :alt="index"
                  >
                  <img
                    v-else-if="index == 2"
                    src="/img/icon/box2.png"
                    class="icon"
                    :alt="index"
                  >
                  <img
                    v-else
                    src="/img/icon/both.svg"
                    class="icon"
                    :alt="index"
                  >
                </p>
                <p class="container-box__name">
                  <b v-if="index == 1">{{ $t('productType.industrial') }}</b>
                  <b v-else-if="index == 2">{{ $t('productType.miscellaneous') }}</b>
                  <b v-else>{{ $t('productType.alcoholic') }}</b>
                </p>
              </div>
              <div class="title-right d-lg-flex flex-wrap align-items-center">
                <p class="btn-delete">
                  <span @click="removeWishlist">{{ $t('wishlist_product.removeWishlist') }}</span>
                </p>
              </div>
            </div>
            <ProductGroupItem
              :product-info="item"
              :product-t-p="parseInt(index)"
              :is-remove-checked="isRemoveChecked"
              @get-all-checkbox-ids="getAllCheckboxIds"
              @toggle-checkbox-id="toggleCheckboxId"
            />
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
    />
  </section>
</template>

<script>
    import RightBar from '../components/RightBar'
    import ProductGroupItem from '../components/ProductGroupItem'
    import { countNumber } from '../mixins/countNumber'
    import { groupByKeyMixin } from '../mixins/groupByKeyMixin'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    import LoadingPage from '../components/LoadingPage'

    export default {
        components: {
            RightBar,
            ProductGroupItem,
            ConfirmDialogue,
            LoadingPage,
        },
        mixins: [
            countNumber,
            groupByKeyMixin,
        ],
        data() {
            return {
                loading: true,
                products: [],
                allIdCheckBox: [],
                idsWithQuantity: [],
                isRemoveChecked: false,
            }
        },
        computed: {
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
        created() {
            this.getWishlistProduct()
        },
        mounted() {
            // reset checked item
            this.$store.state.checkedItem = []
            this.$store.state.idsChecked = []
        },
        methods: {
            getWishlistProduct() {
                this.loading = true
                axios.get(apiURL + "/product/wishlist")
                    .then((res) => {
                        // update name properties
                        this.loading = false
                        let dataResponse = res.data.data
                        dataResponse.forEach(function(obj) {
                            obj.productNm = obj.productName
                            obj.image = obj.productImg
                            delete obj.productImg
                            delete obj.productName
                            obj.options.forEach(option => {
                                option.price = option.supplyUnitPrice
                                option.quantity = option.maxOrder
                                option.optionNm = option.optionName
                                delete obj.maxOrder
                                delete obj.optionName
                            });
                        });
                        this.products = this.groupByKey(dataResponse, 'productTp')
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            getAllCheckboxIds(allId, mixinIds) {
                if (this.allIdCheckBox.length == 0) {
                    for(let j in allId) {
                        this.allIdCheckBox.push(allId[j])
                    }
                } else {
                    if (mixinIds.length == 0) {
                        for(let i = 0; i < allId.length; i++) {
                            this.allIdCheckBox.splice(this.allIdCheckBox.indexOf(allId[i]), 1)
                        }
                    } else {
                        for(let j in allId) {
                            this.allIdCheckBox.push(allId[j])
                        }
                    }
                }
            },
            toggleCheckboxId(id, quantity = null) {
                if (this.allIdCheckBox.includes(id)) {
                    for(let i = 0; i < this.allIdCheckBox.length; i++) {
                        if (id == this.allIdCheckBox[i]) {
                            this.allIdCheckBox.splice(i, 1)
                        }
                    }
                } else {
                    this.allIdCheckBox.push(id)
                }

                if ( this.idsWithQuantity.length == 0) {
                    let obj = {}
                    obj[id] = quantity
                    this.idsWithQuantity.push(obj)
                } else {
                    // check array object has key name
                    let checkKey = this.idsWithQuantity.some(e => e.hasOwnProperty(id));
                    if(checkKey == false) {
                        let obj = {}
                        obj[id] = quantity
                        this.idsWithQuantity.push(obj)
                    } else {
                        for (var i = 0; i < this.idsWithQuantity.length; i++) {
                            // remove if exist key name same id merge
                            if (Object.keys(this.idsWithQuantity[i])[0] == id) {
                                this.idsWithQuantity.splice(i, 1)
                            }
                        }
                    }
                }
            },
            // async addToRegular() {
            //     if (this.idsWithQuantity.length == 0) {
            //         alert(this.$t("modal.chooseProductItem"))
            //     } else {
            //         console.log(this.idsWithQuantity)
            //         // todo: add product to regular
            //     }
            // }
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
            removeWishlist() {
                let productDeleteIds = []
                for (let i in this.getIdsChecked) {
                    productDeleteIds.push(this.getIdsChecked[i])
                }
                if (productDeleteIds.length == 0) {
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
                        message: this.$t('modal.deleteSuccessWishlist'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    let params = {
                        "productIds": [],
                        "productDeleteIds": productDeleteIds,
                    }
                    // reset checked item
                    this.isRemoveChecked = !this.isRemoveChecked
                    this.$store.state.checkedItem = []
                    this.$store.state.idsChecked = []
                    axios.put(apiURL + "/product/add-wishlist", params)
                        .then((res) => {
                            let dataResponse = res.data.data
                            dataResponse.forEach(function(obj) {
                                obj.productNm = obj.productName
                                obj.image = obj.productImg
                                delete obj.productImg
                                delete obj.productName
                                obj.options.forEach(option => {
                                    option.price = option.supplyUnitPrice
                                    option.quantity = option.maxOrder
                                    option.optionNm = option.optionName
                                    delete obj.maxOrder
                                    delete obj.optionName
                                });
                            });
                            this.products = this.groupByKey(dataResponse, 'productTp')
                        }).catch(err => {
                            console.log(err)
                        })
                }
            }
        },
    }
</script>

<style lang="scss" scoped>
#wishlist-product {
    padding: 0 0 50px;
}
.wishlist-title {
    font-size: 14px;
    color: #555;
    margin-bottom: 30px;
    .add-all-cart {
        p {
            font-size: 14px;
            border: 1px solid #ccc;
            padding: 4px 12px;
            border-radius: 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            font-weight: normal;
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
        justify-content: space-between;
        position: relative;
        p {
            font-size: 22px;
            font-weight: bold;
            &.status {
                font-size: 14px;
                font-weight: normal;
                padding-left: 15px;
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
        font-size: 22px;
        margin-bottom: 0;
        span {
            font-size: 12px;
        }
    }
    &__title {
        margin-bottom: 20px;
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
            img {
                width: 8px;
                margin-top: -4px;
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
    color: #999;
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
        font-size: 20px;
        &.other {
            font-size: 30px;
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
    .wishlist-title {
        margin-bottom: 20px;
        &__left {
            border: 1px solid #ddd;
            &:after {
                display: none;
            }
        }
        &__right, &__left {
            padding: 10px 15px;
            height: auto;
        }
        &__right {
            border: 1px solid #ddd;
            margin: 5px 0 15px;
            &:after {
                display: none;
            }
            li {
                &:first-child {
                    padding-left: 0;
                }
            }
        }
    }
}

@media only screen and (max-width: 767px) {
    #wishlist-product {
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
}
</style>
