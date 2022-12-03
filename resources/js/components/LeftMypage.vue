<template>
  <div
    :class="['collapsible-sidebar', addClassToggleTab]"
    @click="toggleTab"
  >
    <button
      type="button"
      class="collapsible-sidebar-toggle"
    >
      <img
        src="/img/icon/down-arrow.svg"
        alt=""
      >
    </button>
    <span class="sidenav-title">{{ $t("left_mypage.myPage") }} </span>
    <div :class="['collapsible-sidebar-body', addClassToggleTab]">
      <ul>
        <li>
          <router-link
            to="/private-info"
            active-class="active"
          >
            {{ $t("left_mypage.personal") }}
          </router-link>
        </li>
        <li>
          <router-link
            to="/store-info"
            active-class="active"
          >
            {{ $t("left_mypage.store") }}
          </router-link>
        </li>
        <li>
          <router-link
            to="/deposit"
            active-class="active"
          >
            {{ $t("left_mypage.deposit") }}
          </router-link>
        </li>
        <!-- <li>
          <router-link to="/return-notice" active-class="active"
            >반품 공지</router-link
          >
        </li> -->
        <!-- <li>
          <router-link to="/faq" active-class="active">FAQ</router-link>
        </li> -->
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "LeftMypage",
  data() {
    return {
      isActive: false,
    };
  },
  computed: {
    addClassToggleTab() {
      return this.isActive ? "active" : "";
    },
  },
  methods: {
    toggleTab() {
      this.isActive = !this.isActive;
    },
  },
};
</script>


<style lang="scss">
$bg-dark: #f4f3f3;
$text-dark: #444444;
$red: #ef4130;

.collapsible-sidebar {
  background: #f4f3f3;
  margin: 0 0 15px 0;
  padding: 10px 0;
  overflow: hidden;
  position: relative;
  font-size: 14px;
  ul {
    margin-bottom: 0;
  }
  &.active {
    .collapsible-sidebar-body {
      transition: all 0.3s ease-in-out;
      display: block;
    }
    .collapsible-sidebar-toggle {
      display: block;
      img {
        transform: rotate(-180deg);
      }
    }
  }
  .sidenav-title {
    display: block;
    margin-left: 10px;
    font-size: 18px;
    font-weight: 500;
  }
  ul {
    padding: 0 15px;

    li {
      padding: 7px 0;

      .active {
        color: $red;
      }
    }
    a {
      color: $text-dark;
    }
  }
  .collapsible-sidebar-toggle {
    position: absolute;
    top: 13px;
    right: 10px;
    padding: 0;
    border: 0;
    background: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    img {
      display: block;
      transition: all 0.3s ease;
    }
  }

  .collapsible-sidebar-body {
    display: none;
  }
}
@media (min-width: 1024px) {
  .collapsible-sidebar {
    .collapsible-sidebar-toggle img {
      display: none;
    }
    .sidenav-title {
      display: none;
    }
    .collapsible-sidebar-body {
      display: block;
    }
  }
}
</style>
