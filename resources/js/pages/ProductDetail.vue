<template>
  <div
    id="product-detail"
    class="product-detail"
  >
    <div
      v-if="product != ''"
      class="container"
    >
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
      <div class="product-detail__main ar-product">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-12 col-12">
            <p class="product-detail__big-img img-product">
              <img
                :src="$format.imageUrl(product.productImgUrl)"
                alt="product"
              >
            </p>
            <div
              v-if="additionalImages.length > 0"
              class="product-detail__thumbnail"
            >
              <Carousel
                :settings="settings"
                :wrap-around="true"
                :breakpoints="breakpoints"
              >
                <Slide
                  v-for="(item, index) in additionalImages"
                  :key="index"
                >
                  <p>
                    <img
                      :src="item"
                      alt="thumbnail"
                      @click="changeImageSlide($event)"
                    >
                  </p>
                </Slide>
                <template #addons>
                  <div
                    v-show="isNav"
                    class="nav-slider"
                  >
                    <Navigation />
                  </div>
                </template>
              </Carousel>
            </div>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12 col-12">
            <h2 class="product-detail__name">
              {{ product.productNm }}
            </h2>
            <p class="product-detail__origin">
              {{ $t("product_detail.origin") }} {{ product.productOriginName }}
            </p>
            <div class="product-detail__info">
              <div class="d-flex align-items-center product-detail__price">
                <p class="title">
                  {{ $t("product_detail.price") }}
                </p>
                <div class="content-product d-flex flex-wrap align-items-center">
                  <span class="content-product__num">{{ $format.price(getPrice.price) }}</span>
                  <span class="currency">{{ $t("product_detail.won") }}</span>
                  <span class="ratios">{{ product.marketableSize }}</span>
                </div>
              </div>
              <div class="d-lg-flex product-detail__density">
                <div class="ar-density">
                  <div class="box-densi d-flex align-items-center">
                    <p class="title">
                      {{ $t("product_detail.prodCodeOri") }}
                    </p>
                    <p class="code">
                      {{ product.productCdName }}
                    </p>
                  </div>
                  <div class="box-densi d-flex align-items-center">
                    <p class="title">
                      {{ $t("product_detail.weight") }}
                    </p>
                    <p class="code">
                      {{ optionActive.weight }}
                    </p>
                  </div>
                  <div class="box-densi d-flex align-items-center">
                    <p class="title">
                      {{ $t("product_detail.volume") }}
                    </p>
                    <p class="code">
                      {{ optionActive.volume }}
                    </p>
                  </div>
                </div>
                <div class="content-product d-flex align-items-center">
                  <p class="content-product__box">
                    <span class="box-img"><img
                      src="/img/icon/icon_truck_detail.svg"
                      alt="truck"
                    ></span>
                    <span class="text">{{ $t("product_detail.direct") }}</span>
                  </p>
                  <p class="content-product__box">
                    <span class="box-img"><img
                      src="/img/icon/icon_de_detail.svg"
                      alt="de"
                    ></span>
                    <span class="text">{{ $t("product_detail.store") }}</span>
                  </p>
                  <p class="content-product__box">
                    <span class="box-img"><img
                      src="/img/icon/icon_star_detail.svg"
                      alt="star"
                    ></span>
                    <span class="text">{{ $t("product_detail.special") }}</span>
                  </p>
                  <p class="content-product__box">
                    <span class="box-img"><img
                      src="/img/icon/icon_fly_detail.svg"
                      alt="fly"
                    ></span>
                    <span class="text">{{ $t("product_detail.body") }}</span>
                  </p>
                </div>
              </div>
              <div class="d-flex product-detail__option">
                <p class="title">
                  {{ $t("product_detail.saleUn") }}
                </p>
                <div class="content-product d-flex align-items-center">
                  <p
                    v-for="(item, idx) in product.options"
                    :key="idx"
                    :class="['content-product__option', item.optionId == compareOptionId ? 'active' : '']"
                    @click="chooseOption(item)"
                  >
                    {{ item.optionName }}
                  </p>
                </div>
              </div>
              <div class="d-flex product-detail__num-price">
                <p
                  class="title"
                  v-html="$t('product_detail.order')"
                />
                <div class="d-flex flex-wrap content-product">
                  <div class="gr-left">
                    <div class="choose-number">
                      <div class="choose-number__number d-flex align-items-center justify-content-center">
                        <span
                          class="reduction"
                          @click="decreaseNumber"
                        >&#8722;</span>
                        <input
                          v-model="quantity"
                          type="number"
                          min="0"
                          class="no"
                        >
                        <span
                          class="increment"
                          @click="increaseNumber($event, maxQuantity)"
                        >&#43;</span>
                      </div>
                      <p class="noted-num">
                        {{ $t("product_detail.maximum") }} {{ maxQuantity }}{{ $t("product_detail.canbe") }}
                      </p>
                    </div>
                  </div>
                  <div class="computed-price">
                    <div class="computed-price__title">
                      {{ $t("product_detail.amountTobe") }}
                    </div>
                    <div class="computed-price__price">
                      <span>{{ $format.price(totalPrice) }}</span>{{ $t("product_detail.won") }}
                    </div>
                    <p class="computed-price__noted">
                      <span class="d-none">{{ $t("product_detail.noOrder") }}</span> {{ $t("product_detail.amountAvail") }} {{ $format.price(creditAmount) }} {{ $t("product_detail.won") }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="d-flex product-detail__num-price">
                <p class="title" />
                <div class="d-flex content-product">
                  <div
                    class="btn-payment red"
                    @click="addToCart($event, product, product.productId, product.image, compareOptionId, quantity, shippingMethod)"
                  >
                    <p>
                      <span>{{ $t("product_detail.addCart") }}</span>
                      <span class="btn-payment__price">{{ $format.price(totalPrice) }}</span>
                    </p>
                  </div>
                  <router-link
                    :to="'/shopping-cart'"
                    class="btn-payment"
                  >
                    <div>
                      {{ $t("product_detail.goCart") }}
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="product-detail__shipping">
        <div class="additional-contents">
          <div
            v-for="(item, index) in additionalContents"
            :key="index"
          >
            <p>
              <img
                :src="item"
                alt="content"
              >
            </p>
          </div>
        </div>
        <h3 class="title">
          {{ $t("product_detail.shipInfo") }}
        </h3>
        <div class="shipping-content">
          <dl class="d-flex align-items-lg-center">
            <dt>
              <img
                src="/img/icon/icon_truck_detail.svg"
                alt=""
              >
            </dt>
            <dd>
              경상북도 포항시 일부 지역은 구매 금액의 총액이 <span>20만원 이상</span>인 경우 직접 배송해드립니다. (해당 지역에서 20만원 미만을 구매하신 경우의 배송비는 7,000원)<br>
              직배송 지역 : 경상북도 포항시 일부 지역 (상세 지역은 고객센터로 문의바랍니다. 1566-3000)<br>
              직배송 마감 : <span>예약제</span>로 운영하며 상품별로 마감시간이 상이합니다.<br>
              미입금/미결제 상태에서는 주문 상품을 출고하지 않습니다. 출고 <span>3시간</span> 전까지 결제를 바랍니다.
            </dd>
          </dl>
          <dl class="d-flex align-items-lg-center">
            <dt>
              <img
                src="/img/icon/icon_de_detail.svg"
                alt=""
              >
            </dt>
            <dd>
              배송비가 부담되시는 경우, 고객님께서 직접 <span>물류센터</span>로 오셔서 상품을 <span>수령</span>하실 수 있습니다.<br>
              수령 장소 : 본사 1물류센터, 포항시 포항구 포항면 포항읍 000<br>
              수령 시간 : 평일 오전 10 ~ 오후 4 / 토요일 오전 10시 ~ 오후 2시 (점심시간 12:00 ~ 13:00, 공휴일 및 일요일은 수령 불가)
            </dd>
          </dl>
          <dl class="d-flex align-items-lg-center">
            <dt>
              <img
                src="/img/icon/icon_box_detail.svg"
                alt=""
              >
            </dt>
            <dd>
              주소지가 불명이거나 연락 두절 시에는 반송처리되며, 고객님께서 <span>왕복 배송료</span>를 부담하시게 됩니다. (냉장, 냉동 상품의 경우에 반송이 되면 전량 폐기처분하며 교환 및 환불이<span> 불가</span>합니다.)<br>
              냉장, 냉동 제품을 직배송 및 바로드림(방문수령)으로 신청하시는 경우에 (아이스 박스가 아닌) ‘일반 포장’을 제공합니다. <br>단순 변심에 의해 냉장, 냉동 상품이 반송되는 경우에도 (재판매 불능 및 훼손 등으로) 교환 및 반품이 불가합니다. (아이스 팩을 포함하고 있는) ‘아이스 박스’는 상품의 부피에 따라 ‘아이스 박스’의 크기와 수량을<span> 자동</span>으로 적용합니다.
            </dd>
          </dl>
        </div>
      </div>

      <div class="product-detail__shipping return">
        <h3 class="title">
          취소 및 반품
        </h3>
        <div class="shipping-content">
          <dl class="d-flex align-items-lg-center">
            <dt>
              <img
                src="/img/icon/icon_nope_detail.svg"
                alt=""
              >
            </dt>
            <dd>
              (일부 상품에 대해) 3개월 단위로 반품이 가능하며 반품 일정 안내는 공지사항을 참고바랍니다.<br>
              예) 제조사 000, 스낵류에 대해 4~6월 구매 상품은 7월에 반품<br>
              취소는 ‘장바구니 ~ 예약 중’까지 가능하며, <span>‘배송 준비중’</span>부터는 취소가 불가합니다. <br>상품의 하자로 판단되는 경우에는 1566-3000 고객센터로 문의바랍니다.
            </dd>
          </dl>
        </div>
      </div>
      <div class="product-detail__relation">
        <h3 class="title">
          {{ $t("product_detail.sameCate") }}
          <span>{{ $t("product_detail.most") }}</span>
        </h3>
        <div
          v-if="bestProducts.length > 0"
          class="relation"
        >
          <Carousel
            :settings="settings"
            :breakpoints="breakpointsRelation"
          >
            <Slide
              v-for="(item, index) in bestProducts"
              :key="index"
            >
              <div class="relation__box">
                <router-link :to="'/product-detail/' + item.productId">
                  <p class="relation__img">
                    <img
                      :src="$format.imageUrl(item.productImg)"
                      alt=""
                    >
                  </p>
                  <p class="relation__price">
                    {{ $format.price(calculatePrice(item.productTp, item, getMarginRates, getStoreInfos).price) }} {{ $t("product.won") }}
                  </p>
                  <p class="relation__name">
                    {{ item.productNm }}
                  </p>
                </router-link>
              </div>
            </Slide>
            <template #addons>
              <Navigation />
            </template>
          </Carousel>
        </div>
      </div>
      <div class="product-detail__content">
        <h3 class="title">
          {{ $t("product_detail.prodDetail") }}
        </h3>
        <div class="row">
          <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 col-12">
            <div :class="['content-toggle-product',addClassToggleContent]">
              <div class="description-product">
                <div v-html="product.productDs" />
              </div>
              <div class="product-detail__memo memo">
                <div class="memo__title">
                  {{ $t("product_detail.prodNoti") }}
                </div>
                <dl>
                  <dt>{{ $t("product_detail.prodName") }}</dt>
                  <dd>{{ product.productNm }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.prodCode") }}</dt>
                  <dd>{{ $t("product_detail.please") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.type") }}</dt>
                  <dd>{{ $t("product_detail.other") }}</dd>
                </dl>

                <dl>
                  <dt>{{ $t("product_detail.capacity") }}</dt>
                  <dd>{{ $t("product_detail.totalCont") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.raw") }}</dt>
                  <dd>{{ $t("product_detail.see") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.nutrition") }}</dt>
                  <dd>{{ $t("product_detail.see") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.producer") }}</dt>
                  <dd>{{ $t("product_detail.dongwon") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.dateOf") }}</dt>
                  <dd>{{ $t("product_detail.expiry") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.precaution") }}</dt>
                  <dd>{{ $t("product_detail.forProd") }}</dd>
                </dl>
                <dl>
                  <dt>{{ $t("product_detail.asManager") }}</dt>
                  <dd>{{ $t("product_detail.customer") }}</dd>
                </dl>
              </div>
            </div>
            <div
              :class="['toggle-content', addClassToggleContent]"
              @click="showProductContent"
            >
              {{ $t("product_detail.seeMore") }}
              <img
                src="/img/icon/down-arrow.svg"
                alt=""
              >
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-12">
            <div :class="['box-summary', addClassFixedBtn]">
              <div class="box-summary__title">
                {{ product.productNm }}
              </div>
              <p class="box-summary__option">
                {{ optionActive.optionName }}
              </p>
              <div class="box-summary__choose-number d-lg-flex align-items-center">
                <div class="box-summary__number d-flex align-items-center">
                  <span
                    class="reduction"
                    @click="decreaseNumber"
                  >&#8722;</span>
                  <input
                    v-model="quantity"
                    type="number"
                    min="0"
                    class="no"
                  >
                  <span
                    class="increment"
                    @click="increaseNumber"
                  >&#43;</span>
                </div>
                <p class="noted-num">
                  {{ $t("product_detail.maximum") }} {{ maxQuantity }}{{ $t("product_detail.canbe") }}
                </p>
              </div>
              <div class="box-summary__status">
                <span v-html="$t('product_detail.alertOrder')" />
                <!-- ‘주문 가능금액’ 부족으로 <span>주문</span>이 <span>불가</span>합니다. -->
              </div>
              <div class="box-summary__price">
                <div class="computed-price">
                  <div class="computed-price__title">
                    {{ $t("product_detail.amountTobe") }}
                  </div>
                  <div class="computed-price__price">
                    <span>{{ $format.price(totalPrice) }}</span>{{ $t("product_detail.won") }}
                  </div>
                  <p class="computed-price__noted">
                    {{ $t("product_detail.amountAvail") }} {{ $format.price(creditAmount) }} {{ $t("product_detail.won") }}
                  </p>
                </div>
              </div>
              <!-- <div class="btn-payment">
                {{ $t("product_detail.toCharge") }}
              </div> -->
              <div class="btn-payment red">
                <p>
                  <span>{{ $t("product_detail.addCart") }}</span>
                  <span class="btn-payment__price">{{ $format.price(totalPrice) }}</span>
                </p>
              </div>
            </div>
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
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import { countNumber } from "../mixins/countNumber";
import LoadingPage from '../components/LoadingPage'

import { mapGetters } from "vuex"
import { addToCart } from '../mixins/addToCart'
import { breadcrumbMixin } from '../mixins/breadcrumbMixin'
import { calculatePriceMixin } from '../mixins/calculatePriceMixin'

export default  {
    components: {
        Carousel,
        Slide,
        Navigation,
        LoadingPage
    },
    mixins: [
        countNumber,
        addToCart,
        breadcrumbMixin,
        calculatePriceMixin,
    ],
    data () {
        return {
            productId: this.$route.params.id,
            isNav: false,
            isFixedButton: false,
            product: {},
            bestProducts: [],
            additionalImages: [],
            additionalContents: [],
            creditAmount: 0,
            isActiveContent: false,
            loading: true,
            settings: {
                snapAlign: 'start',
                mouseDrag: false,
                touchDrag: false,
            },
            breakpoints: {
                // 0 and up
                0: {
                    itemsToShow: 3,
                },
                // 700px and up
                700: {
                    itemsToShow: 3,
                },
                // 1024 and up
                1024: {
                    itemsToShow: 5,
                },
            },
            breakpointsRelation: {
                // 0 and up
                0: {
                    itemsToShow: 2,
                },
                // 700px and up
                700: {
                    itemsToShow: 4,
                },
                // 1024 and up
                1024: {
                    itemsToShow: 8,
                },
            },
            optionActive: {},
            currentOption: {},
            compareOptionId: 0,
            price: 0,
            maxQuantity: 0,
        }
    },
    computed: {
        ...mapGetters(['getProducts', 'getMarginRates', 'getStoreInfos']),
        shippingMethod() {
            if (this.getStoreInfos.defaultShippingMethod != null) {
                return this.getStoreInfos.defaultShippingMethod
            }
            return 2 // default delivery
        },
        getPrice() {
            return this.calculatePrice(this.product.productTp, this.currentOption, this.getMarginRates, this.getStoreInfos)
        },
        getBreadcrumb() {
            if (Object.keys(this.product).length != 0) {
                return this.breadcrumb(this.product)
            }
            return []
        },
        addClassToggleContent() {
            return {'active': this.isActiveContent}
        },
        addClassFixedBtn() {
            return {'active': this.isFixedButton}
        },
        totalPrice() {
            return this.quantity * this.getPrice.price
        },
        productInCart() {
            return this.getProducts.data;
        },
    },
    watch: {
        productInCart(n, o) {
            this.checkQuantity(this.product)
        },
        $route (to){
            if (to.params.id != undefined) {
                this.getInfomationProduct(to.params.id)
                // scroll to top
                window.scrollTo(0,0)
            }
        },
        product(n, o) {
            this.updateProduct(n)
        },
    },
    async mounted () {
        await this.getInfomationProduct(this.productId);
        window.addEventListener('scroll', this.handleScroll)
    },
    created () {
        this.getStoreInfo()
    },
    methods: {
        getStoreInfo() {
            axios.get(apiURL + "/user/credit")
            .then(response => {
                this.creditAmount = response.data.data.order_credit_amount
            })
        },
        //check current quantity product
        checkQuantity(product) {
            this.quantity = 0
            if (this.productInCart != null) {
                for(let i in this.productInCart) {
                    if (product.productId == this.productInCart[i].ds_product_id && this.compareOptionId == this.productInCart[i].ds_option_id) {
                        this.quantity = this.productInCart[i].quantity
                    }
                }
            }
        },
        //get infomation product
        getInfomationProduct(productId) {
            this.loading = true
            axios
                .get(apiURL + "/product/product/" + productId)
                .then((res) => {
                    this.product = res.data.data
                    let categoryId = this.product.mediumCategory
                    if (categoryId == null) {
                        categoryId = this.product.largeCategory
                    }
                    this.getBestProduct(categoryId)
                    this.updateProduct(this.product)
                    this.chooseOption(this.product.options[0])
                })
                .catch(error => {
                    console.log(error);
                })
        },
        //show product detail
        showProductContent() {
            this.isActiveContent = !this.isActiveContent;
        },
        //fixed Payment button in mobile
        handleScroll() {
            if ($(".product-detail__shipping").length > 0){
                let offsetTop = $(".product-detail__shipping").offset().top;
                let hFooter = $("footer").offset().top - $("footer").outerHeight();
                let scroll = $(window).scrollTop();
                if (scroll >= offsetTop && scroll <= hFooter) {
                    this.isFixedButton = true
                } else {
                    this.isFixedButton = false
                }
            }
        },
        chooseOption(option) {
            this.currentOption = option
            this.compareOptionId = option.optionId
            this.price = option.supplyUnitPrice
            this.maxQuantity = option.maxOrder
            let arrProduct = this.productInCart
            let flag = 0
            for(let i in arrProduct) {
                if (this.product.productId == arrProduct[i].ds_product_id && this.compareOptionId == arrProduct[i].ds_option_id) {
                    flag = 1
                    this.quantity = arrProduct[i].quantity
                }
            }
            if (flag == 0) {
                this.quantity = 0
            }
        },
        changeImageSlide(e) {
            let _this = $(e.target)
            let srcImage = _this.attr('src')
            $('.product-detail__big-img img').attr('src', srcImage)
        },
        getBestProduct(categoryId = null) {
            axios.get(apiURL + "/product/best/20", {
                    params: {
                        category_id: categoryId,
                    }
                })
                .then(res => {
                    this.bestProducts = res.data.data
                })
                .catch(err => {
                    console.log(err)
                })
        },
        updateProduct(product) {
            this.additionalImages = []
            this.additionalContents = []
            this.optionActive = product.options[0];
            this.compareOptionId = product.options[0].optionId;
            this.currentOption = product.options[0];
            this.price = product.options[0].supplyUnitPrice;
            this.maxQuantity = product.options[0].maxOrder;
            this.checkQuantity(product)
            this.loading = false
            // add images
            if (product.image != null && product.image != "") {
                this.additionalImages.push(product.image)
            }
            if (product.productAddImgUrlOne != null && product.productAddImgUrlOne != "") {
                this.additionalImages.push(product.productAddImgUrlOne)
            }
            if (product.productAddImgUrlTwo != null && product.productAddImgUrlTwo != "") {
                this.additionalImages.push(product.productAddImgUrlTwo)
            }
            if (product.productAddImgUrlThree != null && product.productAddImgUrlThree != "") {
                this.additionalImages.push(product.productAddImgUrlThree)
            }
            if (product.productAddImgUrlFour != null && product.productAddImgUrlFour != "") {
                this.additionalImages.push(product.productAddImgUrlFour)
            }
            if (product.productAddImgUrlFive != null && product.productAddImgUrlFive != "") {
                this.additionalImages.push(product.productAddImgUrlFive)
            }
            if (this.additionalImages.length > 5) {
                this.isNav = true
            }
            // add contents
            if (product.productContentsUrlOne != null && product.productContentsUrlOne != "") {
                this.additionalContents.push(product.productContentsUrlOne)
            }
            if (product.productContentsUrlTwo != null && product.productContentsUrlTwo != "") {
                this.additionalContents.push(product.productContentsUrlTwo)
            }
            if (product.productContentsUrlThree != null && product.productContentsUrlThree != "") {
                this.additionalContents.push(product.productContentsUrlThree)
            }
            if (product.productContentsUrlFour != null && product.productContentsUrlFour != "") {
                this.additionalContents.push(product.productContentsUrlFour)
            }
            if (product.productContentsUrlFive != null && product.productContentsUrlFive != "") {
                this.additionalContents.push(product.productContentsUrlFive)
            }
        },
    }
}
</script>


<style lang="scss" scoped>
    .product-detail {
        padding: 0 0 50px;
        .toggle-content {
            border: 1px solid #ccc;
            padding: 10px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            margin-top: 30px;
            transition: all 0.3s;
            &.active {
                img {
                    transform: rotate(180deg);
                }
            }
            &:hover {
                opacity: 0.8;
            }
            img {
                width: 15px;
                margin-top: -3px;
                margin-left: 5px;
            }
        }
        .content-toggle-product {
            height: 400px;
            overflow: hidden;
            position: relative;
            &.active {
                height: auto;
                &:after {
                    display: none;
                }
            }
            &:after {
                background: linear-gradient(0deg, white, transparent 100%) ;
                position: absolute;
                left: 0;
                bottom: 0;
                height: 70px;
                content: "";
                width: 100%;
            }
        }
        &__content {
            .description-product {
                box-sizing: border-box;
                img {
                    width: 100%;
                }
            }
            .memo {
                margin-top: 40px;
                &__title {
                    font-weight: bold;
                    color: #757575;
                    font-size: 25px;
                    margin-bottom: 15px;
                }
                dl {
                    font-size: 14px;
                    border-bottom: 1px solid #ccc;
                    display: flex;
                    align-items: center;
                    padding: 15px 0;
                    margin: 0;
                    &:last-child {
                        border-bottom: 0;
                    }
                    dt, dd {
                        margin: 0;
                    }
                    dt {
                        width: 20%;
                    }
                    dd {
                        width: 80%;
                        padding-left: 25px;
                    }
                }
            }
            h3.title {
                font-weight: bold;
                color: #757575;
                font-size: 25px;
                margin-bottom: 15px;
            }
            .box-summary {
                border: 1px solid #e6e6e6;
                padding: 15px;
                background: #f5f5f5;
                font-size: 14px;
                position: sticky;
                top: 10px;
                right: 0;
                &__title {
                    font-weight: bold;
                }
                &__option {
                    padding: 2px 15px;
                    border: 2px solid #ef4130;
                    margin: 15px 0;
                    cursor: pointer;
                    color: #ef4130;
                    display: inline-block;
                    font-weight: bold;
                }
                &__choose-number {
                    display: block;
                }
                &__number {
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
                }
                p.noted-num {
                    font-size: 11px;
                    margin-bottom: 0;
                    margin-left: 10px;
                }
                &__status {
                    width: 80%;
                    background: #fff;
                    padding: 10px;
                    text-align: center;
                    border: 1px solid #ccc;
                    margin: 38px auto;
                    span {
                        color: #ef4130;
                    }
                }
                .computed-price {
                    &__title {
                        font-weight: bold;
                    }
                    &__price {
                        text-align: right;
                        font-size: 14px;
                        span {
                            font-weight: bold;
                            color: #ef4130;
                            font-size: 30px;
                        }
                    }
                    &__noted {
                        text-align: right;
                    }
                }
                .btn-payment {
                    width: 100%;
                    padding: 10px;
                    text-align: center;
                    background: #007DB2;
                    font-size: 16px;
                    color: #fff;
                    margin-bottom: 5px;
                    margin-right: 5px;
                    cursor: pointer;
                    transition: all 0.3s;
                    &:hover {
                        opacity: 0.8;
                    }
                    &.red {
                        background: #ef4130;
                        margin-bottom: 0;
                    }
                    p {
                        margin-bottom: 0;
                    }
                    &__price {
                        font-size: 12px;
                        padding-left: 10px;
                    }
                }
            }
        }
        &__relation {
            padding: 50px 0 70px;
            h3.title {
                font-weight: bold;
                color: #757575;
                font-size: 25px;
                margin-bottom: 10px;
                span {
                    font-size: 14px;
                }
            }
            .relation {
                &__box {
                    text-align: left;
                    margin: 0 5px;
                    a {
                        color: #757575;
                    }
                }
                &__img {
                    border: 1px solid #e6e6e6;
                    padding: 5px;
                    text-align: center;
                    height: 135px;
                    margin-bottom: 0;
                    img {
                        width: auto;
                        height: 100%;
                    }
                }
                &__price {
                    font-size: 14px;
                    font-weight: bold;
                    margin: 5px 0;
                }
                &__name {
                    font-size: 14px;
                    margin-bottom: 0;
                }
            }
        }
        &__shipping {
            padding: 50px 0;
            &.return {
                padding: 0;
                dl {
                    &:last-child {
                        border-bottom: 0;
                    }
                }
            }
            .additional-contents {
                img {
                    max-width: 100%;
                }
            }
            h3.title {
                font-weight: bold;
                color: #757575;
                font-size: 25px;
                margin-bottom: 10px;
            }
            dl {
                border-bottom: 1px solid #ccc;
                padding: 10px 0;
                margin-bottom: 0;
                dt {
                    width: 80px;
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 1px solid #ccc;
                    img {
                        height: 30px;
                        width: auto;
                    }
                }
                dd {
                    width: calc(100% - 80px);
                    font-size: 14px;
                    line-height: 20px;
                    margin-bottom: 0;
                    padding-left: 15px;
                    span {
                        color: #ef4130;
                        font-weight: bold;
                    }
                }
            }
        }
        &__point {
            margin-top: 5px;
            p.point {
                margin-bottom: 0;
                font-weight: bold;
                color: #999999;
            }
        }
        &__big-img {
            border: 1px solid #ccc;
            margin: 0 5px 10px;
            padding: 10px;
            height: 400px;
            img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }
        &__thumbnail {
            p {
                border: 1px solid #ccc;
                img {
                    width: 100%;
                }
            }
            .carousel__slide {
                p {
                    margin: 0 5px;
                    height: 80px;
                    width: 100%;
                    img {
                        width: 100%;
                        height: 100%;
                        object-fit: contain;
                    }
                }
            }
        }
        &__main {
            position: relative;
            .noted {
                margin: 0;
                span {
                    background: #45bae8;
                    color: #fff;
                    font-size: 11px;
                    padding: 3px 4px;
                    border-radius: 4px 0 4px 0;
                    margin-right: 5px;
                    &.green {
                        background: #2bbc43;
                    }
                }
            }
        }
        &__name {
            font-size: 25px;
            font-weight: bold;
            margin: 0 0 15px;
        }
        &__origin {
            font-size: 14px;
        }
        &__info {
            font-size: 14px;
            & > div {
                margin-bottom: 20px;
            }
            p.title {
                width: 70px;
                margin-bottom: 0;
            }
            .content-product {
                width: calc(100% - 70px);
                &__num {
                    font-size: 30px;
                    font-weight: bold;
                }
                span {
                    &.currency {
                        padding: 0 10px;
                    }
                }
                &__box {
                    border: 1px solid #ccc;
                    background: #f5f5f5;
                    text-align: center;
                    font-size: 12px;
                    width: 60px;
                    height: 60px;
                    margin-right: 5px;
                    display: flex;
                    flex-wrap: wrap;
                    align-content: center;
                    margin-bottom: 0;
                    span {
                        display: block;
                        width: 100%;
                        color: #797979;
                        &.box-img {
                            img {
                                height: 25px;
                                width: auto;
                            }
                        }
                        &.text {
                            font-size: 11px;
                            margin-top: 5px;
                        }
                    }
                }
                .choose-number {
                    border: 1px solid #ccc;
                    background: #f5f5f5;
                    padding: 15px 20px 10px;
                    display: inline-block;
                    &__number {
                        input {
                            width: 40px;
                            text-align: center;
                            outline: none;
                            border: 1px solid #ccc;
                            border-radius: 0;
                            height: 25px;
                            margin: 0 5px;
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
                    }
                    p.noted-num {
                        font-size: 11px;
                        margin-bottom: 0;
                        margin-top: 7px;
                    }
                }
                .computed-price {
                    text-align: right;
                    padding-left: 50px;
                    &__title {
                        font-size: 14px;
                        font-weight: bold;
                    }
                    p {
                        margin-bottom: 0;
                    }
                    &__price {
                        line-height: 32px;
                        span {
                            font-weight: bold;
                            color: #ef4130;
                            font-size: 30px;
                        }
                    }
                    &__noted {
                        font-size: 12px;
                        span {
                            color: #ef4130;
                        }
                    }
                }
            }
            .box-densi {
                p {
                    &.code {
                        margin-bottom: 0;
                    }
                }
            }
            .btn-payment {
                background: #007DB2;
                color: #fff;
                border-radius: 2px;
                height: 70px;
                padding: 0 60px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                margin-left: 10px;
                cursor: pointer;
                transition: all 0.3s;
                &:hover {
                    opacity: 0.8;
                }
                &:first-child {
                    margin-left: 0;
                }
                &.red {
                    background: #ef4130;
                }
                &__price {
                    display: block;
                }
                p {
                    margin-bottom: 0;
                }
            }
        }
        &__density {
            .ar-density {
                width: 30%;
            }
            .content-product {
                width: 70%;
            }
        }
        &__option {
            .content-product {
                &__option {
                    padding: 2px 20px;
                    border: 1px solid #ccc;
                    margin-left: 10px;
                    margin-bottom: 0;
                    cursor: pointer;
                    color: #797979;
                    &.active {
                        border: 1px solid #ef4130;
                        color: #ef4130;
                        font-weight: bold;
                    }
                    &:first-child {
                        margin-left: 0;
                    }
                }
            }
        }
        .relation .carousel__slide {
            align-items: flex-start;
        }
    }

    @media only screen and (max-width: 991px) {
        .product-detail {
            &__density {
                .ar-density {
                    width: 100%;
                }
                .content-product {
                    width: 100%;
                    padding-left: 70px;
                }
            }
            &__option {
                .content-product {
                    &__option {
                        padding: 4px 10px;
                        margin-left: 5px;
                    }
                }
            }
            &__content {
                .box-summary {
                    margin-top: 15px;
                    p.noted-num {
                        margin-left: 0;
                        margin-top: 5px;
                    }
                    &__status {
                        width: 100%;
                        margin: 20px 0;
                    }
                }
            }
            &__point {
                p.point {
                    font-size: 14px;
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .product-detail {
            &__shipping {
                padding: 30px 0;
            }
            .toggle-content {
                margin-top: 15px;
            }
            &__num-price {
                .content-product {
                    justify-content: space-between;
                }
            }
            &__info {
                .content-product {
                    .computed-price {
                        padding-left: 0;
                        text-align: right;
                        width: 48%;
                    }
                    .gr-left {
                        width: 50%;
                    }
                }
                .btn-payment {
                    width: 48%;
                    margin-left: 0;
                    height: 55px;
                    padding: 0;
                    text-align: center;
                    justify-content: center;
                }
                > div {
                    margin-bottom: 15px;
                }
            }
            &__name {
                font-size: 20px;
            }
            &__origin {
                margin-bottom: 5px;
            }
            &__density {
                .content-product {
                    margin-top: 15px;
                }
            }
            &__relation {
                padding: 40px 0;
                .relation__price {
                    font-size: 16px;
                    margin: 10px 0;
                }
            }
            &__big-img {
                margin: 0 0 10px;
            }
            &__thumbnail {
                overflow: hidden;
                margin: 0 -5px;
                .carousel__next {
                    transform: translateX(0);
                }
            }
            &__content {
                .memo {
                    dl {
                        dt {
                            width: 30%;
                        }
                        dd {
                            width: 70%;
                        }
                    }
                }
                .box-summary {
                    opacity: 0;
                    transition: opacity 0.5s;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    background: #fff;
                    height: auto;
                    margin-top: 0;
                    top: auto;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 10px;
                    border: none;
                    border-top: 1px solid #e6e6e6;
                    z-index: 4;
                    pointer-events: none;
                    &.active {
                        opacity: 1;
                        pointer-events: inherit;
                    }
                    &__title, &__option, &__choose-number, &__status, &__price {
                        display: none;
                    }
                    .btn-payment {
                        width: 48.5%;
                        height: 60px;
                        padding: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-wrap: wrap;
                        margin: 0;
                        p {
                            span {
                                display: block;
                                width: 100%;
                            }
                        }
                    }
                }
            }

        }
        .relation {
            margin: 0 -5px;
            .carousel__slide {
                display: block;
            }
        }
    }
</style>
