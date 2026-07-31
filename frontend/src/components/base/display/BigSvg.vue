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

const assetsBaseUrl = import.meta.env.VITE_ASSETS_BASE_URL ?? 'http://localhost:3006/app/'

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