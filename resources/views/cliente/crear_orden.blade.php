@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Crear nueva orden</h1>

    <form id="order-form" method="POST" action="{{ route('cliente.orden.store') }}">
        @csrf
        <div class="mt-4">
            <label for="direccion_entrega">Dirección de entrega</label>
            <input id="direccion_entrega" name="direccion_entrega" type="text" class="border p-2 w-full" required />
        </div>

        <div class="mt-4">
            <label>Productos</label>
            <div id="productos-list">
                {{-- Se llenará con JS --}}
            </div>
            <button type="button" id="add-product" class="mt-2 px-3 py-1 bg-gray-200 rounded">Agregar producto</button>
        </div>

        <div class="mt-4">
            <label for="tipo_envio">Tipo de envío</label>
            <select id="tipo_envio" name="tipo_envio" class="border p-2">
                <option value="estandar">Estándar</option>
                <option value="premium">Premium</option>
            </select>
        </div>

        <div class="mt-4">
            <label for="distancia">Distancia (km) — opcional</label>
            <input id="distancia" name="distancia" type="number" step="0.1" class="border p-2" />
        </div>

        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Enviar orden</button>
        </div>
    </form>
</div>

<script>
    (function () {
        const products = @json($products ?? []);
        const list = document.getElementById('productos-list');
        const addBtn = document.getElementById('add-product');
        let idx = 0;

        function renderRow(product=null) {
            const id = idx++;
            const wrapper = document.createElement('div');
            wrapper.className = 'product-row mt-2';
            wrapper.innerHTML = `
                <select name="productos[${id}][id]" class="border p-2">
                    ${products.map(p => `<option value="${p.id}">${p.nombre} — ${p.precio}</option>`).join('')}
                </select>
                <input name="productos[${id}][cantidad]" type="number" min="1" value="1" class="border p-2 w-20 ml-2" />
                <button type="button" class="remove-product ml-2 px-2 py-1 bg-red-600 text-white rounded">X</button>
            `;
            list.appendChild(wrapper);
            wrapper.querySelector('.remove-product').addEventListener('click', () => wrapper.remove());
        }

        addBtn.addEventListener('click', () => renderRow());

        // Inicial: si hay productos pasados desde el servidor, agrega uno
        if (products.length) renderRow();

        // Prevent default submit and submit via fetch to show JSON response
        document.getElementById('order-form').addEventListener('submit', function (e) {
            // Use normal POST to let controller handle validation — allow basic submit
        });
    })();
</script>

@endsection
