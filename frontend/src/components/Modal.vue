<template>
    <div id="modal" v-if="opened || cssHide" @click="close" :class="{'hidden': cssHide && !opened}">
        <div id="model-modal" @click.stop>
            <svg id="close-button" @click="close" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
            <h1>{{mtitle}}</h1>
            <slot />
        </div>
    </div>
</template>
<script>
export default {
    data: ()=>({
        opened: false,
        onOpen: ()=>{},
        onClose: ()=>{}
    }),
    mounted(){
        this.$on("close", ()=>{
            this.opened = false
        })
        this.$on("open", ()=>{
            this.opened = false
        })
    },
    props: {
        'mtitle': {default: ""},
        'cssHide': {
            default: false
        }
    },
    methods: {
        close(){
            this.opened = false
            this.onClose()
        },
        open(){
            this.opened = true
            this.onOpen()
        }
    }
}
</script>
<style lang="scss" scoped>
#modal {
    position: fixed;
    z-index: 100000;
    width: 100%;
    height: 100%;
    left: 0px;
    top: 0px;
    background: #00000009;
    #model-modal {
        max-height: 60%;
        overflow: auto;
        position: fixed;
        z-index: 1000000;
        min-width: 400px;
        left:  50%;
        top: 40%;
        background: #FFF;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0px 1px 10px 0px #00000011;
        transform: translateX(-50%) translateY(-50%);
        h1 {
            font-size: 27px;
            margin-top: 3px;
            margin-left: 2px;
            margin-bottom: 16px;
            color: #434343;
        }

        #close-button {
            width:  39px;
            height: 39px;
            position: absolute;
            right: 10px;
            padding: 2px;
            border-radius: 56px;
            cursor: pointer;

            &:hover {
                background: #00000011;
            }
        }
    }

    &.hidden {
        display: none;
    }
}

@media screen and (max-width: 720px) {
    #modal {
        #model-modal {
            top: none;
            bottom: 0px;
            transform: translateX(-50%);
            width: 100%;
            padding: 20px;
            overflow: auto;

            h1 {
                font-size: 25px;
            }
        }
    }
}
</style>
