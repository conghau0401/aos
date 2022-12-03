<template>
  <div class="purchase-history">
    <div class="container">
      <h2 class="purchase-history__title">
        {{ $t("monthly_purchase_history.monthlyPurc") }}
      </h2>
      <form
        action=""
        @submit.prevent="getMonthlyHistory"
      >
        <div class="purchase-history__filter d-md-flex align-items-center">
          <p class="purchase-history__filter--title">
            {{ $t("monthly_purchase_history.viewPeriod") }}
          </p>
          <div class="purchase-history__filter--box">
            <input
              id="datemin"
              v-model="startDate"
              type="date"
              placeholder="MM/DD/YYYY"
            >
          </div>
          <p class="between-icon">
            ~
          </p>
          <div class="purchase-history__filter--box">
            <input
              id="datemax"
              v-model="endDate"
              type="date"
              placeholder="MM/DD/YYYY"
              @change="updateMinMaxDate"
            >
          </div>
          <button
            type="submit"
            class="purchase-history__filter--submit"
          >
            {{ $t("monthly_purchase_history.lookUp") }}
          </button>
        </div>
      </form>
      <div class="purchase-history__content best-product">
        <b-tabs
          v-model="tabIndex"
          content-class="mt-5"
          align="center"
        >
          <b-tab
            v-if="industrialProducts.length > 0"
            :title="$t('order_form.indust')"
            active
          >
            <div
              v-for="(item, index) in industrialProducts"
              :key="index"
              class="purchase-history__ar"
            >
              <ProductItemMonthlyPurchase :product="item" />
            </div>
          </b-tab>
          <b-tab :title="$t('order_form.miscell')" v-if="miscellaneousProducts.length > 0">
            <div
              v-for="(item, index) in miscellaneousProducts"
              :key="index"
              class="purchase-history__ar"
            >
              <ProductItemMonthlyPurchase :product="item" />
            </div>
          </b-tab>
          <b-tab :title="$t('order_form.alcohol')" v-if="alcoholicProducts.length > 0">
            <div
              v-for="(item, index) in alcoholicProducts"
              :key="index"
              class="purchase-history__ar"
            >
              <ProductItemMonthlyPurchase :product="item" />
            </div>
          </b-tab>
        </b-tabs>
      </div>
    </div>
    <LoadingPage v-if="loading" />
  </div>
</template>

<script>
import LoadingPage from '../components/LoadingPage'
import ProductItemMonthlyPurchase from '../components/ProductItemMonthlyPurchase'
import moment from 'moment-timezone'
import { groupByKeyMixin } from '../mixins/groupByKeyMixin'

export default {
    components: {
        LoadingPage,
        ProductItemMonthlyPurchase
    },
    mixins: [
        groupByKeyMixin,
    ],
    data() {
        return {
            tabIndex: 0,
            width: 550,
            height: 400,
            left: 10,
            top: 10,
            open: false,
            product: {
                id: 1,
                image: "/img/product/product.png"
            },
            startDate: null,
            endDate: null,
            productsByDate: {},
            industrialProducts: [],
            miscellaneousProducts: [],
            alcoholicProducts: [],
            loading: false,
            selectedProduct: null,
        }
    },
    created() {
        // set start date (-1 month)
        this.startDate = moment().tz('Asia/Seoul').subtract(30, 'days').format('YYYY-MM-DD')
        this.endDate = moment().tz('Asia/Seoul').format('YYYY-MM-DD')

        this.getMonthlyHistory()
    },
    mounted() {
        // set default min - max
        let datemin = new Date();
        datemin.setDate(datemin.getDate() - 180)
        $('#datemin').attr('min', datemin.toISOString().substr(0, 10))
        $('#datemin').attr('max', new Date().toISOString().substr(0, 10))

        $('#datemax').attr('max', new Date().toISOString().substr(0, 10))
    },
    methods: {
        getMonthlyHistory() {
            this.loading = true
            axios.post(apiURL + "/product/monthly-history", {
                "startDate": this.startDate,
                "endDate": this.endDate,
            })
            .then(response => {
                this.loading = false
                this.industrialProducts = []
                this.miscellaneousProducts = []
                this.alcoholicProducts = []
                let products = response.data.data
                for (const key in products) {
                    if (products[key].productTp == '1') {
                        this.industrialProducts.push(products[key])
                    } else if (products[key].productTp == '2') {
                        this.miscellaneousProducts.push(products[key])
                    } else if (products[key].productTp == '3') {
                        this.alcoholicProducts.push(products[key])
                    }
                }
                this.productsByDate = this.groupByKey(response.data.data, 'forward_mm')
            }).catch(error => {
                console.log(error)
            });
        },
        closeRDWindow(status) {
            this.open = status
        },
        updateMinMaxDate() {
            let currentMax = $('#datemax').val()
            let arrDate = currentMax.split('-')
            let datemin = new Date(arrDate[0], arrDate[1], arrDate[2]);
            datemin.setDate(datemin.getDate() - 180)
            $('#datemin').attr('min', datemin.toISOString().substr(0, 10))
            $('#datemin').attr('max', currentMax)

            this.endDate = currentMax
        },
    }
};
</script>


<style lang="scss">
    @import '/css/tabs.scss';
    .purchase-history {
        padding: 40px 0;
        &__filter {
            margin-bottom: 30px;
            &--submit {
                border: none;
                background: #999999;
                height: 35px;
                padding: 0 30px;
                color: #fff;
                margin-left: 10px;
                font-size: 14px;
                border-radius: 5px;
                transition: all 0.3s;
                &:hover {
                    opacity: 0.8;
                }
            }
            &--title {
                margin: 0;
                font-weight: bold;
                text-transform: uppercase;
                padding-right: 10px;
            }
            .between-icon {
                margin: 0 10px 0;
            }
            &--box {
                input {
                    border: 1px solid #ccc;
                    height: 35px;
                    padding: 0 10px 0 15px;
                    margin-left: 5px;
                    outline: none;
                    color: #555555;
                    background: #fff;
                    border-radius: 5px;
                }
            }
        }
        &__ar {
            padding: 15px 0;
            border-bottom: 1px solid #e8e8e8;
        }
        &__content {
            margin-bottom: 30px;
        }
        &__right {
            display: flex;
            align-items: center;
            justify-content: space-between;
            &--info {
                width: 75%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                dl {
                    width: 48%;
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    font-size: 14px;
                    word-break: break-all;
                    margin-bottom: 10px;
                    dt, dd {
                        margin: 0;
                        font-weight: normal;
                    }
                    dt {
                        width: 40%;
                    }
                    dd {
                        width: 60%;
                    }
                }
            }
            &--btn {
                width: 22%;
                padding: 0;
                margin: 0;
                li {
                    list-style: none;
                    width: 100%;
                    border: 1px solid #ccc;
                    padding: 5px;
                    font-size: 12px;
                    text-align: center;
                    border-bottom: 0;
                    color: #555;
                    cursor: pointer;
                    a {
                        color: #555;
                    }
                    &:last-child {
                        border-bottom: 1px solid #ccc;
                    }
                    &:hover {
                        border-left: 2px solid #ef4130;
                        border-right: 2px solid #ef4130;
                        color: #ef4130;
                        a {
                            color: #ef4130;
                        }
                        img {
                            filter: grayscale(0%);
                            opacity: 1;
                        }
                    }
                    img {
                        width: 10px;
                        filter: grayscale(100%);
                        opacity: 0.6;
                    }
                }
            }
        }
        &__info {
            width: 55%;
            text-align: left;
            display: flex;
            align-items: center;
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
                margin: 5px 0;
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
        &__title {
            font-size: 30px;
            text-align: center;
            margin-bottom: 40px;
        }
        &__content {
            &--title {
                font-weight: bold;
                position: relative;
                &.other {
                    font-size: 14px;
                    font-weight: normal;
                    p {
                        margin-left: 0;
                        padding-left: 0;
                    }
                }
                p {
                    display: inline-block;
                    background: #fff;
                    margin: 0 0 0 100px;
                    position: relative;
                    padding: 0 15px;
                    max-width: 80%;
                }
                &:before {
                    position: absolute;
                    content: "";
                    width: 100%;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    height: 5px;
                    background: #e8e8e8;
                }
                span {
                    font-size: 20px;
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        .purchase-history {
            padding: 25px 0;
            &__filter {
                margin-bottom: 30px;
                &--submit {
                    margin-left: 0;

                }
                &--title {
                    margin: 0;
                    font-weight: bold;
                    text-transform: uppercase;
                    padding-right: 10px;
                }
                .between-icon {
                    display: none;
                }
                &--box {
                    input {
                        margin: 5px 0;
                        width: 100%;
                    }
                }
            }
            &__content {
                &:last-child {
                    margin-bottom: 0;
                }
            }
            &__title {
                font-size: 25px;
                margin-bottom: 25px;
            }
            &__content {
                &--title {
                    p {
                        padding-left: 0;
                        margin-left: 0;
                    }
                }
            }
            &__info {
                width: 100%;
                display: block;
            }
            &__name {
                width: 100%;
                h3 {
                    font-size: 14px;
                    margin: 10px 0 5px;
                }
                p.price {
                    margin-bottom: 0;
                }
            }
            &__image{
                width: 50%;
            }
            &__right {
                &--info {
                    width: 56%;
                    dl {
                        width: 100%;
                        margin-bottom: 5px;
                        display: block;
                        dt, dd {
                            width: 100%;
                        }
                        dt {
                            font-weight: bold;
                        }
                        dd {
                            font-size: 13px;
                        }
                    }
                }
                &--btn {
                    width: 42%;
                }
            }
        }
    }
</style>
