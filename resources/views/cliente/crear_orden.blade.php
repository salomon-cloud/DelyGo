@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Crear nueva orden</h1>

    <form id="order-form" method="POST" action="{{ route('cliente.orden.store') }}">
        @csrf
        <div id="order-message" class="mt-4 hidden"></div>
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

            const body = {
                direccion_entrega: document.getElementById('direccion_entrega').value,
                tipo_envio: document.getElementById('tipo_envio').value,
                distancia: document.getElementById('distancia').value || null,
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
            }).then(r => r.json())
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
                    const text = 'Respuesta inesperada.';
                    if (typeof showToast === 'function') showToast(text, 'info');
                    else {
                        msg.classList.remove('hidden');
                        msg.className = 'mt-4 p-3 bg-yellow-100 text-yellow-800 rounded';
                        msg.innerText = text;
                    }
                }
            }).catch(err => {
                const text = 'Error de red al enviar la orden.';
                if (typeof showToast === 'function') showToast(text, 'error');
                else {
                    msg.classList.remove('hidden');
                    msg.className = 'mt-4 p-3 bg-red-100 text-red-800 rounded';
                    msg.innerText = text;
                }
                console.error(err);
            });
        });
    })();
</script>

@endsection
