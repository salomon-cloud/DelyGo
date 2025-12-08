@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestión de Productos</h1>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            + Crear Producto
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Filtros</h3>
        <form method="GET" action="{{ route('admin.productos') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Restaurante</label>
                <select name="restaurante_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los restaurantes</option>
                    @foreach($restaurantes as $rest)
                        <option value="{{ $rest->id }}" {{ ($filtros['restaurante_id'] ?? '') == $rest->id ? 'selected' : '' }}>
                            {{ $rest->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Disponibilidad</label>
                <select name="disponible" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ ($filtros['disponible'] ?? '') === '1' ? 'selected' : '' }}>Disponibles</option>
                    <option value="0" {{ ($filtros['disponible'] ?? '') === '0' ? 'selected' : '' }}>No disponibles</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar por nombre</label>
                <input type="text" name="search" value="{{ $filtros['search'] ?? '' }}" 
                       placeholder="Nombre del producto..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-md transition duration-200">
                    Filtrar
                </button>
                <a href="{{ route('admin.productos') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md transition duration-200">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponibilidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="productosTable">
                    @foreach($productos as $producto)
                    <tr data-id="{{ $producto->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $producto->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                            @if($producto->descripcion)
                                <div class="text-sm text-gray-500">{{ Str::limit($producto->descripcion, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $producto->restaurante->nombre ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ${{ number_format($producto->precio, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleDisponibilidad({{ $producto->id }})" 
                                    id="disponible-{{ $producto->id }}"
                                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $producto->disponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $producto->disponible ? 'Disponible' : 'No disponible' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openEditModal({{ $producto->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </button>
                            <button onclick="deleteProducto({{ $producto->id }})" 
                                    class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($productos->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-500 text-lg">No hay productos registrados</div>
            <button onclick="openCreateModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Crear el primer producto
            </button>
        </div>
        @endif
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $productos->links() }}
    </div>
</div>

<!-- Modal para Crear/Editar Producto -->
<div id="productoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Crear Producto</h3>
            
            <form id="productoForm">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Restaurante *</label>
                        <select name="restaurante_id" id="restaurante_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona un restaurante...</option>
                            @foreach($restaurantes as $rest)
                                <option value="{{ $rest->id }}">{{ $rest->nombre }}</option>
                            @endforeach
                        </select>
                        <span class="text-red-500 text-sm" id="error_restaurante_id"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto *</label>
                        <input type="text" name="nombre" id="nombre" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ej: Pizza Hawaiana">
                        <span class="text-red-500 text-sm" id="error_nombre"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descripción del producto (opcional)"></textarea>
                        <span class="text-red-500 text-sm" id="error_descripcion"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="precio" id="precio" step="0.01" min="0.01" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                        </div>
                        <span class="text-red-500 text-sm" id="error_precio"></span>
                    </div>
                    
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="disponible" id="disponible" value="1" checked
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Producto disponible</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" id="submitButton"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Crear Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentProducto = null;
let isEditing = false;

function openCreateModal() {
    isEditing = false;
    currentProducto = null;
    document.getElementById('modalTitle').textContent = 'Crear Producto';
    document.getElementById('submitButton').textContent = 'Crear Producto';
    
    document.getElementById('productoForm').reset();
    document.getElementById('disponible').checked = true;
    clearErrors();
    
    document.getElementById('productoModal').classList.remove('hidden');
}

function openEditModal(productoId) {
    isEditing = true;
    currentProducto = productoId;
    
    document.getElementById('modalTitle').textContent = 'Editar Producto';
    document.getElementById('submitButton').textContent = 'Actualizar Producto';
    
    fetch(`/admin/productos/${productoId}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const producto = data.producto;
            document.getElementById('restaurante_id').value = producto.restaurante_id;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('descripcion').value = producto.descripcion || '';
            document.getElementById('precio').value = producto.precio;
            document.getElementById('disponible').checked = producto.disponible;
            
            clearErrors();
            document.getElementById('productoModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del producto');
    });
}

function closeModal() {
    document.getElementById('productoModal').classList.add('hidden');
    currentProducto = null;
    isEditing = false;
}

function clearErrors() {
    const errorElements = document.querySelectorAll('[id^="error_"]');
    errorElements.forEach(element => {
        element.textContent = '';
    });
}

function showErrors(errors) {
    clearErrors();
    Object.keys(errors).forEach(field => {
        const errorElement = document.getElementById(`error_${field}`);
        if (errorElement) {
            errorElement.textContent = errors[field][0];
        }
    });
}

document.getElementById('productoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = isEditing ? `/admin/productos/${currentProducto}` : '/admin/productos';
    
    if (isEditing) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            if (data.errors) {
                showErrors(data.errors);
            } else {
                alert(data.message || 'Error al procesar la solicitud');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
});

function deleteProducto(productoId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
        return;
    }
    
    fetch(`/admin/productos/${productoId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`tr[data-id="${productoId}"]`);
            if (row) {
                row.remove();
            }
            
            showSuccessMessage(data.message);
        } else {
            alert(data.message || 'Error al eliminar el producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function toggleDisponibilidad(productoId) {
    fetch(`/admin/productos/${productoId}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const button = document.getElementById(`disponible-${productoId}`);
            if (data.disponible) {
                button.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800';
                button.textContent = 'Disponible';
            } else {
                button.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800';
                button.textContent = 'No disponible';
            }
        } else {
            alert(data.message || 'Error al cambiar disponibilidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
    successDiv.role = 'alert';
    successDiv.textContent = message;
    
    const container = document.querySelector('.container');
    const firstChild = container.children[1];
    container.insertBefore(successDiv, firstChild);
    
    setTimeout(() => {
        successDiv.remove();
    }, 5000);
}

document.getElementById('productoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
