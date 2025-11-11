document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('open-users-modal');
    const modal = document.getElementById('users-modal');
    const closeBtn = document.getElementById('users-modal-close');
    const closeBtn2 = document.getElementById('users-modal-close-2');
    const rowsContainer = document.getElementById('users-modal-rows');

    const roles = ['cliente','restaurante','repartidor','admin'];

    function csrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function showModal() {
        modal.classList.remove('hidden');
        loadUsers();
    }

    function hideModal() {
        modal.classList.add('hidden');
    }

    function loadUsers() {
        fetch('/admin/api/users', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                rowsContainer.innerHTML = '';
                data.forEach(user => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="py-2">${escapeHtml(user.name)}</td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>
                            <select data-user-id="${user.id}" class="role-select border rounded px-2 py-1">
                                ${roles.map(r => `<option value="${r}" ${r===user.rol? 'selected' : ''}>${r}</option>`).join('')}
                            </select>
                        </td>
                        <td>
                            <button class="save-role px-3 py-1 bg-green-600 text-white rounded" data-user-id="${user.id}">Guardar</button>
                            <button class="delete-user px-3 py-1 bg-red-600 text-white rounded ml-2" data-user-id="${user.id}">Eliminar</button>
                        </td>
                    `;
                    rowsContainer.appendChild(tr);
                });

                // attach handlers
                document.querySelectorAll('.save-role').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.userId;
                        const select = document.querySelector(`select[data-user-id="${id}"]`);
                        const rol = select.value;
                        fetch(`/admin/api/users/${id}/role`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken(),
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ rol }),
                        }).then(r => r.json())
                          .then(resp => {
                              if (resp.success) {
                                  alert('Rol actualizado.');
                              } else if (resp.error) {
                                  alert('Error: ' + resp.error);
                              } else {
                                  alert('Respuesta: ' + JSON.stringify(resp));
                              }
                          }).catch(e => alert('Error de red'));
                    });
                });

                document.querySelectorAll('.delete-user').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.userId;
                        if (!confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')) return;
                        fetch(`/admin/api/users/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken(),
                            },
                            credentials: 'same-origin'
                        }).then(r => r.json())
                          .then(resp => {
                              if (resp.success) {
                                  alert('Usuario eliminado');
                                  loadUsers();
                              } else {
                                  alert('Error: ' + (resp.error || JSON.stringify(resp)));
                              }
                          }).catch(e => alert('Error de red'));
                    });
                });
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c];
        });
    }

    if (openBtn) openBtn.addEventListener('click', showModal);
    if (closeBtn) closeBtn.addEventListener('click', hideModal);
    if (closeBtn2) closeBtn2.addEventListener('click', hideModal);
});
