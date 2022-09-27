var request_form = new Vue({
    el: '#request_form',
    data: {
        env: 'prod',
        api: {
            bonita: {
                url: "http://3.94.97.60:8080/bonita/",
                credentials: {
                    username: 'graphic.interface',
                    password: 'bpm'
                },
                process: 'Customer',
                token: null,
                cookie: null,
                idProcess: null,
                deployedBy: null,
                idCase: null,
                humanTaskId: null,
                assignedActor: false,
                executeProcess: false,
            },
            crm: {
                url: "https://b24-93ms6r.bitrix24.co/rest/1/55rujkbk0u1c3lee/",
            },
            erp: {
                url: "https://api.alegra.com/api/",
                token: "Y2Jhc3FibGw5NUBnbWFpbC5jb206MTViM2Y4OTI4OWQxZTUzMWRjNmQ=",
            }
        },
        request: {
            id_factura: null,
            tipo_documento: "",
            num_documento: null,
            id_producto_ant: "",
            categoria_producto_ant: "",
            valor_producto_ant: "",
            id_producto_nuevo: "",
            categoria_producto_nuevo: "",
            valor_producto_nuevo: "",
            email: null,
            motivo: "",
            observaciones: null,
            ciudad: null,
        },
        documentos: [
            { id: "CC", text: "Cédula de ciudadanía (CC)" },
            { id: "CE", text: "Cédula de extrangería (CE)" },
            { id: "TI", text: "Tarjeta de identidad (TI)" },
        ],
        motivos: [
            { id: "Me quedo grande", text: "Me quedó grande" },
            { id: "Me quedo pequeño", text: "Me quedó pequeño" },
            { id: "No era lo que esperaba", text: "No era lo que esperaba" },
            { id: "No me gusto", text: "No me gustó" },
            { id: "Llego con defecto", text: "Llegó con defecto" },
        ],
        ciudades: [
            { id: "Bogotá", text: "Bogotá" },
            { id: "Medellín", text: "Medellín" },
            { id: "Girardot", text: "Girardot" },
            { id: "Cali", text: "Cali" },
            { id: "Tunja", text: "Tunja" },
            { id: "Chía", text: "Chía" },
            { id: "Madrid", text: "Madrid" },
            { id: "Mosquera", text: "Mosquera" },
        ],
        dataProductos: [],
        productos: [],
        productosNuevo: [],
        factura: null,
        errors: {
            factura: false
        }
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
                self.dataProductos = productos;
                for (let i = 0; i < productos.length; i++ ) {
                    const producto = productos[i];
                    self.productos.push({ id: producto.id, text: producto.name });
                }
            });
        },
        getFactura() {
            let self = this;
            let id_factura = self.request.id_factura;
            if (id_factura !== "" && id_factura !== null) {
                fetch(self.api.erp.url + 'v1/invoices/' + id_factura, {
                    method: 'GET',
                    cache: 'no-cache',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Basic ' + self.api.erp.token,
                    },
                }).then(async (response) => {
                    self.errors.factura = false;
                    self.factura = await response.json();
                    self.request.id_producto_ant = self.factura.items[0].id;
                    self.request.valor_producto_ant = parseFloat(self.factura.items[0].price);
                }).catch(error => {
                    self.factura = null;
                    self.errors.factura = true;
                });
            }
        },
        async registerRequest() {
            let self = this;
            try {
                await self.validateEmail();
                await self.validateValues();
                await self.bonitaCall();
                toastr.success("Solicitud registrada correctamente");
            } catch (error) {
                toastr.error(error);
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
                    // TODO: Validar que la direccion email buscada y encontrada sean las mismas
                    valid = true;
                }
            });
            
            if (!valid) {
                throw Error("La dirección de correo no corresponde a ningún cliente de DTF");
            }
        },
        async validateValues() {
            // Validar id de factura
            if (this.factura === null) {
                throw Error("Factura no válida");
            }
            if (this.request.tipo_documento === "") {
                throw Error("El Tipo de Documento es obligatorio");
            }
            if (this.request.num_documento === null) {
                throw Error("El Producto Anterior es obligatorio");
            }
            if (this.request.id_producto_ant === "") {
                throw Error("El Producto Anterior es obligatorio");
            }
            if (this.request.id_producto_nuevo === "") {
                throw Error("El Producto Nuevo es obligatorio");
            }
            if (this.request.email === null) {
                throw Error("El Email es obligatorio");
            }
        },
        async bonitaCall() {
            let self = this;
            try {
                await this.bonitaLogin();
                await this.bonitaGetProcess();
                await this.bonitaStartProcess();
                await this.bonitaHumanTask();
                await this.bonitaAssignActor();
                await this.bonitaExecuteProcess();
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
            await fetch('/index.php/Bonita/Login', {
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
                    throw Error("Respuesta incorrecta en el Login de Bonita");
                }
            }).catch(error => {
                throw Error("Error en el Login de Bonita");
            });
        },
        async bonitaGetProcess() {
            let self = this;
            await fetch('/index.php/Bonita/GetProcess', {
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
                    self.api.bonita.deployedBy = res.data.deployedBy;
                    console.log("deployedBy "+self.api.bonita.deployedBy);
                } else {
                    throw Error("Respuesta incorrecta en getProcess de Bonita");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el getProcess de Bonita");
            });
        },
        async bonitaStartProcess() {
            let self = this;
            await fetch('/index.php/Bonita/StartProcess/'+self.api.bonita.idProcess, {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: self.api.bonita.token,
                    cookie: self.api.bonita.cookie,
                    host: self.api.bonita.url
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.idCase = res.data.caseId;
                    console.log("idCase "+self.api.bonita.idCase);
                } else {
                    throw Error("Respuesta incorrecta en startProcess de Bonita");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el startProcess de Bonita");
            });
        },
        async bonitaHumanTask() {
            let self = this;
            await fetch('/index.php/Bonita/HumanTask/'+self.api.bonita.deployedBy, {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: self.api.bonita.token,
                    cookie: self.api.bonita.cookie,
                    host: self.api.bonita.url
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.humanTaskId = res.data.id;
                    console.log("humanTaskId "+self.api.bonita.humanTaskId);
                } else {
                    throw Error("Respuesta incorrecta en humanTask de Bonita");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el humanTask de Bonita");
            });
        },
        async bonitaAssignActor() {
            let self = this;
            await fetch('/index.php/Bonita/AssignActor', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: self.api.bonita.token,
                    cookie: self.api.bonita.cookie,
                    host: self.api.bonita.url,
                    deployedBy: self.api.bonita.deployedBy,
                    humanTaskId: self.api.bonita.humanTaskId,
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.assignedActor = res.data.success;
                    console.log("assignedActor "+self.api.bonita.assignedActor);
                } else {
                    throw Error("Respuesta incorrecta en assignedActor de Bonita");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el assignedActor de Bonita");
            });
        },
        async bonitaExecuteProcess() {
            let self = this;
            await fetch('/index.php/Bonita/ExecuteProcess', {
                method: 'POST',
                cache: 'no-cache',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    token: self.api.bonita.token,
                    cookie: self.api.bonita.cookie,
                    host: self.api.bonita.url,
                    humanTaskId: self.api.bonita.humanTaskId,
                    request: self.request,
                }),
            }).then(async (response) => {
                if (response.status == 200) {
                    let res = await response.json();
                    self.api.bonita.executeProcess = res.data.success;
                    console.log("executeProcess "+self.api.bonita.executeProcess);
                } else {
                    throw Error("Respuesta incorrecta en executeProcess de Bonita");
                }
            }).catch(error => {
                console.error(error);
                throw Error("Error en el executeProcess de Bonita");
            });
        },
        buscarProducto(id_producto) {
            let self = this;
            let filtro = self.dataProductos.filter(producto => producto.id == id_producto);

            if (Array.isArray(filtro) && filtro.length > 0) {
                return filtro[0];
            }
        },
        logrequest() {
            console.log("Request",this.request);
        }
    },
    watch: {
        'request.id_producto_ant'(val) {
            console.log("entraaa");
            let self = this;
            if (val !== undefined && val !== null && val !== "") {
                let producto = self.buscarProducto(val);
                self.request.categoria_producto_ant = producto.itemCategory.name;

                self.productosNuevo = self.productos.filter(producto => {
                    return parseInt(producto.id) !== parseInt(val);
                });
            } else {
                self.productosNuevo = self.productos;
                self.request.categoria_producto_ant = "";
            }
        },
        'request.id_producto_nuevo'(val) {
            let self = this;
            if (val !== undefined && val !== null && val !== "") {
                let producto = self.buscarProducto(val);
                self.request.categoria_producto_nuevo = producto.itemCategory.name;
                self.request.valor_producto_nuevo = parseFloat(producto.price[0].price);
            } else {
                self.request.categoria_producto_nuevo = "";
            }
        },
    }
 });

Vue.component('select2', {
    props: ['options', 'value', 'placeholder', 'readonly'],
    template: '#select2-template',
    mounted: function () {
        var vm = this
        $(this.$el)
        // init select2
        .select2({ 
            allowClear: true,
            data: this.options,
            placeholder: this.placeholder ?? "Seleccione",
        })
        .val(this.value)
        .trigger('change')
        // emit event on change.
        .on('change', function () {
            vm.$emit('input', this.value)
        })
        if (this.readonly) {
            console.log("with readonly");
            $(this.$el).prop("disabled", true);
        }
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