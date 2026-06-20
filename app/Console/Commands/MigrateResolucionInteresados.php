<?php

namespace App\Console\Commands;

use App\Models\LegalEntity;
use App\Models\NaturalPerson;
use App\Models\Resolucion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateResolucionInteresados extends Command
{
    protected $signature = 'app:migrate-resolucion-interesados';

    protected $description = 'Migra los interesados de las resoluciones de los campos de texto a la tabla pivot polimórfica.';

    public function handle()
    {
        $resoluciones = Resolucion::all();
        $count = 0;

        $this->info('Iniciando migración de '.$resoluciones->count().' resoluciones...');

        foreach ($resoluciones as $res) {
            DB::transaction(function () use ($res, &$count) {
                $attached = false;

                // 1. Intentar por RUC (Persona Jurídica)
                if (! empty($res->ruc)) {
                    $rucs = explode(',', $res->ruc);
                    foreach ($rucs as $ruc) {
                        $entity = LegalEntity::where('ruc', trim($ruc))->first();
                        if ($entity) {
                            $res->legalEntities()->syncWithoutDetaching([$entity->id]);
                            $attached = true;
                        }
                    }
                }

                // 2. Intentar por DNI (Persona Natural o Trabajador)
                if (! empty($res->dni)) {
                    $dnis = explode(',', $res->dni);
                    foreach ($dnis as $dni) {
                        $dni = trim($dni);

                        // Prioridad a Usuario (Trabajador UGEL)
                        $user = User::where('dni', $dni)->first();
                        if ($user) {
                            $res->users()->syncWithoutDetaching([$user->id]);
                            $attached = true;
                        } else {
                            // Si no es usuario, buscar en NaturalPerson
                            $person = NaturalPerson::where('dni', $dni)->first();
                            if ($person) {
                                $res->naturalPeople()->syncWithoutDetaching([$person->id]);
                                $attached = true;
                            }
                        }
                    }
                }

                // 3. Intentar por Cédula
                if (! empty($res->cedula)) {
                    $cedulas = explode(',', $res->cedula);
                    foreach ($cedulas as $cedula) {
                        $person = NaturalPerson::where('cedula', trim($cedula))->first();
                        if ($person) {
                            $res->naturalPeople()->syncWithoutDetaching([$person->id]);
                            $attached = true;
                        }
                    }
                }

                if ($attached) {
                    $count++;
                    // Sincronizar datos por si acaso (limpia duplicados o espacios)
                    $res->syncInteresadosData();
                }
            });
        }

        $this->info("Migración completada. Se vincularon $count resoluciones.");
    }
}
