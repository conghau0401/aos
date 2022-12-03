<template>
  <section class="shopping-cart">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("cart.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("cart.shoppingCart") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="shopping-cart__step">
        <ul class="d-flex align-items-center justify-content-start">
          <li class="active">
            <span>01</span>{{ $t("cart.shoppingCart") }}
          </li>
          <li class="gray">
            <span>02</span>{{ $t("cart.orderForm") }}
          </li>
          <li><span>03</span>{{ $t("cart.orderComp") }}</li>
        </ul>
      </div>
      <div class="row">
        <div class="col-lg-9">
          <div class="shopping-cart__main">
            <div class="container-box">
              <div class="delivery">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("cart.sameDay") }} ({{ countSameDay }}) <span>&#43;</span>
                </h3>
                <!-- <div class="delivery__content">
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Explicabo aspernatur quis tempore corrupti, quasi voluptates, natus vel totam ullam eligendi, ab fuga non qui necessitatibus laudantium dolor sapiente soluta accusantium.
                                </div> -->
              </div>
              <div class="discount-title d-flex flex-wrap align-items-center justify-content-lg-end">
                <p class="noted">
                  {{ $t("cart.selectProd") }}
                </p>
                <p
                  class="btn-delete"
                  @click="deleteProduct()"
                >
                  <span class="img"><img
                    src="/img/icon/delete.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.delete") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('1')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.centerPick") }}{{ $t("cart.change") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('2')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.changeDeli") }}</span>
                </p>
              </div>
              <div
                v-for="(item, idx) in groupByProductsSameDay"
                :key="idx"
                class="group-item"
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
                      <b v-if="idx == 1">{{ $t("cart.industGoods") }}</b>
                      <b v-else-if="idx == 2">{{ $t("cart.miscellGoods") }}</b>
                      <b v-else>{{ $t("cart.alcoGoods") }}</b>
                      <span>{{ $t("cart.default") }}</span>
                    </p>
                  </div>
                  <div class="wrap-btn-update d-flex">
                    <p
                      class="btn-update update-all-cart"
                      @click="updateCartMultiple"
                    >
                      {{ $t("cart.updateAll") }}
                    </p>
                  </div>
                </div>
                <div class="container-box__product box-product">
                  <ProductGroupItem
                    :product-info="item"
                    :is-remove-checked="isRemoveChecked"
                    :page="getPage"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="shopping-cart__main">
            <div class="container-box">
              <div class="delivery">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("cart.centerPick") }} ({{ countPickUpCenter }}) <span>&#43;</span>
                </h3>
                <!-- <div class="delivery__content">
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Explicabo aspernatur quis tempore corrupti, quasi voluptates, natus vel totam ullam eligendi, ab fuga non qui necessitatibus laudantium dolor sapiente soluta accusantium.
                                </div> -->
              </div>
              <div class="discount-title d-flex flex-wrap align-items-center justify-content-lg-end">
                <p class="noted">
                  {{ $t("cart.selectProd") }}
                </p>
                <p
                  class="btn-delete"
                  @click="deleteProduct()"
                >
                  <span class="img"><img
                    src="/img/icon/delete.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.delete") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('2')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.changeDeli") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('3')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.changeToSameDay") }}</span>
                </p>
              </div>
              <div
                v-for="(item, idx) in groupByProductsPickUpCenter"
                :key="idx"
                class="group-item"
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
                      <b v-if="idx == 1">{{ $t("cart.industGoods") }}</b>
                      <b v-else-if="idx == 2">{{ $t("cart.miscellGoods") }}</b>
                      <b v-else>{{ $t("cart.alcoGoods") }}</b>
                      <span>{{ $t("cart.default") }}</span>
                    </p>
                  </div>
                  <div class="wrap-btn-update d-flex">
                    <p
                      class="btn-update update-all-cart"
                      @click="updateCartMultiple"
                    >
                      {{ $t("cart.updateAll") }}
                    </p>
                  </div>
                </div>
                <div class="container-box__product box-product">
                  <ProductGroupItem
                    :product-info="item"
                    :is-remove-checked="isRemoveChecked"
                    :page="getPage"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="shopping-cart__main">
            <div class="container-box">
              <div class="delivery">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("cart.direct") }} ({{ countDelivery }})<span>&#43;</span>
                </h3>
                <!-- <div class="delivery__content">
                                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Explicabo aspernatur quis tempore corrupti, quasi voluptates, natus vel totam ullam eligendi, ab fuga non qui necessitatibus laudantium dolor sapiente soluta accusantium.
                                </div> -->
              </div>
              <div class="discount-title d-flex flex-wrap align-items-center justify-content-lg-end">
                <p class="noted">
                  {{ $t("cart.selectProd") }}
                </p>
                <p
                  class="btn-delete"
                  @click="deleteProduct()"
                >
                  <span class="img"><img
                    src="/img/icon/delete.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.delete") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('1')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.centerPick") }}{{ $t("cart.change") }}</span>
                </p>
                <p
                  class="btn-delete print"
                  @click="switchShippingMethod('3')"
                >
                  <span class="img"><img
                    src="/img/icon/return.svg"
                    alt="Delete"
                  ></span>
                  <span>{{ $t("cart.changeToSameDay") }}</span>
                </p>
              </div>
              <div
                v-for="(item, idx) in groupByProductsDelivery"
                :key="idx"
                class="group-item"
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
                      <b v-if="idx == 1">{{ $t("cart.industGoods") }}</b>
                      <b v-else-if="idx == 2">{{ $t("cart.miscellGoods") }}</b>
                      <b v-else>{{ $t("cart.alcoGoods") }}</b>
                      <span>{{ $t("cart.default") }}</span>
                    </p>
                  </div>
                  <div class="wrap-btn-update d-flex">
                    <p
                      class="btn-update update-all-cart"
                      @click="updateCartMultiple"
                    >
                      {{ $t("cart.updateAll") }}
                    </p>
                  </div>
                </div>
                <div class="container-box__product box-product">
                  <ProductGroupItem
                    :product-info="item"
                    :is-remove-checked="isRemoveChecked"
                    :page="getPage"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <RightBarOld
            :page="getPage"
          />
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
    import RightBarOld from '../components/RightBarOld'
    import ProductGroupItem from '../components/ProductGroupItem'
    import LoadingPage from '../components/LoadingPage'
    import ConfirmDialogue from '../components/ConfirmDialogue'
    export default {
        components: {
            RightBarOld,
            ProductGroupItem,
            LoadingPage,
            ConfirmDialogue
        },
        data() {
            return {
                dataShoppingCart: [],
                loading: true,
                isRemoveChecked: false,
            }
        },
        computed: {
            getCheckedItem() {
                return this.$store.state.checkedItem;
            },
            getIdsChecked() {
                return this.$store.state.idsChecked;
            },
            getPage() {
                return this.$route.path
            },
            allProducts() {
                return this.dataShoppingCart
            },
            productsPickUpCenter() {
                return this.shippingMethodProducts(this.allProducts, 1)
            },
            productsDelivery() {
                return this.shippingMethodProducts(this.allProducts, 2)
            },
            productsSameDay() {
                return this.shippingMethodProducts(this.allProducts, 3)
            },
            groupByProductsSameDay() {
                if (this.productsSameDay != null) {
                    return this.groupByProductTp(this.productsSameDay, 'productTp')
                } else {
                    return null
                }
            },
            groupByProductsPickUpCenter() {
                if (this.productsPickUpCenter != null) {
                    return this.groupByProductTp(this.productsPickUpCenter, 'productTp')
                } else {
                    return null
                }
            },
            countPickUpCenter() {
                if (this.productsPickUpCenter != null) {
                    return this.productsPickUpCenter.length;
                }
                return 0
            },
            countSameDay() {
                if (this.productsSameDay != null) {
                    return this.productsSameDay.length;
                }
                return 0
            },
            groupByProductsDelivery() {
                if (this.productsDelivery != null) {
                    return this.groupByProductTp(this.productsDelivery, 'productTp')
                } else {
                    return null
                }
            },
            countDelivery() {
                if (this.productsDelivery != null) {
                    return this.productsDelivery.length;
                }
                return 0
            },
        },
        mounted() {
            // reset checked item
            this.$store.state.checkedItem = []
            this.$store.state.idsChecked = []
            this.getListProductCart()
        },
        methods: {
            getListProductCart() {
                this.loading = true
                axios.get(apiURL + '/carts')
                    .then((res) => {
                        this.loading = false
                        this.$store.commit('GET_CART', res)
                        this.dataShoppingCart = res.data.data
                    })
            },
            async updateCartMultiple() {
                let itemChecked = []
                let isAvailable = true
                for (let i in this.getCheckedItem) {
                    for (let j in this.getIdsChecked) {
                        if (this.getCheckedItem[i].id == this.getIdsChecked[j]) {
                            let item = {
                                "ds_product_id": this.getCheckedItem[i].ds_product_id,
                                "ds_option_id": this.getCheckedItem[i].ds_option_id,
                                "shipping_method": this.getCheckedItem[i].shipping_method,
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
                    // check valid add cart
                    this.dataShoppingCart.forEach(item => {
                        itemChecked.forEach(checked => {
                            if (item.option_sell_yn == true || (item.limited == true && item.stockQty < checked.quantity)) {
                                isAvailable = false
                            }
                        });
                    });
                    if (!isAvailable) {
                        alert(this.$t("modal.notEnoughInventory"))
                        return
                    }

                    //unchecked all checkbox
                    $("input[name='checkboxItem']:checked").each(function(index) {
                        $(this).prop("checked", false)
                        $("input[name='itemCheckAll']").prop("checked", false)
                    })
                    this.isRemoveChecked = !this.isRemoveChecked
                    //show notice dialogue
                    this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.addSuccess'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    let params = {
                        "listRequest": itemChecked
                    }
                    axios.post(apiURL + "/carts/create-multiple", params)
                    .then((res) => {
                        for (const index in res.data.data) {
                            // reset checked item
                            this.$store.state.checkedItem = []
                            this.$store.state.idsChecked = []
                            //update product
                            this.$store.commit('updateQuantityProduct', res.data.data[index]);
                        }
                    }).catch(err => {
                        console.log(err)
                    })
                }
            },
            groupByProductTp (object, key) {
                return object.reduce(function(type, obj) {
                    (type[obj[key]] = type[obj[key]] || []).push(obj);
                    return type;
                }, {});
            },
            shippingMethodProducts(products, method) {
                if (products != null) {
                    let pro = products.filter(function(obj) {
                        return obj.shipping_method == method;
                    });
                    return pro;
                }
            },
            toggleDeliveryContent(e) {
                e.stopPropagation();
                let _this = $(e.target);
                _this.next(".delivery__content").slideToggle();
                _this.toggleClass("active");
                if(_this.hasClass("active")) {
                    _this.children("span").html("&#8722;");
                } else {
                    _this.children("span").html("&#43;");
                }
            },
            deleteProduct() {
                let id = this.getIdsChecked.join(",")
                this.$store.commit("deleteProductInCart", this.getIdsChecked)
                this.dataShoppingCart = this.dataShoppingCart.filter(item => !this.getIdsChecked.includes(item.id))
                // reset checked item
                this.$store.state.idsChecked = []
                this.isRemoveChecked = !this.isRemoveChecked
                axios
                    .delete(apiURL + '/carts/delete/' + id)
                    .then(res => {
                        this.$emit("deleteProductInCart", id)
                        // refresh cart to get shippingFee
                        // this.$store.dispatch("getProducts");
                    })
                    .catch(error => {
                        console.log(error)
                    })
            },
            switchShippingMethod(shippingMethod) {
                let ids = this.getIdsChecked
                if (ids.length == 0) {
                    //show notice dialogue
                    this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: this.$t('modal.nothingUpdated'),
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                } else {
                    this.loading = true
                    // reset checked item
                    this.$store.state.idsChecked = []
                    this.isRemoveChecked = !this.isRemoveChecked
                    axios.put(apiURL + '/carts/update', {
                            ids: ids,
                            shipping_method: shippingMethod
                        }).then(res => {
                            this.getListProductCart()
                        }).catch(error => {
                            console.log(error)
                        })
                }
            },
        }
    }
</script>


<style lang="scss" scoped>
    .shopping-cart {
        color: #555;
        padding-bottom: 50px;
        &__step {
            margin-bottom: 30px;
            ul {
                margin: 0;
                padding: 0;
                li {
                    padding: 10px 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #f1f1f1;
                    position: relative;
                    &:after {
                        position: absolute;
                        content: "";
                        top: 50%;
                        transform: translateY(-50%);
                        left: 100%;
                        border-left: 12px solid #d7d7d7;
                        border-bottom: 10px solid transparent;
                        border-top: 10px solid transparent;
                        z-index: 1;
                    }
                    &.gray {
                        background: #d7d7d7;
                        &:after {
                            position: absolute;
                            content: "";
                            top: 50%;
                            transform: translateY(-50%);
                            left: 100%;
                            border-left: 12px solid #d7d7d7;
                            border-bottom: 10px solid transparent;
                            border-top: 10px solid transparent;
                            z-index: 1;
                        }
                    }
                    &:last-child {
                        &::after {
                            display: none;
                        }
                    }
                    &.active {
                        background: #ef4130;
                        color: #fff;
                        &:after {
                            border-left: 12px solid #ef4130;
                        }
                    }
                    span {
                        background: #fff;
                        font-size: 16px;
                        font-weight: bold;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 50%;
                        width: 30px;
                        height: 30px;
                        color: #555;
                        margin-right: 30px;
                    }
                }
            }
        }
    }
    .delivery {
        padding-bottom: 20px;
        h3 {
            font-weight: bold;
            color: #1375c9;
            position: relative;
            font-size: 25px;
                cursor: pointer;
            span {
                position: absolute;
                display: block;
                font-size: 40px;
                font-weight: normal;
                top: 50%;
                transform: translateY(-50%);
                right: 0;
                pointer-events: none;
            }
        }
        &__content {
            display: none;
            padding: 20px 0 0;
        }
    }
    .discount-title {
        font-size: 14px;
        color: #555;
        margin-bottom: 20px;
        p.noted {
            margin: 0;
        }
        .btn-delete {
            border: 1px solid #ccc;
            padding: 3px 12px;
            margin: 0 0 5px 15px;
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
        position: relative;
        height: 50px;
        p {
            &.price {
                font-size: 20px;
                font-weight: bold;
                display: flex;
                align-items: center;
                span.small {
                    font-size: 14px;
                    padding-left: 20px;
                    font-weight: normal;
                }
            }
            &:last-child {
                color: #ef4130;
                font-size: 14px;
                font-weight: bold;
            }
        }
    }
}

.container-box {
    margin-bottom: 30px;
    border-top: 5px solid #f1f1f1;
    padding-top: 30px;
    .group-item {
        margin-bottom: 30px;
    }
    &__icon {
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

        .btn-update {
            font-size: 14px;
            color: #555;
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
        &:last-child {
            border-bottom: 0;
        }
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
    }
    &__water {
        width: 10%;
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
        width: 100px;
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
            line-height: 25px;
            text-align: center;
            margin-left: 1px;
            padding: 0 3px;
        }
    }
}
@media only screen and (max-width: 991px) {
    .shopping-cart {
        &__step {
            ul {
                li {
                    padding: 10px 20px;
                }
            }
        }
    }
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
    .discount-title {
        margin-bottom: 20px;
        &__left {
            border: 1px solid #ddd;
            p.price {
                display: block;
                span {
                    display: block;
                    &.small {
                        padding-left: 0;
                        margin: 5px 0;
                    }
                }
            }
            &:after {
                display: none;
            }
        }
        &__left {
            padding: 10px 15px;
            height: auto;
        }
    }
}

@media only screen and (max-width: 767px) {
    .discount-title {
        .btn-delete {
            margin-left: 5px;
            padding: 3px 7px;
        }
        p.noted {
            display: none;
        }
    }
    .shopping-cart {
        &__step {
            margin-bottom: 20px;
            ul {
                li {
                    padding: 10px;
                    font-size: 12px;
                    width: calc(100%/3);
                    text-align: center;
                    span {
                        display: none;
                    }
                }
            }
        }
    }
    .delivery {
        h3 {
            font-size: 18px;
            span {
                font-size: 35px;
            }
        }
    }
    #discount-product {
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
                padding: 0 10px;
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
}
</style>
