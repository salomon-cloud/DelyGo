@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Órdenes pendientes</h1>

    <div id="rest-message" class="hidden mb-4"></div>

    <ul class="space-y-3">
        @foreach($ordenes as $orden)
            <li class="p-3 bg-white rounded shadow flex items-center justify-between">
                <div>
                    <div class="font-medium">#{{ $orden->id }} — {{ $orden->estado }}</div>
                    <div class="text-sm text-gray-600">Cliente: {{ $orden->cliente->name ?? 'N/A' }}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <select id="estado-select-{{ $orden->id }}" class="border p-1 rounded">
                        <option value="preparando">preparando</option>
                        <option value="listo">listo</option>
                        <option value="en_camino">en_camino</option>
                        <option value="entregado">entregado</option>
                    </select>
                    <button onclick="changeEstado({{ $orden->id }})" class="px-3 py-1 bg-blue-600 text-white rounded">Cambiar</button>
                </div>
            </li>
        @endforeach
    </ul>

    <script>
        function showMsg(text, ok=true){
            const el = document.getElementById('rest-message');
            el.className = ok ? 'p-3 mb-4 bg-green-100 text-green-800 rounded' : 'p-3 mb-4 bg-red-100 text-red-800 rounded';
            el.innerText = text;
            el.classList.remove('hidden');
            setTimeout(()=> el.classList.add('hidden'), 4000);
        }

        function changeEstado(ordenId){
            const sel = document.getElementById('estado-select-'+ordenId);
            const estado = sel.value;
            fetch("{{ url('restaurante/ordenes') }}/"+ordenId+"/estado",{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept':'application/json'
                },
                body: JSON.stringify({estado: estado})
            }).then(r=>r.json()).then(data=>{
                if(data.success) showMsg(data.message || 'Estado actualizado'); else showMsg(data.message || 'Error', false);
            }).catch(e=>{ showMsg('Error de red', false); console.error(e); });
        }
    </script>
</div>
@endsection
