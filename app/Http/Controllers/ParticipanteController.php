<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use Illuminate\Http\Request;

class ParticipanteController extends Controller
{
    public function index()
    {
        $participantes = Participante::all();
        return view('participante.index', compact('participantes'));
    }

    public function create()
    {
        return view('participante.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_de_inscripcion' => 'required|date',
            'ano_de_inscripcion' => 'required|integer|min:1900|max:9999',
            'participante' => 'required|in:primaria,secundaria',
            'partida_de_nacimiento' => 'required|boolean',
            'boletin_o_diploma_2024' => 'required|boolean',
            'cedula_tutor' => 'required|boolean',
            'cedula_participante_adulto' => 'required|boolean',
            'programas' => 'required|array|min:1',
            'programas.*' => 'in:RAC,RACREA,CLC,CLCREA,DJ,BM,CLM',
            'lugar_de_encuentro_del_programa' => 'required|string',
            'primer_nombre_p' => 'required|string|max:255',
            'segundo_nombre_p' => 'nullable|string|max:255',
            'primer_apellido_p' => 'required|string|max:255',
            'segundo_apellido_p' => 'nullable|string|max:255',
            'ciudad_p' => 'required|string|max:255',
            'departamento_p' => 'required|string|max:255',
            'fecha_de_nacimiento_p' => 'required|date',
            'edad_p' => 'required|integer|min:0',
            'cedula_participante_adulto_str' => 'nullable|string|max:255',
            'genero' => 'required|string|max:255',
            'comunidad_p' => 'required|string|max:255',
            'escuela_p' => 'required|string|max:255',
            'comunidad_escuela' => 'required|string|max:255',
            'grado_p' => 'required|string|max:255',
            'turno' => 'required|string|max:255',
            'repite_grado' => 'required|boolean',
            'dias_de_asistencia_al_programa' => 'required|array|min:1',
            'dias_de_asistencia_al_programa.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes',
            'programa' => 'required|array|min:1',
            'programa.*' => 'in:Éxito Académico,Desarrollo Juvenil,Biblioteca',
            'tutor_principal' => 'required|string|max:255',
            'nombres_y_apellidos_tutor_principal' => 'required|string|max:255',
            'numero_de_cedula_tutor' => 'required|string|max:255',
            'comunidad_tutor' => 'required|string|max:255',
            'direccion_tutor' => 'required|string|max:255',
            'telefono_tutor' => 'required|string|max:255',
            'sector_economico_tutor' => 'required|string|max:255',
            'nivel_de_educacion_formal_adquirido_tutor' => 'required|string|max:255',
            'expectativas_del_programa_tutor_principal' => 'required|string',
            'tutor_secundario' => 'nullable|string|max:255',
            'nombres_y_apellidos_tutor_secundario' => 'nullable|string|max:255',
            'numero_de_cedula_tutor_secundario' => 'nullable|string|max:255',
            'comunidad_tutor_secundario' => 'nullable|string|max:255',
            'telefono_tutor_secundario' => 'nullable|string|max:255',
            'asiste_a_otros_programas' => 'nullable|required|boolean',
            'otros_programas' => 'nullable|string',
            'dias_asiste_a_otros_programas' => 'nullable|integer|min:0',
        ]);
    
        // Convertir el array a string antes de guardar
        $validated['dias_de_asistencia_al_programa'] = implode(',', $validated['dias_de_asistencia_al_programa']);
        $validated['programas'] = implode(',', $request->input('programas', []));
        $validated['programa'] = implode(',', $validated['programa']);


        Participante::create($validated);
    
        return redirect()->back()->with('success', 'Participante registrado correctamente.');
    }
    

    public function show($id)
    {
        $participante = Participante::findOrFail($id);
        return view('participante.show', compact('participante'));
    }

    public function edit(Participante $participante)
    {
        return view('participante.edit', compact('participante'));
    }

    public function update(Request $request, Participante $participante)
{
    $validated = $request->validate([
        'fecha_de_inscripcion' => 'required|date',
        'ano_de_inscripcion' => 'required|integer|min:1900|max:9999',
        'participante' => 'required|in:primaria,secundaria',
        'partida_de_nacimiento' => 'required|boolean',
        'boletin_o_diploma_2024' => 'required|boolean',
        'cedula_tutor' => 'required|boolean',
        'cedula_participante_adulto' => 'required|boolean',
        'programa' => 'required|array|min:1',
        'programa.*' => 'in:Éxito Académico,Desarrollo Juvenil,Biblioteca',
        'programas' => 'required|array|min:1',
        'programas.*' => 'in:RAC,RACREA,CLC,CLCREA,DJ,BM,CLM',
        'lugar_de_encuentro_del_programa' => 'required|string',
        'primer_nombre_p' => 'required|string|max:255',
        'segundo_nombre_p' => 'nullable|string|max:255',
        'primer_apellido_p' => 'required|string|max:255',
        'segundo_apellido_p' => 'nullable|string|max:255',
        'ciudad_p' => 'nullable|string|max:255',
        'departamento_p' => 'nullable|string|max:255',
        'fecha_de_nacimiento_p' => 'required|date',
        'edad_p' => 'required|integer|min:0',
        'cedula_participante_adulto_str' => 'nullable|string|max:255',
        'genero' => 'required|string|max:255',
        'comunidad_p' => 'required|string|max:255',
        'escuela_p' => 'required|string|max:255',
        'comunidad_escuela' => 'required|string|max:255',
        'grado_p' => 'required|string|max:255',
        'turno' => 'nullable|string|max:255',
        'repite_grado' => 'nullable|boolean',
        'dias_de_asistencia_al_programa' => 'required|array|min:1',
        'dias_de_asistencia_al_programa.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes',
        'tutor_principal' => 'required|string|max:255',
        'nombres_y_apellidos_tutor_principal' => 'required|string|max:255',
        'numero_de_cedula_tutor' => 'required|string|max:255',
        'comunidad_tutor' => 'required|string|max:255',
        'direccion_tutor' => 'required|string|max:255',
        'telefono_tutor' => 'required|string|max:255',
        'sector_economico_tutor' => 'required|string|max:255',
        'nivel_de_educacion_formal_adquirido_tutor' => 'required|string|max:255',
        'expectativas_del_programa_tutor_principal' => 'required|string',
        'tutor_secundario' => 'nullable|string|max:255',
        'nombres_y_apellidos_tutor_secundario' => 'nullable|string|max:255',
        'numero_de_cedula_tutor_secundario' => 'nullable|string|max:255',
        'comunidad_tutor_secundario' => 'nullable|string|max:255',
        'telefono_tutor_secundario' => 'nullable|string|max:255',
        'asiste_a_otros_programas' => 'nullable|boolean',
        'otros_programas' => 'nullable|string',
        'dias_asiste_a_otros_programas' => 'nullable|integer|min:0',
    ]);

    // Convertir arrays a strings
    $validated['dias_de_asistencia_al_programa'] = implode(',', $request->input('dias_de_asistencia_al_programa', []));
    $validated['programas'] = implode(',', $request->input('programas', []));
    $validated['programa'] = implode(',', $request->input('programa', []));

    $participante->update($validated);

    return redirect()->route('participante.index')->with('success', 'Participante actualizado correctamente.');
}


    public function destroy(Participante $participante)
    {
        $participante->delete();
        return redirect()->route('participante.index')->with('success', 'Participant deleted successfully.');
    }
}