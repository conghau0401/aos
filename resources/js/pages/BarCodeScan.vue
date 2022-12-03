<template>
  <section class="bar-code-scan">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li><a href="#">{{ $t("bar_code_scan.home") }}</a></li>
          <li><a href="#">{{ $t("bar_code_scan.orderRegis") }}</a></li>
        </ul>
      </div>
      <div class="bar-code-scan__main ar-product">
        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-12 col-12 bar-code-scan__left">
            <h2 class="title">
              {{ $t("bar_code_scan.orderRegis") }}
            </h2>
            <div class="row">
              <div class="col-lg-6">
                <p class="bar-code-scan__des">
                  {{ $t("bar_code_scan.orderInfo") }}
                </p>
                <div
                  class="btn-scan"
                  @click="showCamera"
                >
                  {{ $t("bar_code_scan.filming") }}
                </div>
                <div
                  v-show="isPhotoTaken"
                  class="bar-code-scan__code"
                >
                  <p>{{ $t("bar_code_scan.barcodeNum") }}</p>
                  <p class="code">
                    {{ barcode }}
                  </p>
                  <p>{{ $t("bar_code_scan.theBarcode") }}</p>
                </div>
              </div>
              <div
                v-show="isPhotoTaken"
                class="col-lg-6"
              >
                <p
                  v-if="!isNotFound"
                  class="bar-code-scan__small-img img-product"
                >
                  <img
                    :src="$format.imageUrl(product.image)"
                    alt="product"
                  >
                </p>
              </div>
            </div>
            <p class="img-code bar-code-scan__cap">
              <!-- <video
                v-show="isPhotoTaken"
                id="video-vitural"
                ref="camera"
                width="500"
                height="250"
                autoplay
              />
              <canvas
                v-show="isPhotoTaken"
                id="photoTaken"
                ref="canvas"
                :width="500"
                :height="250"
              /> -->
            </p>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12 col-12 bar-code-scan__right">
            <VideoDecode
              v-if="openWebcam"
              @barcode="getBarcode"
            />
            <div
              v-if="isNotFound"
              class="barcode-not-found"
            >
              <p>상품을 찾을 수 없습니다</p>
            </div>
            <div v-else>
              <div
                v-show="isPhotoTaken"
                class="content-product-barcode"
              >
                <h2 class="bar-code-scan__name">
                  {{ product.productNm }}
                </h2>
                <p class="bar-code-scan__origin">
                  {{ $t("bar_code_scan.origin") }} {{ product.productOriginName }}
                </p>
                <div class="bar-code-scan__info">
                  <div class="d-flex align-items-center bar-code-scan__price">
                    <p class="title">
                      {{ $t("bar_code_scan.price") }}
                    </p>
                    <div class="content-product d-flex flex-wrap align-items-center">
                      <span class="content-product__num">{{ $format.price(price) }}</span>
                      <span class="currency">{{ $t("bar_code_scan.won") }}</span>
                      <span class="ratios">{{ product.marketableSize }}</span>
                    </div>
                  </div>
                  <div class="d-lg-flex bar-code-scan__density">
                    <div class="ar-density">
                      <div class="box-densi d-flex align-items-center">
                        <p class="title">
                          {{ $t("bar_code_scan.price") }}
                        </p>
                        <p class="code">
                          {{ product.productCdName }}
                        </p>
                      </div>
                      <div class="box-densi d-flex align-items-center">
                        <p class="title">
                          {{ $t("bar_code_scan.weight") }}
                        </p>
                        <p class="code">
                          {{ optionActive.weight }}
                        </p>
                      </div>
                      <div class="box-densi d-flex align-items-center">
                        <p class="title">
                          {{ $t("bar_code_scan.volume") }}
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
                        <span class="text">{{ $t("bar_code_scan.directDeli") }}</span>
                      </p>
                      <p class="content-product__box">
                        <span class="box-img"><img
                          src="/img/icon/icon_de_detail.svg"
                          alt="de"
                        ></span>
                        <span class="text">{{ $t("bar_code_scan.centerPick") }}</span>
                      </p>
                      <p class="content-product__box">
                        <span class="box-img"><img
                          src="/img/icon/icon_star_detail.svg"
                          alt="star"
                        ></span>
                        <span class="text">{{ $t("bar_code_scan.special") }}</span>
                      </p>
                      <p class="content-product__box">
                        <span class="box-img"><img
                          src="/img/icon/icon_fly_detail.svg"
                          alt="fly"
                        ></span>
                        <span class="text">{{ $t("bar_code_scan.body") }}</span>
                      </p>
                    </div>
                  </div>
                  <div class="d-flex bar-code-scan__option">
                    <p class="title">
                      {{ $t("bar_code_scan.price") }}
                    </p>
                    <div class="content-product d-flex align-items-center">
                      <p
                        v-for="(option, idx) in product.options"
                        :key="idx"
                        :class="['content-product__option', option.optionId == compareOptionId ? 'active' : '']"
                        @click="chooseOption(option)"
                      >
                        {{ option.optionName }}
                      </p>
                    </div>
                  </div>
                  <div class="d-flex bar-code-scan__num-price">
                    <p class="title">
                      {{ $t("bar_code_scan.orderQuan") }}
                    </p>
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
                            {{ $t("bar_code_scan.max") }} {{ maxQuantity }}{{ $t("bar_code_scan.canbe") }}
                          </p>
                        </div>
                      </div>
                      <div class="computed-price">
                        <div class="computed-price__title">
                          {{ $t("bar_code_scan.amount") }}
                        </div>
                        <div class="computed-price__price">
                          <span>{{ $format.price(totalPrice) }}</span>{{ $t("bar_code_scan.won") }}
                        </div>
                        <p class="computed-price__noted">
                          <span></span> {{ $t("bar_code_scan.amountAvail") }} {{ $format.price(creditAmount) }} {{ $t("bar_code_scan.won") }}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex bar-code-scan__num-price">
                    <p class="title" />
                    <div class="d-flex flex-wrap content-product">
                      <div class="btn-payment">
                        <router-link to="/shopping-cart">{{ $t("bar_code_scan.viewCart") }}</router-link>
                      </div>
                      <div class="btn-payment red">
                        <p @click="addToCart($event, product, product.productId, product.image, compareOptionId, quantity, shippingMethod)">
                          <span>{{ $t("bar_code_scan.addCart") }}</span>
                          <span class="btn-payment__price">{{ $format.price(totalPrice) }} {{ $t("bar_code_scan.won") }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
    import "../dbr"; // import side effects. The license, engineResourcePath, so on.
    import { BarcodeScanner } from 'dynamsoft-javascript-barcode'
    import VideoDecode from "./VideoDecode";
    import { ref, onMounted } from "vue";
    import { countNumber } from "../mixins/countNumber";
    import { StreamBarcodeReader } from "vue-barcode-reader";

    import LoadingPage from '../components/LoadingPage'
    import { addToCart } from '../mixins/addToCart'
    import { mapGetters } from "vuex"
    import ConfirmDialogue from '../components/ConfirmDialogue'
    export default  {
        components: {
            StreamBarcodeReader,
            LoadingPage,
            ConfirmDialogue,
            VideoDecode,
        },
        mixins: [
            countNumber,
            addToCart,
        ],
        setup() {
            const bShowScanner = ref(true);
            onMounted(async () => {
                try {
                    //Load the library on page load to speed things up.
                    await BarcodeScanner.loadWasm();
                } catch (ex) {
                    let errMsg;
                    if (ex.message.includes("network connection error")) {
                        errMsg = "Failed to connect to Dynamsoft License Server: network connection error. Check your Internet connection or contact Dynamsoft Support (support@dynamsoft.com) to acquire an offline license.";
                    } else {
                        errMsg = ex.message||ex;
                    }
                    console.error(errMsg);
                }
            });
            const showScanner = () => {
                bShowScanner.value = true;
            };
            return {
                bShowScanner,
                showScanner
            };
        },
        data() {
            return {
                loading: false,
                barcode: '',
                creditAmount: 0,
                openWebcam: false,
                isPhotoTaken: false,
                product: [],
                optionActive: {},
                compareOptionId: 0,
                price: 0,
                maxQuantity: 0,
                shippingMethod: 2,
                isNotFound: false
            };
        },
        computed: {
            totalPrice() {
                return this.quantity * this.price
            },
            ...mapGetters(['getProducts']),
            productInCart() {
                return this.getProducts.data;
            },
        },
        watch:{
            // close camera on change router
            $route (to, from){
                // close camera
                this.openWebcam = false
                // this.stopCameraStream()
            }
        },
        mounted() {
            this.getStoreInfo()
        },
        methods: {
            getBarcode(code) {
                this.barcode = code
                // capture code
                this.isPhotoTaken = true
                // const context = this.$refs.canvas.getContext('2d')
                // context.drawImage(this.$refs.camera, 0, 0, 500, 250)
                // close camera
                this.openWebcam = false
                this.checkBarCode(this.barcode)
            },
            async createCameraElement() {
                const constraints = (window.constraints = {
                    audio: false, video: true
                });
                await navigator.mediaDevices.getUserMedia(constraints)
                    .then(stream => {
                        this.$refs.camera.srcObject = stream;
                    }).catch(error => {
                        alert(this.$t("alert.deviceNotFound"))
                        console.log(error)
                    });
            },
            checkQuantity(product) {
                if (this.productInCart != null) {
                    for(let i in this.productInCart) {
                        if (product.productId == this.productInCart[i].ds_product_id && this.compareOptionId == this.productInCart[i].ds_option_id) {
                            this.quantity = this.productInCart[i].quantity
                        }
                    }
                }
            },
            getStoreInfo() {
                axios.get(apiURL + "/user/credit")
                .then(response => {
                    this.creditAmount = response.data.data.order_credit_amount
                })
            },
            getProductDetail(productId) {
                axios
                    .get(apiURL + "/product/product/" + productId)
                    .then(res => {
                        this.product = res.data.data
                        this.optionActive = this.product.options[0];
                        this.compareOptionId = this.product.options[0].optionId;
                        this.price = this.product.options[0].supplyUnitPrice;
                        this.maxQuantity = this.product.options[0].maxOrder;
                        this.checkQuantity(this.product)
                        this.loading = false
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            checkBarCode(code) {
                this.loading = true
                axios.get(apiURL + "/product/product-option-list-by-barcode", {
                    params: {
                        barcode: code
                    }
                })
                .then(res => {
                    this.loading = false
                    console.log(res.data.data)
                    if (res.data.data != null) {
                        this.isNotFound = false
                        let productId = res.data.data.productId
                        this.getProductDetail(productId)
                    } else {
                        this.isNotFound = true
                        this.$refs.ConfirmDialogue.show({
                            title: this.$t('modal.titleDialog'),
                            message: '상품을 찾을 수 없습니다',
                            cancelButton: this.$t('modal.btnOk'),
                            isDialog: true,
                        })
                    }
                })
                .catch(err => {
                    console.log(err)
                })
            },
            onDecode(code) {
                try {
                    this.barcode = code
                    // capture code
                    this.isPhotoTaken = true
                    const context = this.$refs.canvas.getContext('2d')
                    context.drawImage(this.$refs.camera, 0, 0, 500, 250)
                    // close camera
                    this.openWebcam = false
                    this.stopCameraStream()
                    this.checkBarCode(this.barcode)
                } catch (error) {
                    console.log(error)
                }
            },
            showCamera() {
                this.openWebcam = true
                this.barcode = null
                this.isPhotoTaken = false
                // this.createCameraElement()
            },
            stopCameraStream() {
                if (this.$refs.camera.srcObject != null) {
                    let tracks = this.$refs.camera.srcObject.getTracks();
                    tracks.forEach(track => {
                        track.stop();
                    });
                }
            },
            chooseOption(option) {
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
        }
    }
</script>

<style lang="scss" scoped>
    .barcode-not-found {
        color: red;
    }
    #video-vitural {
        opacity: 0;
        pointer-events: none;
        position: absolute;
        bottom: 0;
        left: 0;
    }
    .btn-scann {
        z-index: 99999;
        position: relative;
    }
    .bar-code-scan {
        padding: 0 0 50px;
        &__cap {
            video, canvas {
                max-width: 100%;
            }
        }
        &__right {
            video {
                width: 100%;
            }
        }
        &__small-img {
            padding: 15px;
        }
        &__des {
            font-size: 16px;
            color: #797979;
            margin: 15px 0;
        }
        &__code {
            margin-top: 20px;
            p {
                margin-bottom: 10px;
                color: #797979;
                &.code {
                    border: 1px solid #ccc;
                    background: #f1f1f1;
                    padding: 5px 10px;
                    text-align: center;
                    font-weight: bold;
                    font-size: 22px;
                }
            }
        }
        .btn-scan {
            width: 100%;
            padding: 7px 10px;
            color: #fff;
            background: #797979;
            color: #fff;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border-radius: 2px;
            transition: all 0.3s;
            cursor: pointer;
            &:hover {
                opacity: 0.8;
            }
        }
        &__main {
            position: relative;
            .img-code {
                img {
                    width: 100%;
                }
            }
            h2.title {
                font-weight: bold;
                font-size: 30px;
            }
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
        &__small-img {
            img {
                width: 100%;
            }
        }
        &__name {
            font-size: 25px;
            font-weight: bold;
            margin: 15px 0;
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
                padding: 10px 60px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                cursor: pointer;
                transition: all 0.3s;
                font-size: 14px;
                margin-left: 10px;
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
                a {
                    display: block;
                    color: #fff;
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
                    margin-left: 15px;
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
    }

    @media only screen and (max-width: 991px) {
        .bar-code-scan {
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
        }
    }

    @media only screen and (max-width: 767px) {
        .bar-code-scan {
            &__main {
                .img-code {
                    display: none;
                    width: 60%;
                    margin: 15px auto;
                }
            }
            &__shipping {
                padding: 30px 0;
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
                        text-align: left;
                        width: 100%;
                        margin-top: 15px;
                    }
                    .gr-left {
                        width: 100%;
                    }
                    .d-btn {
                        width: 100%;
                        margin-bottom: 5px;
                    }
                }
                .btn-payment {
                    width: 48%;
                    margin-left: 0;
                    height: auto;
                    padding: 0;
                    text-align: center;
                    justify-content: center;
                    padding: 5px 0;
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
        }
    }
</style>
