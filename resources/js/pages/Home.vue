<template>
  <div class="container">
    <div class="banner-card">
      <div
        v-if="getSliders.length > 0"
        class="card-content"
      >
        <Carousel
          :settings="settings"
          :breakpoints="breakpoints"
        >
          <Slide
            v-for="(banner, index) in getSliders"
            :key="index"
          >
            <p class="slide-item">
              <a
                :href="banner.landing_url"
                target="_blank"
              ><img
                class="img-slide"
                :src="banner.image_url"
                :alt="banner.mall_cd"
              ></a>
            </p>
          </Slide>
          <template #addons>
            <navigation />
            <pagination />
          </template>
        </Carousel>
      </div>
    </div>
    <div class="slide-content">
      <div class="title">
        <router-link to="/recommend-product">
          {{ $t('home.storeSize') }}
        </router-link>
      </div>
      <div v-if="storeSize.length > 0">
        <Carousel
          :settings="settings"
          :breakpoints="breakpointStore"
        >
          <Slide
            v-for="(store, index) in storeSize"
            :key="index"
          >
            <div class="carousel__item">
              <router-link
                :to="
                  '/recommend-product/' +
                    store.bundleStoresizeId
                "
              >
                <div class="card-title">
                  <h2>
                    <router-link
                      :to="
                        '/recommend-product/' +
                          store.bundleStoresizeId
                      "
                    >
                      {{ store.bundleStoresizeNm }}
                    </router-link>
                  </h2>
                  <span>{{ $t('home.under') }} {{ store.storeScale }}{{ $t('home.pyeong') }}</span>
                </div>
                <div class="card-content">
                  <p class="content-item-1">
                    {{ $t('home.storeSize') }}
                  </p>
                  <p class="content-item-2">
                    {{ store.largeCategoryNms.join(',') }}
                  </p>
                </div>
              </router-link>
            </div>
          </Slide>
          <template #addons>
            <navigation />
          </template>
        </Carousel>
      </div>
    </div>
    <div class="special-discount">
      <h2 class="special-discount__title">
        {{ $t('home.promotion') }}
      </h2>
      <div v-if="productDiscount.length > 0">
        <Carousel
          :settings="settings"
          :breakpoints="breakpointDiscount"
        >
          <Slide
            v-for="(item, index)
              in productDiscount"
            :key="index"
          >
            <Product
              :product="item"
              :product-in-cart="productsInCart"
            />
          </Slide>
          <template #addons>
            <navigation />
          </template>
        </Carousel>
      </div>
    </div>
    <LoadingPage v-if="loading" />
  </div>
</template>

<script>
    import "vue3-carousel/dist/carousel.css";
    import { Carousel, Slide, Pagination, Navigation } from "vue3-carousel";
    import { countNumber } from "../mixins/countNumber";
    import LoadingPage from "../components/LoadingPage";
    import { mapGetters } from "vuex"
    import Product from '../components/Product'
    export default {
        name: "HomeComponent",
        components: {
            Carousel,
            Slide,
            Pagination,
            Navigation,
            LoadingPage,
            Product,
        },
        mixins: [countNumber],
        data() {
            return {
                storeSize: [],
                productDiscount: [],
                loading: true,
                settings: {
                    snapAlign: "start",
                },
                breakpoints: {
                    // 0 and up
                    0: {
                        itemsToShow: 1.5,
                        touchDrag: false,
                        mouseDrag: false,
                    },
                    // 700px and up
                    700: {
                        itemsToShow: 1.5,
                    },
                    // 1024 and up
                    1024: {
                        itemsToShow: 2,
                        mouseDrag: false,
                    },
                },
                breakpointStore: {
                    // 0 and up
                    0: {
                        itemsToShow: 2,
                        touchDrag: false,
                        mouseDrag: false,
                    },
                    // 700px and up
                    700: {
                        itemsToShow: 4,
                    },
                    // 1024 and up
                    1024: {
                        itemsToShow: 6,
                    },
                },
                breakpointDiscount: {
                    // 0 and up
                    0: {
                        itemsToShow: 2,
                        touchDrag: false,
                        mouseDrag: false,
                    },
                    // 700px and up
                    700: {
                        itemsToShow: 3,
                    },
                    // 1024 and up
                    1024: {
                        itemsToShow: 4,
                    },
                },
            };
        },
        computed: {
            ...mapGetters(['getProducts', 'getSliders']),
            productsInCart() {
                return this.getProducts.data;
            }
        },
        mounted() {
            // get margin rate
            this.$store.dispatch("getSliders")
            this.getStoreSize();
            this.getProductDiscount()
        },
        methods: {
            getStoreSize() {
                this.loading = true
                axios
                    .get(apiURL + "/product/bundle-storesize")
                    .then((res) => {
                        this.loading = false
                        this.storeSize = res.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            getProductDiscount() {
                axios
                    .get(apiURL + "/product/discount")
                    .then((res) => {
                        this.productDiscount = res.data.data;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
        },
    };
</script>

<style lang="scss" scoped>
    @import "/css/category.scss";
    .carousel__slide--visible {
        transform: rotateY(0);
    }
    .special-discount {
        margin: 50px 0;
        &__title {
            font-size: 25px;
            text-align: center;
            margin: 0 0 30px;
            font-weight: bold;
            color: #777474;
        }
        .carousel {
            text-align: initial;
            margin: 0 -15px;
        }
        .ar-product {
            margin: 0 15px;
        }
    }
    .card-content {
        .carousel {
            overflow: hidden;
            margin: 0 -5px;
        }
    }
    .banner-card {
        .carousel__item {
            min-height: 200px;
            width: 100%;
            background-color: var(--vc-clr-primary);
            color: var(--vc-clr-white);
            font-size: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .carousel__slide {
            padding: 10px 0;
        }
        button.carousel__pagination-button.carousel__pagination-button--active {
            background: #ef4130;
        }

        .carousel__pagination {
            padding: 0 10px 0 0;
        }

        .carousel__prev,
        .carousel__next {
            box-sizing: content-box;
            border: 3px solid white;
            background-color: #ef4130;
            transform: translate(0, -50%);
            top: 45%;
            svg {
                fill: #fff;
                border: none;
            }
            left: 5px;
        }
        .carousel__next {
            left: auto;
            right: 5px;
        }
        .img-slide {
            height: auto;
            width: 100%;
        }
        .slide-item {
            margin: 0 5px;
            width: 100%;
            img {
                width: 100%;
            }
        }
    }
    .slide-content {
        margin: 30px 0;
        color: #777474;
        .carousel {
            margin: 0 -10px;
        }
        a {
            color: #777474;
        }
        .title {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 30px;
            a {
                color: #777474;
            }
        }
        .carousel__item {
            padding: 20px;
            border: 1px solid #ededed;
            border-radius: 10px;

            width: 100%;
            height: 255px;
        }
        .carousel__slide {
            // margin: 10px;
            padding: 0 10px;
            -webkit-user-select: none; /* Chrome all / Safari all */
            -moz-user-select: none; /* Firefox all */
            -ms-user-select: none; /* IE 10+ */
            user-select: none; /* Likely future */
        }

        .card-title {
            text-align: right;
        }
        .card-title h2 {
            margin-bottom: 0;
            font-weight: bold;
            a {
                display: block;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                color: #777474;
                font-size: 25px;
            }
        }
        .card-title span {
            font-size: 12px;
            margin-bottom: 15px;
        }

        p.content-item-1 {
            text-align: right;
            margin-bottom: 45px;
            font-size: 12px;
        }

        p.content-item-2 {
            text-align: center;
            font-size: 11px;
            word-break: break-all;
        }
    }

    @media only screen and (max-width: 767px) {
        .special-discount {
            .carousel {
                margin: 0;
                padding-bottom: 40px;
            }
            .ar-product {
                margin: 0;
                padding: 0 5px 20px;
                border-bottom: 0;
                width: 100%;
            }
            &__title {
                font-size: 22px;
                margin: 0 0 20px;
            }
            .carousel__prev,
            .carousel__next {
                bottom: 0;
                top: auto;
                left: 45%;
            }
            .carousel__next {
                left: auto;
                right: 45%;
            }
        }
        .slide-content {
            .carousel__prev,
            .carousel__next {
                bottom: 0;
                top: auto;
                left: 45%;
            }
            .carousel__next {
                left: auto;
                right: 45%;
            }
        }
        .card-content {
            .carousel {
                margin: 0 -5px;
            }
            .slide-item {
                margin: 0;
                padding: 0 5px;
            }
        }
        .slide-content {
            padding-left: 5px;
            padding-right: 5px;
            margin: 20px 0;
            .carousel {
                padding-bottom: 60px;
            }
            .title {
                font-size: 22px;
                margin-bottom: 20px;
            }
            .carousel {
                margin: 0;
            }
            .carousel__item {
                padding: 15px;
                margin: 0 5px;
            }
        }
        .special-discount {
            margin: 30px 0 50px;
            padding-left: 5px;
            padding-right: 5px;
        }
    }
</style>
