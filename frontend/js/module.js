/** config para hacer request */
async function getFetch(url, data) {
    var miInit = {
        method: 'POST',
        mode: 'cors',
        cache: 'default',
        body: data
    };
    let _response = await fetch(url, miInit);

    let _res = await _response.text();
    return _res;
}
async function renderFetch(url) {
    let _response = await fetch(url);

    let _res = await _response.text();
    return _res;
}

function zeroFill(number, width) {
    width -= number.toString().length;
    if (width > 0) {
        return new Array(width + (/\./.test(number) ? 2 : 1)).join('0') + number;
    }
    return number + ''; // always return a string
}

function dateFormat(fecha) {
    let months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    let d = new Date(fecha);
    let fechatransformada = d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
    return fechatransformada;
}

function avatarNull(nombre) {
    const color = "#" + Math.random().toString(16).slice(-3);
    const letra = nombre.charAt(0);
    return `<div style="
    width: 24px;
    height: 24px;
    padding: 3px;
    border-radius: 5px;
    background-color: ${color};
    font-size: small;
    color: white;
    text-align: center;
    border: 1px solid  ${color};
    float: left;
    margin-right: 5px;
    font-weight: 600;
    ">
        ${letra}
    </div>`;

}

function selectInput(idInput, value = 0) {
    const select = document.querySelector(`#${idInput}`);
    const options = Array.from(select.options);
    const selectedOption = options.find((item) => item.value === value);

    if (selectedOption) {
        selectedOption.selected = true;
    }
}
function loading() {
    document.getElementById("body").classList.remove('no-loader');
    setTimeout(function () {
        document.getElementById("body").classList.add('no-loader');
    }, 1000);
}
/**
 * 
 * @param {*} title 
 * @param {*} body 
 * @param {*} footer 
 * @param {string} size sm xl
 */
function modalDinamico(title, body, footer, size = "sm", modalStatic = false) {

    configurarModal(title, body, footer, size, modalStatic);

}

function configurarModal(title, body, footer, size, modalStatic) {
    $('#themes-info').modal('dispose'); // Reiniciar configuraciones del modal si no es estático

    if (modalStatic) {
        $('#themes-info').modal({
            backdrop: 'static',
            keyboard: false
        });
    }

    document.getElementById("modal-title").innerHTML = title;
    document.getElementById("modal-body").innerHTML = body;
    document.getElementById("modal-footer").innerHTML = footer || "";

    // Configurar tamaño
    let modalClass = "modal-dialog modal-fullscreen-sm-down modal-dialog-centered";
    if (size === "xl") {
        modalClass += " modal-xl";
    } else if (size === "lg") {
        modalClass += " modal-lg";
    }
    document.getElementById("modal-dialog").className = modalClass;

    $('#themes-info').modal('show');
}

function offcanvasDinamico(title, body, position, footer = "") {

    let Offcanvas = document.getElementById('offcanvas');
    // cambiar la posicion del offcanvas
    Offcanvas.className = `offcanvas offcanvas-${position}`;
    document.getElementById("offcanvas-title").innerHTML = title;
    document.getElementById("offcanvas-body").innerHTML = body;
    document.getElementById("offcanvas-footer").innerHTML = footer;
    let bsOffcanvas = new bootstrap.Offcanvas(Offcanvas);
    // backdrop

    bsOffcanvas.show();
    return bsOffcanvas;
}

function enforce_maxlength(event) {
    var t = event.target;
    if (t.hasAttribute('maxlength')) {
        t.value = t.value.slice(0, t.getAttribute('maxlength'));
    }
}

async function paises() {
    let res = await renderFetch('https://restcountries.com/v2/lang/es')
    res = JSON.parse(res);
    return res;
}

let monedaFormat = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });

export const module = {
    paises,
    renderFetch,
    getFetch,
    dateFormat,
    zeroFill,
    monedaFormat,
    alert: function (status, message, info = false) {
        toastr.options = {
            progressBar: true,
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            timeOut: 8000
        };
        if (status) {
            if (info) {
                toastr.info(message);
            } else {
                toastr.success(message);
            }
        } else {
            toastr.error(message);
        }

    },
    avatarNull,
    selectInput,
    loading,
    modalDinamico,
    enforce_maxlength,
    url: "https://dev.publicity.e-pagos.services/",
    configDataTable: function () {
        return {
            dom: '<"row"<"col-6"B><"col-6"f>>rtip',
            language: {
                lengthMenu: 'Mostrar _MENU_ registros',
                zeroRecords: 'Datos no encontrados',
                info: 'Pagina _PAGE_ a _PAGES_ Total de registros _TOTAL_ ',
                infoEmpty: 'No se encontraron registros',
                emptyTable: 'No se encontraron registros',
                infoFiltered: '(flitrado de _MAX_ registros totales)',
                search: "Buscar",
                paginate: {
                    first: "primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "ultimo"
                }
            },
            select: true,
            buttons: [
                'excel',
            ]
        }
    },
    offcanvasDinamico
};
