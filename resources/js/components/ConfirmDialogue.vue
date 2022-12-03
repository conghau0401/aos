<template>
  <popup-modal ref="popup">
    <div :class="isDialog ? 'pop-dialog popup-content': 'popup-content'">
      <h3 style="margin-top: 0">
        {{ title }}
      </h3>
      <div
        class="mb-2"
        v-html="message"
      />
      <div class="btns mt-2">
        <button
          class="cancel-btn"
          @click="_cancel"
        >
          {{ cancelButton }}
        </button>
        <span
          v-if="okButton"
          class="ok-btn"
          @click="_confirm"
        >{{ okButton }}</span>
      </div>
    </div>
  </popup-modal>
</template>

<script>
import PopupModal from './PopupModal.vue'


export default {
    name: 'ConfirmDialogue',

    components: { PopupModal },
    mixins: [],

    data: () => ({
        // Parameters that change depending on the type of dialogue
        title: undefined,
        message: undefined, // Main text content
        okButton: undefined, // Text for confirm button; leave it empty because we don't know what we're using it for
        cancelButton: 'Go Back', // text for cancel button
        isDialog: false,
        hasProducts: false,
        groupByProductTp: [],

        // Private variables
        resolvePromise: undefined,
        rejectPromise: undefined,
    }),

    methods: {
        show(opts = {}) {
            this.title = opts.title
            this.message = opts.message
            this.okButton = opts.okButton
            this.isDialog = opts.isDialog
            this.hasProducts = opts.hasProducts
            this.groupByProductTp = opts.groupByProductTp
            if (opts.cancelButton) {
                this.cancelButton = opts.cancelButton
            }
            // Once we set our config, we tell the popup modal to open
            this.$refs.popup.open()
            // Return promise so the caller can get results
            return new Promise((resolve, reject) => {
                this.resolvePromise = resolve
                this.rejectPromise = reject
            })
        },

        _confirm() {
            this.$refs.popup.close()
            this.resolvePromise(true)
        },

        _cancel() {
            this.$refs.popup.close()
            this.resolvePromise(false)
            // Or you can throw an error
            // this.rejectPromise(new Error('User cancelled the dialogue'))
        },
    },
}
</script>

<style lang="scss" scoped>
.btns {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.ok-btn, .cancel-btn {
    padding: 0.4em 2em;
    background-color: #d5eae7;
    color: #35907f;
    border: 1px solid #0ec5a4;
    border-radius: 5px;
    font-weight: bold;
    font-size: 13px;
    text-transform: uppercase;
    cursor: pointer;
}
.ok-btn{
    background-color: #e73950;
    color: #fff;
}

.popup-content {
    text-align: center;
    background: #fff;
    border-radius: 5px;
    box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.2);
    width: auto;
    min-width: 250px;
    margin: 0 auto;
    padding: 1.5rem;
    position: relative;
}
.popup-content h3 {
    font-weight: 600;
}
.pop-dialog .btns {
    justify-content: center;
}

/* order detail items */
.box-detail {
    font-size: 14px;
    &__title {
        background: #f1f1f1;
        padding: 10px 15px;
        &--left {
            span {
                display: block;
                &.img {
                    width: 30px;
                    margin-right: 10px;
                }
                &.text {
                    font-size: 18px;
                    padding-top: 3px;
                }
            }
        }
        &--right {
            span {
                display: block;
                margin-left: 20px;
            }
        }
    }
    &__type {
        p {
            padding: 0 10px;
            margin-bottom: 0;
        }
        &--header {
            padding: 15px 0 5px;
        }
        &--body {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        &--name {
            width: 40%;
            span {
                display: block;
                &.checkbox {
                    width: 20px;
                }
                &.img {
                    width: 50px;
                    img {
                        width: 100%;
                        height: 35px;
                        width: auto;
                    }
                }
                &.img-product {
                    width: 70px;
                    margin: 0 10px;
                    border: 1px solid #f1f1f1;
                    padding: 10px;
                    img {
                        width: 100%;
                    }
                }
                &.text {
                    font-weight: bold;
                    font-size: 16px;
                    padding-left: 10px;
                }
                &.name {
                    text-align: left;
                    width: calc(100% - (80px + 20px));
                    a {
                        color: #555;
                        display: block;
                    }
                    b {
                        color: #fff;
                        background: #6db2e0;
                        border-radius: 5px 0 5px 0;
                        padding: 2px 5px;
                        font-size: 11px;
                        font-weight: 500;
                        margin-right: 5px;
                        &.green {
                            background: #27b538;
                        }
                    }
                }
            }
        }
        &--option {
            width: 7%;
        }
        &--water {
            width: 7%;
        }
        &--price {
            width: 15%;
            span {
                font-size: 20px;
            }
        }
        &--quantity {
            width: 8%;
        }
        &--total {
            width: 15%;
            span {
                font-size: 20px;
                font-weight: bold;
            }
        }
        &--status {
            width: 10%;
        }
    }
}
</style>
