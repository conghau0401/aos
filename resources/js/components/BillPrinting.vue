<template>
  <section class="billPrinting py-3 container">
    <div class="header d-flex align-items-center justify-content-center">
      <p class="icon border px-2 py-1 mx-2">
        <img
          src="/img/icon/print.svg"
          alt=""
          srcset=""
        >인쇄
      </p>
      <p class="icon border px-2 py-1">
        <img
          src="/img/icon/close.svg"
          alt=""
          srcset=""
        >닫기
      </p>
    </div>
    <div class="bill">
      <div class="upper-part">
        <div class="row">
          <div class="col-6 border border-none-bottom border-none-right">
            <div class="d-flex align-items-center p-3">
              <h1>주문 영수증</h1>
              <h6>공급받는자 보관용</h6>
            </div>
          </div>
          <div class="col-6 border border-none-bottom">
            <div class="p-1">
              <p>
                영수 일시 : <span>2022</span>년 <span>01</span>월
                <span>20</span>일
              </p>
              <p>주문 번호 : <span>20220120-0000329</span></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6 px-0 border border-none-right">
            <div class="row">
              <div
                class="col-1"
                style="width: 45px"
              >
                <p style="display: table-caption; padding: 14px">
                  공급자
                </p>
              </div>
              <div
                class="col-11 border-left"
                style="width: calc(100% - 45px)"
              >
                <div class="row">
                  <div class="col-3 p-0 border-right border-bottom">
                    <div class="h-50 border-bottom">
                      <p>등록번호</p>
                    </div>
                    <div class="">
                      <p>상호 (법인명)</p>
                    </div>
                  </div>
                  <div class="col-3 p-0 border-right border-bottom">
                    <div class="h-50 border-bottom">
                      <span>123-45-67890 </span>
                    </div>
                    <div class="">
                      <p>포항시 주식회사</p>
                    </div>
                  </div>
                  <div class="col-3 p-0 border-right border-bottom">
                    <div class="h-50 border-bottom">
                      <p>성명 (대표자)</p>
                    </div>
                    <div class="">
                      <p>박포항</p>
                    </div>
                  </div>
                  <div class="col-3 p-4 border-bottom">
                    <img
                      src="/img/icon/stamp.png"
                      alt=""
                      srcset=""
                    >
                  </div>
                </div>
                <div class="row">
                  <div class="col-3 p-0 border-right">
                    <div class="h-50 border-bottom">
                      <p>사업장 소재지</p>
                    </div>
                    <div class="">
                      <p>업태</p>
                    </div>
                  </div>
                  <div class="col-9 p-0">
                    <div class="h-50 border-bottom">
                      <span>포항시 포항구 포항읍 포항면 포항 000-000 </span>
                    </div>
                    <div class="row h-50">
                      <div class="col-4 border-right">
                        도매, 상품중개업
                      </div>
                      <div class="col-4 border-right">
                        종목
                      </div>
                      <div class="col-4">
                        도매, 상품중개업
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 px-0 border">
            <div class="row h-100">
              <div
                class="col-1 border-right"
                style="width: 8%; padding: 9.5px 0"
              >
                <p
                  style="display: table-caption"
                  class="p-3"
                >
                  공 급 받 는 자
                </p>
              </div>
              <div
                class="col-11"
                style="width: 92%"
              >
                <div class="row border-bottom">
                  <div class="col-3 border-right p-2">
                    상호
                  </div>
                  <div class="col-9 p-2">
                    좋은 마트
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- purchase items list  -->
        <div class="row border border-none-top">
          <div class="col-1 p-2 border-right">
            월
          </div>
          <div class="col-1 p-2 border-right">
            월
          </div>
          <div class="col-2 p-2 border-right">
            품목
          </div>
          <div class="col-2 p-2 border-right">
            상품코드
          </div>
          <div class="col-1 p-2 border-right">
            수량
          </div>
          <div class="col-1 p-2 border-right">
            단가
          </div>
          <div class="col-1 p-2 border-right">
            공급가액
          </div>
          <div class="col-1 p-2 border-right">
            부가세액
          </div>
          <div class="col-1 p-2 border-right">
            공병
          </div>
          <div class="col-1 p-2">
            컨테이너
          </div>
        </div>
        <div class="row border border-none-top">
          <div class="col-1 p-2 border-right">
            01
          </div>
          <div class="col-1 p-2 border-right">
            20
          </div>
          <div class="col-2 p-1 border-right">
            세타필 모이스춰라이징로션 591ML [BOX]
          </div>
          <div class="col-2 p-1 border-right">
            220025700009871
          </div>
          <div class="col-1 p-2 border-right">
            6,490
          </div>
          <div class="col-1 p-2 border-right">
            632,790
          </div>
          <div class="col-1 p-2 border-right">
            70,310
          </div>
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2" />
        </div>
        <!-- <div class="row border border-none-top">
          <div class="col-1 p-2 border-right">01</div>
          <div class="col-1 p-2 border-right">20</div>
          <div class="col-2 p-1 border-right">
            니베아 아트릭스 핸드앤네일로션 400ML
          </div>
          <div class="col-2 p-1 border-right">220025700009871</div>
          <div class="col-1 p-2 border-right">6,490</div>
          <div class="col-1 p-2 border-right">632,790</div>
          <div class="col-1 p-2 border-right">70,310</div>
          <div class="col-1 p-2 border-right"></div>
          <div class="col-1 p-2 border-right"></div>
          <div class="col-1 p-2"></div>
        </div> -->
        <!-- product type total amount -->
        <div class="row border border-none-top">
          <div class="col-6 py-1 px-2">
            □ 공산품
          </div>
          <div class="col-1 py-1">
            소계
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>730,890</span>
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>81,210</span>
          </div>
          <div class="col-1 py-1 border-right" />
          <div class="col-1 py-1" />
        </div>
        <!-- another purchase items list -->
        <div class="row border border-none-top">
          <div class="col-1 p-2 border-right">
            01
          </div>
          <div class="col-1 p-2 border-right">
            20
          </div>
          <div class="col-2 p-1 border-right">
            세타필 모이스춰라이징로션 591ML [BOX]
          </div>
          <div class="col-2 p-1 border-right">
            220025700009871
          </div>
          <div class="col-1 p-2 border-right">
            6,490
          </div>
          <div class="col-1 p-2 border-right">
            632,790
          </div>
          <div class="col-1 p-2 border-right">
            70,310
          </div>
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2" />
        </div>
        <!--   product type total amount -->
        <div class="row border border-none-top">
          <div class="col-6 py-1 px-2">
            ♤ 신선 상품
          </div>
          <div class="col-1 py-1">
            소계
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>730,890</span>
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>81,210</span>
          </div>
          <div class="col-1 py-1 border-right" />
          <div class="col-1 py-1" />
        </div>
        <!-- another purchase items list -->
        <div class="row border border-none-top">
          <div class="col-1 p-2 border-right">
            01
          </div>
          <div class="col-1 p-2 border-right">
            20
          </div>
          <div class="col-2 p-1 border-right">
            세타필 모이스춰라이징로션 591ML [BOX]
          </div>
          <div class="col-2 p-1 border-right">
            220025700009871
          </div>
          <div class="col-1 p-2 border-right">
            6,490
          </div>
          <div class="col-1 p-2 border-right">
            632,790
          </div>
          <div class="col-1 p-2 border-right">
            70,310
          </div>
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2 border-right" />
          <div class="col-1 p-2" />
        </div>
        <!--   product type total amount -->
        <div class="row border border-none-top">
          <div class="col-6 py-1 px-2">
            △ 주류
          </div>
          <div class="col-1 py-1">
            소계
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>730,890</span>
          </div>
          <div class="col-1 py-1 px-2 border-right">
            <span>81,210</span>
          </div>
          <div class="col-1 py-1 border-right" />
          <div class="col-1 py-1" />
        </div>
        <!-- total amount -->
        <div class="lower-part row border border-none-top">
          <div class="col-3 p-2">
            예치금
          </div>
          <div class="col-1 p-2 border-right">
            2,092,100
          </div>
          <div class="col-1 p-2">
            배송비
          </div>
          <div class="col-1 p-2 border-right">
            9,000
          </div>
          <div class="col-4 p-2 border-right">
            합계1,029,800
          </div>
          <div class="col-1 p-2" />
        </div>
        <div class="footer border border-none-top">
          <div class="row py-3 px-2">
            <p class="col-6">
              입금 계좌 : 농협 123-4567-8901-12 (주)포항시
            </p>
            <p class="col-6 text-end">
              위 금액을 영수 함
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script>
export default {
  name: "BillPrinting",
};
</script>
<style lang="scss" scoped>
.billPrinting {
  font-size: 13px;

  div {
    white-space: break-spaces;
  }
  .header {
    img {
      width: 20px;
    }
  }
  .bill {
    color: rgb(5, 153, 221);

    .row {
      --bs-gutter-x: 0 !important;
    }
    .fix-width {
      width: 55px;
    }
    .border {
      border: 1px solid rgb(5, 153, 221) !important;
    }
    .border-top {
      border-top: 1px solid rgb(5, 153, 221) !important;
    }
    .border-bottom {
      border-bottom: 1px solid rgb(5, 153, 221) !important;
    }
    .border-left {
      border-left: 1px solid rgb(5, 153, 221) !important;
    }
    .border-right {
      border-right: 1px solid rgb(5, 153, 221) !important;
    }
    .border-none-left {
      border-left: none !important;
    }
    .border-none-top {
      border-top: none !important;
    }
    .border-none-right {
      border-right: none !important;
    }
    .border-none-bottom {
      border-bottom: none !important;
    }
    .border-bottom {
      border-bottom: 1px solid rgb(5, 153, 221) !important;
    }
    .upper-part {
      width: 1200px;
    }
    .lower-part {
      background: lightgoldenrodyellow;
    }
  }
}
@media only screen and (max-width: 1024px) {
  .billPrinting {
    .bill {
      overflow-x: auto;
      white-space: nowrap;
    }
  }
}
</style>
