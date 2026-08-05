<template>
  <div class="login-screen" :class="currentStep">
    <div class="left-part">

      <div class="logo-area">
        <big-icon name="menu-eventlab" />
        EVENTLAB
      </div>

      <div class="credientials step-1">
        <text-input v-model="emailValue"
          label="Your email" placeholder="Enter email ..." />
        <click-button variant="secondary" @click="sendCode">
          Send me a code
        </click-button>
      </div>

      <div class="credientials step-2">
        <six-digits-input v-model="codeValue" />
        <click-button variant="secondary" @click="checkCode">
          Log me in
        </click-button>
      </div>

    </div>
    <div class="right-part">
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const emailValue = ref('')
const codeValue  = ref('')

const currentStep = ref('current-1')

const sendCode  = () => {currentStep.value = "current-2"}
const checkCode = () => {currentStep.value = "current-3"}

const baseUrl = import.meta.env.VITE_ASSETS_BASE_URL ?? import.meta.env.BASE_URL ?? '/app/'
const assetsBaseUrl = baseUrl.endsWith('/') ? baseUrl : `${baseUrl}/`
const localImage = computed(() => { return `url(${assetsBaseUrl}images/hero-pyramides.webp)`})

</script>
<style>
    .right-part {
        background-image: v-bind(localImage);
    }
    .step-1, .step-2 {
      transition: filter 300ms;
    }
    .current-1, .current-3 {
      .step-2 { filter: blur(14px) }
    }
    .current-2, .current-3{
      .step-1 { filter: blur(14px) }
    }
</style>