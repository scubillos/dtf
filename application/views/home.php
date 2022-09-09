<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title> Calculadora de IMC </title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-social.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.css') ?>" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-secondary" style="background-color:#23345a !important;">
            <a class="navbar-brand" href="#">Calculadora de IMC</a>
        </nav>
        <div class="container" style="width:30%;min-height:auto !important;height: auto !important;" id="home">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-12 mx-auto">
                    <div class="myform form" v-show="login">
                        <div class="logo mb-3">
                            <div class="col-md-12 text-center">
                                <h4>Login</h4>
                            </div>
                        </div>
                        <form action="" method="post" name="login">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" v-model="datalogin.email" class="form-control" placeholder="example@mail.com">
                            </div>
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="password" v-model="datalogin.password"  class="form-control" placeholder="**********">
                            </div>
                            <div class="text-center ">
                                <button type="button" class="btn btn-block btn-primary tx-tfm" @click="ingresar()">Login</button>
                            </div>
                            <div class="">
                                <div class="login-or">
                                    <hr class="hr-or">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <a class="btn btn-block btn-social btn-facebook text-center text-light" @click="formRegister()">
                                    <i class="fa fa-user"></i> Crear cuenta
                                </a>
                            </div>
                            <div class="col-md-12 mb-3">
                                <a class="btn btn-block btn-social btn-facebook text-center text-light" href="<?php echo $facebookUrl; ?>">
                                    <i class="fab fa-facebook"></i> Ingrese con Facebook
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="myform form " v-show="register">
                        <div class="logo mb-3">
                            <div class="col-md-12 text-center">
                                <h4 >Crear cuenta</h4>
                            </div>
                        </div>
                        <form action="#" name="registration">
                            <div class="form-group">
                                <label>Nombres</label>
                                <input type="text" v-model="dataregistro.nombres" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Apellidos</label>
                                <input type="text" v-model="dataregistro.apellidos" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" v-model="dataregistro.email" class="form-control" placeholder="example@mail.com">
                            </div>
                            <div class="form-group">
                                <label>Clave</label>
                                <input type="password" v-model="dataregistro.clave" class="form-control" placeholder="**********">
                            </div>
                            <div class="form-group">
                                <label>Confirmar Clave</label>
                                <input type="password" v-model="dataregistro.confirma_clave" class="form-control" placeholder="**********">
                            </div>
                            <div class="col-md-12 text-center mb-3">
                                <button type="button" class=" btn btn-block mybtn btn-primary tx-tfm" @click="registro()">Registrarse</button>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <p class="text-center"><a href="#" @click="formLogin()">¿Ya tienes una cuenta?</a></p>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>   
        <script src="https://kit.fontawesome.com/0030ea6865.js"></script>
        <script src="//code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/vue-resource.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/home.js') ?>"></script>
    </body>
</html>