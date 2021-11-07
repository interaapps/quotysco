import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import './assets/scss/app.scss'
import ApiClient from './ApiClient'
import VueObserveVisibility from 'vue-observe-visibility'
import IAOauth from './IAOauth'

Vue.use(VueObserveVisibility)

Vue.config.productionTip = false
const apiClient = new ApiClient(process.env.VUE_APP_BASE_URL, localStorage["session"])
Vue.mixin({
  data:()=>({
    api: apiClient
  })
})

apiClient.loadUser()

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')

export const eventBus = new Vue();

export async function login(){
  return await new IAOauth(process.env.VUE_APP_OAUTH_CLIENT_ID)
    .addScope("user:read")
    .openInNewWindow("/logging_in.html")
    .then(async res => {
        await apiClient.get("/authorization/oauth2/interaapps/callback", {
            code: res.code,
            popup: "true"
        })
            .then(res=>res.json())
            .then(async res => {
                localStorage["session"] = res.session
                apiClient.setApiKey(res.session)
                await apiClient.getUser()
                    .then(res => {
                        store.state.auth.loggedIn = true
                        store.state.auth.user = res
                    })
            })
    })
}


HTMLTextAreaElement.prototype.getCaretPosition = function () { //return the caret position of the textarea
  return this.selectionStart;
};
HTMLTextAreaElement.prototype.setCaretPosition = function (position) { //change the caret position of the textarea
  this.selectionStart = position;
  this.selectionEnd = position;
  this.focus();
};
HTMLTextAreaElement.prototype.hasSelection = function () { //if the textarea has selection then return true
  if (this.selectionStart == this.selectionEnd) {
      return false;
  } else {
      return true;
  }
};
HTMLTextAreaElement.prototype.getSelectedText = function () { //return the selection text
  return this.value.substring(this.selectionStart, this.selectionEnd);
};
HTMLTextAreaElement.prototype.setSelection = function (start, end) { //change the selection area of the textarea
  this.selectionStart = start;
  this.selectionEnd = end;
  this.focus();
};

