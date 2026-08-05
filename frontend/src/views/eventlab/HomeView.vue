<template>
  <div class="body-three-split">
    <div class="body-header">
        <div class="left-items">
          [item 1] [item 2] [item 3]
        </div>
        <div class="right-items">
          [ADD] [info] [msg] [avatar]
        </div>
    </div>
    <div class="body-filter">
      <div class="left-items">
        [searchbar]
      </div>
      <div class="right-items">
        [filters]
      </div>
    </div>
    <div class="body-content">
      Things in the body
    </div>
  </div>
</template>

<script setup>
</script>

<style>
  .body-three-split {
    display: flex;
    flex-direction: column;
    padding: var(--style-dist);
    gap: 8px;

    .body-filter,
    .body-header {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      border-radius: var(--radius-md);
      padding: 4px 10px;
      min-height: 40px;
      /*background: rgba(255,255,255, 0.4);*/
    }
    .body-content {
      /*background: rgba(255,255,255, 0.4);*/
      border-radius: var(--radius-md);
      min-width: 100%;
      min-height: calc(100dvh - 176px);
      padding: 4px 10px;
    }
  }

</style>