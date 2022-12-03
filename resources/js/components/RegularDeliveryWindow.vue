<template>
  <div class="delivery-window">
    <div class="container">
      <div class="delivery-window__content">
        <div class="delivery-window__top">
          <p class="delivery-window__img">
            <img
              :src="url + product.image"
              alt=""
            >
          </p>
          <p class="delivery-window__name">
            MYPICK 매운 닭가슴살 샐러드 195G
          </p>
        </div>
        <div class="delivery-window__bottom">
          <div class="delivery-window__date">
            <p class="delivery-window__bottom--title">
              배송일
            </p>
            <ul>
              <li
                v-for="(item, index) in weekdays"
                :key="index"
                :class="{'active': weekdayInt == index}"
                @click="chooseWeekday(index)"
              >
                {{ item }}
              </li>
            </ul>
          </div>
          <div class="delivery-window__choose">
            <p class="delivery-window__bottom--title">
              옵션
            </p>
            <ul>
              <li
                v-for="(item, index) in options"
                :key="index"
                :class="{'active': optionInt == index}"
                @click="chooseOption(index)"
              >
                {{ item }}
              </li>
            </ul>
          </div>
          <div class="delivery-window__water">
            <p class="delivery-window__bottom--title">
              입수
            </p>
            <ul>
              <li>1개</li>
              <li>1개</li>
              <li>1개</li>
            </ul>
          </div>
          <div class="delivery-window__num">
            <p class="delivery-window__bottom--title">
              소계 / 수량
            </p>
            <div class="delivery-window__num--box">
              <p class="num">
                22,500<span> 원</span>
              </p>
              <div class="delivery-window__number">
                <span
                  class="reduction"
                  @click="decreaseNumber"
                >&#8722;</span>
                <input
                  type="number"
                  min="0"
                  class="no"
                  value="0"
                >
                <span
                  class="increment"
                  @click="increaseNumber"
                >&#43;</span>
              </div>
              <p class="noted-num">
                최대 10개 구매 가능
              </p>
            </div>
          </div>
        </div>
        <div class="delivery-window__btn">
          <p class="save">
            <img
              :src="url + '/img/icon/save.svg'"
              alt=""
            >저장
          </p>
          <p
            class="close"
            @click="closeWindowPortal"
          >
            <img
              :src="url + '/img/icon/close.svg'"
              alt=""
            >닫기
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { countNumber } from '../mixins/countNumber'
export default {
    mixins: [countNumber],
    props: {
        open: Boolean,
        product: {
            type: Object,
            default: () => {}
        },
        url: {
            type: String,
            default: ''
        }
    },
    emits: ["closeRDWindow"],
    data() {
        return {
            isOpen: false,
            weekdayInt: 0,
            optionInt: 0,
            weekdays: [
                "월", "화", "수", "목", "금", "보류"
            ],
            options: [
                "낱개", "소포장", "박스"
            ]
        }
    },
    methods: {
        closeWindowPortal() {
            this.$emit("closeRDWindow", this.isOpen)
        },
        chooseWeekday(idx) {
            this.weekdayInt = idx
        },
        chooseOption (idx) {
            this.optionInt = idx
        }
    }
}
</script>

<style lang="scss" scoped>
    .delivery-window {
        max-width: 500px;
        margin: 0 auto;
        padding: 25px 10px;
        &__top {
            display: flex;
            align-items: center;
        }
        &__img {
            width: 25%;
            border: 1px solid #e8e8e8;
            img {
                width: 100%;
            }
        }
        &__name {
            width: 75%;
            font-size: 14px;
            padding-left: 20px;
        }
        &__bottom {
            display: flex;
            justify-content: center;
            &--title {
                font-size: 14px;
                text-align: center;
            }
        }
        &__date {
            width: 25%;
            ul {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                padding: 0 10%;
                margin: 0;
                li {
                    display: block;
                    text-align: center;
                    border: 1px solid #ddd;
                    width: 48%;
                    font-size: 12px;
                    padding: 5px 0;
                    cursor: pointer;
                    margin-bottom: 4px;
                    &.active {
                        background: #f8e25b;
                    }
                }
            }
        }
        &__choose {
            width: 25%;
            ul {
                padding: 0 10%;
                margin: 0;
                li {
                    display: block;
                    text-align: center;
                    border: 1px solid #ddd;
                    width: 100%;
                    font-size: 12px;
                    padding: 5px 0;
                    cursor: pointer;
                    margin-bottom: 4px;
                    &:last-child {
                        margin-bottom: 0;
                    }
                    &.active {
                        background: #f8e25b;
                    }
                }
            }
        }
        &__water {
            width: 15%;
            ul {
                padding: 0;
                margin: 0;
                text-align: center;
                li {
                    padding: 5px 0;
                    font-size: 14px;
                    margin-bottom: 4px;
                    &:last-child {
                        margin-bottom: 0;
                    }
                }
            }
        }
        &__num {
            width: 35%;
            text-align: center;
            &--box {
                border: 1px solid #e8e8e8;
                padding: 6px 10px;
                margin-bottom: 4px;
                border-radius: 2px;
            }
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
        &__number {
            margin: 5px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            input {
                width: 40px;
                text-align: center;
                outline: none;
                border: 1px solid #ccc;
                border-radius: 0;
                height: 25px;
                margin: 0 1px;
                font-size: 14px;
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
        }
        &__btn {
            text-align: center;
            margin-top: 20px;
            p {
                display: inline-block;
                padding: 3px 15px;
                font-size: 13px;
                border: 1px solid #e8e8e8;
                border-radius: 25px;
                margin: 0 10px;
                cursor: pointer;
                img {
                    width: 12px;
                    margin-right: 5px;
                }
            }
        }
    }
    @media only screen and (max-width: 480px) {
        .delivery-window {
            &__date {
                ul {
                    padding: 0;
                }
            }
            &__num {
                &--box {
                    p.num {
                        font-size: 16px;
                    }
                }
            }
            &__bottom {
                &--title {
                    font-size: 12px;
                }
            }
            &__btn {
                p {
                    margin: 0 5px;
                }
            }
        }
    }
</style>
