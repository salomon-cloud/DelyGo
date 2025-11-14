@extends('app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Gestión de Restaurantes</h1>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            + Crear Restaurante
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

    <!-- Lista de Restaurantes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propietario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="restaurantesTable">
                    @foreach($restaurantes as $restaurante)
                    <tr data-id="{{ $restaurante->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $restaurante->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $restaurante->nombre }}</div>
                            @if($restaurante->descripcion)
                                <div class="text-sm text-gray-500">{{ Str::limit($restaurante->descripcion, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $restaurante->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $restaurante->user->email ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ Str::limit($restaurante->direccion, 40) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openEditModal({{ $restaurante->id }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </button>
                            <button onclick="deleteRestaurante({{ $restaurante->id }})" 
                                    class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($restaurantes->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-500 text-lg">No hay restaurantes registrados</div>
            <button onclick="openCreateModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Crear el primer restaurante
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Modal para Crear/Editar Restaurante -->
<div id="restauranteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Crear Restaurante</h3>
            
            <form id="restauranteForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Información del Usuario -->
                    <div class="md:col-span-2 border-b pb-4 mb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">Información del Propietario</h4>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Propietario</label>
                        <input type="text" name="nombre_usuario" id="nombre_usuario" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-red-500 text-sm" id="error_nombre_usuario"></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email del Propietario</label>
                        <input type="email" name="email_usuario" id="email_usuario" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-red-500 text-sm" id="error_email_usuario"></span>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" name="password" id="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <div class="text-sm text-gray-500 mt-1" id="passwordHelp">Mínimo 6 caracteres. Deja vacío al editar para mantener la contraseña actual.</div>
                        <span class="text-red-500 text-sm" id="error_password"></span>
                    </div>

                    <!-- Información del Restaurante -->
                    <div class="md:col-span-2 border-b pb-4 mb-4 mt-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">Información del Restaurante</h4>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Restaurante</label>
                        <input type="text" name="nombre_restaurante" id="nombre_restaurante" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-red-500 text-sm" id="error_nombre_restaurante"></span>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descripción opcional del restaurante"></textarea>
                        <span class="text-red-500 text-sm" id="error_descripcion"></span>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="direccion" id="direccion" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <span class="text-red-500 text-sm" id="error_direccion"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-3">
                    <button type="button" onclick="closeModal()" 
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" id="submitButton"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Crear Restaurante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentRestaurante = null;
let isEditing = false;

function openCreateModal() {
    isEditing = false;
    currentRestaurante = null;
    document.getElementById('modalTitle').textContent = 'Crear Restaurante';
    document.getElementById('submitButton').textContent = 'Crear Restaurante';
    document.getElementById('passwordHelp').textContent = 'Mínimo 6 caracteres.';
    document.getElementById('password').required = true;
    
    // Limpiar formulario
    document.getElementById('restauranteForm').reset();
    clearErrors();
    
    document.getElementById('restauranteModal').classList.remove('hidden');
}

function openEditModal(restauranteId) {
    isEditing = true;
    currentRestaurante = restauranteId;
    
    // Buscar datos del restaurante en la tabla
    const row = document.querySelector(`tr[data-id="${restauranteId}"]`);
    if (!row) return;
    
    document.getElementById('modalTitle').textContent = 'Editar Restaurante';
    document.getElementById('submitButton').textContent = 'Actualizar Restaurante';
    document.getElementById('passwordHelp').textContent = 'Mínimo 6 caracteres. Deja vacío para mantener la contraseña actual.';
    document.getElementById('password').required = false;
    
    // Aquí idealmente harías una petición AJAX para obtener todos los datos
    // Por simplicidad, usaremos los datos disponibles en la tabla
    fetch(`/admin/restaurantes/${restauranteId}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const restaurante = data.restaurante;
            document.getElementById('nombre_usuario').value = restaurante.user.name;
            document.getElementById('email_usuario').value = restaurante.user.email;
            document.getElementById('nombre_restaurante').value = restaurante.nombre;
            document.getElementById('descripcion').value = restaurante.descripcion || '';
            document.getElementById('direccion').value = restaurante.direccion;
            
            clearErrors();
            document.getElementById('restauranteModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del restaurante');
    });
}

function closeModal() {
    document.getElementById('restauranteModal').classList.add('hidden');
    currentRestaurante = null;
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

document.getElementById('restauranteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = isEditing ? `/admin/restaurantes/${currentRestaurante}` : '/admin/restaurantes';
    const method = isEditing ? 'PUT' : 'POST';
    
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
            location.reload(); // Recargar la página para mostrar los cambios
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

function deleteRestaurante(restauranteId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este restaurante? Esta acción eliminará también al usuario propietario y no se puede deshacer.')) {
        return;
    }
    
    fetch(`/admin/restaurantes/${restauranteId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover la fila de la tabla
            const row = document.querySelector(`tr[data-id="${restauranteId}"]`);
            if (row) {
                row.remove();
            }
            
            // Mostrar mensaje de éxito
            const successDiv = document.createElement('div');
            successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
            successDiv.role = 'alert';
            successDiv.textContent = data.message;
            
            const container = document.querySelector('.container');
            const firstChild = container.children[1]; // Después del header
            container.insertBefore(successDiv, firstChild);
            
            // Remover el mensaje después de 5 segundos
            setTimeout(() => {
                successDiv.remove();
            }, 5000);
        } else {
            alert(data.message || 'Error al eliminar el restaurante');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('restauranteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection