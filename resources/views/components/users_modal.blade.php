<div id="users-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg w-11/12 max-w-3xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Usuarios registrados</h2>
            <button id="users-modal-close" class="text-gray-600">✕</button>
        </div>

        <div id="users-modal-content">
            <table class="w-full table-auto">
                <thead>
                    <tr class="text-left">
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="users-modal-rows">
                    <!-- Filas inyectadas por JS -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <button id="users-modal-close-2" class="px-4 py-2 bg-gray-200 rounded">Cerrar</button>
        </div>
    </div>
</div>
