<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Participante;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function create(Request $request)
    {
        // Inicializar $programa y $fechaInicio con valores por defecto
        $programa = $request->input('programa', '');
        $fechaInicio = $request->input('fecha_inicio', now()->startOfWeek()->format('Y-m-d'));

        // Validar que la fecha de inicio sea un lunes
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        if ($fechaInicioCarbon->dayOfWeek !== Carbon::MONDAY) {
            return redirect()->route('asistencia.create')
                ->withErrors(['fecha_inicio' => 'La fecha de inicio debe ser un lunes.'])
                ->withInput();
        }

        // Obtener participantes filtrados por programa
        $query = Participante::query();
        if ($programa) {
            $query->where('programa', 'LIKE', '%' . $programa . '%');
        }
        $participantes = $query->get();

        // Generar días de la semana (lunes a viernes)
        $diasSemana = [];
        for ($i = 0; $i < 5; $i++) {
            $fecha = $fechaInicioCarbon->copy()->addDays($i);
            $diasSemana[$fecha->translatedFormat('l')] = $fecha->format('Y-m-d');
        }

        // Obtener asistencias existentes para la semana seleccionada
        $asistencias = [];
        foreach ($participantes as $participante) {
            $asistenciasParticipante = Asistencia::where('participante_id', $participante->participante_id)
                ->whereBetween('fecha_asistencia', [$fechaInicio, $fechaInicioCarbon->copy()->addDays(4)])
                ->get()
                ->keyBy('fecha_asistencia');

            foreach ($diasSemana as $dia => $fecha) {
                $estado = $asistenciasParticipante->get($fecha)?->presente;
                $asistencias[$participante->participante_id][$dia] = match ($estado) {
                    1 => 'Presente',
                    0 => 'Ausente',
                    null => 'Justificado',
                    default => 'Ausente',
                };
            }

            // Calcular total asistido y porcentaje
            $totalAsistido = Asistencia::where('participante_id', $participante->participante_id)
                ->whereBetween('fecha_asistencia', [$fechaInicio, $fechaInicioCarbon->copy()->addDays(4)])
                ->where('presente', 1)
                ->count();
            $participante->totalAsistido = $totalAsistido;
            $participante->porcentajeAsistencia = count($diasSemana) > 0 ? ($totalAsistido / count($diasSemana)) * 100 : 0;
        }

        // Obtener lugar de encuentro
        $lugar_encuentro = $programa ? Participante::where('programa', 'LIKE', '%' . $programa . '%')->first()?->lugar_de_encuentro_del_programa : null;

        // Pasar todas las variables a la vista
        return view('asistencia.attendance', compact('participantes', 'programa', 'fechaInicio', 'diasSemana', 'asistencias', 'lugar_encuentro'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'programa' => 'required|string',
            'fecha_inicio' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*' => 'array',
            'asistencias.*.*' => 'in:Presente,Ausente,Justificado',
        ]);

        $programa = $request->input('programa');
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'));
        $asistencias = $request->input('asistencias');

        // Generar días de la semana
        $diasSemana = [];
        for ($i = 0; $i < 5; $i++) {
            $fecha = $fechaInicio->copy()->addDays($i);
            $diasSemana[$fecha->translatedFormat('l')] = $fecha->format('Y-m-d');
        }

        DB::beginTransaction();
        try {
            // Eliminar asistencias previas para la semana seleccionada
            Asistencia::whereIn('participante_id', array_keys($asistencias))
                ->whereBetween('fecha_asistencia', [$fechaInicio, $fechaInicio->copy()->addDays(4)])
                ->delete();

            // Guardar nuevas asistencias
            foreach ($asistencias as $participanteId => $dias) {
                foreach ($dias as $dia => $estado) {
                    $fecha = $diasSemana[$dia];
                    $presente = match ($estado) {
                        'Presente' => 1,
                        'Ausente' => 0,
                        'Justificado' => null,
                    };
                    Asistencia::create([
                        'participante_id' => $participanteId,
                        'fecha_asistencia' => $fecha,
                        'presente' => $presente,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('asistencia.create', ['programa' => $programa, 'fecha_inicio' => $fechaInicio->format('Y-m-d')])
                ->with('success', 'Asistencia registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al registrar la asistencia: ' . $e->getMessage()])->withInput();
        }
    }
}