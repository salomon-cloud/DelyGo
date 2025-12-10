@extends('layout')

@section('content')
<div class="container" style="padding: 2rem 0;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <h2 class="card-title">📝 Editar Producto</h2>
        <p style="color: #666; margin-top: 0.5rem;">{{ $producto->nombre }}</p>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: var(--shadow-sm); max-width: 600px;">
        <form method="POST" action="{{ route('productos.update', $producto) }}">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div style="margin-bottom: 1.5rem;">
                <label for="nombre" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Nombre del Producto *</label>
                <input 
                    type="text" 
                    id="nombre" 
                    name="nombre" 
                    value="{{ old('nombre', $producto->nombre) }}"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;"
                    placeholder="Ej: Hamburguesa Premium"
                >
                @error('nombre')
                    <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div style="margin-bottom: 1.5rem;">
                <label for="descripcion" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Descripción</label>
                <textarea 
                    id="descripcion" 
                    name="descripcion"
                    rows="4"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; font-family: inherit;"
                    placeholder="Describe tu producto"
                >{{ old('descripcion', $producto->descripcion) }}</textarea>
                @error('descripcion')
                    <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio -->
            <div style="margin-bottom: 1.5rem;">
                <label for="precio" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Precio ($) *</label>
                <input 
                    type="number" 
                    id="precio" 
                    name="precio"
                    value="{{ old('precio', $producto->precio) }}"
                    step="0.01"
                    min="0"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;"
                    placeholder="0.00"
                >
                @error('precio')
                    <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Disponibilidad -->
            <div style="margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                <input 
                    type="checkbox" 
                    id="disponible" 
                    name="disponible" 
                    value="1"
                    @checked(old('disponible', $producto->disponible))
                    style="width: 20px; height: 20px; cursor: pointer;"
                >
                <label for="disponible" style="margin: 0; font-weight: 500; cursor: pointer;">
                    ✓ Disponible en el menú
                </label>
                @error('disponible')
                    <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div style="display: flex; gap: 1rem;">
                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="flex: 1;"
                >
                    💾 Guardar Cambios
                </button>
                <a 
                    href="{{ route('productos.index') }}" 
                    class="btn btn-outline"
                    style="flex: 1; text-align: center; text-decoration: none;"
                >
                    ❌ Cancelar
                </a>
            </div>

            <!-- Botón de Eliminar -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('productos.destroy', $producto) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        class="btn"
                        style="background-color: #dc2626; color: white; padding: 0.75rem 1.5rem; border-radius: 4px; border: none; cursor: pointer; font-size: 1rem; width: 100%;"
                    >
                        🗑️ Eliminar Producto
                    </button>
                </form>
            </div>
        </form>
    </div>

    <!-- Información -->
    <div style="background: #f0fdf4; padding: 1rem; border-radius: 6px; margin-top: 2rem; border-left: 4px solid var(--success-color);">
        <p style="margin: 0; color: #166534; font-size: 0.875rem;">
            <strong>Nota:</strong> Los cambios se guardarán inmediatamente. Puedes usar el botón de "Disponible" para ocultar/mostrar el producto sin eliminarlo.
        </p>
    </div>
</div>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
    }

    .card-title {
        margin: 0;
        font-size: 1.75rem;
        color: var(--dark-color, #1f2937);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .btn-primary {
        background-color: var(--primary-color, #3b82f6);
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-outline {
        background-color: transparent;
        color: var(--primary-color, #3b82f6);
        border: 1px solid var(--primary-color, #3b82f6);
    }

    .btn-outline:hover {
        background-color: #f0f9ff;
    }
</style>
@endsection
