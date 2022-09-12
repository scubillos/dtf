var request_form = new Vue({
    el: '#request_form',
    data: {
        api: {
            bonita: "",
            crm: "",
            erp: "",
        },
        request: {
            id_factura: null,
            tipo_documento: "",
            num_documento: null,
            id_producto: "",
            email: null,
            observaciones: null,
        },
        documentos: [
            { value: "CC", text: "Cédula de ciudadanía (CC)" },
            { value: "CE", text: "Cédula de extrangería (CE)" },
            { value: "TI", text: "Tarjeta de identidad (TI)" },
        ],
        productos: []    
    },
    created: function() {
        
    },
    methods: {
        
    }
 });