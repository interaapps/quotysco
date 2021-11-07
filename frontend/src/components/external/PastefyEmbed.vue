<template>
    <div>
        <div class="paste">
            <h1>{{paste.title}}</h1>
            <pre>{{paste.content}}</pre>
            <a class="paste-logo" :href="'https://pastefy.ga/'+paste.id" target="_blank"><img src="https://raw.githubusercontent.com/interaapps/pastefy/master/frontend/src/assets/logo.png" ></a>
            <br><br>
        </div>
    </div>
</template>
<script>
import { Prajax } from 'cajaxjs'

export default {
    name: 'pastefy-embed',
    data: ()=>({
        paste: {}
    }),
    mounted(){
        Prajax.get("https://pastefy.ga/api/v2/paste/"+this.pasteid)
            .then(res => res.json())
            .then(res => {
                this.paste = res
            })
    },
    props: {
        pasteid: {default: ''}
    }
}
</script>
<style lang="scss" scoped>
.paste {
    padding: 10px;
    background: #262b39;
    color: #FFF;
    border-radius: 10px;
    font-size: 20px;

    h1 {
        font-size: 30px !important;
        color: #FFF !important;
        margin-bottom: 0px !important;
    }

    pre {
        overflow: auto;
        margin: 0px !important;
        &::-webkit-scrollbar {
           display: none;
        }
    }

    .paste-logo {
        float: right;
        img {
            display: block;
            width: 130px;
        }
    }
}
</style>