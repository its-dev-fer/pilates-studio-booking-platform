<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mt-9 mx-auto mb-8">
        <a href="/clientes" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors">
            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver a mi Panel
        </a>
    </div>
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Adquiere tus Créditos</h2>
            <p class="mt-4 text-xl text-gray-600">
                @if(session()->has('pending_appointment'))
                    Para confirmar tu clase del <strong>{{ session('pending_appointment')['date'] }}</strong>, necesitas adquirir un paquete de créditos.
                @else
                    Compra un paquete para poder agendar tus próximas clases. Un crédito equivale a una sesión.
                @endif
            </p>
        </div>


        @if($activeCredits > 0)
            <div class="max-w-3xl mx-auto bg-amber-50 border-l-4 border-amber-500 p-6 mb-12 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h3 class="text-lg font-bold text-amber-800">Ya tienes créditos activos</h3>
                        <p class="text-amber-700 mt-1">Actualmente tienes <strong>{{ $activeCredits }} créditos disponibles</strong>. Según nuestras políticas, solo puedes adquirir un nuevo paquete cuando tus créditos actuales se hayan agotado por completo.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach($packages as $package)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 flex flex-col justify-between hover:scale-105 transition-transform duration-300">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 text-center mb-4">{{ $package->name }}</h3>
                    <div class="text-center mb-6">
                        <span class="text-5xl font-extrabold text-emerald-600">${{ number_format($package->price, 2) }}</span>
                        <span class="text-gray-500 font-medium text-lg">MXN</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <strong>{{ $package->credits_amount }}</strong>&nbsp;Créditos (Clases)
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-6 h-6 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Vigencia de 30 días
                        </li>
                    </ul>
                </div>
                @if($activeCredits > 0)
                    <button disabled class="w-full block text-center bg-gray-200 text-gray-500 font-bold py-3 px-6 rounded-xl cursor-not-allowed">
                        No Disponible
                    </button>
                @else
                    <a href="{{ route('checkout.process', $package->id) }}" class="w-full block text-center bg-black hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg">
                        Comprar Paquete
                    </a>
                @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

