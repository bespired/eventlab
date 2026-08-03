<template>
  <div class="big-icon big-svg">█</div>
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
  .big-svg {
    line-height: .9;
    overflow: hidden;
    color: transparent;
    display: inline-block;
    background-image: v-bind(localImage);
    background-position: center;
    background-repeat: no-repeat;

  }
</style>