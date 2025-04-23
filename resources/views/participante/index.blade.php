<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-900">
                Listado de Participantes
            </h2>
            <x-crear-button onclick="window.location.href='{{ route('participante.create') }}'"></x-crear-button>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    @if($participantes->isEmpty())
                        <div class="text-center text-gray-600 bg-gray-50 p-4 rounded-lg">
                            No hay participantes registrados.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Inscripción</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Programa</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($participantes as $participante)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">{{ $participante->participante_id ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                                {{ $participante->primer_nombre_p ?? 'N/A' }} {{ $participante->primer_apellido_p ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                                {{ \Carbon\Carbon::parse($participante->fecha_de_inscripcion)->translatedFormat('d/m/Y') ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                                {{ $participante->programa ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                                <a href="{{ route('participante.show', $participante) }}"
                                                   class="text-blue-600 hover:text-blue-800 mr-3">Ver</a>
                                                <a href="{{ route('participante.edit', $participante) }}"
                                                   class="text-yellow-600 hover:text-yellow-800 mr-3">Editar</a>
                                                <form action="{{ route('participante.destroy', $participante) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800"
                                                            onclick="return confirm('¿Estás seguro de eliminar este participante?')">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>