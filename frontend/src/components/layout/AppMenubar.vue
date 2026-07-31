<template>
  <aside class="side-menu">
    <ul >
      <li><big-icon name="menu-eventlab" />
          <div class="menu-popup">Eventlab</div>
      </li>
      <li><big-icon name="menu-gauge" />
          <div class="menu-popup">Dashboard</div>
      </li>
      <li><big-icon name="menu-segment" />
          <div class="menu-popup">Analytics</div>
      </li>
      <li><big-icon name="menu-profile" />
          <div class="menu-popup">Profile</div>
      </li>
      <li><big-icon name="menu-mail" />
          <div class="menu-popup">Mailer</div>
      </li>
      <li><big-icon name="menu-rocket" />
          <div class="menu-popup">Automation</div>
      </li>
      <li><big-icon name="menu-cog" />
          <div class="menu-popup">Settings</div>
      </li>
    </ul>
  </aside>
</template>

<script setup>

</script>

<style>
  .side-menu {
    backdrop-filter: blur(3px);
  }
  .side-menu li {
    display: flex;
    position: relative;
    font-size: 2.5em;
    color: var(--color-menu);
    height: 1em;
    justify-self: center;
    transition: color 200ms;
    cursor: pointer;
    margin-bottom: 0.2em;
    .alpha-mask {
       z-index: 1;
    }
    .menu-popup {
        font-size: 1.1rem;
        font-weight: 500;
        line-height: 1;
        position: absolute;

        top: 10px;
        left: -6px;
        background: var(--color-menu);
        color: white;
        padding: 0 12px 3px 56px;
        border-radius: 20px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 200ms;
        z-index: 0;
    }
  }
  .side-menu li:hover{
    color: white;
    .menu-popup {
      opacity: 1;
      z-index: 0;
    }

  }
</style>