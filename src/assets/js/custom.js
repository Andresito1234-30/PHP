// assets/js/custom.js


// Funciones globales personalizadas

class AdminApp {

    constructor() {

        this.init();

    }

    

    init() {

        this.setupEventListeners();

        this.setupAjaxDefaults();

    }

    

    setupEventListeners() {

        // Confirmación para acciones destructivas

        $(document).on('click', '.btn-delete', function(e) {

            if (!confirm('¿Estás seguro de que deseas eliminar este registro?')) {

                e.preventDefault();

            }

        });

        

        // Auto-hide alerts después de 5 segundos

        $('.alert').delay(5000).fadeOut(300);

    }

    

    setupAjaxDefaults() {

        $.ajaxSetup({

            headers: {

                'X-Requested-With': 'XMLHttpRequest'

            }

        });

    }

    

    // Función para mostrar notificaciones

    showNotification(type, message, title = '') {

        toastr[type](message, title);

    }

    

    // Función para cargar contenido via AJAX

    loadContent(url, container) {

        $.get(url, function(data) {

            $(container).html(data);

        }).fail(function() {

            this.showNotification('error', 'Error al cargar el contenido');

        });

    }

}


// Inicializar la aplicación cuando el documento esté listo

$(document).ready(function() {

    window.adminApp = new AdminApp();

});