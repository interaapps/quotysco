<template>
  <div class="home">
    <div id="category-top">
        <div class="content">
            <h1>Trending</h1>
        </div>
    </div>
    <div class="content">
      <br><br>
      <div class="post-list">
        <post v-for="(post, i) of posts" :key="i" :post='post' />
      </div>
      <a @click="loadPage(postsPagination.page+1)" class="big-action-button" v-if="postsPagination.page < postsPagination.pages">LOAD MORE</a>
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
    this.loadPage(1)
  },
  methods: {
    loadPage(page = 1){
      this.api.get("/api/v1/global/trending", {limit: 10, page, trending: 'true'})
        .then(res=>res.json())
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
    }
  },
  components: {
    Post
  }
}
</script>
<style lang="scss" scoped>
.content {
  max-width: 900px;
  padding: 10px;
  margin: auto;
}
</style>