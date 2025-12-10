@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb / Volver -->
        <div class="mb-4">
            <a href="{{ route('cliente.orden.create') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cambiar restaurante
            </a>
        </div>

        <!-- Información del restaurante -->
        @if(isset($restaurante))
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">{{ $restaurante->nombre }}</h1>
                    @if($restaurante->descripcion)
                        <p class="text-blue-100 mb-2">{{ $restaurante->descripcion }}</p>
                    @endif
                    <div class="flex items-center text-blue-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $restaurante->direccion }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                        <div class="text-sm text-blue-100">Productos disponibles</div>
                        <div class="text-2xl font-bold">{{ $products->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Crear nueva orden</h2>

            <form id="order-form" method="POST" action="{{ route('cliente.orden.store') }}">
                @if(isset($restaurante))
                    <input type="hidden" name="restaurante_id" value="{{ $restaurante->id }}">
                @endif
                @csrf
                <div id="order-message" class="hidden"></div>

                <!-- Dirección de entrega -->
                <div class="mb-6">
                    <label for="direccion_entrega" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección de entrega *
                    </label>
                    <input id="direccion_entrega" 
                           name="direccion_entrega" 
                           type="text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Ej: Calle Principal #123, Col. Centro" 
                           required />
                </div>

                <!-- Productos -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Selecciona tus productos *
                    </label>
                    
                    @if($products->isEmpty())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                            <p class="text-yellow-800">Este restaurante no tiene productos disponibles en este momento.</p>
                        </div>
                    @else
                        <div id="productos-list" class="space-y-3">
                            {{-- Se llenará con JS --}}
                        </div>
                        
                        <button type="button" 
                                id="add-product" 
                                class="mt-3 inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Agregar otro producto
                        </button>

                        <!-- Resumen de la orden -->
                        <div id="order-summary" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Resumen del pedido</h3>
                            <div id="summary-items" class="text-sm text-gray-600 space-y-1">
                                <p class="text-gray-500 italic">Agrega productos para ver el resumen</p>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-300">
                                <div class="flex justify-between font-semibold text-gray-800">
                                    <span>Subtotal:</span>
                                    <span id="summary-total">$0.00</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">+ Costo de envío (se calculará al confirmar)</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Tipo de envío -->
                <div class="mb-6">
                    <label for="tipo_envio" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de envío *
                    </label>
                    <select id="tipo_envio" 
                            name="tipo_envio" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="estandar">Estándar (2-3 días) - Más económico</option>
                        <option value="premium">Premium (24 horas) - Entrega rápida</option>
                    </select>
                </div>

                <!-- Distancia -->
                <div class="mb-6">
                    <label for="distancia" class="block text-sm font-medium text-gray-700 mb-2">
                        Distancia aproximada (km) - Opcional
                    </label>
                    <input id="distancia" 
                           name="distancia" 
                           type="number" 
                           step="0.1" 
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Ej: 5.5" />
                    <p class="text-xs text-gray-500 mt-1">Esto ayuda a calcular el costo de envío</p>
                </div>

                <!-- Botones -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirmar y Crear Orden
                    </button>
                    <a href="{{ route('cliente.orden.create') }}" 
                       class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition duration-200 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const products = @json($products ?? []);
        const list = document.getElementById('productos-list');
        const addBtn = document.getElementById('add-product');
        const summaryItems = document.getElementById('summary-items');
        const summaryTotal = document.getElementById('summary-total');
        let idx = 0;

        function updateSummary() {
            const rows = Array.from(document.querySelectorAll('.product-row'));
            if (rows.length === 0) {
                summaryItems.innerHTML = '<p class="text-gray-500 italic">Agrega productos para ver el resumen</p>';
                summaryTotal.textContent = '$0.00';
                return;
            }

            let total = 0;
            const items = rows.map(row => {
                const select = row.querySelector('select');
                const qtyInput = row.querySelector('input[type="number"]');
                const selectedOption = select.options[select.selectedIndex];
                const productId = parseInt(select.value);
                const product = products.find(p => p.id === productId);
                const qty = parseInt(qtyInput.value || '1', 10);
                const price = product ? parseFloat(product.precio) : 0;
                const subtotal = price * qty;
                total += subtotal;

                return {
                    nombre: product ? product.nombre : 'Producto',
                    qty: qty,
                    price: price,
                    subtotal: subtotal
                };
            });

            summaryItems.innerHTML = items.map(item => `
                <div class="flex justify-between">
                    <span>${item.qty}x ${item.nombre}</span>
                    <span>$${item.subtotal.toFixed(2)}</span>
                </div>
            `).join('');

            summaryTotal.textContent = `$${total.toFixed(2)}`;
        }

        function renderRow(product=null) {
            const id = idx++;
            const wrapper = document.createElement('div');
            wrapper.className = 'product-row flex items-center gap-2 bg-gray-50 p-3 rounded-lg';
            wrapper.innerHTML = `
                <div class="flex-1">
                    <select name="productos[${id}][id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent product-select">
                        ${products.map(p => `<option value="${p.id}" data-precio="${p.precio}">${p.nombre} - $${parseFloat(p.precio).toFixed(2)}${p.descripcion ? ' — ' + p.descripcion : ''}</option>`).join('')}
                    </select>
                </div>
                <div class="w-24">
                    <input name="productos[${id}][cantidad]" 
                           type="number" 
                           min="1" 
                           value="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent product-qty" 
                           placeholder="Cant." />
                </div>
                <button type="button" class="remove-product px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            list.appendChild(wrapper);
            
            wrapper.querySelector('.remove-product').addEventListener('click', () => {
                wrapper.remove();
                updateSummary();
            });

            wrapper.querySelector('.product-select').addEventListener('change', updateSummary);
            wrapper.querySelector('.product-qty').addEventListener('input', updateSummary);

            updateSummary();
        }

        if (addBtn) {
            addBtn.addEventListener('click', () => renderRow());
        }

        // Inicial: si hay productos pasados desde el servidor, agrega uno
        if (products.length) {
            renderRow();
        }

        // Submit via AJAX to show friendly feedback without full page reload
        document.getElementById('order-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const msg = document.getElementById('order-message');
            msg.classList.add('hidden');

            const rows = Array.from(document.querySelectorAll('.product-row'));
            const productos = rows.map((row, i) => {
                const select = row.querySelector('select');
                const qty = row.querySelector('input[type="number"]');
                return { id: select.value, cantidad: parseInt(qty.value || '1', 10) };
            }).filter(p => p.id);

            // Calcular total basado en productos seleccionados
            let total = 0;
            rows.forEach(row => {
                const select = row.querySelector('select');
                const qtyInput = row.querySelector('input[type="number"]');
                const productId = parseInt(select.value);
                const product = products.find(p => p.id === productId);
                const qty = parseInt(qtyInput.value || '1', 10);
                const price = product ? parseFloat(product.precio) : 0;
                total += price * qty;
            });

            // Obtener restaurante_id del campo oculto
            const restauranteIdInput = document.querySelector('input[name="restaurante_id"]');
            const restaurante_id = restauranteIdInput ? restauranteIdInput.value : null;

            const body = {
                restaurante_id: restaurante_id,
                direccion_entrega: document.getElementById('direccion_entrega').value,
                total: total,
                productos: productos
            };

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(body)
            }).then(r => {
                if (!r.ok) {
                    return r.json().then(err => Promise.reject(err));
                }
                return r.json();
            })
            .then(data => {
                    if (data.orden_id) {
                        const text = 'Orden creada correctamente. ID: ' + data.orden_id;
                        if (typeof showResultModal === 'function') showResultModal('Éxito', text, true);
                        else if (typeof showToast === 'function') showToast(text, 'success');
                        else {
                            msg.classList.remove('hidden');
                            msg.className = 'mt-4 p-3 bg-green-100 text-green-800 rounded';
                            msg.innerText = text;
                        }
                    // Optionally clear form
                    // document.getElementById('order-form').reset();
                } else if (data.message) {
                    const text = data.message;
                    if (typeof showResultModal === 'function') showResultModal('Éxito', text, true);
                    else {
                        msg.classList.remove('hidden');
                        msg.className = 'mt-4 p-3 bg-green-100 text-green-800 rounded';
                        msg.innerText = text;
                    }
                } else if (data.error) {
                    const text = data.error || 'Error al crear la orden.';
                    if (typeof showResultModal === 'function') showResultModal('Error', text, false);
                    else if (typeof showToast === 'function') showToast(text, 'error');
                    else {
                        msg.classList.remove('hidden');
                        msg.className = 'mt-4 p-3 bg-red-100 text-red-800 rounded';
                        msg.innerText = text;
                    }
                } else {
                    const text = 'Respuesta inesperada: ' + JSON.stringify(data);
                    if (typeof showToast === 'function') showToast(text, 'info');
                    else {
                        msg.classList.remove('hidden');
                        msg.className = 'mt-4 p-3 bg-yellow-100 text-yellow-800 rounded';
                        msg.innerText = text;
                    }
                }
            }).catch(err => {
                console.error('Error:', err);
                const text = err.error || err.message || 'Error de red al enviar la orden.';
                if (typeof showToast === 'function') showToast(text, 'error');
                else {
                    msg.classList.remove('hidden');
                    msg.className = 'mt-4 p-3 bg-red-100 text-red-800 rounded';
                    msg.innerText = text;
                }
            });
        });
    })();
</script>

@endsection
