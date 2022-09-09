var imc_home = new Vue({
    el: '#imc_home',
    data: function() {
        return {
            imc_arr: [],
            imc: {},
            historico: false,
            btnHistorico: '<i class="fas fa-history"></i> Ver Histórico'
        };
    },
    mounted: function() {
        let self = this;
        self.obtenerImc();
    },
    methods: {
        toggleHistorico: function() {
            let self = this;
            self.historico=!self.historico;
            self.btnHistorico = self.historico ? '<i class="fas fa-eye-slash"></i> Ocultar Histórico' : '<i class="fas fa-history"></i> Ver Histórico';
        },
        registraImc: function () {
            let self = this;
            let data = self.imc;
            self.$http.post('/Imc_ajax/calcular',data).then(function(response){
                toastr.success(response.body.msg[0]);
                self.obtenerImc();
                if (!self.historico) {
                    self.toggleHistorico();
                }
            },function(response){
                if (response.body.msg !== undefined) {
                    for (let i in response.body.msg) {
                        let msg = response.body.msg[i];
                        toastr.error(msg);
                    }
                } else {
                    toastr.error("Error en el servidor");
                }
            });
        },
        obtenerImc: function() {
            let self = this;
            self.imc = {
                peso: null,
                estatura: null
            };
            self.$http.get('/Imc_ajax').then(function(response){
                self.imc_arr = [];
                for (let i in response.body.data) {
                    self.imc_arr.push(response.body.data[i]);
                }
            },function(response){
                if (response.body.msg !== undefined) {
                    for (let i in response.body.msg) {
                        let msg = response.body.msg[i];
                        toastr.error(msg);
                    }
                } else {
                    toastr.error("Error en el servidor");
                }
            });
        },
    }
 });