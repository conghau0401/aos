<template>
  <div class="container">
    <div class="breadcrumbs">
      <ul>
        <li>
          <router-link to="#">
            {{ $t("notice.home") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("notice.notice") }}
          </router-link>
        </li>
        <li>
          <router-link to="#">
            {{ $t("notice.return") }}
          </router-link>
        </li>
      </ul>
    </div>
    <div class="body">
      <div class="header d-flex justify-content-between align-items-center">
        <h3>{{ $t("notice.notice") }}</h3>
        <ul class="d-flex">
          <li>{{ $t("notice.allnoti") }}</li>
          <li>{{ $t("notice.unread") }}</li>
          <li class="active">
            {{ $t("notice.returnNoti") }}
          </li>
          <li>{{ $t("notice.pre") }}</li>
          <li>{{ $t("notice.with") }}</li>
          <li>{{ $t("notice.prodInquiry") }}</li>
          <li>{{ $t("notice.inquiry") }}</li>
          <li>
            <a
              href="https://help.ff.kdlcenter.net/space/manualffkdlc/6717448/2092.1+%EC%B9%B4%ED%85%8C%EA%B3%A0%EB%A6%AC"
              target="_blank"
            >{{ $t("notice.faq") }}</a>
          </li>
        </ul>
      </div>
      <div class="body-content">
        <div class="title d-flex justify-content-between mt-2">
          <h6 class="fw-bold d-flex align-items-center">
            <span class="img"><img src="/img/header/notification.svg"></span>
            <span class="text">{{ $t("notice.return") }}</span>
          </h6>
          <div class="d-flex align-items-center">
            <p class="border round mx-2">
              {{ $t("notice.full") }}
            </p>
            <p class="border round">
              <img
                src="/img/icon/delete.svg"
                class="icon"
              >{{ $t("notice.delete") }}
            </p>
          </div>
        </div>
        <div class="noti-tab">
          <ul
            v-for="(item, index) in items"
            :key="index"
            class="p-0"
          >
            <li class="border-bottom d-flex py-4">
              <img
                :src="item.avt"
                class="rounded"
              >
              <ul>
                <li>{{ item.title }} {{ item.content }}</li>
                <li>{{ item.time }} {{ item.author }}</li>
              </ul>
              <span
                class="x-btn"
                @click="deleteObject(index)"
              >&times;</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "Notice",
  data() {
    return {
      items: [
        {
          avt: "https://randomuser.me/api/portraits/thumb/men/75.jpg",
          title: this.$t("notice.return1"),
          content: this.$t("notice.thereturn"),
          time: this.$t("notice.hours"),
          author: this.$t("notice.incharge"),
        },
        {
          avt: "https://randomuser.me/api/portraits/thumb/men/75.jpg",
          title: this.$t("notice.return1"),
          content: this.$t("notice.thereturn"),
          time: this.$t("notice.hours"),
          author: this.$t("notice.incharge"),
        },
        {
          avt: "https://randomuser.me/api/portraits/thumb/men/75.jpg",
          title: this.$t("notice.return1"),
          content: this.$t("notice.thereturn"),
          time: this.$t("notice.hours"),
          author: this.$t("notice.incharge"),
        },
      ],
    };
  },
  methods: {
    deleteObject(index) {
      this.items.splice(index, 1);
    },
  },
};
</script>
<style lang="scss">
.body {
  font-size: clamp(12px, 2vw, 15px);

  .header {
    margin-bottom: 25px;
    h3 {
      margin: 0;
    }
    ul {
      margin: 0;
    }
    li {
      a {
        color: #444;
      }
      &.active {
        color: red;
      }
      &:not(:last-child):after {
        content: "•";
        padding: 0 5px;
      }
    }
  }
  .body-content {
    margin-bottom: 50px;
    .title {
      margin-bottom: 20px;
      img {
        width: 30px;
      }

      .icon {
        width: 8px;
        margin-right: 5px;
      }
      .round {
        padding: 5px 20px;
        border-radius: 20px;
        margin: 0;
      }
      h6 {
        margin: 0;
        span {
          display: block;
          &.img {
            margin-right: 10px;
          }
        }
      }
    }
  }
  .noti-tab {
    ul {
      margin-bottom: 0;
      li {
        position: relative;
        &:nth-child(2) {
          font-size: clamp(12px, 2vw, 12px);
        }
        .x-btn {
          font-size: clamp(20px, 2vw, 30px);
          color: rgb(116, 108, 108);
          position: absolute;
          right: 0;
          top: 50%;
          transform: translateY(-50%);
          cursor: pointer;
        }
      }
    }
    img {
      height: 100%;
    }
  }
}
@media only screen and (max-width: 991px) {
  .body {
    .header {
      overflow-x: auto;
      white-space: nowrap;
      display: block !important;
      h3 {
        margin-bottom: 15px;
      }
      ul {
        padding: 0;
        margin-bottom: 15px;
        li {
          display: block;
        }
      }
    }
    .body-content {
      .title {
        font-size: 12px;
      }
      .noti-tab {
        font-size: 14px;
        ul {
          padding-right: 10%;
        }
      }
    }
  }
}
</style>
