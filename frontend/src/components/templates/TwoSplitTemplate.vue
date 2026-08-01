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

    background-color: rgba(255, 255, 255, 0.5);
    border-radius: 20px;
    margin: 20px;
    min-width: calc(100vw - 40px);
    min-height: calc(100dvh - 40px);
    overflow: hidden;

    .split-left {
        width: 240px;
        background-color: rgba(255,255,255,0.8);
        border-right: 1px solid var(--border-color);
        border-top-right-radius: var(--radius-lg);
        border-bottom-right-radius: var(--radius-lg);
        margin: var(--style-dist);
        margin-left: 0;
    }

}
</style>