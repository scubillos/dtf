var home = new Vue({
    el: '#home',
    data: function() {
        return {
            login: true,
            register: false,
            datalogin: {
                email: null,
                password: null
            },
            dataregistro: {
                nombres: null,
                apellidos: null,
                email: null,
                clave: null,
                confirma_clave: null,
            }
        };
    },
    created: function() {
        
    },
    methods: {
        ingresar: function () {
            let self = this;
            let data = self.datalogin;
            if (data.email == null || data.password == null) {
                toastr.error('El email y la clave son obligatorios');
            } else {
                self.$http.post('/User_ajax/login',data).then(function(response){
                    toastr.success(response.body.msg);
                    window.setTimeout(function(){ 
                        window.location.href = '/';
                    },2000);
                    
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
            }
        },
        registro: function () {
            let self = this;
            let data = self.dataregistro;
            if (data.nombres == null || data.apellidos == null || data.email == null || data.clave == null || data.confirma_clave == null) {
                toastr.error('Todos los datos son obligatorios');
            } else {
                if (data.clave == data.confirma_clave) {
                    self.$http.post('/User_ajax/registro',data).then(function(response){
                        toastr.success(response.body.msg[0]);
                        self.register = false;
                        self.login = true;
                        self.datalogin = {
                            email: null,
                            password: null
                        };
                        self.dataregistro = {
                            nombres: null,
                            apellidos: null,
                            email: null,
                            clave: null,
                            confirma_clave: null,
                        };
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
                } else {
                    toastr.error("Las claves no coinciden");
                }
            }
        },
        formRegister: function() {
            let self = this;
            self.login = false;
            self.register = true;
        },
        formLogin: function() {
            let self = this;
            self.login = true;
            self.register = false;
        }
    }
 });