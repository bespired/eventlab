<template>
    <div class="modal-template">
        <router-view />
    </div>
</template>
<script setup>
</script>
<style>
.modal-template {
    display: flex;
    flex-direction: row;
    margin: auto;
    min-width: calc(100vw - 40px);
    min-height: calc(100dvh - 40px);
    overflow: hidden;

}
</style>