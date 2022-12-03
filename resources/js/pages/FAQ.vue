<template>
  <div class="container">
    <div class="faq">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              HOME
            </router-link>
          </li>
          <li>
            <router-link to="#">
              Library
            </router-link>
          </li>
          <li>
            <router-link to="#">
              FAQ
            </router-link>
          </li>
        </ul>
      </div>
      <div class="content">
        <!--  -->
        <MyPageHeader />
        <div class="row content-body">
          <div class="col-lg-2 content-tabs">
            <LeftMypage />
          </div>
          <div class="col-lg-10 faq-content">
            <section class="section">
              <div class="title-page">
                <img
                  src="/img/icon/Picture1.png"
                  alt=""
                  srcset=""
                >
                <h3 class="">
                  FAQ 상품
                </h3>
              </div>
              <div class="table-content">
                <div class="table-title">
                  <p class="type col-lg-6">
                    구분
                  </p>
                  <p class="type col-lg-6">
                    자주찾는 질문
                  </p>
                </div>
                <div class="table-item">
                  <vue-collapsible-panel-group accordion>
                    <vue-collapsible-panel :expanded="true">
                      <template
                        #title
                        class="row"
                      >
                        <span class="question-label">상품</span>
                        <span class="question">
                          Q. 상품 및 서비스에 대한 문의는 어디에 하나요 ?</span>
                      </template>
                      <template #content>
                        <span class="answer">
                          A. 홈페이지 내 1:1 게시판 혹은 1555-1555 고객센터로
                          연락을 바랍니다.
                        </span>
                      </template>
                    </vue-collapsible-panel>
                  </vue-collapsible-panel-group>
                </div>
                <div class="table-item">
                  <vue-collapsible-panel-group accordion>
                    <vue-collapsible-panel :expanded="true">
                      <template
                        #title
                        class="row"
                      >
                        <span class="question-label">상품</span>
                        <span class="question">
                          Q. 상품 및 서비스에 대한 문의는 어디에 하나요 ?</span>
                      </template>
                      <template #content>
                        <span class="answer">
                          A. 홈페이지 내 1:1 게시판 혹은 1555-1555 고객센터로
                          연락을 바랍니다.
                        </span>
                      </template>
                    </vue-collapsible-panel>
                  </vue-collapsible-panel-group>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import LeftMypage from "../components/LeftMypage";
import MyPageHeader from "../components/MyPageHeader";
import {
  VueCollapsiblePanelGroup,
  VueCollapsiblePanel,
} from "@dafcoe/vue-collapsible-panel";
import "@dafcoe/vue-collapsible-panel/dist/vue-collapsible-panel.css";
export default {
  name: "Faq",
  components: {
    VueCollapsiblePanelGroup,
    VueCollapsiblePanel,
    LeftMypage,
    MyPageHeader,
  },
};
</script>
<style lang="scss">
$--bg-gray: #f4f3f3;
$--border: #e9e8e8;
$--bg-white: #fff;
$text-color: #484848;

.faq {
  padding: 0 0 20px;
  .faq-content {
    .title-page {
      display: flex;
      margin-bottom: 20px;
      h3 {
        margin: 0 15px;
        padding: 6px 0;
        font-size: 22px;
      }
    }
    .table-title {
      justify-content: space-around;
    }
    .table-item {
      border-bottom: 2px solid $--border;
      font-size: 14px;
    }

    .vcp__header-icon {
      color: $--border;
    }

    .vcpg {
      border: none;
    }
    .vcpg[data-v-63943e43] .vcp__header {
      background-color: $--bg-white;
      height: auto !important;
      .vcp__header-title {
        width: 100%;
        span.question {
          width: 80%;
        }
        .question-label {
          // flex-basis: 100px;
          width: 20%;
        }
      }
    }
    .vcpg[data-v-63943e43] .vcp__header:hover {
      background-color: $--bg-white;
    }
    .vcp__body {
      border-top: none;
      margin-bottom: 5px;
      height: auto !important;
    }
    .vcp__body-content {
      background: $--bg-gray;
      display: flex;
      width: 100%;
      padding-left: 20.5%;
    }

    .row.table-title {
      background: $--bg-gray;
    }

    .table-title {
      display: flex;
      background: $--bg-gray;
      color: $text-color;
    }

    p.type.col-lg-6 {
      margin-bottom: 0;
      padding: 2px 0 2px 12px;
      font-weight: 500;
    }
  }
}
@media only screen and (max-width: 767px) {
  .faq {
    .vcp__body-content {
      .answer {
        padding-left: 0 !important;
      }
    }
  }
}
</style>

