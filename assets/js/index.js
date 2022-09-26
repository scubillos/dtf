window.fbAsyncInit = function() {
  FB.init({
    appId      : '392921829700489',
    cookie     : true,
    xfbml      : true,
    version    : 'v15.0'
  });

  FB.getLoginStatus(function(response) {
    if (response.status === 'connected') {
      console.log("response", response);
    } else {
      console.log("responseElse", response);
    }
  });
    
  //FB.AppEvents.logPageView();
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

function fbLogin() {
  FB.login(function (response) {
    if (response.authResponse) {
      getFbUserData();
    } else {
      document.getElementById("msgLoginFb").innerHTML = "El usuario no hizo el login por completo";
    }
  }, { scope: 'email' });
}

function getFbUserData() {
  FB.api('/me', { locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture' },
    function (response) {
      document.getElementById('fbLink').setAttribute("onclick", "fbLogout");
      document.getElementById('fbLink').innerHTML = "Salir de Facebook";
      document.getElementById('msgLoginFb').innerHTML = "<b>Hola "+ response.first_name +"</b>";

      saveUserData(response);
    }  
  );
}

function saveUserData(response) {
  localStorage.setItem("userFB", JSON.stringify(response));
}

function fbLogout() {
  FB.logout(function() {
    document.getElementById('fbLink').setAttribute("onclick", "fbLogin()");
    document.getElementById('fbLink').innerHTML = "Continuar con facebook";
    document.getElementById('msgLoginFb').innerHTML = "<b>Cerrar sesion exitoso</b>";
  })
}