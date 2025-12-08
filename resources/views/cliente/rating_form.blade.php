@if(auth()->check())
<div class="mt-4 p-4 bg-white rounded shadow">
    <h4 class="font-medium">Calificar esta orden</h4>
    <form method="POST" action="{{ route('ratings.store') }}" class="mt-3">
        @csrf
        <input type="hidden" name="orden_id" value="{{ $orden->id }}" />

        <div class="mb-3">
            <label class="block text-sm font-medium">Puntuación</label>
            <select name="puntuacion" class="w-28 px-2 py-1 border rounded">
                <option value="5">5 — Excelente</option>
                <option value="4">4 — Muy bueno</option>
                <option value="3">3 — Bueno</option>
                <option value="2">2 — Regular</option>
                <option value="1">1 — Malo</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="block text-sm font-medium">Comentario (opcional)</label>
            <textarea name="comentario" rows="3" class="w-full px-3 py-2 border rounded" placeholder="Cuenta tu experiencia..."></textarea>
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Enviar calificación</button>
        </div>
    </form>
</div>
@endif
