<template>
  <header>
    <div class="container">
      <div class="header-top">
        <div class="row align-items-center">
          <div class="col1 col-md-3 col-sm-6 col-5">
            <div class="d-flex align-items-center justify-content-between">
              <div
                class="icon-bar"
                @click="displayMenuBar"
              >
                <span class="bar1" />
                <span class="bar2" />
                <span class="bar3" />
              </div>
              <p class="logo">
                <router-link to="/">
                  <img
                    onerror="this.src='/img/header/logo.svg'"
                    :src="getStoreSettings.logo_url"
                    alt=""
                  >
                </router-link>
              </p>
            </div>
          </div>
          <div class="col2 col-md-6 col-sm-12 col-xs-12">
            <div :class="['search', addClassSearch]">
              <form
                action="/search"
                method="GET"
                @submit.prevent="searchProducts"
              >
                <input
                  v-model="keyword"
                  type="text"
                  name="keyword"
                  :placeholder="$t('header.riceCrack')"
                >
                <!-- <input type="text" name="search" placeholder="abc"> -->
                <router-link :to="'/bar-code-scan'" title="barcode" class="ic-barcode">
                    <img src="/img/icon/icon-barcode.png" alt="barcode scanner" />
                </router-link>
                <button>
                  <img
                    src="/img/header/search.svg"
                    alt="icon search"
                  >
                </button>
              </form>
            </div>
          </div>
          <div class="col3 col-md-3 col-sm-6 col-7">
            <div class="icon-header">
              <ul class="d-flex align-items-center justify-content-end">
                <li
                  class="i-search"
                  @click="searchToggle"
                >
                  <img
                    src="/img/header/search.svg"
                    alt="cart"
                  >
                </li>
                <li class="percent">
                  <router-link
                    to="#"
                    @click="logout"
                  >
                    <img
                      src="/img/header/icon.svg"
                      alt="user"
                    >
                  </router-link>
                </li>
                <li class="user">
                  <router-link to="/private-info">
                    <img
                      src="/img/header/user.svg"
                      alt="user"
                    >
                  </router-link>
                </li>
                <li
                  id="cart"
                  class="cart"
                >
                  <router-link to="/shopping-cart">
                    <img
                      src="/img/header/cart.svg"
                      alt="cart"
                    >
                  </router-link>
                  <span class="num-cart">{{ numProductInCart }}</span>
                </li>
                <li class="notice">
                  <router-link to="/notice">
                    <img
                      src="/img/header/notification.svg"
                      alt="notice"
                    >
                    <span class="num">{{ noticeNum }}</span>
                  </router-link>
                </li>
                <li
                  class="lang"
                  @mouseover="langToggle"
                  @mouseout="langToggle"
                >
                  <span class="img"><img
                    src="/img/header/lang.svg"
                    alt=""
                  ><b>{{ $i18n.locale }}</b></span>
                  <span :class="['lang-toggle', addClassLangOption]">
                    <a
                      href="#"
                      :class="$i18n.locale == 'en' ? 'active' : ''"
                      @click="changeLocale('en')"
                    >EN</a>
                    <a
                      href="#"
                      :class="$i18n.locale == 'ko' ? 'active' : ''"
                      @click="changeLocale('ko')"
                    >KO</a>
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="header-bottom">
        <div class="row align-items-center">
          <div class="ar-title-bar col-xl-2">
            <p
              class="title-bar"
              @mouseover="categoryToggle"
              @mouseout="categoryToggle"
            >
              {{ $t("header.cate") }}<span />
            </p>
          </div>
          <div class="col-xl-9 col-lg-11 col-md-11">
            <nav id="menu">
              <ul class="d-flex flex-wrap align-items-center justify-content-lg-center desktop">
                <li
                  v-for="(category, index) in menuList"
                  :key="index"
                >
                  <router-link :to="category.url">
                    {{ category.name }}
                  </router-link>
                </li>
              </ul>
              <Carousel
                :settings="settings"
                :breakpoints="breakpoints"
                class="mobile"
              >
                <Slide
                  v-for="(category, index) in menuList"
                  :key="index"
                >
                  <router-link :to="category.url">
                    {{ category.name }}
                  </router-link>
                </Slide>
                <template #addons>
                  <Navigation />
                </template>
              </Carousel>
            </nav>
          </div>
          <div class="col-xl-1 col-lg-1 col-md-1">
            <p class="notice-user">
              <router-link to="/notice">
                <img
                  src="/img/header/notification.svg"
                  alt="notice"
                >
                <span class="num">{{ noticeNum }}</span>
              </router-link>
            </p>
          </div>
        </div>
        <div
          id="category-list"
          :class="[addClassMenuBar, addClassCategoryMenu]"
          @mouseover="categoryToggle"
          @mouseout="categoryToggle"
        >
          <div class="nav-list">
            <ul>
              <li
                v-for="(item, idx) in getMenus"
                :key="idx"
                @mouseover="isMobile ? null : submenuToggle($event)"
              >
                <router-link :to="'/category/' + item.category">
                  <span class="img"><img
                    src="/img/category-menu/xo.svg"
                    alt="Food"
                  ></span>
                  <span class="name">{{ item.category_nm1 }}</span>
                  <span
                    v-if="item.sub_menu != ''"
                    class="has-sub"
                  ><img
                    src="/img/icon/arrow_right_thin.svg"
                    alt="arrow"
                  ></span>
                </router-link>
                <span
                  v-if="item.sub_menu != ''"
                  class="icon-open"
                  @click="showSubMenuMobile"
                />
                <ul
                  v-if="item.sub_menu != ''"
                  class="sub-memu"
                >
                  <li
                    v-for="(item1, idx1) in item.sub_menu"
                    :key="idx1"
                    @mouseover="isMobile ? null : submenuToggle($event)"
                  >
                    <router-link :to="'/category/' + item1.category">
                      <span class="name">{{ item1.category_nm1 }}</span>
                      <span
                        v-if="item1.sub_menu != ''"
                        class="has-sub"
                      ><img
                        src="/img/icon/arrow_right_thin.svg"
                        alt="arrow"
                      ></span>
                    </router-link>
                    <span
                      v-if="item1.sub_menu != ''"
                      class="icon-open"
                      @click="showSubMenuMobile"
                    />
                    <ul
                      v-if="item1.sub_menu != ''"
                      class="sub-memu"
                    >
                      <li
                        v-for="(item2, idx2) in item1.sub_menu"
                        :key="idx2"
                        @mouseover="isMobile ? null : submenuToggle($event)"
                      >
                        <router-link :to="'/category/' + item2.category">
                          <span class="name">{{ item2.category_nm1 }}</span>
                          <span
                            v-if="item2.sub_menu != ''"
                            class="has-sub"
                          ><img
                            src="/img/icon/arrow_right_thin.svg"
                            alt="arrow"
                          ></span>
                        </router-link>
                        <span
                          v-if="item2.sub_menu != ''"
                          class="icon-open"
                          @click="showSubMenuMobile"
                        />
                        <ul
                          v-if="item2.sub_menu != ''"
                          class="last"
                        >
                          <li
                            v-for="(item3, idx3) in item2.sub_menu"
                            :key="idx3"
                            @mouseover="isMobile ? null : submenuToggle($event)"
                          >
                            <router-link :to="'/category/' + item3.category">
                              {{ item3.category_nm1 }}
                            </router-link>
                          </li>
                        </ul>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div
        :class="['overlay', addClassMenuBar]"
        @click="displayMenuBar"
      />
    </div>
    <LoadingPage v-if="loading" />
  </header>
</template>

<script>
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import LoadingPage from '../../components/LoadingPage'
import { mapGetters } from "vuex"
export default {
    components: {
        Carousel,
        Slide,
        Navigation,
        LoadingPage,
    },
    data () {
        return {
            loading: false,
            keyword: this.$route.query.keyword,
            showMemu: false,
            categoryMenu: false,
            showSearchInput: false,
            showLang: false,
            noticeNum: 10,
            showParentMenu: false,
            isMobile: window.matchMedia('(max-width: 767px)').matches,
            settings: {
                snapAlign: 'start',
            },
            breakpoints: {
                // 0 and up
                0: {
                    itemsToShow: 4.5,
                },
                // 700px and up
                700: {
                    itemsToShow: 6.5,
                },
                // 1024 and up
                1024: {
                    itemsToShow: 9,
                },
            },
        }
    },
    computed: {
        ...mapGetters(['getMenus', 'getStoreSettings', 'getCenterInfos']),
        addClassMenuBar() {
            return { active: this.showMemu };
        },
        addClassSearch() {
            return { toggle: this.showSearchInput };
        },
        addClassCategoryMenu() {
            return {on: this.categoryMenu};
        },
        addClassLangOption() {
            return {on: this.showLang};
        },
        numProductInCart() {
            if (this.$store.state.products.data != null) {
                return this.$store.state.products.data.length;
            }
            return 0
        },
        menuList() {
            return [
                {
                    name: this.$t("header.planProd"),
                    url: "/plan-product"
                },
                {
                    name: this.$t("header.specOffer"),
                    url: "/discount-product"
                },
                {
                    name: this.$t("header.newProd"),
                    url: "/new-product"
                },
                {
                    name: this.$t("header.best"),
                    url: "/best-product"
                },
                {
                    name: this.$t("header.monthlyPurc"),
                    url: "/monthly-purchase-history"
                },
                {
                    name: this.$t("header.wish"),
                    url: "/wishlist-product"
                },
                {
                    name: this.$t("header.regular"),
                    url: "/regular-delivery"
                },
                {
                    name: this.$t("header.orRegis"),
                    url: "/bar-code-scan"
                },
                {
                    name: this.$t("header.orInqui"),
                    url: "/order-history"
                },
            ]
        }
    },
    mounted() {
        //dispatch menus
        this.$store.dispatch("getMenus");
        //dispatch product in cart
        this.$store.dispatch("getProducts");
        this.$store.dispatch("getWishlistProducts");
        this.$store.dispatch("getRegularProducts");
        this.$store.dispatch("getStoreSettings");
        this.$store.dispatch("getCenterInfos");
        this.$store.dispatch("getStoreInfos")
        // get margin rate
        this.$store.dispatch("getMarginRates")
    },
    created () {
        window.addEventListener('resize', this.mq)
        window.addEventListener('scroll', this.handleScroll)
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.mq)
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        changeLocale(locale) {
            this.loading = true
            this.$i18n.locale = locale
            localStorage.setItem('language', locale)
            // set language API
            axios.put(apiURL + '/user/update-language', {
                    languange: locale
                }).then(res => {
                    if (res.data.status == '200') {
                        // logout => get new token
                        axios.get(apiURL + '/logout')
                        .then(res => {
                            localStorage.removeItem('app_token')
                            window.location.href = '/login'
                        })
                    } else {
                        this.loading = false
                    }
                }).catch((err) => {
                    console.log(err)
                    this.loading = false
                })
        },
        displayMenuBar() {
            this.showMemu = !this.showMemu;
        },
        searchToggle() {
            this.showSearchInput = !this.showSearchInput;
        },
        categoryToggle(e) {
            this.categoryMenu = !this.categoryMenu;
            $(e.target).parent(".header-bottom").children("#category-list").find("li").removeClass("active");
        },
        langToggle() {
            this.showLang = !this.showLang;
        },
        showSubMenuMobile(e) {
            e.stopPropagation();
            $(e.target).next("ul").slideToggle();
            $(e.target).toggleClass("minute");
        },
        submenuToggle(e) {
            $(e.target).parent("li").addClass("active");
            $(e.target).parent("li").children("ul").find("li").removeClass("active");
            $(e.target).parent("li").siblings("li").removeClass("active");
        },
        //check device PC & SP
        mq () {
            this.isMobile = window.matchMedia('(max-width: 767px)').matches;
        },
        handleScroll() {
            let scrollY = window.scrollY;
            let header = $("header").outerHeight();
            if (scrollY > header) {
                $("header").addClass("active");
            } else {
                $("header").removeClass("active");
            }
        },
        logout() {
            localStorage.removeItem('app_token');
            window.location.href = '/login'
        },
        searchProducts() {
            this.$router.push({ path: '/search', query: { keyword: this.keyword } })
        }
    }
}
</script>

<style lang="scss" scoped>
    header {
        border-bottom: 3px solid #ef4130;
        font-size: 16px;
        position: relative;
        -webkit-transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
        -o-transition: all 0.5s ease;
        transition: all 0.5s ease;
        &.active {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            z-index: 11;
            border-bottom: 1px solid #ef4130;
            .header-top {
                padding: 10px 0;
            }
            .header-bottom {
                display: none;
            }
        }
        .header-top {
            padding: 25px 0;
            .logo {
                text-align: left;
                margin-bottom: 0;
                img {
                    max-height: 65px;
                }
            }
            .icon-bar {
                width: 23px;
                position: relative;
                display: none;
                width: 12%;
                margin-right: 10px;
                span {
                    display: block;
                    width: 100%;
                    border-bottom: 1px solid #575e60;
                    &.bar1, &.bar2 {
                        margin-bottom: 35%;
                    }
                }
            }
            .search {
                width: 100%;
                margin: 0 auto;
                form {
                    position: relative;
                }
                input {
                    width: 100%;
                    height: 45px;
                    border: 1px solid #b4b4b4;
                    border-radius: 30px;
                    outline: none;
                    padding-left: 20px;
                }
                button {
                    background: none;
                    border: none;
                    width: 20px;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    right: 20px;
                }
                .ic-barcode {
                    img {
                        width: 20px;
                        position: absolute;
                        right: 60px;
                        top: 50%;
                        transform: translateY(-50%);
                    }
                }
            }
            .icon-header {
                ul {
                    margin-bottom: 0;
                    padding-left: 0;
                    li {
                        width: 25px;
                        margin-left: 11%;
                        &.cart {
                            position: relative;
                            .num-cart {
                                position: absolute;
                                content: "";
                                top: -7px;
                                right: -7px;
                                width: 17px;
                                height: 17px;
                                line-height: 17px;
                                text-align: center;
                                font-size: 11px;
                                background: #ef4130;
                                color: #fff;
                                border-radius: 50%;
                            }
                        }
                        &.lang {
                            position: relative;
                            z-index: 2;
                            width: auto;
                            &:hover {
                                opacity: 1;
                            }
                            span {
                                &.img {
                                    display: flex;
                                    align-items: center;
                                    img {
                                        width: 25px;
                                    }
                                    b {
                                        text-transform: uppercase;
                                        margin-left: 5px;
                                        font-weight: normal;
                                        font-size: 14px;
                                    }
                                }
                                &.lang-toggle {
                                    position: absolute;
                                    top: 100%;
                                    width: auto;
                                    background: #fff;
                                    border: 1px solid #e4e4e4;
                                    right: 0;
                                    font-size: 14px;
                                    border-radius: 5px;
                                    display: none;
                                    &.on {
                                        display: block;
                                    }
                                    a {
                                        display: block;
                                        border-bottom: 1px solid #e4e4e4;
                                        padding: 5px 20px;
                                        &:last-child {
                                            border-bottom: 0;
                                        }
                                    }
                                }
                            }
                            a {
                                color: #000;
                                &.active {
                                    color: #0d6efd;
                                }
                            }
                        }
                        &:hover {
                            opacity: 0.7;
                            transition: all 0.3s ease;
                        }
                        &.notice {
                            position: relative;
                            display: none;
                            span.num {
                                position: absolute;
                                top: 50%;
                                transform: translateY(-50%);
                                left: 0;
                                right: 0;
                                width: 20px;
                                height: 12px;
                                line-height: 12px;
                                text-align: center;
                                font-size: 11px;
                                margin: 0 auto;
                                border-radius: 2px;
                                color: #fff;
                                background: #ef4130;
                            }
                        }
                        &.i-search {
                            display: none;
                        }
                    }
                }
            }
        }
        .header-bottom {
            position: relative;
            .title-bar {
                position: relative;
                background: #ef4130;
                color: #fff;
                height: 45px;
                line-height: 47px;
                text-align: center;
                padding-left: 20px;
                margin-bottom: 0;
                border-radius: 10px 12px 0 0;
                width: 80%;
                cursor: pointer;
                &:before {
                    content: "";
                    position: absolute;
                    background-color: inherit;
                    width: 42px;
                    height: 44px;
                    border-top-right-radius: 30%;
                    transform: rotate(-16deg) skewY(17deg) scale(0.6, 1.05) translate(227%);
                    right: 33%;
                }
                span {
                    position: absolute;
                    left: 25px;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 15px;
                    height: 13.5px;
                    border-bottom: 1px solid #fff;
                    border-top: 1px solid #fff;
                    display: block;
                    &:after {
                        width: 100%;
                        height: 1px;
                        background: #fff;
                        left: 0;
                        top: 50%;
                        transform: translateY(-50%);
                        content: "";
                        position: absolute;
                    }
                }
            }
            #menu {
                ul {
                    padding-left: 0;
                    margin-bottom: 0;
                    overflow-x: auto;
                    scroll-snap-type: x mandatory;
                    scrollbar-width: 2px;
                    &::-webkit-scrollbar {
                        display: none;
                    }
                    li {
                        a {
                            color: #444;
                            text-decoration: none;
                            padding: 0 15px;
                            &:hover {
                                color: #ef4130;
                            }
                        }
                    }
                }
                .mobile {
                    display: none;
                }
            }
            p.notice-user {
                text-align: right;
                margin-bottom: 0;
                position: relative;
                img {
                    width: 30px;
                }
                span.num {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    right: 3px;
                    width: 24px;
                    height: 15px;
                    line-height: 15px;
                    text-align: center;
                    font-size: 12px;
                    margin: 0 auto;
                    border-radius: 2px;
                    color: #fff;
                    background: #ef4130;
                }
            }
        }
        img {
            width: 100%;
        }
        ul {
            list-style: none;
        }
        #category-list {
            display: none;
            -moz-box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
            -webkit-box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
            box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
            padding: 20px 5px 20px 30px;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            width: 100%;
            z-index: 2;
            .nav-list {
                overflow-y: auto;
                height: 250px;
                &::-webkit-scrollbar-track
                {
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.15);
                    background-color: #F5F5F5;
                }

                &::-webkit-scrollbar
                {
                    width: 6px;
                    background-color: #F5F5F5;
                }

                &::-webkit-scrollbar-thumb
                {
                    background-color: #ef4130;
                }
            }
            &.on {
                display: block;
            }
            ul {
                background: #fff;
                font-size: 15px;
                position: relative;
                padding: 0;
                width: 180px;
                margin: 0;
                display: table;
                li {
                    a {
                        position: relative;
                        padding-right: 15px;
                    }
                    span.icon-open {
                        position: absolute;
                        top: 12px;
                        right: 5px;
                        display: block;
                        width: 28px;
                        height: 28px;
                        display: none;
                        cursor: pointer;
                        &:before,
                        &:after{
                            content: "";
                            position: absolute;
                            background-color: black;
                            transition: transform 0.25s ease-out;
                        }
                        /* Vertical line */
                        &:before{
                            top: 50%;
                            left: 50%;
                            width: 2px;
                            height: 14px;
                            margin-left: -1px;
                            margin-top: -7px;
                        }
                        /* horizontal line */
                        &:after{
                            top: 50%;
                            left: 50%;
                            width: 14px;
                            height: 2px;
                            margin-top: -1px;
                            margin-left: -7px;
                        }
                        &.minute {
                            &:before{ transform: rotate(90deg); }
                            &:after{ transform: rotate(180deg); transform: translateY(-50%); }
                        }
                    }
                    span.has-sub {
                        width: 9px;
                        margin-left: 10px;
                        margin-top: -3px;
                        img {
                            width: 100%;
                        }
                    }
                    a {
                        color: #444;
                        padding: 5px;
                        display: flex;
                        align-items: center;
                        span {
                            pointer-events: none;
                        }
                        &:hover {
                            color: #ef4130;
                        }
                        span {
                            display: block;
                            &.img {
                                width: 15px;
                                margin-right: 8px;
                                margin-top: -4px;
                            }
                        }
                    }
                    &.active {
                        > ul {
                            display: block;
                        }
                    }
                    ul {
                        position: absolute;
                        top: 0;
                        left: 100%;
                        height: 100%;
                        // background: #f00;
                        display: none;
                        li {
                            ul {
                                width: 100%;
                            }
                        }
                    }
                }
            }
        }
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 3;
        opacity: 0;
        pointer-events: none;
        &.active {
            opacity: 1;
            transition: all 0.3s;
            pointer-events: initial;
        }
    }


    @media only screen and (max-width: 1199px) {
        header {
            .header-bottom {
                .ar-title-bar {
                    display: none;
                }
            }
        }
    }

    @media only screen and (max-width: 991px) {
        header {
            .header-top {
                .logo {
                    img {
                        width: 100%;
                    }
                }
                .icon-header {
                    ul {
                        li {
                            &.lang {
                                img {
                                    width: 20px;
                                }
                            }
                            img {
                                width: 20px;
                            }
                        }
                    }
                }
            }
            .header-bottom {
                padding: 5px 0;
                #menu ul li a {
                    padding: 0 6px;
                }
            }
        }
    }

    @media only screen and (max-width: 767px) {
        header {
            padding: 15px 0 5px;
            .header-top {
                padding: 0 0 15px;
                .icon-bar {
                    display: block;
                    cursor: pointer;
                }
                .col2 {
                    order: 3;
                }
                .col3 {
                    order: 2;
                }
                .search {
                    padding-top: 15px;
                    transform: translateY(-50px);
                    display: none;
                    &.toggle {
                        display: block;
                        transform: translateY(0);
                    }
                    input {
                        height: 35px;
                        font-size: 14px;
                        border: 1px solid #dddddd;
                    }
                    button {
                        right: 15px;
                        top: 44%;
                        width: 15px;
                    }
                    .ic-barcode {
                        img {
                            right: 40px;
                        }
                    }
                }
            }
            #category-list {
                position: fixed;
                left: -75%;
                top: 0;
                width: 75%;
                height: 100%;
                background: #f3f3f3;
                padding: 3% 5%;
                z-index: 4;
                transition: 0.3s ease-out;
                display: block;
                .nav-list {
                    height: 100%;
                }
                &.active {
                    left: 0;
                    transition: 0.3s ease-in-out;
                }
                ul {
                    padding-left: 0;
                    width: 100%;
                    height: auto;
                    display: block;
                    background: transparent;
                    li {
                        font-size: 14px;
                        position: relative;
                        a {
                            color: #444;
                            text-decoration: none;
                            padding: 10px 0;
                            border-bottom: 1px solid rgba(0,0,0,0.03);
                            &:hover {
                                color: #ef4130;
                            }
                            span.has-sub {
                                display: none;
                            }
                        }
                        span {
                            &.icon-open {
                                display: block;
                            }
                        }
                        ul {
                            position: relative;
                            top: auto;
                            left: auto;
                            display: block;
                            flex-direction: initial;
                            flex-wrap: initial;
                            width: auto;
                            display: none;
                            li {
                                a {
                                    padding-left: 10px;
                                }
                                ul {
                                    &.last {
                                        width: auto;
                                    }
                                    li {
                                        a {
                                            padding-left: 20px;
                                        }
                                        ul {
                                            li {
                                                a {
                                                    padding-left: 30px;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            .header-bottom {
                p.notice-user {
                    display: none;
                }
                #menu {
                    .desktop {
                        display: none !important;
                    }
                    .mobile {
                        display: block;
                    }
                    ol {
                        li {
                            font-size: 13px;
                            a {
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                color: #555;
                            }
                        }
                    }
                }
            }
        }
    }

    @media only screen and (max-width: 480px) {
        header {
            .header-top {
                .icon-header {
                    ul {
                        li {
                            width: 5vw;
                            margin-left: 7%;
                            &.percent {
                                display: none;
                            }
                            &.i-search, &.notice {
                                display: block;
                            }
                            img {
                                width: 5vw;
                            }
                            &.lang {
                                span.img {
                                    img {
                                        width: 20px;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
