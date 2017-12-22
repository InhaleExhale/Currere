import Vue from 'vue'
import Router from 'vue-router'
import HelloWorld from '@/components/HelloWorld'
import Activities from '@/components/Activities/Activities'
import Connectors from '@/components/Connectors/Connectors'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'HelloWorld',
      component: HelloWorld
    },
    {
      path: '/activities',
      name: 'Activities',
      component: Activities
    },
    {
      path: '/connectors',
      name: 'Connectors',
      component: Connectors
    }
  ],
  linkActiveClass: 'is-primary'
})
