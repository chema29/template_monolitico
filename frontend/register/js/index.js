import { module } from './../../js/module.js';

/* Objeto para controlar los campos del form */
const data_register = {
    nombre:"",
    correo:"",
    password:"",
    password2:"",
    usuario:""
};

const register = {
    init: function(){
        this.addListeners();
    },
    form:{
        nombre: () =>{ 
            let input = document.getElementById('name');
            return input;
        },
        correo: () =>{ 
            let input = document.getElementById('email');
            return input;
        },
        password: () =>{ 
            let input = document.getElementById('password');
            return input;
        },
    },
    addListeners: function(){
        const form = document.getElementById('form-register');
        form.addEventListener('submit', this.submitForm);
    },
    submitForm: async function(e){
        e.preventDefault();
        const data = new FormData();
        data.append('nombre', register.form.nombre().value);
        data.append('user', register.form.correo().value);
        data.append('password', register.form.password().value);
        
        let res = await module.getFetch(module.url+'login/register', data);
        res = JSON.parse(res);
        if(res.status){
            swal("Registro!", res.message, "success");
            return true;
        }

        swal("Error!", res.message, "error");
    }
};

register.init();
