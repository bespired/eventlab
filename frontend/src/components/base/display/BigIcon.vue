<template>
  <div class="big-icon alpha-mask">█</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name:     { type: String,  default: null },
  label:    { type: String,  default: '' },
  disabled: { type: Boolean, default: false }
})

const assetsBaseUrl = import.meta.env.VITE_ASSETS_BASE_URL ?? 'http://localhost:3006/app/'

const localImage = computed(() => { return `url(${assetsBaseUrl}svgicons/${props.name}.svg)`})

</script>


<style>
  .alpha-mask {
    line-height: .9;
    overflow: hidden;

    display: inline-block;
    mask-image: v-bind(localImage);
    mask-mode: alpha;
    mask-position: center;
    mask-repeat: no-repeat;
    transform: translateY(-.2em);
  }
</style>