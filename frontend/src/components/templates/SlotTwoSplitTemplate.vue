<template>
  <div class="slot-two-split-template">
    <div class="slot-split-left">
      <slot name="left">
        <BasePanel title="Left Pane">Left content placeholder</BasePanel>
      </slot>
    </div>
    <div class="slot-split-right">
      <slot name="right">
        <BasePanel title="Right Pane">Right content placeholder</BasePanel>
      </slot>
    </div>
  </div>
</template>

<script setup>
</script>