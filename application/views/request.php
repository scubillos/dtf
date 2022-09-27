<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title> Crear solicitud </title>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-social.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/select2.min.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/custom.css') ?>" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-secondary" style="background-color:#23345a !important;">
            <a class="navbar-brand" href="#">
                <img src="<?php echo base_url('assets/images/logo.png'); ?>" style="height: 50px;" />
                Crear solicitud
            </a>
            <div class="mx-auto"></div>
            <div class="nav navbar-nav navbar-right">
                <!--<a href="<?php echo base_url('User/logout') ?>" class="btn btn-outline-danger bg-light"> <i class="fas fa-sign-out-alt"></i> Cerrar Sesion</a>-->
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
                            <form @submit.prevent="registerRequest" >
                                <div class="form-group row">
                                    <label class="col-md-4"># Factura</label>
                                    <div class="input-group col-md-8">
                                        <input 
                                            type="number" 
                                            step="any" min="0" class="form-control" v-model="request.id_factura" @change="getFactura" 
                                            :style="(errors.factura) ? 'border-color: red;' : ''"
                                            required >
                                        <span class="col-12" style="font-size:12px;color:red;" v-if="errors.factura">El número de factura no es válido</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Tipo documento</label>
                                    <div class="input-group col-md-8">
                                        <select2 :options="documentos" class="form-control" v-model="request.tipo_documento" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4"># Documento</label>
                                    <div class="input-group col-md-8">
                                        <input type="number" min="0" class="form-control" v-model="request.num_documento" required >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Producto Anterior</label>
                                    <div class="input-group col-md-8">
                                        <select2 :options="productos" class="form-control" v-model="request.id_producto_ant" :readonly="true" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Producto Nuevo</label>
                                    <div class="input-group col-md-8">
                                        <select2 :options="productos" class="form-control" v-model="request.id_producto_nuevo" id="producto_nuevo" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Email</label>
                                    <div class="input-group col-md-8">
                                        <input type="email" class="form-control" v-model="request.email" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Motivo</label>
                                    <div class="input-group col-md-8">
                                        <select2 :options="motivos" class="form-control" v-model="request.motivo" id="motivo" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Ciudad de destino</label>
                                    <div class="input-group col-md-8">
                                        <select2 :options="ciudades" class="form-control" v-model="request.ciudad" id="ciudad" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4">Observaciones</label>
                                    <div class="input-group col-md-8">
                                        <textarea class="form-control" v-model="request.observaciones" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                                        <button type="button" class="btn btn-success" v-if="env=='dev'" @click="logrequest"><i class="fas fa-save"></i> Log</button>
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
        <script src="https://unpkg.com/vue-cookies@1.8.1/vue-cookies.js"></script>
        <script src="<?php echo base_url('assets/js/select2.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/request.js') ?>"></script>

        <script type="text/x-template" id="select2-template">
            <select>
                <slot></slot>
            </select>
        </script>
    </body>
</html>