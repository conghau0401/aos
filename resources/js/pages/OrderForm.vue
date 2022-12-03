<template>
  <section
    class="order-form"
    onload="jusoCallBack('roadAddrPart1', 'addrDetail', 'zipNo')"
  >
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("order_form.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("order_form.cart") }}
            </router-link>
          </li>
        </ul>
      </div>
      <form
        id="js-form"
        method="post"
        @submit.prevent="checkoutSubmit"
      >
        <input
          id="has-sameday"
          v-model="activePopupAddressSameDay"
          type="hidden"
          name="has_sameday"
        >
        <div class="row">
          <div class="col-lg-9">
            <div class="order-form__step">
              <ul class="d-flex align-items-center justify-content-start">
                <li><span>01</span>{{ $t("order_form.cart") }}</li>
                <li class="active">
                  <span>02</span>{{ $t("order_form.orderForm") }}
                </li>
                <li><span>03</span>{{ $t("order_form.orderComp") }}</li>
              </ul>
            </div>
            <div
              v-if="getOrderType == 'sameDay' || getOrderType == null"
              class="order-form__delivery"
            >
              <div class="order-form__delivery--title">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("cart.sameDay") }} ({{ countSameDay != null ? countSameDay : 0 }})<span>&#43;</span>
                </h3>
                <div
                  v-if="countSameDay > 0"
                  class="wrap-date"
                >
                  <div class="row">
                    <div class="col-sm-6" />
                    <div class="col-sm-6">
                      <dl>
                        <dt>
                          <label
                            for="time_same_day"
                            class="form-label"
                          >{{ $t("order_form.pickTime") }} </label>
                        </dt>
                        <dd>
                          <Datepicker
                            v-model="timeSameDay"
                            required
                            time-picker
                            auto-apply
                            :min-time="minTimeSameDay"
                            :minutes-increment="10"
                            placeholder=""
                          >
                            <template #input-icon>
                              <svg
                                version="1.1"
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 32 32"
                                class="dp__icon dp__input_icon dp__input_icons"
                              ><path d="M16 1.333c-8.095 0-14.667 6.572-14.667 14.667s6.572 14.667 14.667 14.667c8.095 0 14.667-6.572 14.667-14.667s-6.572-14.667-14.667-14.667zM16 4c6.623 0 12 5.377 12 12s-5.377 12-12 12c-6.623 0-12-5.377-12-12s5.377-12 12-12z" /><path d="M14.667 8v8c0 0.505 0.285 0.967 0.737 1.193l5.333 2.667c0.658 0.329 1.46 0.062 1.789-0.596s0.062-1.46-0.596-1.789l-4.596-2.298c0 0 0-7.176 0-7.176 0-0.736-0.597-1.333-1.333-1.333s-1.333 0.597-1.333 1.333z" /></svg>
                            </template>
                          </Datepicker>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <div class="order-form__delivery--content">
                  <!-- <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nobis doloribus recusandae velit tempora? Explicabo ab voluptas nobis quis ratione voluptate qui, doloremque autem fuga, adipisci fugiat, eius modi illum totam?</p> -->
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
                          <b v-if="idx == 1">{{ $t("order_form.indust") }}</b>
                          <b v-else-if="idx == 2">{{ $t("order_form.miscell") }}</b>
                          <b v-else>{{ $t("order_form.alcohol") }}</b>
                          <span>{{ $t("order_form.default") }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="container-box__product box-product">
                      <ProductGroupItem
                        :product-info="item"
                        :page="getPage"
                        @total-price-type="totalPriceType($event, idx)"
                      />
                    </div>
                  </div>
                </div>
                <!-- <p class="order-form__delivery--btn">
                                    <router-link to="#">센터수령 안내 문구 Display</router-link>
                                </p> -->
              </div>
              <div
                v-if="countSameDay > 0"
                class="order-form__main order-form__sameday"
              >
                <p class="order-form__main--title">
                  {{ $t("order_form.please") }}
                </p>
                <dl class="info">
                  <dt>
                    <label
                      for="info"
                      class="form-label"
                    />
                  </dt>
                  <dd class="title-address-box d-flex align-items-center flex-wrap justify-content-between">
                    <p>
                      <input
                        id="sameday-member-address"
                        type="radio"
                        name="radio-group"
                        value="회원정보 동일"
                        checked
                      >
                      <label
                        for="sameday-member-address"
                        @click="addressOption(0, true)"
                      >{{ $t("order_form.same") }}</label>
                    </p>
                    <p
                      class="btn-popup-address"
                      @click="activeSameDay(); togglePopupAddress(0);"
                    >
                      {{ $t('order_form.manageAddress') }}
                    </p>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="receiverSameDay"
                      class="form-label"
                    >{{ $t("order_form.name") }}</label>
                  </dt>
                  <dd>
                    <input
                      id="receiverSameDay"
                      v-model="state.receiverSameDay"
                      type="text"
                      name="receiverSameDay"
                      class="form-control"
                      placeholder="홍길동"
                    >
                    <p
                      v-for="error of v$.receiverSameDay.$errors"
                      :key="error.$uid"
                      class="error-msg"
                    >
                      {{ error.$message }}
                    </p>
                  </dd>
                </dl>
                <dl class="code">
                  <dt>
                    <label
                      for="codeSameDay"
                      class="form-label"
                    >{{ $t("order_form.addr") }}</label>
                  </dt>
                  <dd>
                    <div class="row">
                      <div class="code-box col-lg-6">
                        <input
                          id="codeSameDay"
                          v-model="state.codeSameDay"
                          type="number"
                          name="codeSameDay"
                          class="form-control"
                          placeholder="168168"
                        >
                        <p
                          v-for="error of v$.codeSameDay.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                      <div class="code-box col-lg-6">
                        <input
                          v-if="newAdress"
                          type="button"
                          value="우편번호 찾기"
                          @click="goPopup();"
                        >
                      </div>
                      <div class="code-box col-lg-12">
                        <input
                          id="address01SameDay"
                          v-model="state.address01SameDay"
                          type="text"
                          name="address01SameDay"
                          class="form-control"
                          placeholder="서울시 서초구"
                        >
                        <p
                          v-for="error of v$.address01SameDay.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                      <div class="code-box col-lg-12">
                        <input
                          id="address02SameDay"
                          v-model="state.address02SameDay"
                          type="text"
                          name="address02SameDay"
                          class="form-control"
                          placeholder="Test"
                        >
                        <p
                          v-for="error of v$.address02SameDay.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                    </div>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="phoneSameDay"
                      class="form-label"
                    >{{ $t("order_form.telephone") }}</label>
                  </dt>
                  <dd>
                    <div class="row">
                      <div class="col-lg-6">
                        <input
                          id="phoneSameDay"
                          v-model="state.phoneSameDay"
                          type="tel"
                          name="phoneSameDay"
                          class="form-control"
                          placeholder="012456789"
                        >
                      </div>
                      <p
                        v-for="error of v$.phoneSameDay.$errors"
                        :key="error.$uid"
                        class="error-msg"
                      >
                        {{ error.$message }}
                      </p>
                    </div>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="noteSameDay"
                      class="form-label"
                    >{{ $t("order_form.mess") }}</label>
                  </dt>
                  <dd>
                    <textarea
                      id="noteSameDay"
                      v-model="state.noteSameDay"
                      name="noteSameDay"
                      class="form-control"
                      :placeholder="$t('order_form.messPlaceholder')"
                    />
                  </dd>
                </dl>
              </div>
            </div>
            <div
              v-if="getOrderType == 'pickup' || getOrderType == null"
              class="order-form__delivery"
            >
              <div class="order-form__delivery--title">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("order_form.pickUp") }} ({{ countPickUpCenter != null ? countPickUpCenter : 0 }})<span>&#43;</span>
                </h3>
                <div
                  v-if="countPickUpCenter > 0"
                  class="wrap-date"
                >
                  <div class="row">
                    <div class="col-sm-6">
                      <dl>
                        <dt>
                          <label
                            for="date_pickup"
                            class="form-label"
                          >{{ $t("order_form.expected") }} </label>
                        </dt>
                        <dd>
                          <Datepicker
                            v-model="datePickup"
                            required
                            auto-apply
                            :format="formatDate"
                            :min-date="new Date()"
                            placeholder=""
                          />
                        </dd>
                      </dl>
                    </div>
                    <div class="col-sm-6">
                      <dl>
                        <dt>
                          <label
                            for="time_pickup"
                            class="form-label"
                          >{{ $t("order_form.pickTime") }} </label>
                        </dt>
                        <dd>
                          <Datepicker
                            v-model="timePickup"
                            required
                            time-picker
                            auto-apply
                            :no-minutes-overlay="true"
                            :minutes-increment="10"
                            placeholder=""
                            :minutes-grid-increment="10"
                            @update:model-value="handleTimeMinute"
                          >
                            <template #input-icon>
                              <svg
                                version="1.1"
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 32 32"
                                class="dp__icon dp__input_icon dp__input_icons"
                              ><path d="M16 1.333c-8.095 0-14.667 6.572-14.667 14.667s6.572 14.667 14.667 14.667c8.095 0 14.667-6.572 14.667-14.667s-6.572-14.667-14.667-14.667zM16 4c6.623 0 12 5.377 12 12s-5.377 12-12 12c-6.623 0-12-5.377-12-12s5.377-12 12-12z" /><path d="M14.667 8v8c0 0.505 0.285 0.967 0.737 1.193l5.333 2.667c0.658 0.329 1.46 0.062 1.789-0.596s0.062-1.46-0.596-1.789l-4.596-2.298c0 0 0-7.176 0-7.176 0-0.736-0.597-1.333-1.333-1.333s-1.333 0.597-1.333 1.333z" /></svg>
                            </template>
                          </Datepicker>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <div class="order-form__delivery--content">
                  <!-- <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nobis doloribus recusandae velit tempora? Explicabo ab voluptas nobis quis ratione voluptate qui, doloremque autem fuga, adipisci fugiat, eius modi illum totam?</p> -->
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
                          <b v-if="idx == 1">{{ $t("order_form.indust") }}</b>
                          <b v-else-if="idx == 2">{{ $t("order_form.miscell") }}</b>
                          <b v-else>{{ $t("order_form.alcohol") }}</b>
                          <span>{{ $t("order_form.default") }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="container-box__product box-product">
                      <ProductGroupItem
                        :product-info="item"
                        :page="getPage"
                        @total-price-type="totalPriceType($event, idx)"
                      />
                    </div>
                  </div>
                </div>
                <!-- <p class="order-form__delivery--btn">
                                    <router-link to="#">센터수령 안내 문구 Display</router-link>
                                </p> -->
              </div>
            </div>
            <div
              v-if="getOrderType == 'delivery' || getOrderType == null"
              class="order-form__delivery last"
            >
              <div class="order-form__delivery--title">
                <h3 @click="toggleDeliveryContent">
                  {{ $t("order_form.direct") }} ({{ countDelivery != null ? countDelivery : 0 }})<span>&#43;</span>
                </h3>
                <div
                  v-if="countDelivery > 0"
                  class="wrap-date"
                >
                  <div class="row">
                    <div class="col-sm-6">
                      <dl>
                        <dt>
                          <label
                            for="date_delivery"
                            class="form-label"
                          >{{ $t("order_form.desiredDate") }}</label>
                        </dt>
                        <dd>
                          <Datepicker
                            v-model="dateDelivery"
                            required
                            auto-apply
                            :format="formatDate"
                            :min-date="minDateDelivery"
                            placeholder=""
                          />
                        </dd>
                      </dl>
                    </div>
                    <div class="col-sm-6">
                      <dl>
                        <dt>
                          <label
                            for="time_delivery"
                            class="form-label"
                          >{{ $t("order_form.desiredTime") }}</label>
                        </dt>
                        <dd>
                          <Datepicker
                            v-model="timeDelivery"
                            required
                            time-picker
                            auto-apply
                            :minutes-increment="10"
                            placeholder=""
                          >
                            <template #input-icon>
                              <svg
                                version="1.1"
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 32 32"
                                class="dp__icon dp__input_icon dp__input_icons"
                              ><path d="M16 1.333c-8.095 0-14.667 6.572-14.667 14.667s6.572 14.667 14.667 14.667c8.095 0 14.667-6.572 14.667-14.667s-6.572-14.667-14.667-14.667zM16 4c6.623 0 12 5.377 12 12s-5.377 12-12 12c-6.623 0-12-5.377-12-12s5.377-12 12-12z" /><path d="M14.667 8v8c0 0.505 0.285 0.967 0.737 1.193l5.333 2.667c0.658 0.329 1.46 0.062 1.789-0.596s0.062-1.46-0.596-1.789l-4.596-2.298c0 0 0-7.176 0-7.176 0-0.736-0.597-1.333-1.333-1.333s-1.333 0.597-1.333 1.333z" /></svg>
                            </template>
                          </Datepicker>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
                <div class="order-form__delivery--content">
                  <!-- <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nobis doloribus recusandae velit tempora? Explicabo ab voluptas nobis quis ratione voluptate qui, doloremque autem fuga, adipisci fugiat, eius modi illum totam?</p> -->
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
                          <b v-if="idx == 1">{{ $t("order_form.indust") }}</b>
                          <b v-else-if="idx == 2">{{ $t("order_form.miscell") }}</b>
                          <b v-else>{{ $t("order_form.alcohol") }}</b>
                          <span>{{ $t("order_form.default") }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="container-box__product box-product">
                      <ProductGroupItem
                        :product-info="item"
                        :page="getPage"
                        @total-price-type="totalPriceType($event, idx)"
                      />
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="countDelivery > 0 || countPickUpCenter > 0"
                class="order-form__main"
              >
                <p class="order-form__main--title">
                  {{ $t("order_form.please") }}
                </p>
                <dl class="info">
                  <dt>
                    <label
                      for="info"
                      class="form-label"
                    />
                  </dt>
                  <dd class="title-address-box d-flex align-items-center flex-wrap justify-content-between">
                    <p>
                      <input
                        id="member-address"
                        type="radio"
                        name="radio-group"
                        value="회원정보 동일"
                        checked
                      >
                      <label
                        for="member-address"
                        @click="addressOption(0)"
                      >{{ $t("order_form.same") }}</label>
                    </p>
                    <!-- Hold 새 배달 주소 -->
                    <!-- <p>
                                            <input type="radio" name="radio-group"
                                            value="새 배달 주소"
                                            id="new-address">
                                            <label for="new-address" @click="addressOption(1)">{{$t("order_form.new")}}</label>
                                        </p> -->
                    <p
                      class="btn-popup-address"
                      @click="deActiveSameDay(); togglePopupAddress(0);"
                    >
                      {{ $t('order_form.manageAddress') }}
                    </p>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="receiver"
                      class="form-label"
                    >{{ $t("order_form.name") }}</label>
                  </dt>
                  <dd>
                    <input
                      id="receiver"
                      v-model="state.receiver"
                      type="text"
                      name="receiver"
                      class="form-control"
                      placeholder="홍길동"
                    >
                    <p
                      v-for="error of v$.receiver.$errors"
                      :key="error.$uid"
                      class="error-msg"
                    >
                      {{ error.$message }}
                    </p>
                  </dd>
                </dl>
                <dl class="code">
                  <dt>
                    <label
                      for="code"
                      class="form-label"
                    >{{ $t("order_form.addr") }}</label>
                  </dt>
                  <dd>
                    <div class="row">
                      <div class="code-box col-lg-6">
                        <input
                          id="code"
                          v-model="state.code"
                          type="number"
                          name="code"
                          class="form-control"
                          placeholder="168168"
                        >
                        <p
                          v-for="error of v$.code.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                      <div class="code-box col-lg-6">
                        <input
                          v-if="newAdress"
                          type="button"
                          value="우편번호 찾기"
                          @click="goPopup();"
                        >
                      </div>
                      <div class="code-box col-lg-12">
                        <input
                          id="address01"
                          v-model="state.address01"
                          type="text"
                          name="address01"
                          class="form-control"
                          placeholder="서울시 서초구"
                        >
                        <p
                          v-for="error of v$.address01.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                      <div class="code-box col-lg-12">
                        <input
                          id="address02"
                          v-model="state.address02"
                          type="text"
                          name="address02"
                          class="form-control"
                          placeholder="Test"
                        >
                        <p
                          v-for="error of v$.address02.$errors"
                          :key="error.$uid"
                          class="error-msg"
                        >
                          {{ error.$message }}
                        </p>
                      </div>
                    </div>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="phone"
                      class="form-label"
                    >{{ $t("order_form.telephone") }}</label>
                  </dt>
                  <dd>
                    <div class="row">
                      <div class="col-lg-6">
                        <input
                          id="phone"
                          v-model="state.phone"
                          type="tel"
                          name="phone"
                          class="form-control"
                          placeholder="012456789"
                        >
                      </div>
                      <p
                        v-for="error of v$.phone.$errors"
                        :key="error.$uid"
                        class="error-msg"
                      >
                        {{ error.$message }}
                      </p>
                    </div>
                  </dd>
                </dl>
                <dl>
                  <dt>
                    <label
                      for="receiver"
                      class="form-label"
                    >{{ $t("order_form.mess") }}</label>
                  </dt>
                  <dd>
                    <textarea
                      id="note"
                      v-model="state.note"
                      name="note"
                      class="form-control"
                      :placeholder="$t('order_form.messPlaceholder')"
                    />
                  </dd>
                </dl>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <RightBarOld
              :page="getPage"
              @total-order="totalOrder"
            />
          </div>
        </div>
        <div
          v-if="activePopupAddress"
          id="address-book"
          class="address-book"
        >
          <p class="address-book__title">
            {{ $t('order_form.addressManagerment') }}
            <span
              class="icon-close"
              @click="togglePopupAddress(1)"
            >
              &#x2715;
            </span>
          </p>
          <div class="address-book__tab">
            <ul class="title-tab d-flex align-items-center">
              <li
                :class="[shippingAddress == 0 ? 'active' : '']"
                @click="chooseAddress(0)"
              >
                {{ $t('order_form.addressList') }}
              </li>
              <li
                :class="[shippingAddress == 1 ? 'active' : '']"
                @click="chooseAddress(1)"
              >
                {{ $t('order_form.addressAdd') }}
              </li>
            </ul>
            <div
              v-if="shippingAddress == 0"
              class="tab-content"
            >
              <div class="search-text">
                <input
                  v-model="keyword"
                  type="text"
                  :placeholder="$t('order_form.addressNotice')"
                  class="form-control"
                >
              </div>
              <div class="list-address">
                <div
                  v-for="(item, idx) in addressesLoaded"
                  :key="idx"
                  class="list-address__box"
                >
                  <input
                    :id="'addressbox' + idx"
                    v-model="pickAddress"
                    type="radio"
                    name="addressbox"
                    :value="item"
                  >
                  <label :for="'addressbox' + idx">
                    <span class="name">{{ item.address_name }}</span>
                    {{ item.address_basic }}
                  </label>
                  <span
                    class="edit-address"
                    @click="editAddress(item)"
                  >
                    {{ $t('order_form.addressEdit') }}
                  </span>
                </div>
                <div class="wrapper-more pt-2">
                  <a
                    v-if="showMoreBtn"
                    class="btn-load-more text-decoration-underline text-danger"
                    @click="loadMoreAddress()"
                  >{{ $t('order_form.loadMore') }}</a>
                </div>
              </div>
              <p
                :class="['choose-address', disableSelectAddress]"
                @click="selectAddress()"
              >
                {{ $t('order_form.addressApply') }}
              </p>
            </div>
            <div
              v-else
              class="tab-content form"
            >
              <div class="group-item">
                <label for="">{{ $t('order_form.addressName') }}</label>
                <input
                  v-model="newName"
                  type="text"
                  :placeholder="$t('order_form.addressName')"
                >
              </div>
              <div class="group-item">
                <label for="">{{ $t('order_form.addressFullname') }}</label>
                <input
                  v-model="newFullName"
                  type="text"
                  :placeholder="$t('order_form.addressFullname')"
                >
              </div>
              <div class="group-item">
                <label for="">{{ $t('order_form.addressDetail') }}</label>
                <input
                  type="text"
                  readonly
                  :placeholder="$t('order_form.addressDetail')"
                  class="full-address"
                  :value="fullAddress"
                >
                <p
                  class="find-address"
                  @click="goPopup()"
                >
                  {{ $t('order_form.addressSearch') }}
                </p>
              </div>
              <div class="group-item">
                <input
                  v-model="newPhone"
                  type="tel"
                  :placeholder="$t('order_form.addressPhone1')"
                >
              </div>
              <div class="group-item">
                <input
                  v-model="newMobile"
                  type="tel"
                  :placeholder="$t('order_form.addressPhone2')"
                >
              </div>
              <div class="group-item">
                <div class="d-flex align-items-center">
                  <input
                    id="default-address"
                    v-model="isDefault"
                    type="checkbox"
                    name="default-address"
                  >
                  <label for="default-address">{{ $t('order_form.addressSetDefault') }}</label>
                </div>
              </div>
              <p
                v-if="allFieldsIsRequired"
                class="smg-error"
              >
                {{ $t('order_form.addressErrorRequired') }}
              </p>
              <p
                v-if="newAddressCondition"
                class="confirm-address"
                @click="confirmAddress(0)"
              >
                {{ $t('order_form.addressAddNew') }}
              </p>
              <p
                v-else
                class="confirm-address"
                @click="confirmAddress(1)"
              >
                {{ $t('order_form.addressUpdate') }}
              </p>
            </div>
          </div>
        </div>
        <div
          v-if="activePopupAddress"
          class="address-book--overlay"
          @click="togglePopupAddress(1)"
        />
      </form>
    </div>
    <ConfirmDialogue ref="ConfirmDialogue" />
    <LoadingPage
      v-if="loading"
    />
    <!-- form payment -->
    <form
      action=""
      name="payForm"
      method="post"
      @submit.prevent="payment"
    >
      <input
        type="hidden"
        name="PayMethod"
        value="CARD,VBANK"
      >
      <input
        type="hidden"
        name="GoodsName"
        value="Credit charge"
      >
      <input
        type="hidden"
        name="Amt"
        :value="amountResult"
      >
      <input
        type="hidden"
        name="MID"
        :value="paymentInfo.mid"
      >
      <input
        type="hidden"
        name="Moid"
        :value="paymentInfo.moid"
      >
      <input
        type="hidden"
        name="BuyerName"
        :value="getStoreInfos.storeNm"
      >
      <input
        type="hidden"
        name="BuyerEmail"
        :value="getStoreInfos.email"
      >
      <input
        type="hidden"
        name="BuyerTel"
        :value="getStoreInfos.mobile"
      >
      <input
        type="hidden"
        name="ReturnURL"
        :value="currentUrl"
      >
      <input
        type="hidden"
        name="VbankExpDate"
        :value="paymentInfo.vitural_date"
      >

      <input
        type="hidden"
        name="NpLang"
        value="KO"
      > <!-- EN:English, CN:Chinese, KO:Korean -->
      <input
        type="hidden"
        name="GoodsCl"
        value="1"
      ><!-- products(1), contents(0)) -->
      <input
        type="hidden"
        name="TransType"
        value="0"
      ><!-- USE escrow false(0)/true(1) -->
      <input
        type="hidden"
        name="CharSet"
        value="utf-8"
      ><!-- Return CharSet -->
      <input
        type="hidden"
        name="ReqReserved"
        :value="customFields"
      ><!-- mall custom field -->
      <!-- DO NOT CHANGE -->
      <input
        type="hidden"
        name="EdiDate"
        :value="paymentInfo.date"
      ><!-- YYYYMMDDHHMISS -->
      <input
        type="hidden"
        name="SignData"
        :value="paymentInfo.hash"
      ><!-- EncryptData -->
    </form>
  </section>
</template>

<script>
    import RightBarOld from "../components/RightBarOld.vue"
    import ProductGroupItem from '../components/ProductGroupItem'
    import useVuelidate from '@vuelidate/core'
    import { required } from "../i18n/validators";
    import LoadingPage from '../components/LoadingPage'
    import { reactive, computed } from "vue";
    import ConfirmDialogue from '../components/ConfirmDialogue.vue'
    import Datepicker from '@vuepic/vue-datepicker';
    import '@vuepic/vue-datepicker/dist/main.css';
    import { ref } from 'vue';
    import { mapGetters } from "vuex"

    export default {
        components: {
            RightBarOld,
            ProductGroupItem,
            LoadingPage,
            ConfirmDialogue,
            Datepicker,
        },
        setup() {
            const state = reactive({
                receiver: "",
                code: "",
                address01: "",
                address02: "",
                phone: "",
                receiverSameDay: "",
                codeSameDay: "",
                address01SameDay: "",
                address02SameDay: "",
                phoneSameDay: "",
            });
            const rules = computed(() => {
                return {
                    receiver: { required },
                    code: { required },
                    address01: { required },
                    address02: { required },
                    phone: { required },
                    receiverSameDay: { },
                    codeSameDay: { },
                    address01SameDay: { },
                    address02SameDay: { },
                    phoneSameDay: { },
                };
            });
            const dateDelivery = ref(new Date());
            dateDelivery.value.setDate(dateDelivery.value.getDate() + 1);
            const timeDelivery = ref({
                hours: 0,
                minutes: 0
            });

            const date = ref(new Date());
            const formatDate = (date) => {
                const day = date.getDate()
                const month = date.getMonth() + 1
                const year = date.getFullYear()
                return `${day}/${month}/${year}`
            }

            const datePickup = ref();
            const timePickup = ref();
            const dateTimeSameDay = ref(new Date());
            const timeSameDay = ref({
                hours: dateDelivery.value.getHours() + 2,
                minutes: 0
            });
            const minTimeSameDay = ref({
                hours: dateDelivery.value.getHours() + 2,
                minutes: 0
            });

            const handleTimeMinute = (modelData) => {
                modelData.minutes = 0
            }

            const v$ = useVuelidate(rules, state);
            return {
                state,
                v$,
                dateDelivery,
                timeDelivery,
                datePickup,
                timePickup,
                timeSameDay,
                date,
                formatDate,
                handleTimeMinute,
                minTimeSameDay,
            };
        },
        data() {
            return {
                loading: false,
                userInfomation: [],
                typeAddressOption: 0,
                dataShoppingCart: [],
                minDatePickup: new Date().toISOString().substr(0, 10),
                minDateDelivery: new Date(),
                minTimePickup: '00:00',
                minTimeDelivery: '00:00',
                timeDate: new Date(),
                shippingAddress: 0,
                listAddress: [],
                activePopupAddress: false,
                activePopupAddressSameDay: false,
                pickAddress: "",
                allFieldsIsRequired: false,
                isDefault: true,
                newName: "",
                newFullName: "",
                newPhone: "",
                newMobile: "",
                newAddressCondition: true,
                idAddress: Number,
                length: 5,
                showMoreBtn: false,
                keyword: "",
                totalPrice: 0,
                credit: 0,
                balance: 0,
                useBalance: false,
                paymentInfo: {},
                amountResult: 0,
                customFields: "",
            }
        },
        computed: {
            // all products in cart
            ...mapGetters(['getStoreInfos']),
            currentUrl() {
                return window.location
            },
            addressesLoaded() {
                let fakeList = this.listAddress
                if (this.keyword != "") {
                    fakeList = fakeList.filter(address => {
                        return address.address_name.toLowerCase().includes(this.keyword.toLowerCase()) ||
                            address.full_name.toLowerCase().includes(this.keyword.toLowerCase()) ||
                            address.address_basic.toLowerCase().includes(this.keyword.toLowerCase()) ||
                            address.address_detail.toLowerCase().includes(this.keyword.toLowerCase())
                    })
                    if (fakeList.length > this.length) {
                        this.showMoreBtn = true
                    } else {
                        this.showMoreBtn = false
                    }
                    return fakeList
                }
                if (fakeList.length > this.length) {
                    this.showMoreBtn = true
                }
                return fakeList.slice(0, this.length);
            },
            fullAddress() {
                if (this.activePopupAddressSameDay == false && this.state.code != '') {
                    return this.state.code + ', ' + this.state.address01 + ', ' + this.state.address02
                } else if (this.activePopupAddressSameDay == true && this.state.codeSameDay != '') {
                    return this.state.codeSameDay + ', ' + this.state.address01SameDay + ', ' + this.state.address01SameDay
                }
                return ''
            },
            getOrderType() {
                return this.$route.query.type
            },
            newAdress() {
                return this.typeAddressOption == 1
            },
            getPage() {
                return this.$route.path
            },
            allProducts() {
                if (this.$store.state.products.data != null) {
                    return this.$store.state.products.data
                }
                return []
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
            groupByProductsPickUpCenter() {
                if (this.productsPickUpCenter != null) {
                    return this.groupByProductTp(this.productsPickUpCenter, 'productTp')
                } else {
                    return null
                }
            },
            groupByProductsSameDay() {
                if (this.productsSameDay != null) {
                    return this.groupByProductTp(this.productsSameDay, 'productTp')
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
            countSameDay() {
                if (this.productsSameDay != null) {
                    return this.productsSameDay.length;
                }
                return 0
            },
            disableSelectAddress() {
                return {'disable': this.pickAddress == ''}
            }
        },
        created() {
            this.minDateDelivery.setDate(this.minDateDelivery.getDate() + 1)
        },
        mounted() {
            this.getUserAPI()
            this.listAddressBook()
        },
        methods: {
            editAddress(data) {
                //status address (add address is TRUE, edit address is FALSE)
                this.newAddressCondition = false
                //set active new address
                this.shippingAddress = 1
                //assign data user in form
                this.newName = data.address_name
                this.newFullName = data.full_name
                this.state.address01 = data.address_basic
                this.state.address02 = data.address_detail
                this.state.code = data.zip_code
                this.newPhone = data.phone
                this.newMobile = data.mobile
                this.idAddress = data.id
                this.isDefault = data.is_default == 1 ? true : false
                if (this.activePopupAddressSameDay == true) {
                    this.state.address01SameDay = data.address_basic
                    this.state.address02SameDay = data.address_detail
                    this.state.codeSameDay = data.zip_code
                }
            },
            confirmAddress(params) {
                if (this.newName != ""
                    && this.newFullName != ""
                    && this.fullAddress != ""
                    && this.newPhone != ""
                ) {
                    this.allFieldsIsRequired = false
                    this.loading = true
                    let dataUser = {
                        "address_name": this.newName,
                        "full_name": this.newFullName,
                        "address_basic": this.state.address01,
                        "address_detail": this.state.address02,
                        "phone": this.newPhone,
                        "mobile": this.newMobile,
                        "zip_code": this.state.code,
                        "is_default": this.isDefault == true ? 1 : 0
                    }
                    // check sameday
                    if (this.activePopupAddressSameDay) {
                        dataUser.address_basic = this.state.address01SameDay
                        dataUser.address_detail = this.state.address02SameDay
                        dataUser.zip_code = this.state.codeSameDay
                    }
                    //params == 0 is Add new address, params == 1 is Edit current address
                    if (params == 0) {
                        axios
                            .post(apiURL + "/addresses",
                                dataUser
                            )
                            .then(res => {
                                this.activePopupAddress = false
                                this.shippingAddress = 0
                                this.loading = false
                                this.listAddress.push(res.data.data)
                                //assign user data
                                if (!this.activePopupAddressSameDay) {
                                    this.state.receiver = res.data.data.full_name
                                    this.state.code = res.data.data.zip_code
                                    this.state.address01 = res.data.data.address_basic
                                    this.state.address02 = res.data.data.address_detail
                                    this.state.phone = res.data.data.phone
                                    this.state.note = ""
                                } else {
                                    this.state.receiverSameDay = res.data.data.full_name
                                    this.state.codeSameDay = res.data.data.zip_code
                                    this.state.address01SameDay = res.data.data.address_basic
                                    this.state.address02SameDay = res.data.data.address_detail
                                    this.state.phoneSameDay = res.data.data.phone
                                    this.state.noteSameDay = ""
                                }
                            })
                            .catch(err => {
                                console.log(err)
                            })
                    } else {
                        axios
                            .put(apiURL + "/addresses/" + this.idAddress,
                                dataUser
                            )
                            .then(res => {
                                this.loading = false
                                alert(this.$t("status.completed"))
                                if (res.data.success == true) {
                                    this.listAddress.map(item => {
                                        if (item.id == this.idAddress) {
                                            item.address_name = this.newName
                                            item.full_name = this.newFullName
                                            item.mobile = this.newMobile
                                            item.phone = this.newPhone
                                             if (this.activePopupAddressSameDay) {
                                                item.zip_code = this.state.codeSameDay
                                                item.address_basic = this.state.address01SameDay
                                                item.address_detail = this.state.address02SameDay
                                            }
                                        }
                                    })
                                    this.shippingAddress = 0
                                }

                            })
                            .catch(err => {
                                console.log(err)
                            })
                    }
                } else {
                    this.allFieldsIsRequired = true
                }
            },
            selectAddress() {
                if (this.pickAddress != "") {
                    if (!this.activePopupAddressSameDay) {
                        this.state.receiver = this.pickAddress.full_name
                        this.state.code = this.pickAddress.zip_code
                        this.state.address01 = this.pickAddress.address_basic
                        this.state.address02 = this.pickAddress.address_detail
                        this.state.phone = this.pickAddress.phone
                    } else {
                        this.state.receiverSameDay = this.pickAddress.full_name
                        this.state.codeSameDay = this.pickAddress.zip_code
                        this.state.address01SameDay = this.pickAddress.address_basic
                        this.state.address02SameDay = this.pickAddress.address_detail
                        this.state.phoneSameDay = this.pickAddress.phone
                    }
                }
                this.activePopupAddress = false
            },
            activeSameDay() {
                this.activePopupAddressSameDay = true
            },
            deActiveSameDay() {
                this.activePopupAddressSameDay = false
            },
            resetAddNewAddress() {
                // reset new address popup
                this.newAddressCondition = true
                this.newName = ""
                this.newFullName = ""
                this.newPhone = ""
                this.newMobile = ""
            },
            togglePopupAddress(param) {
                this.activePopupAddress = !this.activePopupAddress
                this.shippingAddress = 0
                this.resetAddNewAddress()
                if (this.activePopupAddressSameDay == false) {
                    if (param == 0) {
                        this.state.receiver = ""
                        this.state.code = ""
                        this.state.address01 = ""
                        this.state.address02 = ""
                        this.state.phone = ""
                        this.state.note = ""
                    } else {
                        if (this.state.receiver == "") {
                            this.assignUserData(this.userInfomation)
                        }
                    }
                } else {
                    if (param == 0) {
                        this.state.receiverSameDay = ""
                        this.state.codeSameDay = ""
                        this.state.address01SameDay = ""
                        this.state.address02SameDay = ""
                        this.state.phoneSameDay = ""
                        this.state.noteSameDay = ""
                    } else {
                        if (this.state.receiverSameDay == "") {
                            this.assignUserData(this.userInfomation, true)
                        }
                    }
                }
            },
            async checkoutSubmit() {
                const isFormCorrect = await this.v$.$validate();
                if (!isFormCorrect) return;
                // this.loading = true
                // check balance greater than credit amount
                if (this.useBalance == true && this.balance > this.credit) {
                    this.$refs.ConfirmDialogue.show({
                        title: this.$t('modal.titleDialog'),
                        message: "max balance",
                        cancelButton: this.$t('modal.btnOk'),
                        isDialog: true,
                    })
                    return false
                }

                // prepare params
                let params = {
                    "order_type": this.getOrderType,
                    "receiver_name": this.state.receiver,
                    "receiver_zipcode": this.state.code,
                    "receiver_address1": this.state.address01,
                    "receiver_address2": this.state.address02,
                    "receiver_phone": this.state.phone,
                    "note": this.state.note,
                    "token": localStorage.getItem("app_token"),
                    // same day
                    "order_type_sameday": this.getOrderTypeSameDay,
                    "receiver_name_sameday": this.state.receiverSameDay,
                    "receiver_zipcode_sameday": this.state.codeSameDay,
                    "receiver_address1_sameday": this.state.address01SameDay,
                    "receiver_address2_sameday": this.state.address02SameDay,
                    "receiver_phone_sameday": this.state.phoneSameDay,
                    "note_sameday": this.state.noteSameDay,
                }

                if (this.timePickup != undefined && (this.getOrderType == 'pickup' || this.getOrderType == null)) {
                    // get date time pickup
                    let datePickup = new Date(this.datePickup)
                    let dayPickup = datePickup.getDate()
                    let monthPickup = datePickup.getMonth()
                    let yearPickup = datePickup.getFullYear()
                    let datePickupResult = new Date(yearPickup, monthPickup, dayPickup, this.timePickup.hours, this.timePickup.minutes, '00').toISOString()
                    params.date_time_pickup = datePickupResult
                }

                if (this.timeDelivery != undefined && (this.getOrderType == 'delivery' || this.getOrderType == null)) {
                    // get date time delivery
                    let dateDelivery = new Date(this.dateDelivery)
                    let dayDelivery = dateDelivery.getDate()
                    let monthDelivery = dateDelivery.getMonth()
                    let yearDelivery = dateDelivery.getFullYear()
                    let dateDeliveryResult = new Date(yearDelivery, monthDelivery, dayDelivery, this.timeDelivery.hours, this.timeDelivery.minutes, '00').toISOString()
                    params.date_time_delivery = dateDeliveryResult
                }

                if (this.timeSameDay != undefined && (this.getOrderType == 'sameDay' || this.getOrderType == null)) {
                    // get date time delivery
                    let dateSameDay = new Date()
                    let daySameDay = dateSameDay.getDate()
                    let monthSameDay = dateSameDay.getMonth()
                    let yearSameDay = dateSameDay.getFullYear()
                    let dateSameDayResult = new Date(yearSameDay, monthSameDay, daySameDay, this.timeSameDay.hours, this.timeSameDay.minutes, '00').toISOString()
                    params.date_time_same_day = dateSameDayResult
                }
                this.customFields = encodeURIComponent(JSON.stringify(params))

                if (this.useBalance == false) { // not use deposit
                    if (this.credit >= this.totalPrice) {
                        this.sendForm(params)
                    } else {
                        this.loading = true
                        this.amountResult = this.totalPrice
                        this.showNicePay()
                    }
                } else { // use deposit
                    if (this.balance >= this.totalPrice) {
                        this.sendForm(params)
                    } else {
                        this.loading = true
                        this.amountResult = this.totalPrice - this.balance
                        this.showNicePay()
                    }
                }
            },
            showNicePay() {
                axios.post(apiURL + "/order/payment-info", {
                        amount: this.amountResult
                    })
                    .then(response => {
                        this.loading = false
                        this.paymentInfo = response.data.data
                        // poup nicepay delay 0.5s
                        setTimeout(() => {
                            if(checkPlatform(window.navigator.userAgent) == "mobile") {
                                document.payForm.action = "https://web.nicepay.co.kr/v3/v3Payment.jsp"
                                document.payForm.acceptCharset="euc-kr"
                                document.payForm.submit()
                            } else {
                                goPay(document.payForm)
                            }
                        }, 600);
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
            sendForm(params) {
                this.loading = true
                axios
                    .post(apiURL + "/order/create", params)
                    .then(res => {
                        this.loading = false
                        console.log(res.data)
                        if (res.data.success == true) {
                            // remove products in cart
                            this.$store.commit('removeCart', this.getOrderType)
                            // redirect to complete page
                            this.$router.push({
                                path: '/order-completed',
                                query: {
                                    orderId: res.data.data.order_numbers,
                                    createdDate: res.data.data.created_date,
                                }
                            })
                        } else {
                            this.$refs.ConfirmDialogue.show({
                                title: this.$t('modal.titleDialog'),
                                message: res.data.data.fail[1].message,
                                cancelButton: this.$t('modal.btnOk'),
                                isDialog: true,
                            })
                        }
                    })
                    .catch(error => {
                        console.log(error)
                        this.loading = false
                        alert("시스템 오류, 나중에 재설정하십시오");
                    })
            },
            toggleDeliveryContent(e) {
                e.stopPropagation();
                let _this = $(e.target);
                _this.siblings(".order-form__delivery--content").slideToggle();
                _this.toggleClass("active");
                if(_this.hasClass("active")) {
                    _this.children("span").html("&#8722;");
                } else {
                    _this.children("span").html("&#43;");
                }
            },
            totalPriceType(result, productTp) {
                result["productTp"] = parseInt(productTp)
                this.dataShoppingCart.push(result)
            },
            groupByProductTp (object, key) {
                return object.reduce(function(type, obj) {
                    (type[obj[key]] = type[obj[key]] || []).push(obj);
                    return type;
                }, {});
            },
            shippingMethodProducts(products, method) {
                if (products != null) {
                    const pro = products.filter(function(obj){
                        return obj.shipping_method == method;
                    });
                    return pro;
                }
            },
            assignUserData(data, sameDay = false) {
                if (!sameDay) {
                    this.state.receiver = data.deliveryReceiver
                    this.state.code = data.deliveryPostCd
                    this.state.address01 = data.deliveryAddressBasic
                    this.state.address02 = data.deliveryAddressDetail
                    this.state.phone = data.phone
                    this.state.note = data.storeNt
                } else {
                    this.state.receiverSameDay = data.deliveryReceiver
                    this.state.codeSameDay = data.deliveryPostCd
                    this.state.address01SameDay = data.deliveryAddressBasic
                    this.state.address02SameDay = data.deliveryAddressDetail
                    this.state.phoneSameDay = data.phone
                    this.state.noteSameDay = data.storeNt
                }
            },
            addressOption(type, sameDay = false) {
                if (!sameDay) {
                    this.typeAddressOption = type
                    if (type == 1) {
                        this.state.receiver = ""
                        this.state.code = ""
                        this.state.address01 = ""
                        this.state.address02 = ""
                        this.state.phone = ""
                        this.state.note = ""
                    } else {
                        //asign user data
                        this.assignUserData(this.userInfomation)
                    }
                } else {
                    this.typeAddressOptionSameDay = type
                    if (type == 1) {
                        this.state.receiverSameDay = ""
                        this.state.codeSameDay = ""
                        this.state.address01SameDay = ""
                        this.state.address02SameDay = ""
                        this.state.phoneSameDay = ""
                        this.state.noteSameDay = ""
                    } else {
                        //asign user data
                        this.assignUserData(this.userInfomation, true)
                    }
                }
            },
            getUserAPI() {
                this.loading = true
                axios
                    .get(apiURL + "/user/store")
                    .then(res => {
                        this.loading = false
                        this.userInfomation = res.data.data
                        //asign user data
                        this.assignUserData(this.userInfomation)
                        this.assignUserData(this.userInfomation, true)
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            goPopup(){
                window.open("/popup-address","pop","width=570,height=420, scrollbars=yes, resizable=yes");
            },
            chooseAddress(num) {
                this.shippingAddress = num
                this.resetAddNewAddress()
            },
            listAddressBook() {
                axios
                    .get(apiURL + "/addresses")
                    .then(res => {
                        this.listAddress = res.data.data
                    })
                    .catch(err => {
                        console.log(err);
                    })
            },
            loadMoreAddress() {
                this.length = this.length + 5
                if (this.length > this.listAddress.length) {
                    this.showMoreBtn = false
                    return
                }
            },
            totalOrder(order) {
                this.totalPrice = order.totalPrice
                this.credit = order.credit
                this.balance = order.balance
                this.useBalance = order.useBalance
            },
        }
    }
</script>

<style lang="scss" scoped>
    .address-book {
        width: 700px;
        max-height: 85%;
        overflow-y: auto;
        margin: 0 auto;
        border: 1px solid #ccc;
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        left: 0;
        right: 0;
        background: #fff;
        z-index: 12;
        &::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 4px rgba(0,0,0,0.3);
            border-radius: 10px;
            background-color: #F5F5F5;
        }
        &::-webkit-scrollbar
        {
            width: 6px;
            background-color: #F5F5F5;
        }

        &::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 4px rgba(0,0,0,.3);
            background-color: #ddd;
        }
        .smg-error {
            color: #ef4130;
            font-size: 16px;
            font-weight: bold;
        }
        &--overlay {
            background: rgba(0,0,0,0.6);
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 11;
            cursor: pointer;
        }
        &__title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 25px;
            padding: 15px 10px;
            margin: 0;
            position: relative;
            span.icon-close {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                right: 30px;
                cursor: pointer;
                font-size: 14px;
                color: #666;
            }
        }
        .title-tab {
            border-top: 1px solid #ccc;
            padding: 0;
            li {
                width: 50%;
                padding: 10px;
                text-align: center;
                text-transform: uppercase;
                font-size: 16px;
                border-bottom: 1px solid #ccc;
                background: #eee;
                cursor: pointer;
                &.active {
                    border-bottom: 0;
                    background: #fff;
                }
                &:first-child {
                    border-right: 1px solid #ccc;
                }
            }
        }
        p.slogan {
            margin: 0;
            border-bottom: 1px solid #ccc;
            padding: 20px 30px;
        }
        .list-address {
            padding: 30px;
            padding-top: 10px;
            &__box {
                display: flex;
                align-items: center;
                justify-content: space-between;
                text-align: left;
                padding: 5px 0;
                input {
                    width: 10%;
                    width: 18px;
                    height: 18px;
                }
                label {
                    width: calc(90% - 18px);
                    padding: 0px 30px 0 15px;
                    line-height: 25px;
                    font-size: 14px;
                    span {
                        display: block;
                        font-weight: bold;
                    font-size: 16px;
                    }
                }
                span.edit-address {
                    width: 10%;
                    background: #969696;
                    color: #fff;
                    text-align: center;
                    padding: 3px 10px;
                    display: block;
                    font-size: 14px;
                    cursor: pointer;
                }
            }
        }
        &__tab {
            padding-bottom: 30px;
        }
        .choose-address {
            display: table;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 5px 20px;
            font-size: 16px;
            cursor: pointer;
            &.disable {
                pointer-events: none;
                opacity: 0.5;
            }
        }
        .tab-content {
            &.form {
                padding: 20px 30px 0;
            }
            .group-item {
                position: relative;
                .find-address {
                    position: absolute;
                    right: 0;
                    bottom: 10px;
                    z-index: 2;
                    border: 1px solid #ccc;
                    border-radius: 20px;
                    padding: 2px 10px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0;
                }
                label {
                    display: block;
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    &[for="default-address"] {
                        margin: 2px 0 0;
                        padding-left: 10px;
                    }
                }
                input[type="text"], input[type="tel"] {
                    width: 100%;
                    border: none;
                    border-bottom: 1px solid #ccc;
                    padding: 8px 0;
                    outline: none;
                    font-size: 15px;
                    &.full-address {
                        padding-right: 100px;
                    }
                    &::-webkit-input-placeholder { /* Edge */
                        color: #9f9f9f;
                    }
                    &:-ms-input-placeholder { /* Internet Explorer 10-11 */
                        color: #9f9f9f;
                    }

                    &::placeholder {
                        color: #9f9f9f;
                    }
                }
            }
            .confirm-address {
                width: 100%;
                background: #eee;
                font-size: 16px;
                text-align: center;
                padding: 10px 0;
                border-radius: 5px;
                cursor: pointer;
                margin: 0;
            }
        }
    }

    .order-form {
        color: #555;
        padding-bottom: 50px;
        .btn-load-more {
            cursor: pointer;
        }
        .search-text {
            padding: 10px 30px;
        }
        p.btn-popup-address {
            margin: 0 !important;
            padding: 5px 20px;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: all 0.3s;
            &:hover {
                background: #ececec;
            }
        }
        &__main {
            width: 75%;
            margin: 0 auto;
            input, textarea {
                color: #616161;
                font-size: 14px;
                padding: 8px 12px;
                &[type="button"] {
                    width: 100%;
                    background: #f2f2f2;
                    border: 1px solid #ced4da;
                }
                &::-webkit-input-placeholder { /* Edge */
                    color: #bebebe;
                }
                &:-ms-input-placeholder { /* Internet Explorer 10-11 */
                    color: #bebebe;
                }
                &::placeholder {
                    color: #bebebe;
                }
            }
            &--title {
                font-size: 18px;
                text-transform: uppercase;
                font-weight: bold;
                margin-bottom: 30px;
            }
            dl {
                display: flex;
                width: 100%;
                &.info {
                    p {
                        margin: 0;
                        margin-right: 25px;
                    }
                }
                dt {
                    width: 15%;
                    padding-top: 7px;
                }
                dd {
                    width: 85%;
                    margin: 0;
                    .error-msg {
                        margin: 5px 0 0;
                        color: #ef4130;
                        font-size: 13px;
                        font-style: italic;
                    }
                    textarea {
                        height: 100px;
                    }
                    [type="radio"]:checked,
                    [type="radio"]:not(:checked) {
                        position: absolute;
                        left: -9999px;
                    }
                    [type="radio"]:checked + label,
                    [type="radio"]:not(:checked) + label
                    {
                        position: relative;
                        padding-left: 28px;
                        cursor: pointer;
                        line-height: 20px;
                        display: inline-block;
                        color: #666;
                    }
                    [type="radio"]:checked + label:before,
                    [type="radio"]:not(:checked) + label:before {
                        content: '';
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 18px;
                        height: 18px;
                        border: 1px solid #ccc;
                        border-radius: 100%;
                        background: #fff;
                    }
                    [type="radio"]:checked + label:after,
                    [type="radio"]:not(:checked) + label:after {
                        content: '';
                        width: 12px;
                        height: 12px;
                        background: #ef4130;
                        position: absolute;
                        top: 3px;
                        left: 3px;
                        border-radius: 100%;
                        -webkit-transition: all 0.2s ease;
                        transition: all 0.2s ease;
                    }
                    [type="radio"]:not(:checked) + label:after {
                        opacity: 0;
                        -webkit-transform: scale(0);
                        transform: scale(0);
                    }
                    [type="radio"]:checked + label:after {
                        opacity: 1;
                        -webkit-transform: scale(1);
                        transform: scale(1);
                    }

                }
                &.code {
                    margin-bottom: 0;
                    .code-box {
                        margin-bottom: 15px;
                    }
                }
            }
        }
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
        &__delivery {
            border-bottom: 5px solid #f1f1f1;
            margin-bottom: 20px;
            padding-bottom: 20px;
            &.last {
                border: none;
            }
            &--title {
                margin: 0;
                h3 {
                    font-weight: bold;
                    color: #1375c9;
                    position: relative;
                    font-size: 25px;
                    margin: 0;
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
                .wrap-date {
                    dl {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        width: 100%;
                        margin-bottom: 0;
                        margin-top: 20px;
                        dt {
                            width: 35%;
                            text-align: right;
                            padding-right: 10px;
                        }
                        dd {
                            width: 65%;
                        }
                    }
                }
            }
            &--content {
                margin: 0;
                padding: 20px 0 0;
                display: none;
            }
            &--btn {
                background: #f1f1f1;
                border: 1px solid #e8e8e8;
                text-align: center;
                font-weight: bold;
                font-size: 20px;
                margin: 20px 0 0;
                a {
                    color: #555;
                    display: block;
                    padding: 15px 20px;
                }
            }
        }
    }
    .group-item {
        margin-bottom: 30px;
    }
    .container-box {
        margin-bottom: 30px;
        border-top: 5px solid #f1f1f1;
        padding-top: 30px;
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
        .order-form {
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
            &__delivery {
                &--title {
                    h3 {
                        font-size: 16px;
                        span {
                            font-size: 30px;
                        }
                    }
                }
                &--btn {
                    a {
                        padding: 10px 20px;
                        font-size: 14px;
                    }
                }
            }
            &__main {
                width: 100%;
                &--title {
                    margin-bottom: 15px;
                }
                dl {
                    flex-wrap: wrap;
                    dt, dd {
                        width: 100%;
                    }
                    &.info {
                        p {
                            margin: 0 0 10px;
                            width: 100%;
                        }
                    }
                }
            }
        }

        .order-form__main dl.info .title-address-box p {
            width: auto;
            margin: 0;
            font-size: 4.5vw;
        }
        .order-form p.btn-popup-address {
            width: auto !important;
            padding: 1vw 4vw;
        }
        .address-book {
            width: 94%;
            max-height: 96%;
            height: 96%;
            .smg-error {
                font-size: 4.5vw;
            }
            &__title {
                span.icon-close {
                    right: 4vw;
                }
            }
            .tab-content {
                .group-item {
                    label {
                        font-size: 4vw;
                    }
                    input[type="text"], input[type="tel"] {
                        font-size: 4.5vw;
                    }
                    .find-address {
                        font-size: 4vw;
                        padding: 1vw 3vw;
                        bottom: 2.5vw;
                    }
                }
                .confirm-address {
                    font-size: 5vw;
                    padding: 2.5vw 2vw;
                }
            }
            &__tab {
                padding-bottom: 5vw;
            }
            .choose-address {
                padding: 1vw 5vw;
                font-size: 4.5vw;
            }
            &__title {
                font-size: 5vw;
                padding: 4vw 3vw;
            }
            .title-tab {
                li {
                    padding: 3vw 2vw;
                    font-size: 4vw;
                }
            }
            p.slogan {
                padding: 3vw 3.5vw;
                font-size: 4vw;
            }
            .list-address {
                padding: 3vw 3.5vw;
                &__box {
                    span.edit-address {
                        padding: 1vw 2vw;
                        font-size: 4vw;
                        width: 17%;
                    }
                    label {
                        font-size: 4vw;
                        width: calc(80% - 18px);
                        span {
                            font-size: 4.5vw;
                        }
                    }
                }
            }
        }
    }
</style>
