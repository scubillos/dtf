var request_form = new Vue({
    el: '#request_form',
    data: {
        api: {
            bonita: {
                url: "http://localhost:8080/bonita/",
                credentials: {
                    username: 'graphic.interface',
                    password: 'bpm'
                },
                process: 'Customer',
                token: null,
                cookie: null,
                idProcess: null,
            },
            crm: {
                url: "https://b24-gg0vby.bitrix24.es/rest/1/zvixuqixiuq7kbhl/",
            },
            erp: {
                url: "https://api.alegra.com/api/",
                token: "Y2Eucmlvc0BqYXZlcmlhbmEuZWR1LmNvOjY2NWM5NmY3OWZjNTZkZjhlZmQw",
            }
        },
        request: {
            id_factura: null,
            tipo_documento: "",
            num_documento: null,
            id_producto_ant: "",
            id_producto_nuevo: "",
            email: null,
            observaciones: null,
        },
        documentos: [
            { id: "CC", text: "Cédula de ciudadanía (CC)" },
            { id: "CE", text: "Cédula de extrangería (CE)" },
            { id: "TI", text: "Tarjeta de identidad (TI)" },
        ],
        productos: [],
        productosNuevo: []
    },
    created: function() {
        this.getProductos();
    },
    methods: {
        getProductos() {
            let self = this;
            fetch(self.api.erp.url + 'v1/items/', {
                method: 'GET',
                cache: 'no-cache',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Basic ' + self.api.erp.token,
                },
            }).then(async (response) => {
                let productos = await response.json();
                for (let i = 0; i < productos.length; i++ ) {
                    const producto = productos[i];
                    self.productos.push({ id: producto.id, text: producto.name });
                }
            });
        },
        async registerRequest() {
            if (await this.validateEmail()) {
                await this.bonitaCall();
            } else {
                toastr.error("La dirección de correo no corresponde a ningún cliente de DTF");
            }
        },
        async validateEmail() {
            let self = this;
            let filter = `?FILTER[EMAIL]=${this.request.email}&SELECT[]=NAME&SELECT[]=LAST_NAME&SELECT[]=EMAIL"`
            let valid = false;
            await fetch(self.api.crm.url + 'crm.contact.list.json' + filter, {
                method: 'GET',
                cache: 'no-cache'
            }).then(async (response) => {
                let res = await response.json();
                if (res.total != 0 && res.result.length != 0) {
                    valid = true;
                }
            });

            return valid;
        },
        async bonitaCall() {
            let self = this;
            try {
                await this.bonitaLogin();
                this.bonitaGetProcess();
                this.bonitaStartProcess();
                this.bonitaHumanTask();
                this.bonitaAssignedActor();
                this.bonitaExecuteProcess();
                toastr.success("Solicitud registrada correctamente");
            } catch (error) {
                toastr.error(error);
            }
        },
        async bonitaLogin() {
            let self = this;
            let logininfo = [];
            logininfo.push('username='+self.api.bonita.credentials.username);
            logininfo.push('password='+self.api.bonita.credentials.password);
            logininfo.push('host='+self.api.bonita.url);
            await fetch('/Bonita/Login', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    credentials: self.api.bonita.credentials,
                    host: self.api.bonita.url
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.token = res.data.token;
                    self.api.bonita.cookie = res.data.cookie;

                    console.log("Token "+self.api.bonita.token);
                    console.log("Cookie "+self.api.bonita.cookie);
                } else {
                    throw Error("Error en el Login de Bonita1");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el Login de Bonita2");
            });
        },
        async bonitaGetProcess() {
            let self = this;
            await fetch('/Bonita/GetProcess', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    process: self.api.bonita.process,
                    token: self.api.bonita.token,
                    cookie: self.api.bonita.cookie,
                    host: self.api.bonita.url
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.idProcess = res.data.id;
                    console.log("idProcess "+self.api.bonita.idProcess);
                } else {
                    throw Error("Error en el getProcess de Bonita1");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el getProcess de Bonita2");
            });
        },
        bonitaStartProcess() {

        },
        bonitaHumanTask() {

        },
        bonitaAssignedActor() {

        },
        bonitaExecuteProcess() {

        }
    },
    watch: {
      'request.id_producto_ant'(val) {
        let self = this;
        if (val !== undefined && val !== null && val !== "") {
          self.productosNuevo = self.productos.filter(producto => {
            return parseInt(producto.id) !== parseInt(val);
          });
        } else {
          self.productosNuevo = self.productos;
        }
      }
    }
 });

Vue.component('select2', {
    props: ['options', 'value', 'placeholder'],
    template: '#select2-template',
    mounted: function () {
      var vm = this
      $(this.$el)
        // init select2
        .select2({ 
          allowClear: true,
          data: this.options,
          placeholder: this.placeholder ?? "Seleccione"
        })
        .val(this.value)
        .trigger('change')
        // emit event on change.
        .on('change', function () {
          vm.$emit('input', this.value)
        })
    },
    watch: {
      value: function (value) {
        // update value
        $(this.$el)
            .val(value)
            .trigger('change')
      },
      options: function (options) {
        // update options
        $(this.$el).empty().select2({ data: options })
      }
    },
    destroyed: function () {
      $(this.$el).off().select2('destroy')
    }
});