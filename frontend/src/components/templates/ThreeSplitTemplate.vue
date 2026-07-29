<template>
  <div class="three-split-template">
    <div class="split-left">
      <slot name="left">
        <BasePanel title="Sidebar Pane">Sidebar content</BasePanel>
      </slot>
    </div>
    <div class="split-center">
      <slot name="center">
        <BasePanel title="Main Content Pane">Center main content</BasePanel>
      </slot>
    </div>
    <div class="split-right">
      <slot name="right">
        <BasePanel title="Inspector Pane">Inspector details</BasePanel>
      </slot>
    </div>
  </div>
</template>

<script setup>
</script>
