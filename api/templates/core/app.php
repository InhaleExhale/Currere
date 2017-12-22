<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Currere Development Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/buefy/lib/buefy.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<div id="app">
<section class="section">
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <img src="<?php echo \Config::get('core/appRoot');?>/frontend/src/assets/logo.png" alt="Currere">
            </a>
        </div>

        <div class="navbar-menu">
            <div class="navbar-start">
                <router-link to="/connectors"  active-class="is-active">Connectors</router-link>
                <router-link to="/activities" active-class="is-active" >Activities</router-link>
            </div>

        </div>
    </nav>
</section>
<section>
    <div class="container is-fluid">
        <h1 class="title">
            Hello World
        </h1>
        <p class="subtitle">
            My first website with <strong>Bulma</strong>!
        </p>
    </div>
</section>
</div>

<script src="https://unpkg.com/buefy"></script>
<script src="<?php echo \Config::get('core/appRoot');?>/templates/vuejs/lib/vue.js"></script>
<script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
<script>
    Vue.use(VueRouter);
    var app = new Vue({
        el: '#app',
    })
</script>
</body>
</html>