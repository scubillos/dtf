<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title> DTF - Cambios de producto </title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-social.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.css') ?>" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-secondary" style="background-color:#23345a !important;">
            <a class="navbar-brand" href="#">DTF - Cambios de producto</a>
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
                        <div class="col-md-12 mb-3">
                            <a class="btn btn-block btn-social btn-facebook text-center text-light" id="fbLink">
                                <i class="fab fa-facebook"></i> Ingrese con Facebook
                            </a>
                        </div>
                        <div id="msgLoginFb"></div>
                    </div>
                </div>
            </div>
        </div>   
        <script src="https://kit.fontawesome.com/0030ea6865.js"></script>
        <script src="//code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v15.0&appId=392921829700489&autoLogAppEvents=1" nonce="2DJfNNdQ"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/vue-resource.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/index.js') ?>"></script>
    </body>
</html>