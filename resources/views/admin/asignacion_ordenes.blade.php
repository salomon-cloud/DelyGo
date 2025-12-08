@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Asignación de órdenes</h1>

    <div class="mt-4">
        <button type="button" id="open-assign-modal" class="px-4 py-2 rounded text-white" style="background-color:#2563eb;color:#ffffff;">Asignar una orden</button>
    </div>

    @if($ordenes->isEmpty())
        <p class="mt-4">No hay órdenes para asignar.</p>
    @else
        <div class="overflow-x-auto mt-4">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Cliente</th>
                    <th class="p-2 border">Dirección</th>
                    <th class="p-2 border">Productos</th>
                    <th class="p-2 border">Total</th>
                    <th class="p-2 border">Estado</th>
                    <th class="p-2 border">Repartidor</th>
                    <th class="p-2 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($ordenes as $orden)
                <tr id="orden-row-{{ $orden->id }}" class="odd:bg-white even:bg-gray-50">
                    <td class="p-2 border">#{{ $orden->id }}</td>
                    <td class="p-2 border">{{ optional($orden->cliente)->name ?? 'N/A' }}</td>
                    <td class="p-2 border">{{ $orden->direccion_entrega ?? ($orden->direccion ?? 'N/A') }}</td>
                    <td class="p-2 border">
                        @if($orden->productos && $orden->productos->count())
                            <ul class="text-sm">
                            @foreach($orden->productos as $p)
                                <li>{{ $p->nombre }} x {{ $p->pivot->cantidad ?? 1 }} ({{ $p->precio }} c/u)</li>
                            @endforeach
                            </ul>
                        @else
                            <em>No hay productos registrados</em>
                        @endif
                    </td>
                    <td class="p-2 border">{{ number_format($orden->total ?? 0, 2) }}</td>
                    <td class="p-2 border" id="orden-estado-{{ $orden->id }}">{{ $orden->estado }}</td>
                    <td class="p-2 border" id="orden-repartidor-{{ $orden->id }}">{{ optional($orden->repartidor)->name ?? '---' }}</td>
                    <td class="p-2 border">
                        <div class="flex flex-col gap-2">
                            {{-- Asignación removida de la columna de Acciones; solo queda cambio de estado --}}

                            <div class="flex gap-2 items-center">
                                <form method="POST" action="{{ url('/admin/ordenes/'.$orden->id.'/estado') }}" class="estado-form">
                                    @csrf
                                    <select name="nuevo_estado" class="estado-select p-1 border" data-orden-id="{{ $orden->id }}">
                                        @php
                                            $estados = ['preparando','en_camino','entregada','cancelada'];
                                        @endphp
                                        @foreach($estados as $e)
                                            <option value="{{ $e }}" {{ $orden->estado === $e ? 'selected' : '' }}>{{ $e }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="change-estado-btn bg-green-600 text-white px-2 py-1 rounded" data-orden-id="{{ $orden->id }}">Cambiar estado</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    @endif

</div>

<!-- Modal markup (hidden by default) -->
<div id="assign-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-11/12 md:w-2/3 p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Asignar una orden</h3>
            <button type="button" id="close-assign-modal" class="text-gray-500">Cerrar</button>
        </div>

        <div id="assign-modal-body">
            <div class="mb-2">
                <label class="block text-sm font-medium">Dirección de entrega</label>
                <input id="modal-direccion" class="w-full p-2 border" placeholder="Calle, número, referencia" />
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium">Total (opcional)</label>
                <input id="modal-total" type="number" step="0.01" class="w-full p-2 border" placeholder="0.00" />
            </div>

            <div class="mb-2">
                <label class="block text-sm font-medium">Productos (seleccione uno o varios)</label>
                <div class="flex gap-2">
                    <select id="modal-productos" name="productos[]" class="w-2/3 p-2 border" multiple size="6">
                        @foreach($productos as $p)
                            <option value="{{ $p->id }}" data-restaurante-id="{{ $p->restaurante_id }}" data-precio="{{ $p->precio }}">{{ $p->nombre }} — ${{ number_format($p->precio,2) }}</option>
                        @endforeach
                    </select>
                    <div class="w-1/3">
                        <button type="button" id="modal-add-product-btn" class="mb-2 px-3 py-1 bg-indigo-600 text-white rounded">Agregar</button>
                        <div id="modal-productos-added" class="p-2 border rounded" style="max-height:220px; overflow:auto;">
                            <!-- Items agregados aparecerán aquí -->
                            <p id="modal-no-products" class="text-sm text-gray-500">No hay productos agregados.</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Selecciona productos y pulsa <strong>Agregar</strong>. Luego ajusta cantidad si es necesario.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Repartidor (disponibles)</label>
                    <select id="modal-repartidor-select" class="w-full p-2 border">
                        <option value="">-- Seleccionar repartidor --</option>
                        @foreach($sample_repartidores as $sr)
                            <option value="{{ $sr->id }}">{{ $sr->name }} ({{ $sr->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Restaurante (disponibles)</label>
                    <select id="modal-restaurante-select" class="w-full p-2 border">
                        <option value="">-- Seleccionar restaurante --</option>
                        @foreach($sample_restaurantes as $rs)
                            <option value="{{ $rs->id }}">{{ $rs->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <label class="inline-flex items-center">
                    <input id="modal-asignar-ahora" type="checkbox" class="mr-2" />
                    <span class="text-sm">Asignar repartidor ahora (si está marcado, la orden se crea y se asigna inmediatamente)</span>
                </label>
            </div>

                <div class="mt-4 flex justify-end gap-2">
                <button type="button" id="modal-assign-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Confirmar asignación</button>
                <button type="button" id="modal-cancel-btn" class="bg-gray-200 px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        console.log('[admin/asignacion_ordenes] script loaded');
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

        function jsonPost(url, body){
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(body)
            }).then(r => r.json());
        }

        // Intercept form submissions (progressive enhancement): handle POST via AJAX and show modal/toast
        document.querySelectorAll('form.inline-form').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const ordenId = submitBtn ? submitBtn.dataset.ordenId : null;
                const formData = new FormData(form);
                const payload = {};
                formData.forEach((v,k) => payload[k] = v);
                if(submitBtn) submitBtn.disabled = true;
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(payload)
                }).then(r => r.json()).then(data => {
                    if(submitBtn) submitBtn.disabled = false;
                    if(data.success){
                        if(data.repartidor_name && ordenId){ document.getElementById('orden-repartidor-'+ordenId).innerText = data.repartidor_name; }
                        if(data.estado && ordenId){ document.getElementById('orden-estado-'+ordenId).innerText = data.estado; }
                        (typeof showToast === 'function' ? showToast(data.message || 'Operación exitosa', 'success') : alert(data.message || 'Operación exitosa'));
                        if(typeof showResultModal === 'function') showResultModal('Éxito', data.message || 'Operación exitosa', true, 2000);
                    } else {
                        (typeof showToast === 'function' ? showToast(data.message || 'Error', 'error') : alert(data.message || 'Error'));
                        if(typeof showResultModal === 'function') showResultModal('Error', data.message || 'Error', false, 2000);
                    }
                }).catch(err => {
                    if(submitBtn) submitBtn.disabled = false;
                    (typeof showToast === 'function' ? showToast('Error de red', 'error') : alert('Error de red'));
                    if(typeof showResultModal === 'function') showResultModal('Error', 'Error de red', false, 2000);
                    console.error(err);
                });
            });
        });

        document.querySelectorAll('form.estado-form').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const ordenId = submitBtn ? submitBtn.dataset.ordenId : null;
                const formData = new FormData(form);
                const payload = {};
                formData.forEach((v,k) => payload[k] = v);
                if(submitBtn) submitBtn.disabled = true;
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(payload)
                }).then(r => r.json()).then(data => {
                    if(submitBtn) submitBtn.disabled = false;
                    if(data.success){
                        if(payload['nuevo_estado'] && ordenId){ document.getElementById('orden-estado-'+ordenId).innerText = payload['nuevo_estado']; }
                        (typeof showToast === 'function' ? showToast(data.message || 'Estado actualizado', 'success') : alert(data.message || 'Estado actualizado'));
                        if(typeof showResultModal === 'function') showResultModal('Éxito', data.message || 'Estado actualizado', true, 2000);
                    } else {
                        (typeof showToast === 'function' ? showToast(data.message || 'Error', 'error') : alert(data.message || 'Error'));
                        if(typeof showResultModal === 'function') showResultModal('Error', data.message || 'Error', false, 2000);
                    }
                }).catch(err => {
                    if(submitBtn) submitBtn.disabled = false;
                    (typeof showToast === 'function' ? showToast('Error de red', 'error') : alert('Error de red'));
                    if(typeof showResultModal === 'function') showResultModal('Error', 'Error de red', false, 2000);
                    console.error(err);
                });
            });
        });

    // Remove old assign-btn handlers (no-op now since assign UI was removed). Kept intentionally empty.

        // Change estado button
        document.querySelectorAll('.change-estado-btn').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const ordenId = this.dataset.ordenId;
                const select = document.querySelector('.estado-select[data-orden-id="'+ordenId+'"]');
                const nuevoEstado = select.value;
                if(!nuevoEstado){ showToast('Seleccione un estado', 'error'); return; }
                this.disabled = true;
                console.log('POST', '/admin/ordenes/'+ordenId+'/estado', { nuevo_estado: nuevoEstado });
                jsonPost('/admin/ordenes/'+ordenId+'/estado', { nuevo_estado: nuevoEstado })
                    .then(data => {
                        console.log('change-estado response', data);
                        this.disabled = false;
                            if(data.success){
                                document.getElementById('orden-estado-'+ordenId).innerText = nuevoEstado;
                                (typeof showToast === 'function' ? showToast('Estado actualizado.', 'success') : alert('Estado actualizado.'));
                                if(typeof showResultModal === 'function') showResultModal('Éxito', data.message || 'Estado actualizado.', true, 2000);
                            } else {
                                (typeof showToast === 'function' ? showToast(data.message || 'Error al cambiar estado.', 'error') : alert(data.message || 'Error al cambiar estado.'));
                                if(typeof showResultModal === 'function') showResultModal('Error', data.message || 'Error al cambiar estado.', false, 2000);
                            }
                    }).catch(err => { this.disabled = false; (typeof showToast === 'function' ? showToast('Error de red', 'error') : alert('Error de red')); if(typeof showResultModal === 'function') showResultModal('Error', 'Error de red', false, 2000); console.error(err); });
            });
        });

        // Modal handling
        const openBtn = document.getElementById('open-assign-modal');
        const modal = document.getElementById('assign-modal');
        const closeBtn = document.getElementById('close-assign-modal');
        const cancelBtn = document.getElementById('modal-cancel-btn');
        const ordenSelect = document.getElementById('modal-orden-select');
        const clienteSpan = document.getElementById('modal-cliente');
        const direccionSpan = document.getElementById('modal-direccion');
        const productosList = document.getElementById('modal-productos');
        const totalSpan = document.getElementById('modal-total');
        const repartidorSelect = document.getElementById('modal-repartidor-select');
        const restauranteSelect = document.getElementById('modal-restaurante-select');
        const confirmBtn = document.getElementById('modal-assign-btn');

        function openModal(){ modal.classList.remove('hidden'); }
        function closeModal(){ modal.classList.add('hidden'); }

        openBtn && openBtn.addEventListener('click', function(){ openModal(); });
        closeBtn && closeBtn.addEventListener('click', function(){ closeModal(); });
        cancelBtn && cancelBtn.addEventListener('click', function(){ closeModal(); });

        // Manage adding products to the temporary list inside the modal
        const addProductBtn = document.getElementById('modal-add-product-btn');
        const addedContainer = document.getElementById('modal-productos-added');
        let addedProducts = []; // { id, name, precio, cantidad }

        function renderAddedProducts(){
            addedContainer.innerHTML = '';
            if(addedProducts.length === 0){
                const p = document.createElement('p'); p.id = 'modal-no-products'; p.className = 'text-sm text-gray-500'; p.innerText = 'No hay productos agregados.'; addedContainer.appendChild(p); return;
            }
            const ul = document.createElement('ul'); ul.className = 'space-y-2';
            addedProducts.forEach((it, idx) => {
                const li = document.createElement('li'); li.className = 'flex items-center justify-between gap-2';
                li.innerHTML = `
                    <div class="flex-1">
                        <strong>${escapeHtml(it.name)}</strong>
                        <div class="text-xs text-gray-600">$${Number(it.precio).toFixed(2)}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" min="1" value="${it.cantidad}" data-idx="${idx}" class="added-qty p-1 w-20 border" />
                        <button type="button" data-idx="${idx}" class="remove-added-btn px-2 py-1 bg-red-600 text-white rounded">Eliminar</button>
                    </div>
                `;
                ul.appendChild(li);
            });
            addedContainer.appendChild(ul);

            // Attach listeners
            addedContainer.querySelectorAll('.added-qty').forEach(input => {
                input.addEventListener('change', function(){
                    const idx = parseInt(this.dataset.idx);
                    let v = parseInt(this.value) || 1; if(v < 1) v = 1; this.value = v; addedProducts[idx].cantidad = v;
                });
            });
            addedContainer.querySelectorAll('.remove-added-btn').forEach(btn => {
                btn.addEventListener('click', function(){
                    const idx = parseInt(this.dataset.idx);
                    addedProducts.splice(idx,1);
                    renderAddedProducts();
                });
            });
        }

        function escapeHtml(unsafe) {
            return (unsafe+'').replace(/[&<"'`=\/]/g, function (s) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'\/'})[s]; });
        }

        addProductBtn && addProductBtn.addEventListener('click', function(){
            const select = document.getElementById('modal-productos');
            if(!select) return;
            const opts = Array.from(select.selectedOptions);
            if(opts.length === 0){ (typeof showToast === 'function' ? showToast('Seleccione al menos un producto para agregar', 'error') : alert('Seleccione al menos un producto para agregar')); return; }
            opts.forEach(o => {
                const id = parseInt(o.value);
                // Prevent duplicates: if already added, increment cantidad by 1
                const existing = addedProducts.find(x => x.id === id);
                if(existing){ existing.cantidad = existing.cantidad + 1; return; }
                const name = o.textContent.trim();
                const precio = parseFloat(o.dataset.precio || 0);
                addedProducts.push({ id: id, name: name, precio: precio, cantidad: 1 });
            });
            renderAddedProducts();
        });

        // Note: the modal now accepts manual order input; only repartidores and restaurantes are fetched from DB

    confirmBtn && confirmBtn.addEventListener('click', function(e){
            e.preventDefault();
            const repartidorId = repartidorSelect.value;
            const restauranteId = restauranteSelect.value;
            const direccion = document.getElementById('modal-direccion').value.trim();
            const total = document.getElementById('modal-total').value || null;
            const asignarAhora = document.getElementById('modal-asignar-ahora').checked;

            if(!restauranteId){ (typeof showToast === 'function' ? showToast('Seleccione un restaurante', 'error') : alert('Seleccione un restaurante')); return; }
            if(asignarAhora && !repartidorId){ (typeof showToast === 'function' ? showToast('Seleccione un repartidor para asignar ahora', 'error') : alert('Seleccione un repartidor para asignar ahora')); return; }
            if(!direccion){ (typeof showToast === 'function' ? showToast('Ingrese la dirección de entrega', 'error') : alert('Ingrese la dirección de entrega')); return; }

            // Build productos payload from addedProducts
            const productosPayload = addedProducts.map(p => ({ id: p.id, cantidad: parseInt(p.cantidad) || 1 }));

            fetch('/admin/ordenes/crear-asignar', {
                method: 'POST',
                headers: {
                    'Accept':'application/json',
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ repartidor_id: repartidorId, restaurante_id: restauranteId, direccion_entrega: direccion, total: total, productos: productosPayload, asignar_ahora: asignarAhora })
            }).then(r => r.json()).then(data => {
                if(data.success){
                    showToast(data.message || 'Orden creada', 'success');
                    if(typeof showResultModal === 'function') showResultModal('Éxito', data.message || 'Orden creada', true, 2000);
                    closeModal();
                    // reset addedProducts
                    addedProducts = [];
                    renderAddedProducts();
                } else {
                    showToast(data.message || 'Error al crear y asignar', 'error');
                    if(typeof showResultModal === 'function') showResultModal('Error', data.message || 'Error al crear y asignar', false, 2000);
                }
            }).catch(err => { (typeof showToast === 'function' ? showToast('Error de red', 'error') : alert('Error de red')); if(typeof showResultModal === 'function') showResultModal('Error', 'Error de red', false, 2000); console.error(err); });
        });

    // Initial render (show "No hay productos agregados.")
    renderAddedProducts();
    })();
</script>
@endsection