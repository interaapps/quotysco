<template>
  <div class="home">
      
    <div class="contents">
        <div id="post"> 
            <h1>{{post.title}}<i class="uil uil-lock" style="margin-left:10px" v-if="post.state != 'PUBLISHED'" /></h1>
            <img v-if="post.image" id="post-banner" :src="post.image">

            <span class="date">{{getDate()}}</span>
            <router-link to="." class="user">
                <img class="profile-pic" :src="post.blog.image" alt="">
                <span>{{post.blog.type == 'USER' ? post.author.name : post.author.name+' @ '+post.blog.name}} <i class="uil uil-check-circle verified-badge" v-if="post.blog.verified" /></span>
            </router-link>
            
            <div id="action-button">
                <div @click="like" class="action-button" :class="{liked: post.liked}">
                    <i class="uil uil-heart"></i>
                    <span>{{post.likes_count}}</span>
                </div>
                <div class="action-button comments">
                    <i class="uil uil-comment"></i>
                    <span>{{post.comments_count}}</span>
                </div>
            </div>

            <div id="post-contents">
                <div v-for="(part, i) of post.contents.contents" :key="i">
                    <h1 v-if="part.type=='H1'">{{part.contents}}</h1>
                    <h2 v-if="part.type=='H2'">{{part.contents}}</h2>
                    <h3 v-if="part.type=='H3'">{{part.contents}}</h3>
                    <h4 v-if="part.type=='H4'">{{part.contents}}</h4>
                    <div class="markdown-contents" v-else-if="part.type=='TEXT'" v-html="part.contents" />
                    <img v-else-if="part.type=='IMAGE'" :src="part.url" :width="part.width ? part.width : false" />
                    <iframe v-else-if="part.type=='YOUTUBE'" width="560" height="315" :src="'https://www.youtube.com/embed/'+part.id" frameborder="0"  allowfullscreen></iframe>
                    <pastefy-embed v-else-if="part.type=='PASTEFY'" :pasteid="part.id" />
                </div>
            </div>
            <div id="categories">
                <router-link v-for="(category, i) in post.categories" :key="i" class="category" :to="'/category/'+category.name">{{category.display_name}}</router-link>
            </div>
            <div style="float: right">
                <a @click="deletePost" v-if="post.author_is_me" class="button danger">Delete Post</a>
                <router-link :to="`/${post.blog.name}/${post.url}/edit`" v-if="post.blog.member_of" class="button" style="margin-left: 10px">Edit Post</router-link>
            </div>
        </div>
    </div>
    <div style="background: #EEEEEE43; margin-top: 80px;">
        <div class="contents">
            <div class="posts">
                <h1>More by {{post.blog.name}}</h1>
                <div class="post-list">
                    <post v-for="post of moreUserPosts" :key="post.id" :post="post" />
                </div>
            </div>
        </div>
      </div>
    </div>

</template>

<script>
import Post from '../components/Post.vue'
import PastefyEmbed from '../components/external/PastefyEmbed'
import hljs from "highlight.js";
import { login } from '../main'

const markdown = require('markdown-it')({
    html:         false,
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(lang, str).value;
            } catch (e) {
                //
            }
        }
        return str;
    }
});

export default {
  name: 'Home',
  data: ()=>({
      post: {contents: {}},
      moreUserPosts: []
  }),
  created(){
      this.load();
  },
  methods: {
     load(route = null){
        if (!route)
            route = this.$route
        this.api.getBlogPost(route.params.blog, this.$route.params.post)
            .then(res => {
                this.post = res
                for (const i in res.contents.contents) {
                    if (res.contents.contents[i].type == 'TEXT'){
                        res.contents.contents[i].contents = markdown.render(res.contents.contents[i].contents)
                    }
                }
                this.$store.state.pageTitle = `${this.post.blog.display_name} - ${this.post.title}`
            })

        this.api.getBlogPosts(route.params.blog, 2, 0)
            .then(res => {
                this.moreUserPosts = res.data
            })
    },
    async like(){
        if (this.$store.state.auth.loggedIn){
            await this.api.likePost(this.post.blog.name, this.post.url)
            this.post.liked = await this.api.likedPost(this.post.blog.name, this.post.url)
            if (this.post.liked)
                this.post.likes_count++
            else
                this.post.likes_count--
        } else {
            login()
                .then(()=>{
                    this.like()
                })
        }
    },

    getDate(){
        
        const date = new Date(this.post.created_at)
        return (["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"][date.getMonth()])+" "+date.getDate()+(date.getFullYear() == new Date().getFullYear() ? '' : " "+date.getFullYear())
        
        //return this.post.created_at
    },
    async deletePost(){
        await this.api.deleteBlogPost(this.post.blog.name, this.post.url)
        this.$router.push("/"+this.post.blog.name)
    }
  },
  watch: {
    $route(to, from, next){
        this.load(to);
        next();
    }
  },
  components: {
    Post,
    PastefyEmbed
  }
}
</script>
<style lang="scss" scoped>


.posts {
  padding: 50px 0px;
  h1 {
      margin-bottom: 20px;
      font-weight: 500;
      color: #545454;
  }
  .post {
      width: 48%;
      margin-right: 2%;
      vertical-align: top;
      display: inline-block;
  }
}

</style>