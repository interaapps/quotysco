<template>
  <div class="home">
    <div class="contents">
        <div ref="post" id="post" v-if="post.created_at"> 
            <h1>{{post.title}}<i class="uil uil-lock" style="margin-left:10px" v-if="post.state != 'PUBLISHED'" /></h1>
            <img v-if="post.image" id="post-banner" :src="post.image">

            <span class="date" :title="this.post.created_at">{{getDate()}}</span>
            
            <router-link to="." class="user" ref="user">
                <img v-if="post.blog" class="profile-pic" :src="post.blog.image" alt="">
                <!--<span>{{post.blog.type == 'USER' ? post.author.name : post.author.name+' @ '+post.blog.name}} <i class="uil uil-check-circle verified-badge" v-if="post.blog.verified" /></span>-->
                <span>{{post.blog.display_name}} <i class="uil uil-check-circle verified-badge" v-if="post.blog.verified" /></span>
            </router-link>

            <a v-for="contentInfo of [...post.content_information, ...post.blog.content_information]" :key="contentInfo.id" target="_blank" class="content-information" :href="contentInfo.link" :class="[contentInfo.level.toLowerCase()]">
                <img v-if="contentInfo.logo" :src="contentInfo.logo">
                <span>{{contentInfo.information}}</span>
                <i v-if="contentInfo.link" class="uil uil-external-link-alt" />
            </a>
            
            <div id="action-button">
                <div @click="like" class="action-button" :class="{liked: post.liked}">
                    <i class="uil uil-heart"></i>
                    <span>{{post.like_count}}</span>
                </div>
                <div class="action-button comments">
                    <i class="uil uil-comment"></i>
                    <span>{{post.comment_count}}</span>
                </div>
                <div class="progress" :style="{opacity: scrollProgress ? 1 : 0}"><div :style="{width: scrollProgress*100+'%'}"/></div>
            </div>

            <div ref="postContents" id="post-contents">
                <div v-for="(part, i) of post.contents.contents" :key="i">
                    <h1 v-if="part.type == 'H1'">{{part.contents}}</h1>
                    <h2 v-if="part.type == 'H2'">{{part.contents}}</h2>
                    <h3 v-if="part.type == 'H3'">{{part.contents}}</h3>
                    <h4 v-if="part.type == 'H4'">{{part.contents}}</h4>
                    <div class="markdown-contents" v-else-if="part.type=='TEXT'" v-html="part.contents" />
                    <img v-else-if="part.type=='IMAGE'" :src="part.url" :width="part.width ? part.width : false" />
                    <iframe v-else-if="part.type=='YOUTUBE'" width="560" height="315" :src="'https://www.youtube-nocookie.com/embed/'+part.id" frameborder="0"  allowfullscreen></iframe>
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
            <div class="posts" v-if="moreUserPosts.length > 1">
                <h1>More by {{post.blog.name}}</h1>
                <div class="post-list">
                    <template v-for="userPost of moreUserPosts">
                        <post :key="userPost.id" v-if="post.id != userPost.id" :post="userPost" />
                    </template>
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
    },
    breaks: true
    
});

export default {
  name: 'Home',
  data: ()=>({
      post: {contents: {}},
      moreUserPosts: [],
      scrollEvent: null,
      scrollProgress: 0
  }),
  created(){
    this.load();
  },
  mounted(){
      const interval = setInterval(()=>{
          if (this.$refs.user) {
                const observer = new IntersectionObserver( 
                ([e]) => e.target.classList.toggle("is-pinned", e.intersectionRatio < 1),
                { threshold: [1], }
                );

                observer.observe(this.$refs.user.$el);
              clearInterval(interval)
          }
      }, 500)
      
      this.scrollEvent = () => {
          this.scrollProgress = (window.scrollY - this.$refs.postContents.offsetTop) / (this.$refs.postContents.clientHeight - this.$refs.postContents.offsetTop)
          if (this.scrollProgress > 1)
            this.scrollProgress = 1
          if (this.scrollProgress < 0)
            this.scrollProgress = 0
      }

      window.addEventListener("scroll", this.scrollEvent)
  },
  beforeDestroy(){
      window.removeEventListener("scroll", this.scrollEvent)
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
                        res.contents.contents[i].contents = this.renderMarkdown(res.contents.contents[i].contents)
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
                this.post.like_count++
            else
                this.post.like_count--
        } else {
            login()
                .then(()=>{
                    this.like()
                })
        }
    },
    renderMarkdown(md){
        /* const dangerousTags = ['script', 'style'].join("|")
        md = md.replace(new RegExp(`^<(${dangerousTags})( (.*))?>`, 'gism'), (_, tag)=>`&lt;${tag}&gt;`)
               .replace(new RegExp(`^</(${dangerousTags})( (.*))?>$`, 'gism'), (_, tag)=>`&lt;/${tag}&gt;`)*/
        let html = markdown.render(md)
        return html
    },

    getDate(){
        const date = new Date(this.post.created_at.replace(" ", "T"))
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