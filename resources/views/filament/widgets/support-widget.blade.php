<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2 text-primary-600">
                <x-heroicon-o-lifebuoy class="w-6 h-6" />
                <span>Soporte Técnico</span>
            </div>
        </x-slot>

        <x-slot name="description">
            ¿Tienes algún problema con el sistema o necesitas reportar un fallo? El equipo de soporte está listo para ayudarte.
        </x-slot>

        <div class="flex flex-col sm:flex-row gap-4 mt-4">
            {{-- Botón de WhatsApp --}}
            <x-filament::button
                tag="a"
                href="https://wa.me/+529611465703?text=Hola,%20necesito%20soporte%20con%20el%20sistema%20Hannah%20Reforme"
                target="_blank"
                color="success"
                icon="heroicon-o-chat-bubble-left-ellipsis"
            >
                Asistencia por WhatsApp
            </x-filament::button>

            {{-- Botón de Llamada Telefónica --}}
            <x-filament::button
                tag="a"
                href="tel:+529611465703"
                color="gray"
                icon="heroicon-o-phone"
            >
                Llamar a Soporte
            </x-filament::button>

            {{-- Botón de Sistema de Tickets NC --}}
            <x-filament::button
                tag="a"
                href="https://novaconsulting.com.mx/dashboard/login"
                target="_blank"
                color="primary"
                icon="heroicon-o-computer-desktop"
            >
                Portal de Cliente de Nova Consulting
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>