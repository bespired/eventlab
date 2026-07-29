<template>
  <div class="two-split-template">
    <div class="split-left">
      <slot name="left">
        <BasePanel title="Left Pane">Left content placeholder</BasePanel>
      </slot>
    </div>
    <div class="split-right">
      <slot name="right">
        <BasePanel title="Right Pane">Right content placeholder</BasePanel>
      </slot>
    </div>
  </div>
</template>

<script setup>
</script>
