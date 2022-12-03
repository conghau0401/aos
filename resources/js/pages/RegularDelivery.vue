<template>
  <section
    id="wishlist-product"
    class="wrapper-regular"
  >
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("regular_deli.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("regular_deli.regular") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="wishlist-title">
        <div class="row">
          <div class="col-lg-9 col-12">
            <div class="wishlist-title__left d-flex flex-wrap align-items-center">
              <p>{{ $t("regular_deli.regular") }}</p>
              <p class="status">
                {{ $t("regular_deli.after") }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-9">
          <div class="title-right d-flex flex-wrap align-items-center justify-content-end">
            <p
              class="btn-delete"
              @click="updateRegularProduct($event)"
            >
              <span class="img">
                <svg
                  width="24"
                  height="24"
                  fill="#555"
                  viewBox="0 0 24 24"
                  xmlns="http://www.w3.org/2000/svg"
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                ><path
                  fill-rule="evenodd"
                  d="M7 9h-7v-7h1v5.2c1.853-4.237 6.083-7.2 11-7.2 6.623 0 12 5.377 12 12s-5.377 12-12 12c-6.286 0-11.45-4.844-11.959-11h1.004c.506 5.603 5.221 10 10.955 10 6.071 0 11-4.929 11-11s-4.929-11-11-11c-4.66 0-8.647 2.904-10.249 7h5.249v1z"
                /></svg>
              </span>
              <span>{{ $t("regular_deli.edit") }}</span>
            </p>
          </div>
          <div
            v-for="(item, idx) in products"
            :key="idx"
            class="container-box"
          >
            <div class="container-box__title d-lg-flex flex-wrap justify-content-between align-items-center">
              <div class="title-left d-flex align-items-center">
                <p class="container-box__icon">
                  <img
                    v-if="idx == 1"
                    src="/img/icon/box1.png"
                    :alt="idx"
                  >
                  <img
                    v-else-if="idx == 2"
                    src="/img/icon/box2.png"
                    :alt="idx"
                  >
                  <img
                    v-else
                    src="/img/icon/both.svg"
                    :alt="idx"
                  >
                </p>
                <p class="container-box__name">
                  <b v-if="idx == 1">{{ $t("regular_deli.indust") }}</b>
                  <b v-else-if="idx == 2">{{ $t("regular_deli.miscell") }}</b>
                  <b v-else>{{ $t("regular_deli.alcohol") }}</b>
                  <span>{{ $t("regular_deli.default") }}</span>
                </p>
              </div>
            </div>
            <div class="container-box__product box-product">
              <div class="box-product__ar content-product">
                <ProductGroupItem
                  :product-info="item"
                  :page="getPage"
                  :product-t-p="parseInt(idx)"
                  :is-remove-checked="isRemoveChecked"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <RightBar
            :page="getPage"
            :shipping-fee="shippingFee"
          />
        </div>
      </div>
    </div>
    <LoadingPage
      v-if="loading"
    />
    <ConfirmDialogue ref="ConfirmDialogue" />
  </section>
</template>

<script>
    import ProductGroupItem from '../components/ProductGroupItem'
    import RightBar from '../components/RightBar'
    import { countNumber } from '../mixins/countNumber'
    import LoadingPage from '../components/LoadingPage'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    export default {
        components: {
            RightBar,
            ProductGroupItem,
            LoadingPage,
            ConfirmDialogue,
        },
        mixins: [
            countNumber
        ],
        data() {
            return {
                shippingFee: 6000,
                products: [],
                loading: true,
                allIdCheckBox: [],
                isRemoveChecked: false
            }
        },
        computed: {
            getPage() {
                return this.$route.path
            },
            getCheckedItem() {
                return this.$store.state.checkedItem;
            },
            getIdsChecked() {
                return this.$store.state.idsChecked;
            },
        },
        mounted() {
            this.getRegularProduct()
            this.$store.dispatch("getRegularProducts")
            // reset checked item
            this.$store.state.checkedItem = []
            this.$store.state.idsChecked = []
        },
        methods: {
            getRegularProduct() {
                this.loading = true
                axios
                    .get(apiURL + "/product/regular")
                    .then(response => {
                        this.loading = false
                        let res = response.data.data.selected
                        this.products = this.groupByProductTp(res, 'productTp')
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            groupByProductTp (object, key) {
                return object.reduce(function(type, obj) {
                    (type[obj[key]] = type[obj[key]] || []).push(obj);
                    return type;
                }, {});
            },
            updateRegularProduct() {
                let itemChecked = []
                for (let i in this.getCheckedItem) {
                    for (let j in this.getIdsChecked) {
                        if (this.getCheckedItem[i].idMerge == this.getIdsChecked[j]) {
                            let checkedItem = this.getCheckedItem[i]
                            let itemRegular = {
                                "productId": checkedItem.productId,
                                "quantityPerPack": checkedItem.quantityPerPack,
                                "quantity": checkedItem.quantityUpdate,
                                "optionId": checkedItem.optionId,
                                "monday": checkedItem.weekdayChecked.indexOf("월") != -1 ? true : false,
                                "tuesday": checkedItem.weekdayChecked.indexOf("화") != -1 ? true : false,
                                "wednesday": checkedItem.weekdayChecked.indexOf("수") != -1 ? true : false,
                                "thursday": checkedItem.weekdayChecked.indexOf("목") != -1 ? true : false,
                                "friday": checkedItem.weekdayChecked.indexOf("금") != -1 ? true : false,
                                "hold": checkedItem.weekdayChecked.indexOf("보류") != -1 ? true : false,
                            }
                            itemChecked.push(itemRegular)
                        }
                    }
                }
                let params = {
                    "regularDeliveryProductAdd": itemChecked
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
                        message: this.$t('modal.regularUpdated'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    // reset checked item
                    this.isRemoveChecked = !this.isRemoveChecked
                    this.$store.state.checkedItem = []
                    this.$store.state.idsChecked = []
                    // add product to Regular API
                    axios.put(apiURL + "/product/regular-delivery", params)
                    .then((res) => {
                        console.log(res)
                    })
                    .catch(err => {
                        console.log(err)
                    })
                }
            }
        }

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

.title-right {
    font-size: 14px;
    text-align: right;
    p.noted {
        margin: 0;
    }
    .btn-delete {
        border: 1px solid #ccc;
        padding: 3px 12px;
        border-radius: 2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: all 0.3s;
        &:hover {
            opacity: 0.8;
        }
        svg {
            width: 15px;
            height: 15px;
            width: auto;
            margin-right: 7px;
            margin-top: -2px;
        }
        img {
            height: 15px;
            width: auto;
            margin-right: 5px;
            margin-top: -2px;
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
        b {
            font-size: 20px;
            font-weight: normal;
            margin-right: 10px;
        }
        span {
            font-size: 12px;
        }
    }
    &__title {
        margin-bottom: 20px;
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
        width: 50%;
    }
    &__date {
        width: 10%;
    }
    &__option {
        width: 10%;
    }
    &__water {
        width: 10%;
    }
    &__num {
        width: 15%;
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
        width: 50%;
        text-align: left;
        display: flex;
        align-items: center;
    }
    &__date {
        width: 10%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        margin: 0;
        span {
            display: block;
            border: 1px solid #ddd;
            width: 48%;
            font-size: 12px;
            padding: 5px 0;
            cursor: pointer;
            margin-bottom: 4px;
            &.active {
                background: #f8e25b;
            }
        }
    }
    &__option {
        width: 10%;
        margin: 0 10px;
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
        width: 15%;
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
        position: relative;
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
        p.icon-sign {
            padding-left: 20px;
            img {
                width: 30px;
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
            width: 10%;
        }
        &__date {
            width: 10%;
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
            p.icon-sign {
                padding-left: 0;
                text-align: center;
                img {
                    width: 25px;
                }
            }
        }
        &__image {
            width: 100%;
            margin: 0;
        }
        &__option, &__water {
            width: 10%;
        }
        &__num {
            width: 25%;
        }
        &__date {
            margin-bottom: 0;
            width: 10%;
            span {
                width: 100%;
            }
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
    .wishlist-title {
        &__left {
            p.status {
                padding-left: 0;
            }
        }
    }
}
</style>
