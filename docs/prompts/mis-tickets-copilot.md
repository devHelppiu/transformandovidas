# Prompt para Copilot — Vista "Mis Tickets" (cliente)

## Objetivo

Alinear la vista de **Mis Tickets** del rol cliente con el prototipo de Figma:
`https://www.figma.com/design/0vQDTmFsq1rpRYcPiEcOMo/Transformando-vidas?node-id=25-2378&m=dev`
(node `25:2378`)

## Stack

Laravel 11, Blade, Tailwind CSS, Alpine.js.
Tokens de color ya definidos en `tailwind.config.js`:
`tv-blue` (#2227F5), `tv-blue-dark` (#000EBF), `tv-pink` (#E838BF),
`tv-pink-light` (#FC59CE), `tv-bg` (#F0F2FF), `tv-footer` (#222222).
Tipografías: `font-urbanist`, `font-montserrat`, `font-fira`.

## Archivos a modificar

1. `app/Http/Controllers/Cliente/TicketController.php` — método `index`.
2. `resources/views/cliente/tickets/index.blade.php` — vista completa.
3. `routes/web.php` — registrar rutas `cliente.tickets.index` y `cliente.tickets.show` dentro del grupo cliente (si aún no existen).

## ⚠️ Regla crítica de agrupación

En el Figma **cada card representa un sorteo en el que el usuario ha participado, no un ticket individual**. Es decir:

- Los números de ticket **se agrupan por sorteo dentro de una sola tarjeta**, mostrándose como "pills" (chips redondeados con fondo `tv-bg` y texto `tv-blue-dark`).
- **NO** debe haber una fila/tarjeta por cada número de ticket.
- Si un usuario tiene varias compras (`grupo_compra`) sobre el mismo sorteo, todas se consolidan en una sola card de ese sorteo: los números se concatenan, la cantidad y el total se suman.
- "Fecha de la compra" muestra la fecha de la compra más reciente para ese sorteo.
- "Valor del ticket" usa el `precio_ticket` del sorteo (o, si quieres ser estricto, el valor unitario más común entre los pagos del usuario para ese sorteo).

## Cambios en el controlador

Reemplazar el método `index` de `Cliente\TicketController` por una consulta agrupada **por `sorteo_id`**:

```php
public function index(Request $request)
{
    $userId = $request->user()->id;
    $search = trim((string) $request->input('q', ''));

    // Una fila por sorteo (cada card del prototipo = un sorteo)
    $sorteoIdsQuery = Ticket::query()
        ->where('user_id', $userId)
        ->select('sorteo_id')
        ->selectRaw('MAX(created_at) as ultima_compra_at')
        ->groupBy('sorteo_id')
        ->orderByDesc('ultima_compra_at');

    if ($search !== '') {
        $sorteoIdsQuery->whereHas('sorteo', fn ($q) =>
            $q->where('nombre', 'like', "%{$search}%")
        );
    }

    $sorteosPaginados = $sorteoIdsQuery->paginate(10)->withQueryString();

    // Cargar todos los tickets del usuario para esos sorteos
    $ticketsPorSorteo = Ticket::query()
        ->where('user_id', $userId)
        ->whereIn('sorteo_id', $sorteosPaginados->pluck('sorteo_id'))
        ->with(['sorteo', 'pago'])
        ->orderBy('numero')
        ->get()
        ->groupBy('sorteo_id');

    // Construir DTOs por sorteo, preservando el orden del paginador
    $compras = $sorteosPaginados->getCollection()->map(function ($row) use ($ticketsPorSorteo) {
        $tickets = $ticketsPorSorteo->get($row->sorteo_id) ?? collect();
        $first = $tickets->first();
        if (! $first) {
            return null;
        }

        $totalMonto = $tickets->sum(fn ($t) => (float) (optional($t->pago)->monto ?? 0));

        // Estado consolidado: "verificado" sólo si TODOS los pagos están verificados;
        // si hay al menos uno rechazado, "rechazado"; en cualquier otro caso, "pendiente".
        $estados = $tickets->pluck('pago.estado')->filter()->unique();
        $estadoPago = match (true) {
            $estados->contains('rechazado')                       => 'rechazado',
            $estados->count() === 1 && $estados->first() === 'verificado' => 'verificado',
            default                                               => 'pendiente',
        };

        return (object) [
            'sorteo'          => $first->sorteo,
            'fecha_compra'    => $tickets->max('created_at'), // compra más reciente
            'cantidad'        => $tickets->count(),
            'precio_unitario' => $first->sorteo->precio_ticket,
            'total'           => $totalMonto,
            'estado_pago'     => $estadoPago,
            'tickets'         => $tickets, // colección completa para los pills
        ];
    })->filter()->values();

    // Reemplazar la colección del paginador por los DTOs (mantiene total/links)
    $sorteosPaginados->setCollection($compras);

    return view('cliente.tickets.index', [
        'compras' => $sorteosPaginados,
        'search'  => $search,
    ]);
}
```

Imports requeridos en el controlador: `App\Models\Ticket`, `App\Models\Sorteo`, `Illuminate\Http\Request`.

## Cambios en las rutas

En `routes/web.php`, dentro del grupo `cliente.` con middleware `['auth', 'role:cliente']`:

```php
Route::get('/tickets',           [Cliente\TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/{ticket}',  [Cliente\TicketController::class, 'show'])->name('tickets.show');
```

## Cambios en la vista

`resources/views/cliente/tickets/index.blade.php` debe usar `<x-app-layout>`. Estructura:

1. **Header**: título `Mis tickets` (Urbanist ExtraBold uppercase, `text-[#2f2f2f]`, ~32px) a la izquierda y un buscador a la derecha.
   - Buscador: `form` GET a `cliente.tickets.index`, fondo `bg-tv-bg`, `rounded-3xl`, ancho `~272px`, contiene un `<input name="q">` con placeholder "Buscar por sorteo" y un ícono lupa a la derecha.

2. **Lista de cards** (gap vertical 14px). Una card por sorteo (DTO `$compra` del controlador):

   ```
   ┌─ article (bg-white, border border-[#e8ebff], rounded-2xl, px-6 py-4) ────────────┐
   │  ┌─ Imagen sorteo (118×118, rounded-2xl) ─┐  ┌─ Columna izq (~290px) ─┐  ┌─ Columna der (~288px) ─┐
   │  │  $compra->sorteo->imagen (Storage::url)│  │ Fecha del sorteo: dd/mm/aaaa │ Fecha de la compra: dd/mm/aaaa │
   │  │  fallback: placeholder con icono       │  │ NOMBRE DEL SORTEO (uppercase, extrabold, 20px) │ Valor del ticket    $X.XXX (bold, tv-blue-dark) │
   │  └─────────────────────────────────────── ┘  │ Premio  $X.XXX.XXX (tv-pink, bold) │ Cantidad adquirida   NX (bold, tv-blue-dark) │
   │                                              └────────────────────────┘  │ Total                $XX.XXX (bold, tv-blue-dark) │
   │                                                                          └─────────────────────────┘
   │  Badge estado pago (debajo de las dos columnas, alineado a la izquierda):
   │    - "Pago verificado": bg #93ff93 / texto #035c0c
   │    - "Pago pendiente":  bg #ffd693 / texto #b65a09
   │    - "Pago rechazado":  bg #ffd2d2 / texto #c01a1a
   │    Estilos comunes: rounded-lg, px-2 py-0.5, font-urbanist text-sm
   │
   │  ── separador <hr class="border-t border-[#e8ebff]"> ──
   │
   │  Sección "N° Tickets adquiridos":
   │    - Etiqueta (Urbanist 14px, text-[#2f2f2f])
   │    - Pills con flex-wrap gap-2:
   │        @foreach($compra->tickets as $ticket)
   │            <a href="{{ route('cliente.tickets.show', $ticket) }}"
   │               class="bg-tv-bg text-tv-blue-dark font-urbanist font-bold text-xl rounded-3xl px-6 py-2 hover:bg-tv-blue-dark hover:text-white transition-colors">
   │                {{ $ticket->numero }}
   │            </a>
   │        @endforeach
   └────────────────────────────────────────────────────────────────────────────────┘
   ```

   **Importante**: los números **se renderizan como pills dentro del mismo card del sorteo**. Si el usuario tiene 20 tickets en ese sorteo (sumando todas sus compras), aparecen los 20 pills en el mismo card, en una o varias filas (gracias a `flex-wrap`). Bajo ningún concepto se debe crear una fila/card distinta para cada número.

3. **Pie**: a la derecha, `Mostrando {{ $compras->count() }} de {{ $compras->total() }}` (Montserrat medium, 14px) y `{{ $compras->onEachSide(1)->links() }}`.

4. **Estado vacío**: si `$compras->count() === 0`, mostrar una card grande blanca con icono de cupón y mensaje:
   - Si `$search !== ''`: "No encontramos compras que coincidan con \"{{ $search }}\"."
   - Si no: "Aún no tienes tickets comprados."

## Detalles de tipografía y color (para máxima fidelidad)

- Título "Mis tickets": `font-urbanist font-extrabold uppercase text-[#2f2f2f]`, tamaño fluido `clamp(1.5rem, 3vw, 2rem)`.
- Texto "Fecha del sorteo / Fecha de la compra / Valor del ticket / Cantidad / Total / Premio (etiqueta) / N° Tickets adquiridos": `font-urbanist text-sm text-[#2f2f2f]`.
- Nombre del sorteo: `font-urbanist font-extrabold uppercase text-[#2f2f2f] text-xl leading-tight`.
- Valores numéricos del lado derecho ($X.XXX, NX, $XX.XXX): `font-urbanist font-bold text-tv-blue-dark` (Total a 16px, los demás 14px).
- Premio: `font-urbanist font-bold text-tv-pink text-base`.
- Pills: `bg-tv-bg text-tv-blue-dark font-urbanist font-bold text-xl rounded-3xl px-6 py-2`.

## Mapping de estados de pago (badge)

| `$compra->estado_pago` | bg          | text        | label              |
|------------------------|-------------|-------------|--------------------|
| `verificado`           | `#93ff93`   | `#035c0c`   | Pago verificado    |
| `pendiente`            | `#ffd693`   | `#b65a09`   | Pago pendiente     |
| `rechazado`            | `#ffd2d2`   | `#c01a1a`   | Pago rechazado     |

## Criterios de aceptación

1. La pantalla se ve igual al Figma node `25:2378` en desktop (≥1024px) y se adapta razonablemente en mobile (stack vertical de columnas, imagen arriba).
2. Cada **sorteo** del usuario aparece como un único card. Si tiene múltiples compras del mismo sorteo, los pills se concatenan en ese card (no se crea uno por compra).
3. **Ningún número de ticket aparece en una fila individual**. Todos los números viven como pills dentro del card de su sorteo.
4. El buscador filtra por nombre de sorteo (parámetro `q`) sin perder la paginación (`withQueryString`).
5. El badge muestra el estado consolidado correcto:
   - `verificado` solo si todos los pagos del sorteo están verificados.
   - `rechazado` si hay al menos un pago rechazado.
   - `pendiente` en cualquier otro caso.
6. La paginación funciona y muestra "Mostrando X de Y" + links de Laravel a la derecha.
7. Las rutas `cliente.tickets.index` y `cliente.tickets.show` están registradas; `navigation.blade.php` deja de romper para usuarios cliente.
8. Si el sorteo no tiene `imagen`, se muestra un placeholder de 118×118 (`bg-tv-bg` + icono cupón en `text-tv-blue/40`).
9. Click en cualquier pill lleva a `cliente.tickets.show` con el ticket correspondiente.
10. La vista no genera N+1 queries (los tickets de los sorteos paginados se cargan en una sola query con `with(['sorteo', 'pago'])`).

## Lo que NO debes hacer

- ❌ No instales nuevas dependencias.
- ❌ No reemplaces la tabla actual con otra tabla — usa cards.
- ❌ No agrupes por `grupo_compra` (el usuario quiere ver los números agrupados por sorteo, sumando todas sus compras del mismo sorteo).
- ❌ No muestres una fila/card por número de ticket — los números van como pills dentro del card del sorteo.
- ❌ No toques otras vistas (`historial`, `show`, `welcome`, etc.).
