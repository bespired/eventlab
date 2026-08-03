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

const baseUrl = import.meta.env.VITE_ASSETS_BASE_URL ?? import.meta.env.BASE_URL ?? '/app/'
const assetsBaseUrl = baseUrl.endsWith('/') ? baseUrl : `${baseUrl}/`

const localImage = computed(() => { return `url(${assetsBaseUrl}svgicons/${props.name}.svg)`})

</script>

<style>
  .alpha-mask {
    line-height: 1;
    overflow: hidden;

    display: inline-block;
    mask-image: v-bind(localImage);
    mask-mode: alpha;
    mask-position: center;
    mask-repeat: no-repeat;
  }
</style>