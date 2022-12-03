<template>
  <div class="order-completed">
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("order_complete.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_complete.cart") }}
            </router-link>
          </li>
        </ul>
      </div><div class="row">
        <div class="col-lg-9">
          <div class="order-completed__step">
            <ul class="d-flex align-items-center justify-content-start">
              <li><span>01</span>{{ $t("order_complete.cart") }}</li>
              <li class="gray">
                <span>02</span>{{ $t("order_complete.orderForm") }}
              </li>
              <li class="active">
                <span>03</span>{{ $t("order_complete.orderComp") }}
              </li>
            </ul>
          </div>
          <div class="order-completed__content">
            <div class="row">
              <div class="col-lg-2 col-md-2 col-3">
                <p class="icon">
                  <img
                    src="/img/icon/complete.svg"
                    alt=""
                  >
                </p>
              </div>
              <div class="col-lg-10 col-md-10 col-9">
                <h3 class="title">
                  {{ $t("order_complete.your") }}
                </h3>
                <dl>
                  <dt>{{ $t("order_complete.orderNum") }}</dt>
                  <dd class="fw-bold">
                    {{ orderId }}
                  </dd>
                </dl>
                <dl>
                  <dt>{{ $t("order_complete.orderTime") }}</dt>
                  <dd>{{ formatTimeStamp(createdDate) }}</dd>
                </dl>
                <dl v-if="PayMethod">
                  <dt>{{ $t("order_complete.method") }}: </dt>
                  <dd class="fw-bold">
                    {{ PayMethod }}
                  </dd>
                </dl>
                <dl v-if="VbankBankName">
                  <dt>{{ $t("order_complete.bankName") }}: </dt>
                  <dd class="fw-bold">
                    {{ VbankBankName }}
                  </dd>
                </dl>
                <dl v-if="VbankNum">
                  <dt>{{ $t("order_complete.bankNumber") }}: </dt>
                  <dd class="fw-bold">
                    {{ VbankNum }}
                  </dd>
                </dl>
                <dl v-if="BankCode">
                  <dt>{{ $t("order_complete.bankNumber") }}: </dt>
                  <dd class="fw-bold">
                    {{ BankCode }}
                  </dd>
                </dl>
                <div class="order-completed__btn">
                  <router-link to="/order-history">
                    {{ $t("order_complete.check") }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <RightBarOld />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import RightBarOld from "../components/RightBarOld.vue"
    export default {
        components: {
            RightBarOld,
        },
        data() {
            return {
                orderId: this.$route.query.orderId,
                createdDate: this.$route.query.createdDate,
            }
        },
        computed: {
            VbankBankName() {
                return this.$route.query.VbankBankName
            },
            VbankNum() {
                return this.$route.query.VbankNum
            },
            PayMethod() {
                return this.$route.query.PayMethod
            },
            BankCode() {
                return this.$route.query.BankCode
            },
        },
        methods: {
            formatTimeStamp(time) {
                return new Date(parseInt(time))
            }
        }
    }
</script>

<style lang="scss" scoped>
    .order-completed {
        padding: 0 0 50px;
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
                        border-left: 12px solid #f1f1f1;
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
        &__content {
            width: 70%;
            margin: 0 auto;
            padding: 50px 0 0;
            p.icon {
                img {
                    width: 100%;
                }
            }
            h3.title {
                font-size: 25px;
                margin-bottom: 30px;
            }
            dl {
                display: flex;
                align-items: center;
                margin: 0 0 15px 0;
                dt {
                    width: 25%;
                    font-weight: normal;
                }
                dd {
                    width: 75%;
                    margin: 0;
                }
            }
        }
        &__btn {
            display: inline-block;
            padding: 7px 25px;
            background: #0073A8;
            color: #fff;
            border-radius: 5px;
            text-transform: uppercase;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s;
            a {
                color: #fff;
            }
            &:hover {
                opacity: 0.8;
            }
        }
    }
    @media only screen and (max-width: 991px) {
        .order-completed {
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
            &__content {
                width: 100%;
                padding: 10px 0;
                h3.title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                dl {
                    font-size: 14px;
                    dt {
                        width: 35%;
                    }
                    dd {
                        width: 65%;
                    }
                }
            }
        }
    }
</style>
