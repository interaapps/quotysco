import { PrajaxClient } from 'cajaxjs'

import store from './store'

class ApiClient extends PrajaxClient {
    constructor(baseUrl, apiKey=null){
        super({
            baseUrl,
            options: {
                header: {
                    Authorization: "Bearer "+(apiKey ? apiKey : "")
                },
                json: true
            }
        })

        this.handleRequest = res => {
            res = res.json()
            if (!res.success) {
                console.log("ERR");
                const error = new Error()
                if (res.message)
                    error.message = res.message
                if (res.exception)
                    error.name = res.exception
                throw error
            }
            return res
        }
    }

    setApiKey(apiKey){
        this.options.header["Authorization"] = "Bearer "+apiKey
    }


    loadUser() {
        return this.getUser()
            .then(res => {
                store.state.auth.loggedIn = true
                store.state.auth.user = res

                this.getUserBlogs()
                    .then(res => {
                        store.state.auth.blogs = res.data
                    })

                return res
            })
    }

    getUser() {
        return this.get("/api/v1/user")
            .then(this.handleRequest)
    }

    getUserBlogs() {
        return this.get("/api/v1/user/blogs")
            .then(this.handleRequest)
    }

    getBlog(name) {
        return this.get("/api/v1/blogs/"+name)
            .then(this.handleRequest)
    }

    getBlogMembers(name) {
        return this.get("/api/v1/blogs/"+name+"/members")
            .then(this.handleRequest)
    }

    updateBlogMember(blog, userId, data) {
        return this.put("/api/v1/blogs/"+blog+"/members/"+userId, data)
            .then(this.handleRequest)
    }

    addBlogMember(blog, user) {
        return this.post("/api/v1/blogs/"+blog+"/members", {
            name: user
        })
            .then(this.handleRequest)
    }

    removeBlogMember(blog, user) {
        return this.delete("/api/v1/blogs/"+blog+"/members/"+user).then(this.handleRequest)
    }

    createBlog(data) {
        return this.post("/api/v1/blogs", data)
            .then(this.handleRequest)
    }

    updateBlog(name, data) {
        return this.put("/api/v1/blogs/"+name, data)
            .then(this.handleRequest)
    }

    follow(name) {
        return this.post("/api/v1/blogs/"+name+"/follow")
            .then(this.handleRequest)
    }

    isFollowing(name) {
        return this.get("/api/v1/blogs/"+name+"/following")
            .then(this.handleRequest)
            .then(res=>res.following)
    }

    getBlogPosts(name, limit=10, page=1) {
        return this.get("/api/v1/blogs/"+name+"/posts", {limit, page})
            .then(this.handleRequest)
    }

    getCategory(name) {
        return this.get("/api/v1/categories/"+name)
            .then(this.handleRequest)
    }

    getCategoryPosts(name, limit=10, page=1) {
        return this.get("/api/v1/categories/"+name+"/posts", {limit, page})
            .then(this.handleRequest)
    }


    getBlogPost(blog, name) {
        return this.get("/api/v1/posts/"+blog+"/"+name)
            .then(this.handleRequest)
    }


    deleteBlogPost(blog, name) {
        return this.delete("/api/v1/posts/"+blog+"/"+name)
            .then(this.handleRequest)
    }

    likePost(blog, post){
        return this.post("/api/v1/posts/"+blog+"/"+post+"/like")
            .then(this.handleRequest)
    }

    likedPost(blog, post){
        return this.get("/api/v1/posts/"+blog+"/"+post+"/liked")
            .then(this.handleRequest)
            .then(res=>res.liked)
    }
}

export default ApiClient