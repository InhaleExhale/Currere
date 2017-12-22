<template>
<div id="app">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<section class="section">
  <nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="#">
        <img src="./assets/logo.png" alt="Currere">
      </a>
    </div>

    <div class="navbar-menu">
      <div class="navbar-start">
        <router-link to="/connectors" class="navbar-item">Connectors</router-link>
        <router-link to="/activities" class="navbar-item" >Activities</router-link>
      </div>

    </div>
  </nav>
</section>
<section>
    <router-view/>
</section>
</div>
</template>

<script>
export default {
  name: 'app'
}
</script>

<style>
#app {
}
</style>
