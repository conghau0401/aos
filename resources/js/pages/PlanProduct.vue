<template>
  <section id="plan-product">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("plan_product.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("plan_product.planProd") }}
            </router-link>
          </li>
        </ul>
      </div>
      <h2 class="plan-title d-lg-flex align-items-center">
        <p class="plan-title__name">
          {{ $t("plan_product.planProd") }}
        </p>
        <ul class="d-flex align-items-center flex-wrap">
          <li
            v-for="(item, index) in listEvent"
            :key="index"
            :class="planId == item.bundleAnniversaryId ? 'active' : ''"
          >
            <router-link :to="'/plan-product/' + item.bundleAnniversaryId">
              {{ item.bundleAnniversaryNm }}
            </router-link>
          </li>
        </ul>
      </h2>
      <div class="row">
        <div class="col-lg-9">
          <div class="title-right d-flex flex-wrap align-items-center justify-content-end">
            <!-- <p class="btn-delete" @click="updateAllProduct($event)">
                            <span class="img">
                                <svg width="24" height="24" fill="#555"  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd"><path fill-rule="evenodd" d="M7 9h-7v-7h1v5.2c1.853-4.237 6.083-7.2 11-7.2 6.623 0 12 5.377 12 12s-5.377 12-12 12c-6.286 0-11.45-4.844-11.959-11h1.004c.506 5.603 5.221 10 10.955 10 6.071 0 11-4.929 11-11s-4.929-11-11-11c-4.66 0-8.647 2.904-10.249 7h5.249v1z"/></svg>
                            </span>
                            <span>{{$t("regular_deli.edit")}}</span>
                        </p> -->
            <p
              class="btn-delete add-all-cart"
              @click="addCartMultiple"
            >
              {{ $t("cart.addAll") }}
            </p>
          </div>
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
                  :product-t-p="parseInt(item.productTypeCode)"
                  :is-remove-checked="isRemoveChecked"
                />
              </div>
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
                listEvent: [],
                planId: "",
                planFirstId: "",
                loading: true,
                sizeLoading: 50,
                colorLoading: "#41b883",
                isRemoveChecked: false,
                currentPage: this.$route.name,
            }
        },
        computed: {
            getCheckedItem() {
                return this.$store.state.checkedItem;
            },
            getIdsChecked() {
                return this.$store.state.idsChecked;
            },
            shippingFee() {
                return this.$store.state.shippingFee
            }
        },
        watch: {
            $route (to){
                if (to.name != this.currentPage) return
                if (to.params.id == "") {
                    this.planId = this.planFirstId
                    this.getEventDetail(this.planId)
                } else {
                    this.planId = to.params.id
                    this.getEventDetail(this.planId)
                }
            },
            planId(n, o) {
                if (n == "" && this.listEvent.length > 0) {
                    let planId = this.listEvent[0].bundleAnniversaryId
                    this.getEventDetail(planId)
                }
            }
        },
        mounted() {
            // reset checked item
            this.$store.state.checkedItem = []
            this.$store.state.idsChecked = []
            this.planId = this.$route.params.id
            this.getListEvent()
        },
        methods: {
            getListEvent() {
                axios.get(apiURL + "/product/recommended-products-by-event")
                .then(response => {
                    console.log(response.data.data)
                    this.loading = false
                    if(response.data.data[0] != undefined) {
                        if(this.planId == null || this.planId == "") {
                            this.planId = this.planFirstId = response.data.data[0].bundleAnniversaryId
                        }
                        this.listEvent = response.data.data
                        this.getEventDetail(this.planId)
                    }
                }).catch(err => {
                    console.log(err)
                })
            },
            getEventDetail(id) {
                this.loading = true
                axios
                    .get(apiURL + '/product/recommended-products-by-event/detail-products', {
                        params: {
                            bundleAnniversaryId: id
                        }
                    })
                    .then(response => {
                        this.products = response.data.data
                        this.loading = false
                    }).catch(err => {
                        console.log(err)
                    })
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
    #plan-product {
        padding: 0 0 50px;
    }
    .plan-title {
        color: #5b5b5b;
        margin-bottom: 30px;
        &__name {
            font-size: 30px;
            font-weight: bold;
            margin: 0;
        }
        ul {
            margin: 0;
            li {
                font-size: 14px;
                padding: 0 7px;
                position: relative;
                &.active {
                    font-size: 18px;
                    font-weight: bold;
                }
                &:last-child {
                    &:after {
                        display: none;
                    }
                }
                &:after {
                    content: "";
                    position: absolute;
                    right: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 1px;
                    height: 10px;
                    background: #b5b5b5;
                }
                a {
                    color: #555555;
                }
                &:hover {
                    font-size: 18px;
                    font-weight: bold;
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
        .add-all-cart {
            margin-left: 10px;
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

    @media only screen and (max-width: 991px) {
        .plan-title {
            margin-bottom: 0;
            &__name {
                margin-bottom: 10px;
                font-size: 22px;
            }
            ul {
                padding: 0;
                li {
                    margin-bottom: 10px;
                }
            }
        }
        .container-box {
            &__title {
                .title-right {
                    .btn-delete {
                        margin: 0;
                        margin-right: 10px;
                        display: inline-flex;
                    }
                    p.noted {
                        margin: 10px 0;
                        font-size: 14px;
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        #plan-product {
            padding-bottom: 15px;
        }
        .plan-title {
            ul {
                margin: 0 0 0 -7px;
                li {
                    margin-bottom: 5px;
                    &.other {
                        font-size: 16px;
                    }
                }
            }
        }
        .container-box {
            &__name {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }
        }
        .box-product {
            &__title {
                padding: 10px 5px;
                font-size: 13px;
            }
            &__info {
                width: 35%;
            }
            &__option, &__water {
                width: 15%;
            }
            &__num {
                width: 30%;
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
