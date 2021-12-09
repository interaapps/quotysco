<template>
  <div class="home">
    <div id="category-top">
        <div class="content">
            <!-- RECOMMENDATIONS <div id="categories">
                <router-link to="/category/hardware">Hardware</router-link>
                <router-link to="/category/web-development">Web Development</router-link>
            </div>-->
            <h1>{{displayName}}</h1>
        </div>
    </div>
    <div class="content">
      <br><br>
      <!--<post v-if="posts[0]" :post="posts[0]" v-show="i != 0" />-->
      
      <div class="post-list">
        <post v-for="(post, i) of posts" :key="i" :post='post' />
      </div>
      <a @click="loadPage($route.params.name, postsPagination.page+1)" class="big-action-button" v-if="!(postsPagination.page >= postsPagination.pages)">LOAD MORE</a>
    </div>
  </div>
</template>

<script>
import Post from '../components/Post.vue'

export default {
  name: 'Home',
  data: ()=>({
    displayName: false,
    posts: [],
    postsPagination: {
        page: 0,
        pages: 0,
        total: 0
    }
  }),
  created(){
    this.load(this.$route)
  },
  methods: {
    loadPage(name, page = 1){
      this.api.getCategoryPosts(name, 10, page)
        .then(res => {
          for (const post of res.data){
            this.posts.push(post)
          }
          this.postsPagination = {
              page: res.page,
              pages: Math.ceil(res.total/res.page_size),
              total: res.total
          }
        })
    },
    load(route){
      this.$store.state.pageTitle = route.params.name
      this.posts = []
      this.loadPage(route.params.name, 1)
      this.api.getCategory(route.params.name)
        .then(res=>{
          console.log(res);
          this.displayName = res.display_name
        })
    }
  },
  watch: {
    $route(to, from, next){
        this.load(to);
        next();
    }
  },
  components: {
    Post
  }
}
</script>
<style lang="scss" scoped>
.content {
  max-width: 1140px;
  padding: 10px;
  margin: auto;
}
</style>