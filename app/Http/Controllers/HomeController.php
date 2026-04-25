<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user permissions.
     *
     * @return RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Si el usuario puede ver resoluciones, redirigir a resoluciones
        if ($user->can('modulo resoluciones') && ! $user->hasRole('ADMINISTRADOR')) {
            return redirect()->route('resolucions.index');
        }

        // Si el usuario puede ver cargos, redirigir a cargos
        if ($user->can('modulo cargos') || $user->hasRole('ADMINISTRADOR')) {
            return redirect()->route('charges.index');
        }

        // Por defecto, si tiene algun otro acceso, intentar modulos especificos
        if ($user->can('natural people index')) {
            return redirect()->route('natural-people.index');
        }

        // Si no tiene ningun permiso especifico, mostrar una pagina basica o cerrar sesion si es necesario
        // Por ahora, redirigimos a cargos como fallback si esta autenticado
        return redirect()->route('charges.index');
    }
}
