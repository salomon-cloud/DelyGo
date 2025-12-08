@component('mail::message')
# 📦 Actualización de tu Orden #{{ $orden->id }}

Hola {{ $cliente->name }},

Tu orden en **{{ $restaurante->nombre }}** ha cambiado de estado.

## Estado Actual: {{ strtoupper($orden->estado) }}

@if($orden->estado === 'recibida')
✅ Hemos recibido tu orden correctamente. El restaurante la está revisando.

**Detalles de la orden:**
- **Restaurante:** {{ $restaurante->nombre }}
- **Total:** ${{ number_format($orden->total, 2) }}
- **Dirección de entrega:** {{ $orden->direccion_entrega }}

Recibirás actualizaciones conforme avance tu pedido.

@elseif($orden->estado === 'preparando')
👨‍🍳 Tu orden se está preparando en la cocina. ¡Casi lista!

**Tiempo estimado:** Los chefs están trabajando en tu pedido.

@elseif($orden->estado === 'en_camino')
🚚 ¡Tu orden está en camino!

Tu repartidor está en la vía para entregarte tu pedido. 

**Dirección de entrega:** {{ $orden->direccion_entrega }}

@elseif($orden->estado === 'entregada')
✨ ¡Tu orden ha sido entregada!

Esperamos que disfrutes tu comida. 

**¿Cómo fue tu experiencia?** Puedes calificar tu pedido en nuestra app.

@elseif($orden->estado === 'cancelada')
❌ Tu orden ha sido cancelada.

Si esto fue un error, por favor contacta con nuestro equipo de soporte.

@endif

---

**Datos de la Orden:**
- Orden ID: #{{ $orden->id }}
- Estado: {{ ucfirst($orden->estado) }}
- Fecha: {{ $orden->created_at->format('d/m/Y H:i') }}

Con cariño,  
**El equipo de DelyGo** 🚀

@endcomponent
