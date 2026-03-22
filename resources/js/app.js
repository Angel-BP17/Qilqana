import "bootstrap";
import "./bootstrap";
import * as bootstrap from "bootstrap";
import SignaturePad from "signature_pad";

// Importar estilos de iconos (Vite resolverá las fuentes correctamente aquí)
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

// Hacer librerías disponibles globalmente
window.bootstrap = bootstrap;
window.SignaturePad = SignaturePad;
