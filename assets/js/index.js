window.fbAsyncInit = function() {
  FB.init({
    appId      : '392921829700489',
    cookie     : true,
    xfbml      : true,
    version    : '1'
  });
    
  FB.AppEvents.logPageView();
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

FB.getLoginStatus(function(response) {
  //statusChangeCallback(response);
  console.log("response", response);
});

function checkLoginState() {
  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });
}
