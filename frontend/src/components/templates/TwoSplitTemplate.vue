<template>

  <div class="two-split-template">
    <app-menubar />

    <div class="split-left">
      <router-view name="sideContent" />
    </div>
    <div class="split-right">
      <router-view name="bodyContent" />
    </div>
  </div>
</template>

<script setup>
</script>


<style>
  .two-split-template {
    display: flex;
    flex-direction: row;
    /*min-height: 100dvh;*/

    background-color: rgba(255,255,255, 0.5);
    border-radius: 20px;
    margin: 20px;
    min-width: calc(100vw - 40px);
    min-height: calc(100dvh - 40px);
    overflow: hidden;

  }
</style>