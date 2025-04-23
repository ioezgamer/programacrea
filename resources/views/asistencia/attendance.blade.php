<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Registro de Asistencia
            </h2>
            <x-boton-regresar onclick="window.location.href='{{ route('participante.index') }}'"></x-boton-regresar>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Formulario para seleccionar programa y semana -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('asistencia.create') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="programa" class="block text-sm font-medium text-gray-700">Programa <span class="text-red-500">*</span></label>
                            <select name="programa" id="programa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                                <option value="">Seleccione un programa...</option>
                                @foreach (['Éxito Académico', 'Desarrollo Juvenil', 'Biblioteca'] as $prog)
                                    <option value="{{ $prog }}" {{ isset($programa) && $programa == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                                @endforeach
                            </select>
                            @if (isset($programa) && $programa)
                                <div class="mt-2 text-sm text-gray-900">
                                    <span class="font-medium">Lugar de Encuentro:</span>
                                    {{ $lugar_encuentro ?? 'No especificado' }}
                                </div>
                            @endif
                            @error('programa')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Semana (Lunes) <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio ?? now()->startOfWeek()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Mensaje de error -->
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-md" role="alert">
                    <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabla de asistencia -->
            @if (isset($programa) && $programa && $participantes->isNotEmpty())
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <form method="POST" action="{{ route('asistencia.store') }}">
                        @csrf
                        <input type="hidden" name="programa" value="{{ $programa }}">
                        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th scope="col" class="px-6 py-3 text-left">Nombres y Apellidos</th>
                                        <th scope="col" class="px-6 py-3 text-left">Género</th>
                                        <th scope="col" class="px-6 py-3 text-left">Grado</th>
                                        <th scope="col" class="px-6 py-3 text-left">Comunidad</th>
                                        <th scope="col" class="px-6 py-3 text-left">Programa</th>
                                        @foreach ($diasSemana as $dia => $fecha)
                                            <th scope="col" class="w-20 px-6 py-3 text-center">
                                                {{ $dia }}<br>
                                                <span class="text-xs">{{ $fecha }}</span>
                                            </th>
                                        @endforeach
                                        <th scope="col" class="w-20 px-6 py-3 text-center">Total Asistido</th>
                                        <th scope="col" class="w-16 px-6 py-3 text-center">%</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($participantes as $participante)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $participante->primer_nombre_p }} {{ $participante->segundo_nombre_p ?? '' }} {{ $participante->primer_apellido_p }} {{ $participante->segundo_apellido_p ?? '' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participante->genero }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participante->grado_p ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participante->comunidad_p ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participante->programa ?? 'N/A' }}</td>
                                            @foreach ($diasSemana as $dia => $fecha)
                                                <td class="w-20 text-center">
                                                    <select name="asistencias[{{ $participante->participante_id }}][{{ $dia }}]" class="w-16 p-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 asistencia-select" data-participante-id="{{ $participante->participante_id }}">
                                                        <option value="Presente" {{ isset($asistencias[$participante->participante_id][$dia]) && $asistencias[$participante->participante_id][$dia] == 'Presente' ? 'selected' : '' }}>P</option>
                                                        <option value="Ausente" {{ isset($asistencias[$participante->participante_id][$dia]) && $asistencias[$participante->participante_id][$dia] == 'Ausente' ? 'selected' : '' }}>A</option>
                                                        <option value="Justificado" {{ isset($asistencias[$participante->participante_id][$dia]) && $asistencias[$participante->participante_id][$dia] == 'Justificado' ? 'selected' : '' }}>J</option>
                                                    </select>
                                                </td>
                                            @endforeach
                                            <td class="w-20 text-center text-sm text-gray-900 total-asistido" data-participante-id="{{ $participante->participante_id }}">
                                                {{ $participante->totalAsistido ?? 0 }}
                                            </td>
                                            <td class="w-16 text-center text-sm text-gray-900 porcentaje-asistencia" data-participante-id="{{ $participante->participante_id }}">
                                                {{ $participante->porcentajeAsistencia ?? 0 }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            <x-boton-guardar>Guardar Asistencia</x-boton-guardar>
                        </div>
                    </form>
                </div>
            @elseif (isset($programa) && $programa)
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <p class="text-sm text-gray-500">No hay participantes inscritos en este programa para registrar asistencia.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript para funcionalidad -->
    <script>
        // Actualizar total asistido y porcentaje de asistencia
        document.querySelectorAll('.asistencia-select').forEach(select => {
            select.addEventListener('change', function () {
                const participanteId = this.dataset.participanteId;
                const selects = document.querySelectorAll(`select[name^="asistencias[${participanteId}]"]`);
                let totalAsistido = 0;

                selects.forEach(sel => {
                    if (sel.value === 'Presente') {
                        totalAsistido++;
                    }
                });

                const totalDias = selects.length;
                const porcentajeAsistencia = totalDias > 0 ? ((totalAsistido / totalDias) * 100).toFixed(0) : 0;

                document.querySelector(`.total-asistido[data-participante-id="${participanteId}"]`).textContent = totalAsistido;
                document.querySelector(`.porcentaje-asistencia[data-participante-id="${participanteId}"]`).textContent = `${porcentajeAsistencia}%`;
            });
        });

        // Inicializar total asistido y porcentaje al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('tr').forEach(row => {
                const participanteId = row.querySelector('.asistencia-select')?.dataset.participanteId;
                if (participanteId) {
                    const selects = document.querySelectorAll(`select[name^="asistencias[${participanteId}]"]`);
                    let totalAsistido = 0;

                    selects.forEach(sel => {
                        if (sel.value === 'Presente') {
                            totalAsistido++;
                        }
                    });

                    const totalDias = selects.length;
                    const porcentajeAsistencia = totalDias > 0 ? ((totalAsistido / totalDias) * 100).toFixed(0) : 0;

                    document.querySelector(`.total-asistido[data-participante-id="${participanteId}"]`).textContent = totalAsistido;
                    document.querySelector(`.porcentaje-asistencia[data-participante-id="${participanteId}"]`).textContent = `${porcentajeAsistencia}%`;
                }
            });
        });
    </script>
</x-app-layout>