<template>
  <transition name="fade">
    <div
      v-if="isVisible"
      class="popup-modal"
    >
      <slot />
    </div>
  </transition>
</template>

<script>
export default {
    name: 'PopupModal',
    data: () => ({
        isVisible: false,
    }),
    methods: {
        open() {
            this.isVisible = true
        },

        close() {
            this.isVisible = false
        },
    },
}
</script>

<style lang="scss" scoped>
/* css class for the transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}

.popup-modal {
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    z-index: 11;
}

.window {
    background: #fff;
    border-radius: 5px;
    box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
    width: 50%;
    margin-left: auto;
    margin-right: auto;
    padding: 1.5rem;
}

@media only screen and (max-width: 991px) {
    .window {
        width: 200%;
    }
    .popup-modal {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap;
        &::-webkit-scrollbar{
            height: 6px;
            width: 6px;
            background: #f1f1f1;
        }
        &::-webkit-scrollbar-thumb {
            background: #ef4130;
            border-radius: 5px;
        }
    }
}
</style>
