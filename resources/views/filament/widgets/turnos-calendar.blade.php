@php
    $plugin = \Saade\FilamentFullCalendar\FilamentFullCalendarPlugin::get();
@endphp

<x-filament-widgets::widget>
    <x-filament::section class="!p-0 border-none shadow-none">
        
        <!-- Custom Google Calendar Toolbar -->
        <div x-data="{
                viewName: 'timeGridWeek',
                title: '...',
                init() {
                    // Esperamos a que el calendario se monte para enlazar los eventos y títulos
                    window.addEventListener('DOMContentLoaded', () => {
                        let calendarEl = document.querySelector('.filament-fullcalendar');
                        if(calendarEl) {
                            // En Fullcalendar, la instancia puede obtenerse del DOM
                            setTimeout(() => {
                                // Fullcalendar expone la API de varias formas
                                // Intentaremos leer el titulo actualizable si es posible
                            }, 500);
                        }
                    });
                },
                next() {
                    document.querySelector('.fc-next-button')?.click();
                },
                prev() {
                    document.querySelector('.fc-prev-button')?.click();
                },
                today() {
                    document.querySelector('.fc-today-button')?.click();
                },
                changeView(view) {
                    this.viewName = view;
                    if(view === 'timeGridDay') document.querySelector('.fc-timeGridDay-button')?.click();
                    if(view === 'timeGridWeek') document.querySelector('.fc-timeGridWeek-button')?.click();
                    if(view === 'dayGridMonth') document.querySelector('.fc-dayGridMonth-button')?.click();
                }
            }" 
            class="flex items-center justify-between px-4 py-2 bg-white border-b border-gray-200">
            
            <!-- Izquierda: Menú hamburguesa, Logo, Título y Navegación -->
            <div class="flex items-center gap-4">
                <!-- Hamburger menu -->
                <button type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
                
                <!-- Logo & Title -->
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                        B
                    </div>
                    <span class="text-xl text-gray-800 font-medium tracking-tight">Turnos</span>
                </div>
                
                <!-- Navegación "Hoy" y flechas -->
                <div class="flex items-center gap-2 ml-4">
                    <button @click="today()" type="button" class="px-4 py-1.5 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Hoy
                    </button>
                    <button @click="prev()" type="button" class="p-1.5 rounded-full hover:bg-gray-100 text-gray-600 transition-colors" title="Semana anterior">
                        <x-heroicon-o-chevron-left class="w-5 h-5" />
                    </button>
                    <button @click="next()" type="button" class="p-1.5 rounded-full hover:bg-gray-100 text-gray-600 transition-colors" title="Semana siguiente">
                        <x-heroicon-o-chevron-right class="w-5 h-5" />
                    </button>
                    
                    <!-- Title del calendario, se actualizará dinámicamente -->
                    <span class="ml-2 text-lg text-gray-800 font-medium" x-text="title"></span>
                </div>
            </div>

            <!-- Centro: Buscador -->
            <div class="flex-1 max-w-xl px-8 hidden md:block">
                <div class="relative bg-gray-100 rounded-lg flex items-center px-4 py-2 w-full focus-within:bg-white focus-within:shadow-md focus-within:border-transparent transition-all">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-500" />
                    <input type="text" placeholder="Buscar turnos, clientes..." class="bg-transparent border-none focus:ring-0 w-full text-gray-700 placeholder-gray-500 text-base" />
                </div>
            </div>

            <!-- Derecha: Opciones y Perfil -->
            <div class="flex items-center gap-2">
                <!-- Dropdown Vistas -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2 px-3 py-1.5 rounded border border-transparent hover:bg-gray-100 text-sm font-medium text-gray-700 transition-colors">
                        <span x-text="viewName === 'timeGridDay' ? 'Día' : (viewName === 'dayGridMonth' ? 'Mes' : 'Semana')">Semana</span>
                        <x-heroicon-m-chevron-down class="w-4 h-4" />
                    </button>
                    <div x-show="open" x-transition class="absolute right-0 mt-1 w-32 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50">
                        <button @click="changeView('timeGridDay'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Día</button>
                        <button @click="changeView('timeGridWeek'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Semana</button>
                        <button @click="changeView('dayGridMonth'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes</button>
                    </div>
                </div>

                <!-- Iconos herramientas -->
                <button type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors" title="Ayuda">
                    <x-heroicon-o-question-mark-circle class="w-6 h-6" />
                </button>
                <button type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 transition-colors" title="Configuración">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6" />
                </button>
                
                <!-- Perfil simulado -->
                <button type="button" class="ml-2 w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">
                    A
                </button>
            </div>
        </div>

        <div class="flex justify-end flex-1 mb-4" style="display:none;">
            <x-filament-actions::actions :actions="$this->getCachedHeaderActions()" class="shrink-0" />
        </div>

        <div class="filament-fullcalendar p-4" wire:ignore x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-fullcalendar-alpine', 'saade/filament-fullcalendar') }}"
            ax-load-css="{{ \Filament\Support\Facades\FilamentAsset::getStyleHref('filament-fullcalendar-styles', 'saade/filament-fullcalendar') }}"
            x-ignore x-data="fullcalendar({
                locale: @js($plugin->getLocale()),
                plugins: @js($plugin->getPlugins()),
                schedulerLicenseKey: @js($plugin->getSchedulerLicenseKey()),
                timeZone: @js($plugin->getTimezone()),
                config: @js($this->getConfig()),
                editable: @json($plugin->isEditable()),
                selectable: @json($plugin->isSelectable()),
                eventClassNames: {!! htmlspecialchars($this->eventClassNames(), ENT_COMPAT) !!},
                eventContent: {!! htmlspecialchars($this->eventContent(), ENT_COMPAT) !!},
                eventDidMount: {!! htmlspecialchars($this->eventDidMount(), ENT_COMPAT) !!},
                eventWillUnmount: {!! htmlspecialchars($this->eventWillUnmount(), ENT_COMPAT) !!},
            })">
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
