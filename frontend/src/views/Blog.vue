<template>
    <div>
        <br><br><br><br><br>
        <div class="contents">
            <blog-layout :blog="blog">
                <div>
                    <post v-for="post of posts" :key="post.id" :post='post' />
                    <a @click="loadPage(postsPagination.page+1)" class="big-action-button" v-if="!(postsPagination.page >= postsPagination.pages)">LOAD MORE</a>
                </div>
            </blog-layout>
        </div>
    </div>
</template>
<script>
import BlogLayout from '../components/BlogLayout.vue';
import Post from '../components/Post.vue';
export default {
  components: { Post, BlogLayout },
    data: ()=>({
        blog: {},
        posts: [],
        postsPagination: {
            page: 0,
            pages: 0,
            total: 0
        }
    }),
    mounted(){
        this.load()
    },
    watch:{
        '$route'(){
            this.load()
        }
    },
    methods: {
        load() {
            this.posts = []
            this.postsPagination = {
                page: 0,
                pages: 0,
                total: 0
            }
            this.api.getBlog(this.$route.params.blog)
                .then(res => {
                    this.blog = res;
                    this.$store.state.pageTitle = {text: this.blog.display_name, verified: this.blog.verified}
                })
            this.loadPage(1)
        },
        loadPage(page=1){
            this.api.getBlogPosts(this.$route.params.blog, 10, page)
                .then(res => {
                    for (const post of res.data)
                        this.posts.push(post)
                    this.postsPagination = {
                        page: res.page,
                        pages: Math.ceil(res.total/res.page_size),
                        total: res.total
                    }
                })
        }
    }
}
</script>
<style lang="scss" scoped>
</style>