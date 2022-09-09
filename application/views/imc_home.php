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
            <div class="mx-auto"></div>
            <div class="nav navbar-nav navbar-right">
                <a href="<?php echo base_url('User/logout') ?>" class="btn btn-outline-danger bg-light"> <i class="fas fa-sign-out-alt"></i> Cerrar Sesion</a>
            </div>
        </nav>
        <div class="container" id="imc_home">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                    <h4>Bienvenid@ <?php echo $nombre_completo ?></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            Calcula tu Indice de Masa Corporal
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="form-group row">
                                    <label class="col-md-2">Peso</label>
                                    <div class="input-group col-md-10">
                                        <input type="number" step="any" min="0" class="form-control" v-model="imc.peso">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">kg</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2">Estatura</label>
                                    <div class="input-group col-md-10">
                                        <input type="number" min="0" class="form-control" v-model="imc.estatura">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">cm</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <button type="button" class="btn btn-primary" @click="registraImc()"><i class="fas fa-calculator"></i> Calcular</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="button" class="btn btn-info mx-auto" @click="toggleHistorico()" v-html="btnHistorico"></button>
                <!-- Tabla que muestra los registros de la base de datos -->
                <div class="table-responsive col-md-11 col-lg-11 col-sm-12 mx-auto" v-if="historico">
                    <table class="table table-striped table-bordered" id="table_imc">
                        <thead>
                            <th>Peso</th>
                            <th>Estatura</th>
                            <th>IMC Calculado</th>
                            <th>Clasificación</th>
                            <th>Fecha/Hora registro</th>
                        </thead>
                        <tbody>
                            <tr v-for="(imc,i) in imc_arr" :key="i">
                                <td v-text="imc.peso+' kg'"></td>
                                <td v-text="imc.estatura+' cm'"></td>
                                <td v-text="imc.imc_calculado"></td>
                                <td v-text="imc.tipo+'/'+imc.clasificacion"></td>
                                <td v-text="imc.fecha_registro"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>   
        <script src="https://kit.fontawesome.com/0030ea6865.js"></script>
        <script src="//code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="<?php echo base_url('assets/js/vue.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/vue-resource.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/imc_home.js') ?>"></script>
    </body>
</html>