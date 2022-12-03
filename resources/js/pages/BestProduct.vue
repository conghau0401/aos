<template>
  <section class="best-product">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("best_product.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("best_product.best") }}
            </router-link>
          </li>
        </ul>
      </div>
      <b-tabs
        v-model="tabIndex"
        content-class="mt-5"
        align="center"
      >
        <b-tab
          :title="$t('order_form.indust')"
          @click="tabIndexType = 1"
          v-if="industrialProduct.length > 0"
          active
        >
          <div class="best-product__content">
            <div class="best-product__paging d-flex flex-wrap align-items-center">
              <ul class="d-flex flex-wrap align-items-center">
                <li
                  :class="{active: pageTab == 50}"
                  @click="pagination(50, industrialType)"
                >
                  50 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 100}"
                  @click="pagination(100, industrialType)"
                >
                  100 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 200}"
                  @click="pagination(200, industrialType)"
                >
                  200 {{ $t("best_product.unit") }}
                </li>
              </ul>
              <p class="text">
                <span>{{ $t("best_product.highSale") }}</span> |
                <span>{{ $t("best_product.highest") }}</span>
              </p>
            </div>
            <div class="row">
              <div
                v-for="(item, index) in industrialProductPagination"
                :key="index"
                class="col-lg-6 col-md-6 col-12"
              >
                <ProductItemBest
                  :product="item"
                  :idx="index"
                />
              </div>
            </div>
          </div>
        </b-tab>
        <b-tab :title="$t('order_form.miscell')" @click="tabIndexType = 2" v-if="miscellaneousProduct.length > 0">
          <div class="best-product__content">
            <div class="best-product__paging d-flex flex-wrap align-items-center">
              <ul class="d-flex flex-wrap align-items-center">
                <li
                  :class="{active: pageTab == 50}"
                  @click="pagination(50, miscellaneousType)"
                >
                  50 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 100}"
                  @click="pagination(100, miscellaneousType)"
                >
                  100 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 200}"
                  @click="pagination(200, miscellaneousType)"
                >
                  200 {{ $t("best_product.unit") }}
                </li>
              </ul>
              <p class="text">
                <span>{{ $t("best_product.highSale") }}</span> |
                <span>{{ $t("best_product.highest") }}</span>
              </p>
            </div>
            <div class="row">
              <div
                v-for="(item, index) in miscellaneousProductPagination"
                :key="index"
                class="col-lg-6 col-md-6 col-12"
              >
                <ProductItemBest
                  :product="item"
                  :idx="index"
                />
              </div>
            </div>
          </div>
        </b-tab>
        <b-tab :title="$t('order_form.alcohol')" @click="tabIndexType = 3" v-if="alcoholicProduct.length > 0">
          <div class="best-product__content">
            <div class="best-product__paging d-flex flex-wrap align-items-center">
              <ul class="d-flex flex-wrap align-items-center">
                <li
                  :class="{active: pageTab == 50}"
                  @click="pagination(50, alcoholicType)"
                >
                  50 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 100}"
                  @click="pagination(100, alcoholicType)"
                >
                  100 {{ $t("best_product.unit") }}
                </li>
                <li
                  :class="{active: pageTab == 200}"
                  @click="pagination(200, alcoholicType)"
                >
                  200 {{ $t("best_product.unit") }}
                </li>
              </ul>
              <p class="text">
                <span>{{ $t("best_product.highSale") }}</span> |
                <span>{{ $t("best_product.highest") }}</span>
              </p>
            </div>
            <div class="row">
              <div
                v-for="(item, index) in alcoholicProductPagination"
                :key="index"
                class="col-lg-6 col-md-6 col-12"
              >
                <ProductItemBest
                  :product="item"
                  :idx="index"
                />
              </div>
            </div>
          </div>
        </b-tab>
      </b-tabs>
    </div>
    <LoadingPage
      v-if="loading"
    />
  </section>
</template>

<script>
    import ProductItemBest from '../components/ProductItemBest'
    import LoadingPage from '../components/LoadingPage'
    export default {
        components: {
            ProductItemBest,
            LoadingPage,
        },
        data() {
            return {
                tabIndex: 0,
                tabIndexType: 0,
                loading: true,
                open: false,
                url: window.location.origin,
                maxProduct: 1000,
                pageTab: 50,
                products: [],
                industrialType: 1,
                miscellaneousType: 2,
                alcoholicType: 3,
                industrialProductPagination: [],
                miscellaneousProductPagination: [],
                alcoholicProductPagination: [],
            }
        },
        computed: {
            industrialProduct() {
                return this.groupByType(this.products, 1)
            },
            miscellaneousProduct() {
                return this.groupByType(this.products, 2)
            },
            alcoholicProduct() {
                return this.groupByType(this.products, 3)
            },
        },
        watch: {
            products(n, o) {
                if (n) {
                    //first pagination
                    this.pagination(this.pageTab, this.industrialType)
                }
            },
            tabIndexType(n, o) {
                this.pageTab = 50
                switch (n) {
                    case 1:
                        this.pagination(this.pageTab, this.industrialType)
                        break;
                    case 2:
                        this.pagination(this.pageTab, this.miscellaneousType)
                        break;
                    case 3:
                        this.pagination(this.pageTab, this.alcoholicType)
                        break;
                    default:
                        break;
                }
            }
        },
        mounted() {
            this.getBestProduct(this.maxProduct)
        },
        methods: {
            getBestProduct(maxProduct) {
                this.loading = true
                axios
                    .get(apiURL + "/product/best/" + maxProduct)
                    .then(res => {
                        this.loading = false
                        this.products = res.data.data
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            showRegularDelivery() {
                this.open = !this.open
            },
            closeRDWindow(status) {
                this.open = status
            },
            groupByType(obj, type) {
                let result = []
                if (obj != null) {
                    for(let i = 0; i < obj.length; i++) {
                        if (obj[i].productTp == type) {
                            result.push(obj[i])
                        }
                    }
                }
                return result
            },
            pagination(number, type) {
                this.pageTab = number
                if (type == this.industrialType) {
                    this.industrialProductPagination = this.industrialProduct.slice(0, number)
                } else if (type == this.miscellaneousType) {
                    this.miscellaneousProductPagination = this.miscellaneousProduct.slice(0, number)
                } else if (type == this.alcoholicType) {
                    this.alcoholicProductPagination = this.alcoholicProduct.slice(0, number)
                }
            },
        }
    }
</script>
<style lang="scss">
    @import '/css/tabs.scss';
    .best-product {
        .no-product-notication {
            margin: 0;
            background: #f1f1f1;
            text-align: center;
            padding: 15px 20px;
            border-radius: 5px;
            color: #868181;
        }
        font-size: 14px;
        padding: 0 0 50px;
        &__box {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
            &--top {
                margin-bottom: 10px;
            }
            &--no {
                font-size: 18px;
                font-weight: bold;
                color: #ef4130;
                border-bottom: 2px solid #ef4130;
                display: inline-block;
                margin-bottom: 0;
                span {
                    font-size: 30px;
                }
            }
            &--tick {
                margin: 0;
                padding: 0;
                padding-left: 95px;
                li {
                    border: 1px solid #ccc;
                    padding: 3px 10px;
                    color: #999;
                    cursor: pointer;
                    margin-left: 10px;
                    transition: all 0.3s;
                    &:hover {
                        opacity: 0.8;
                    }
                    img {
                        width: 10px;
                        filter: grayscale(1);
                        margin-right: 5px;
                    }
                }
            }
        }
        &__tab {
            ul {
                margin: 0;
                padding: 0;
                li {
                    display: flex;
                    align-items: center;
                    margin: 0 25px;
                    cursor: pointer;
                    padding-bottom: 10px;
                    text-transform: uppercase;
                    font-size: 15px;
                    &.active {
                        color: #ef4130;
                        font-weight: bold;
                        border-bottom: 2px solid #ef4130;
                    }
                    span {
                        display: block;
                        &.img {
                            margin-right: 7px;
                            margin-top: -7px;
                            img {
                                width: 20px;
                                height: auto;
                                filter: opacity(0.5);
                            }
                        }
                    }
                }
            }
        }
        &__paging {
            margin: 30px 0;
            ul, p {
                margin: 0;
                padding: 0;
            }
            ul {
                li {
                    border: 1px solid #ccc;
                    padding: 5px 25px;
                    border-radius: 20px;
                    margin-right: 10px;
                    color: #999;
                    cursor: pointer;
                    &.active {
                        color: #ef4130;
                        border: 1px solid #ef4130;
                    }
                    &:hover {
                        color: #ef4130;
                        border: 1px solid #ef4130;
                    }
                }
            }
            .text {
                margin-left: 15px;
                span {
                    cursor: pointer;
                    &:hover {
                        color: #ef4130;
                        font-weight: bold;
                    }
                }
            }
        }
        &__info {
            text-align: left;
            display: flex;
            align-items: center;
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
            width: calc(100% - 130px);
            p {
                margin-bottom: 0;
            }
            p.density {
                margin: 5px 0 0;
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
            h3 {
                margin: 0;
                font-size: 15px;
                a {
                    color: #555555;
                }
                img {
                    width: 8px;
                    margin-top: -4px;
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .best-product {
            &__paging {
                .text {
                    width: 100%;
                    margin: 10px 0 0;
                }
            }
            &__box {
                &--tick {
                    padding-left: 20px;
                }
            }
            &__tab {
                ul {
                    li {
                        font-size: 14px;
                        margin: 0;
                        width: 33.3%;
                        justify-content: center;
                    }
                }
            }
        }
    }
</style>
