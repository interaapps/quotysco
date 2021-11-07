<template>
  <div class="home">
    <div id="top">
        <h1>Explore the word</h1>
        <div id="categories">
            <router-link to="/category/STORY">Story</router-link>
            <router-link to="/category/SCIENCE">Science</router-link>
            <router-link to="/category/COMPUTER">Computer</router-link>
            <router-link to="/category/TECHNOLOGY">Stories</router-link>
        </div>
    </div>
    <div class="content">
      <div class="posts" v-if="following_posts.length > 0">
        <router-link to="/following" class="button" style="float: right">More</router-link>
        <h1>You follow</h1>
        <div class="post-list">
          <post v-for="post of following_posts" :key="post.id" :post='post' />
        </div>
      </div>
      <div class="posts" v-if="false">
        <h1>Might interest you</h1>
      </div>

      <div class="posts" v-if="global_interesting_posts.length > 0">
        <router-link to="/trends" class="button" style="float: right">More</router-link>
        <h1>Trending on Quotysco</h1>
        <div class="post-list">
          <post v-for="post of global_interesting_posts" :key="post.id" :post='post' />
        </div>
      </div>

      <div class="posts" v-if="global_latest_posts.length > 0">
        <router-link to="/trends" class="button" style="float: right">More</router-link>
        <h1>Newest on Quotysco</h1>
        <div class="post-list">
          <post v-for="post of global_latest_posts" :key="post.id" :post='post' />
        </div>
      </div>

      <div  v-if="categories.length > 0">
        <div v-for="(category, i) of categories" :key="i">
          <router-link :to="/category/+category.name" class="button" style="float: right">More</router-link>
          <h1>Category for you ({{category.name}})</h1>
          <div class="post-list"  v-if="category.data.length > 0">  
            <post v-for="post of category.data" :key="post.id" :post='post' />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Post from '../components/Post.vue'

export default {
  name: 'Home',
  data:()=>({
    following_posts: [],
    interesting_posts: [],
    global_interesting_posts: [],
    global_latest_posts: [],
    categories: [],
  }),
  created(){
    this.$store.state.pageTitle = `Beta`
    this.api.get("/api/v1/user/following_posts", {limit: 4})
      .then(this.api.handleRequest)
      .then(res=>{
        this.following_posts = res.data
      })

    this.api.get("/api/v1/global/trending", {limit: (this.$store.state.auth.loggedIn ? 2 : 6), trending: 'true'})
      .then(this.api.handleRequest)
      .then(res=>{
        this.global_interesting_posts = res.data
      })

    this.api.get("/api/v1/global/latest", {limit: (this.$store.state.auth.loggedIn ? 2 : 4), trending: 'true'})
      .then(this.api.handleRequest)
      .then(res=>{
        this.global_latest_posts = res.data
      })

    
    this.api.get("/api/v1/user/top_categories", {limit: 4})
      .then(this.api.handleRequest)
      .then(res=>{
        const categories = res.data
        this.categories = []
        for (const categoryName of categories) {
          this.api.getCategoryPosts(categoryName, 2)
            .then(res=>{
              this.categories.push({
                name: categoryName,
                ...res
              })
            })
        }
        categories;
      })
  },
  components: {
    Post
  }
}
</script>
<style lang="scss" scoped>
.content {
  max-width: 1000px;
  padding: 10px;
  margin: auto;
}

h1 {
    margin-top: 60px;
    margin-bottom: 20px;
    font-weight: 500;
    color: #545454;
}

#top {
    height: 510px;
    background: url(../assets/img/homebg.svg);
    background-size: cover;
    background-position: center;
    h1 {
        margin-top: 0px;
        padding-top: 180px;
        text-align: center;
        font-size: 60px;
        font-weight: 400;
        margin-bottom: 30px;
    }

    #categories {
        margin: auto;
        width: fit-content;

        a {
            text-decoration: none;
            color: #FFF;
            background: #121212;
            margin: 5px;
            padding: 3px 18px;
            border-radius: 6px;
            font-size: 27px;
        }
    }
}


@media screen and (max-width: 720px) {

  #top {
    h1 {
      font-size: 35px;
    }
    #categories {
      a {
        font-size: 20px;
        padding: 3px 7px;
      }
    }
  }
}
</style>