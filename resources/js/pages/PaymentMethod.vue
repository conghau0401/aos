<template>
  <section
    id="payment-method"
    class="payment"
  >
    <div class="container">
      <div class="breadcrumbs">
        <ul>
          <li>
            <router-link to="#">
              {{ $t("deposit.home") }}
            </router-link>
          </li>
          <li>
            <router-link to="#">
              {{ $t("titlePage.paymentMethod") }}
            </router-link>
          </li>
        </ul>
      </div>
      <div class="row">
        <div class="col-lg-4">
          <div class="payment__left">
            <p class="payment__name">
              Abc mart
            </p>
            <p class="payment__code">
              Store code: 4528
            </p>
            <div class="payment__balance">
              <p class="payment__balance--title">
                Balance
              </p>
              <p class="payment__balance--price">
                100,000 won
              </p>
            </div>
            <div class="payment__balance payment__balance--required-recharge">
              <p class="payment__balance--title">
                Amount required to recharege
              </p>
              <p class="payment__balance--price">
                100,000 won
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          right
        </div>
      </div>
    </div>
  </section>
</template>

<style lang="scss" scoped>
    #payment-method {
        padding: 0 0 50px;
    }
</style>
