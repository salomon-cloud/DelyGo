@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Asignación de órdenes</h1>

    <div class="mt-4">
        <button id="open-assign-modal" class="px-4 py-2 rounded text-white" style="background-color:#2563eb;color:#ffffff;">Asignar una orden</button>
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
                            <div class="flex gap-2">
                                <select class="repartidor-select p-1 border" data-orden-id="{{ $orden->id }}">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($repartidores as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->email }})</option>
                                    @endforeach
                                </select>
                                <button class="assign-btn bg-blue-600 text-white px-2 py-1 rounded" data-orden-id="{{ $orden->id }}">Asignar</button>
                            </div>

                            <div class="flex gap-2 items-center">
                                <select class="estado-select p-1 border" data-orden-id="{{ $orden->id }}">
                                    @php
                                        $estados = ['preparando','en_camino','entregada','cancelada'];
                                    @endphp
                                    @foreach($estados as $e)
                                        <option value="{{ $e }}" {{ $orden->estado === $e ? 'selected' : '' }}>{{ $e }}</option>
                                    @endforeach
                                </select>
                                <button class="change-estado-btn bg-green-600 text-white px-2 py-1 rounded" data-orden-id="{{ $orden->id }}">Cambiar estado</button>
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
            <button id="close-assign-modal" class="text-gray-500">Cerrar</button>
        </div>

        <div id="assign-modal-body">
            <div class="mb-2">
                <label class="block text-sm font-medium">Orden</label>
                <select id="modal-orden-select" class="w-full p-2 border">
                    <option value="">-- Seleccionar orden --</option>
                    @foreach($ordenes as $o)
                        <option value="{{ $o->id }}">#{{ $o->id }} - {{ optional($o->cliente)->name ?? 'N/A' }} - {{ $o->estado }}</option>
                    @endforeach
                </select>
            </div>

            <div id="modal-orden-detalles" class="mb-4 text-sm text-gray-700">
                <p><strong>Cliente:</strong> <span id="modal-cliente">-</span></p>
                <p><strong>Dirección:</strong> <span id="modal-direccion">-</span></p>
                <p><strong>Productos:</strong></p>
                <ul id="modal-productos" class="list-disc ml-6 text-sm"></ul>
                <p><strong>Total:</strong> <span id="modal-total">0.00</span></p>
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

            <div class="mt-4 flex justify-end gap-2">
                <button id="modal-assign-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Confirmar asignación</button>
                <button id="modal-cancel-btn" class="bg-gray-200 px-4 py-2 rounded">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
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

        // Assign button
        document.querySelectorAll('.assign-btn').forEach(btn => {
            btn.addEventListener('click', function(){
                const ordenId = this.dataset.ordenId;
                const select = document.querySelector('.repartidor-select[data-orden-id="'+ordenId+'"]');
                const repartidorId = select.value;
                if(!repartidorId){
                    alert('Seleccione un repartidor.');
                    return;
                }
                this.disabled = true;
                jsonPost('/admin/ordenes/'+ordenId+'/asignar', { repartidor_id: repartidorId })
                    .then(data => {
                        this.disabled = false;
                        if(data.success){
                            document.getElementById('orden-repartidor-'+ordenId).innerText = data.repartidor_name || 'Asignado';
                            if(data.estado){ document.getElementById('orden-estado-'+ordenId).innerText = data.estado; }
                            alert('Repartidor asignado correctamente.');
                        } else {
                            alert(data.message || 'Error al asignar.');
                        }
                    }).catch(err => { this.disabled = false; alert('Error de red'); console.error(err); });
            });
        });

        // Change estado button
        document.querySelectorAll('.change-estado-btn').forEach(btn => {
            btn.addEventListener('click', function(){
                const ordenId = this.dataset.ordenId;
                const select = document.querySelector('.estado-select[data-orden-id="'+ordenId+'"]');
                const nuevoEstado = select.value;
                if(!nuevoEstado){ alert('Seleccione un estado'); return; }
                this.disabled = true;
                jsonPost('/restaurante/ordenes/'+ordenId+'/estado', { nuevo_estado: nuevoEstado })
                    .then(data => {
                        this.disabled = false;
                        if(data.success){
                            document.getElementById('orden-estado-'+ordenId).innerText = nuevoEstado;
                            alert('Estado actualizado.');
                        } else {
                            alert(data.message || 'Error al cambiar estado.');
                        }
                    }).catch(err => { this.disabled = false; alert('Error de red'); console.error(err); });
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

        // Orders data from server
        const ordersData = {!! json_encode($ordenes->map(function($o){
            return [
                'id' => $o->id,
                'estado' => $o->estado,
                'cliente' => $o->cliente ? $o->cliente->name : null,
                'direccion' => $o->direccion_entrega ?? $o->direccion ?? null,
                'productos' => $o->productos ? $o->productos->map(function($p){ return ['nombre'=>$p->nombre, 'cantidad'=>($p->pivot->cantidad ?? 1), 'precio'=>$p->precio]; }) : [],
                'total' => $o->total ?? 0,
                'restaurante_id' => $o->restaurante ? $o->restaurante->id : null,
            ];
        })) !!};

        function openModal(){ modal.classList.remove('hidden'); }
        function closeModal(){ modal.classList.add('hidden'); }

        openBtn && openBtn.addEventListener('click', function(){ openModal(); });
        closeBtn && closeBtn.addEventListener('click', function(){ closeModal(); });
        cancelBtn && cancelBtn.addEventListener('click', function(){ closeModal(); });

        ordenSelect && ordenSelect.addEventListener('change', function(){
            const id = parseInt(this.value);
            const ord = ordersData.find(o => o.id === id);
            if(!ord){ clienteSpan.innerText = '-'; direccionSpan.innerText = '-'; productosList.innerHTML = ''; totalSpan.innerText = '0.00'; return; }
            clienteSpan.innerText = ord.cliente || 'N/A';
            direccionSpan.innerText = ord.direccion || 'N/A';
            productosList.innerHTML = '';
            ord.productos.forEach(p => {
                const li = document.createElement('li');
                li.innerText = `${p.nombre} x ${p.cantidad} (${p.precio} c/u)`;
                productosList.appendChild(li);
            });
            totalSpan.innerText = parseFloat(ord.total).toFixed(2);
            // pre-select restaurante related to order if available
            if(ord.restaurante_id){
                const opt = restauranteSelect.querySelector('option[value="'+ord.restaurante_id+'"]');
                if(opt) opt.selected = true;
            }
        });

        confirmBtn && confirmBtn.addEventListener('click', function(){
            const ordenId = ordenSelect.value;
            const repartidorId = repartidorSelect.value;
            const restauranteId = restauranteSelect.value;
            if(!ordenId){ alert('Seleccione una orden'); return; }
            if(!repartidorId){ alert('Seleccione un repartidor'); return; }
            // For demo we call the existing assign endpoint; restauranteId is informational here
            fetch('/admin/ordenes/'+ordenId+'/asignar', {
                method: 'POST',
                headers: {
                    'Accept':'application/json',
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ repartidor_id: repartidorId })
            }).then(r => r.json()).then(data => {
                if(data.success){
                    alert('Asignación realizada correctamente');
                    closeModal();
                    // update UI if present
                    const repName = data.repartidor_name || 'Asignado';
                    const estado = data.estado || '';
                    const repElem = document.getElementById('orden-repartidor-'+ordenId);
                    const estElem = document.getElementById('orden-estado-'+ordenId);
                    if(repElem) repElem.innerText = repName;
                    if(estElem && estado) estElem.innerText = estado;
                } else {
                    alert(data.message || 'Error al asignar');
                }
            }).catch(err => { alert('Error de red'); console.error(err); });
        });

    })();
</script>

@endsection
