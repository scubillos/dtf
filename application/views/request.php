<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title> Crear solicitud </title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-social.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.css') ?>" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-secondary" style="background-color:#23345a !important;">
            <a class="navbar-brand" href="#">Crear solicitud</a>
            <div class="mx-auto"></div>
            <div class="nav navbar-nav navbar-right">
                <a href="<?php echo base_url('User/logout') ?>" class="btn btn-outline-danger bg-light"> <i class="fas fa-sign-out-alt"></i> Cerrar Sesion</a>
            </div>
        </nav>
        <div class="container" id="request_form">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                    <h4>Bienvenid@ <?php echo $nombre_completo ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <i class="" />
                            Crea una nueva solicitud de cambio
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-md-4"># Factura</label>
                                    <div class="input-group col-md-8">
                                        <input type="number" step="any" min="0" class="form-control" v-model="request.id_factura">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Tipo documento</label>
                                    <div class="input-group col-md-8">
                                        <select v-model="request.tipo_documento" class="form-control" required>
                                            <option value="">Seleccione</option>
                                            <option v-for="documento in documentos" v-value="documento.value">{{documento.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"># Documento</label>
                                    <div class="input-group col-md-8">
                                        <input type="number" min="0" class="form-control" v-model="request.num_documento">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Producto</label>
                                    <div class="input-group col-md-8">
                                        <select v-model="request.id_producto" class="form-control" required>
                                            <option value="">Seleccione</option>
                                            <option v-for="producto in productos" v-value="producto.value">{{producto.text}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Email</label>
                                    <div class="input-group col-md-8">
                                        <input type="email" class="form-control" v-model="request.email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Observaciones</label>
                                    <div class="input-group col-md-8">
                                        <textarea class="form-control" v-model="request.num_documento" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <button type="button" class="btn btn-primary" @click="registraImc()"><i class="fas fa-save"></i> Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        <script src="https://kit.fontawesome.com/0030ea6865.js"></script>
        <script src="//code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/vue-resource.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/request.js') ?>"></script>
    </body>
</html>