<template>
  <div
    :style="styles"
    class="spinner spinner--rotate-diamond"
  >
    <div class="spinner-content">
      <div
        :style="diamondStyle"
        class="diamond"
      />
      <div
        :style="diamondStyle"
        class="diamond"
      />
      <div
        :style="diamondStyle"
        class="diamond"
      />
    </div>
  </div>
</template>

<script>
export default {
    props: {
        size: {
            type: Number,
            default: 60
        },
        color: {
            type: String,
            default: "#41b883"
        }
    },
    computed: {
        diamondStyle () {
            let size = parseInt(this.size)
            return {
                width: size / 4 + 'px',
                height: size / 4 + 'px',
                '--bg-color': this.color
            }
        },
        styles () {
            let size = parseInt(this.size)
            return {
                width: this.size,
                height: size / 4 + 'px'
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    $accent: #41b883;
    $duration: 1500ms;
    $timing: linear;
    .spinner {
        width: 100%;
        height: 100% !important;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255,255,255,0.9);
        z-index: 12;
        * {
            line-height: 0;
            box-sizing: border-box;
        }
        .spinner-content {
            width: 70px;
            margin: 0 auto;
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            transform: 50%;
        }
        .diamond {
            position: absolute;
            left: 0;
            top: 50%;
            transform: 50%;
            border-radius: 2px;
            background: var(--bg-color);
            transform: translateX(-50%) rotate(45deg) scale(0);
            animation: diamonds $duration $timing infinite;
            @for $i from 1 through 4 {
                &:nth-child(#{$i}) {
                animation-delay: -(calc($duration / 1.5) * $i);
                }
            }
        }
    }
    @keyframes diamonds {
        50% {
            left: 50%;
            transform: translateX(-50%) rotate(45deg) scale(1);
        }
        100% {
            left: 100%;
            transform: translateX(-50%) rotate(45deg) scale(0);
        }
    }
</style>
