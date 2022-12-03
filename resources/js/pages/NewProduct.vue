<template>
  <div class="container">
    <div id="category">
      <div class="breadcrumbs">
        <ul>
          <li><a href="">{{ $t("new_product.home") }}</a></li>
          <li><a href="">{{ $t("new_product.newProd") }}</a></li>
        </ul>
      </div>
      <div class="category-main">
        <div class="title-category d-md-flex flex-wrap justify-content-between align-items-center">
          <h2 class="title-category__result">
            {{ $t("new_product.newProd") }} ({{ rows }})
          </h2>
          <div class="sort-filter d-md-flex justify-content-end align-items-center">
            <div class="title-category__sort d-flex align-items-center">
              <p class="title-category__sort--title">
                {{ $t("new_product.prodSort") }}
              </p>
              <ul class="d-flex align-items-center">
                <li
                  :class="horizontalClass"
                  @click="sortList(0)"
                >
                  <img
                    src="/img/icon/horizontal_arrangement.svg"
                    alt="Horizontal"
                  >
                </li>
                <li
                  :class="verticalClass"
                  @click="sortList(1)"
                >
                  <img
                    src="/img/icon/vertical_arrangement.svg"
                    alt="Horizontal"
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div :class="['row', verticalClass]">
          <div
            v-for="(item, index)
              in products"
            :key="index"
            class="item-product col-lg-3 col-md-4 col-sm-6 col-6"
          >
            <Product
              :product="item"
              :product-in-cart="productsInCart"
            />
          </div>
          <div
            v-if="products == ''"
            class="col-lg-12 col-md-12 col-12"
          >
            <p class="no-product-notication">
              {{ $t("new_product.noProd") }}
            </p>
          </div>
          <!-- <div class="pagination-template" v-else>
                        <b-pagination
                            v-model="currentPage"
                            :total-rows="rows"
                            :per-page="perPage"
                            @change="onPageChanged"
                        ></b-pagination>
                    </div> -->
        </div>
      </div>
    </div>
    <LoadingPage
      v-if="loading"
    />
  </div>
</template>

<script>
    import { countNumber } from "../mixins/countNumber"
    import LoadingPage from '../components/LoadingPage'
    import Product from '../components/Product'
    import { mapGetters } from "vuex"

    export default {
        components: {
            LoadingPage,
            Product
        },
        mixins: [countNumber],
        data() {
            return {
                sort: false,
                loading: true,
                products: [],
                rows: 0,
                perPage: 20,
                currentPage: 1,
                paginatedItems: [],
            }
        },
        computed: {
            verticalClass() {
                return {'on': this.sort}
            },
            horizontalClass() {
                return {'on': !this.sort}
            },
            ...mapGetters(['getProducts']),
            productsInCart() {
                return this.getProducts.data;
            }
        },
        watch: {
            currentPage (n, o) {
                if (n) {
                    this.paginate(this.perPage, n - 1);
                }
            },
        },
        async mounted() {
            await this.getCategoryList()
        },
        methods: {
            getCategoryList() {
                this.loading = true
                axios
                    .get(apiURL + "/product/newest")
                    .then((res) => {
                        this.products = res.data.data
                        this.rows = res.data.data.length
                        this.paginate(this.perPage, 0);
                        this.loading = false
                    })
                    .catch(error => {
                        this.loading = false
                        console.log(error);
                    })
            },
            sortList(type) {
                switch (type) {
                    case 1:
                        this.sort = true
                        break;
                    default:
                        this.sort = false
                        break;
                }
            },
            paginate(page_size, page_number) {
                let itemsToParse = this.products;
                this.paginatedItems = itemsToParse.slice(
                    page_number * page_size,
                    (page_number + 1) * page_size
                );
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import '/css/category.scss';
    #category {
        padding: 0 0 50px;
        .title-category {
            text-align: center;
            font-size: 30px;
            margin: 0 0 40px;
            color: #5b5b5b;
            border-top: 5px solid #ebebeb;
            padding-top: 25px;
            &__result {
                font-size: 30px;
                font-weight: bold;
                margin: 0;
            }
            &__sort {
                margin-left: 20px;
                &--title {
                    font-size: 16px;
                    text-transform: uppercase;
                    font-weight: bold;
                    margin: 0;
                    margin-right: 10px;
                    margin-top: 6px;
                }
                ul {
                    padding: 0;
                    margin: 0;
                    li {
                        margin: 0 5px;
                        cursor: pointer;
                        &:last-child {
                            margin-right: 0;
                        }
                        &.on {
                            img {
                                filter: brightness(0) invert(0.8);
                            }
                        }
                    }
                }
                select {
                    font-size: 14px;
                    border: 1px solid #ccc;
                    padding: 0 15px;
                    height: 30px;
                    cursor: pointer;
                    background: #fff;
                    outline: none;
                    border-radius: 2px;
                }
            }
        }
        .category-main {
            .row {
                &.on {
                    .item-product {
                        width: 50%;
                        .ar-product {
                            display: flex;
                            .img-product {
                                width: 40%;
                            }
                            &__info {
                                width: 60%;
                                padding-left: 25px;
                            }
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 991px) {
        #category {
            .category-main {
                .row {
                    &.on {
                        .item-product {
                            width: auto;
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        #category {
            padding: 0 0 50px;
            .title-category {
                font-size: 20px;
                font-weight: bold;
                margin-top: 10px;
                text-align: left;
                &__sort {
                    margin-left: 0;
                    margin-top: 20px;
                }
            }
            .category-main {
                .row {
                    &.on {
                        .item-product {
                            .ar-product {
                                .name-product {
                                    margin: 0 0 10px;
                                }
                                &__option {
                                    margin-bottom: 5px;
                                }
                                &__number {
                                    p.add-to-cart {
                                        width: 100%;
                                        margin-top: 5px;
                                    }
                                    input {
                                        width: calc(60% - 65px);
                                    }
                                }
                                .img-product {
                                    border: none;
                                    a {
                                        border: 1px solid #ededed;
                                    }
                                }
                                &__info {
                                    padding-left: 15px;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
