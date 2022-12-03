<template>
  <div class="container">
    <div id="category">
      <div class="breadcrumbs">
        <ul>
          <li
            v-for="(item, index) in getBreadcrumb"
            :key="index"
          >
            <router-link :to="item.url">
              {{ item.name }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="category-main">
        <div class="title-category d-md-flex flex-wrap justify-content-between align-items-center">
          <h2 class="title-category__result">
            {{ $t("category.total") }} {{ rows }} {{ $t("category.product") }}
          </h2>
          <div class="sort-filter d-md-flex justify-content-end align-items-center">
            <div class="title-category__sort d-flex align-items-center">
              <p class="title-category__sort--title">
                {{ $t("category.productSort") }}
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
                    alt="Vertical"
                  >
                </li>
              </ul>
            </div>
            <div class="title-category__sort d-flex align-items-center">
              <p class="title-category__sort--title">
                {{ $t("category.filter") }}
              </p>
              <select id="">
                <option value="">
                  {{ $t("category.saleQuan") }}
                </option>
                <option value="">
                  {{ $t("category.saleQuan1") }}
                </option>
                <option value="">
                  {{ $t("category.saleQuan2") }}
                </option>
                <option value="">
                  {{ $t("category.saleQuan3") }}
                </option>
              </select>
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
              {{ $t("category.noProduct") }}
            </p>
          </div>
          <div
            v-else
            class="pagination-template"
          >
            <b-pagination
              v-model="currentPage"
              :total-rows="rows"
              aria-controls="my-table"
            />
          </div>
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
    import { breadcrumbMixin } from "../mixins/breadcrumbMixin"
    import LoadingPage from '../components/LoadingPage'
    import Product from '../components/Product'
    import { mapGetters } from "vuex"
    export default {
        components: {
            LoadingPage,
            Product
        },
        mixins: [
            countNumber,
            breadcrumbMixin,
        ],
        data() {
            return {
                sort: false,
                loading: true,
                products: [],
                rows: 0,
                currentPage: 1,
                categoryId: 0,
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
            },
            getBreadcrumb() {
                if (this.products.length > 0) {
                    let breadcrumb = this.breadcrumb(this.products[0])
                    // check level category
                    let level = 0
                    for (const key in breadcrumb) {
                        if (this.$route.params.id == breadcrumb[key].code) {
                            level = breadcrumb[key].level
                        }
                    }
                    return breadcrumb.filter((item) => {
                        return item.level <= level
                    })
                }
                return []
            },
        },
        watch: {
            $route (to){
                this.categoryId = to.params.id
                this.currentPage = 1
                this.getCategoryList(this.currentPage, this.categoryId)
            },
            currentPage (n, o) {
                if (n) {
                    this.getCategoryList(n, this.categoryId);
                }
            },
        },
        async mounted() {
            this.categoryId = this.$route.params.id
            this.getCategoryList(this.currentPage, this.categoryId)
        },
        methods: {
            getCategoryList(page, categoryId) {
                this.loading = true
                axios
                    .get(apiURL + "/product/product/", {
                        params: {
                            category_id: categoryId,
                            page: page
                        }
                    })
                    .then((res) => {
                        this.products = res.data.data
                        this.rows = res.data.count
                        this.loading = false
                        var body = $("html, body");
                            body.stop().animate({scrollTop:0}, 500, 'swing', function() {
                        });
                    })
                    .catch(error => {
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
        },
    }
</script>

<style lang="scss" scoped>
    @import '/css/category.scss';
    .no-product-notication {
        margin: 0;
        background: #f1f1f1;
        text-align: center;
        padding: 15px 20px;
        border-radius: 5px;
        color: #868181;
    }
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
                font-size: 22px;
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
