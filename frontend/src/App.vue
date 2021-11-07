<template>
  <div id="app">
    <Navigation />
    <router-view/>
  </div>
</template>
<script>
import Navigation from '@/components/Navigation'
export default {
  components: {
    Navigation
  },
  watch:{
    '$store.state.pageTitle'(to){
      console.log(to);
      if (to) {
        document.title = to+" | Quotysco"
      } else {
        document.title = "Quotysco"
      }
    }
  }
}
</script>
<style lang="scss">

</style>
