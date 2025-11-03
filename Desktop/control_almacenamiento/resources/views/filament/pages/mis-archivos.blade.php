<x-filament-panels::page>
    <div class="space-y-8">

        <!-- Encabezado -->
        <div class="flex items-center justify-between bg-[#FFCC00]/10 border-l-4 border-[#FFCC00] p-4 rounded-lg shadow-sm">
            <h1 class="text-2xl font-bold text-[#4A4A4A]">Gestión de Archivos</h1>
            <x-heroicon-o-folder class="w-8 h-8 text-[#FFCC00]" />
        </div>

        <!-- Barra de progreso de almacenamiento -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between mb-2">
                <span class="text-sm font-semibold text-[#4A4A4A]">Uso de almacenamiento</span>
                <span class="text-sm font-medium text-[#4A4A4A]">
                    {{ number_format($used / 1024 / 1024, 2) }} MB / {{ number_format($quota / 1024 / 1024, 2) }} MB
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div
                    class="h-3 rounded-full transition-all duration-700
                    @if($percentage < 70)
                        bg-[#43A047]
                    @elseif($percentage < 90)
                        bg-[#FFC107]
                    @else
                        bg-[#E53935]
                    @endif"
                    style="width: {{ $percentage }}%;"
                ></div>
            </div>
        </div>

        <!-- Formulario de Subida -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-[#FFCC00]">
            <h2 class="text-xl font-semibold text-[#4A4A4A] mb-4 flex items-center">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2 text-[#FFCC00]" />
                Subir Nuevo Archivo
            </h2>

            <form wire:submit.prevent="subirArchivo" class="space-y-4">
                {{ $this->form }}

                <div class="pt-4">
                    <x-filament::button
                        type="submit"
                        size="lg"
                        class="bg-[#FFCC00] hover:bg-[#e6b800] text-[#4A4A4A] font-semibold shadow-md hover:shadow-lg transition-all duration-200"
                    >
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2 text-[#4A4A4A]" />
                        Subir Archivo
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Tabla de Archivos -->
        <div class="bg-white rounded-xl shadow-lg border-t-4 border-[#1976D2]">
            <div class="px-6 py-4 border-b border-gray-200 bg-[#1976D2]/10">
                <h2 class="text-lg font-semibold text-[#1976D2]">Mis Archivos Subidos</h2>
            </div>

            <div class="p-6">
                {{ $this->table }}
            </div>
        </div>

        <!-- Información del Usuario -->
        @if(auth()->user()?->hasRole('Usuario'))
            <div class="mt-6 bg-[#1976D2]/10 border border-[#1976D2]/30 rounded-lg p-4 shadow-sm">
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-[#1976D2] mr-3 mt-0.5" />
                    <div>
                        <h3 class="text-sm font-medium text-[#0D47A1]">Resumen de tus archivos</h3>
                        <div class="mt-2 text-sm text-[#0D47A1]/90 space-y-1">
                            <p><strong>Total de archivos:</strong> {{ $totalArchivos ?? 0 }}</p>
                            <p><strong>Espacio utilizado:</strong> {{ number_format($used / 1024 / 1024, 2) }} MB</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
