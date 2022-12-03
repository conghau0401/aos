<template>
  <section id="recommend-product">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("recommend_prod.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("recommend_prod.byStore") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ currentStore.storeScale }}{{ $t("recommend_prod.unit1") }}
            </router-link>
          </li>
        </ul>
      </div>

      <div class="recommend-title">
        <div class="row">
          <div class="col-lg-4 col-12">
            <div class="recommend-title__left d-flex flex-wrap align-items-center jutisfy-content-between">
              <p class="price">
                {{ currentStore.storeScale }}{{ $t("recommend_prod.unit1") }} <span>{{ $t("recommend_prod.store") }}</span>
              </p>
              <p>{{ $t("recommend_prod.numberProd") }} : {{ countProducts }}</p>
            </div>
          </div>
          <div class="col-lg-5 col-12">
            <ul class="recommend-title__right d-flex flex-wrap align-items-center justify-content-lg-end">
              <li
                v-for="(item, index) in listStore"
                :key="index"
                :class="storeSizeId == item.bundleStoresizeId ? 'active' : ''"
              >
                <router-link :to="'/recommend-product/' + item.bundleStoresizeId">
                  {{ item.storeScale }}{{ $t("recommend_prod.unit1") }}
                </router-link>
              </li>
            </ul>
          </div>
          <div class="col-md-3">
            <p class="recommend-title__status">
              {{ $t("recommend_prod.storeOpen") }}
            </p>
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
              <ProductGroupItem
                :product-info="item.products"
                :product-t-p="parseInt(item.productTypeCode)"
              />
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <RightBar :shipping-fee="shippingFee" />
        </div>
      </div>
    </div>
    <LoadingPage
      v-if="loading"
    />
  </section>
</template>

<script>
    import RightBar from '../components/RightBar'
    import { countNumber } from '../mixins/countNumber'
    import ProductGroupItem from '../components/ProductGroupItem'
    import LoadingPage from '../components/LoadingPage'
    export default {
        components: {
            RightBar,
            ProductGroupItem,
            LoadingPage,
        },
        mixins: [countNumber],
        data() {
            return {
                loading: true,
                products: [],
                listStore: [],
                storeSizeId: "",
                storeSizeFirstId: "",
                currentStore: {},
                countProducts: 0,
                currentPage: this.$route.name,
            }
        },
        computed: {
            shippingFee() {
                return this.$store.state.shippingFee
            }
        },
        watch: {
            $route (to){
                if (to.name != this.currentPage) return
                if (to.params.id != "") {
                    this.storeSizeId = to.params.id
                    this.getStoreSizeDetail(this.storeSizeId)
                } else {
                    this.storeSizeId = ""
                    this.storeSizeFirstId = ""
                    this.products = []
                    this.currentStore = {}
                    this.getListStoreSize()
                }
            }
        },
        mounted() {
            this.storeSizeId = this.$route.params.id
            this.getListStoreSize()
        },
        methods: {
            getListStoreSize() {
                axios.get(apiURL + "/product/bundle-storesize")
                .then(response => {
                    if(this.storeSizeId == "") {
                        this.storeSizeId = this.storeSizeFirstId = response.data.data[0].bundleStoresizeId
                    }
                    this.listStore = response.data.data
                    this.getStoreSizeDetail(this.storeSizeId)
                }).catch(err => {
                    console.log(err)
                })
            },
            getStoreSizeDetail(id) {
                this.loading = true
                // update current store size
                for (const key in this.listStore) {
                    if (this.listStore[key].bundleStoresizeId == this.storeSizeId) {
                        this.currentStore = this.listStore[key]
                    }
                }
                axios
                    .get(apiURL + '/product/bundle-storesize/' + id)
                    .then(response => {
                        this.loading = false
                        this.products = response.data.data
                        this.countProducts = response.data.countProducts
                    }).catch(err => {
                        console.log(err)
                    })
            },
        },
    }
</script>

<style lang="scss" scoped>
#recommend-product {
    padding: 0 0 50px;
}
.recommend-title {
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
        border: 1px solid #ddd;
        padding: 0 15px;
        border-right: 0;
        position: relative;
        height: 50px;
        &:after {
            position: absolute;
            width: 20px;
            height: 50px;
            transform: skew(20deg);
            background: transparent;
            content: "";
            top: -1px;
            right: -10px;
            border-right: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }
        p {
            width: 50%;
            &:last-child {
                text-align: right;
            }
            &.price {
                font-size: 22px;
                font-weight: bold;
                span {
                    font-size: 18px;
                }
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
    .recommend-title {
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
    #recommend-product {
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
