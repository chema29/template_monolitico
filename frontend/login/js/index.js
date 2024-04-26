import { module } from './../../js/module.js';

/* Objeto para controlar los campos del form */
const data_login = {
    user: "",
    password: ""
};

const login = {
    init: function () {
        this.addListeners();
    },
    form: {
        user: () => {
            let input = document.getElementById('user');
            return input;
        },
        password: () => {
            let input = document.getElementById('password');
            return input;
        },
    },
    addListeners: function () {
        const form = document.getElementById('form-login');
        form.addEventListener('submit', this.submitForm);
    },
    submitForm: async function (e) {
        e.preventDefault();
        const data = new FormData();
        data.append('user', login.form.user().value);
        data.append('password', login.form.password().value);

        let res = await module.getFetch(module.url + 'login/authentication', data);
        res = JSON.parse(res);
        console.log(res);
        if (res.status) {
            swal("Login!", res.message, "success");
            return true;
        }

        swal("Error!", res.message, "error");
    }
};

login.init();