<template>
  <div class="home">
    <div class="content">
      <div class="posts">
        <router-link to="/following" class="button" style="float: right">More</router-link>
        <br><br>
        <h1>You follow</h1>
        <div class="post-list">
          <post v-for="post of following_posts" :key="post.id" :post='post' />
        </div>
        <a @click="load(postsPagination.page+1)" class="big-action-button" v-if="!(postsPagination.page >= postsPagination.pages)">LOAD MORE</a>
      </div>
    </div>
  </div>
</template>

<script>
import Post from '../components/Post.vue'

export default {
  data:()=>({
    following_posts: [],
    postsPagination: {
        page: 0,
        pages: 0,
        total: 0 
    }
  }),
  created(){
    this.$store.state.pageTitle = `Following`
    this.following_posts = []
    this.load(1)
  },
  methods:{
    load(page){
      this.api.get("/api/v1/user/following_posts", {limit: 8, page})
        .then(this.api.handleRequest)
        .then(res=>{
          for (const post of res.data){
            this.following_posts.push(post)
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
h1 {
    margin-top: 60px;
    margin-bottom: 20px;
    font-weight: 500;
    color: #545454;
}
</style>