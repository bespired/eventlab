<template>
  <div class="login-screen">
    <div class="left-part">
    </div>
    <div class="right-part">
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const baseUrl = import.meta.env.VITE_ASSETS_BASE_URL ?? import.meta.env.BASE_URL ?? '/app/'
const assetsBaseUrl = baseUrl.endsWith('/') ? baseUrl : `${baseUrl}/`
const localImage = computed(() => { return `url(${assetsBaseUrl}images/hero-pyramides.webp)`})

</script>

<style>
  .login-screen {
      display: flex;
      margin: auto;
      width: 60%;
      min-height: 60vh;
      border-radius: 20px;
      border: 1px solid;

    .left-part{
      width: 30%;
      backdrop-filter: brightness(1.5);
      border-top-left-radius: 20px;
      border-bottom-left-radius: 20px;
    }
    .right-part{
      width: 70%;
      background-image: v-bind(localImage);
      background-position: center 37%;
      border-top-right-radius: 20px;
      border-bottom-right-radius: 20px;
      /*background-size: 99%;*/
      /* background-size: cover; */
    }
  }
</style>