# Ajustes pendientes — Transformando Vidas vs Figma

Este documento agrupa todos los ajustes que faltan para alinear el código con el prototipo de Figma. Está pensado para entregarse a Copilot tarea por tarea (en el orden propuesto al final), no necesariamente todo de una sola vez.

## Contexto compartido

**Stack:** Laravel 11, Blade, Tailwind CSS, Alpine.js. Autenticación Breeze.

**Figma:** `https://www.figma.com/design/0vQDTmFsq1rpRYcPiEcOMo/Transformando-vidas`

**Tokens de color (`tailwind.config.js`):**
- `tv-blue` (#2227F5), `tv-blue-dark` (#000EBF)
- `tv-pink` (#E838BF), `tv-pink-light` (#FC59CE)
- `tv-bg` (#F0F2FF), `tv-footer` (#222222)

**Tipografías:** `font-urbanist`, `font-montserrat`, `font-fira`.

**Reglas globales:**
- No instalar nuevas dependencias.
- Mantener convenciones Laravel (`x-component`, route names existentes).
- No romper vistas/rutas que no estén en el alcance del ajuste.
- Los comentarios y nombres de variables en español, igual que el resto del repo.

---

## Ajuste 1 · Mis Tickets — agrupación por sorteo y estado consolidado

**Figma:** node `25:2378`.

**Estado actual:** `Cliente\TicketController@index` agrupa por `grupo_compra`, así que un usuario que compre 3 veces el mismo sorteo ve 3 cards. La vista (`cliente/tickets/index.blade.php`) ya tiene el layout visual correcto pero recibe los datos mal agrupados.

**Resultado esperado:** una sola card por sorteo en el que el usuario haya participado, con todos los números como pills dentro de esa card (sumados a través de todas sus compras del mismo sorteo).

### Archivos
- `app/Http/Controllers/Cliente/TicketController.php` — método `index`.
- `resources/views/cliente/tickets/index.blade.php` — solo si hay diferencias con el contrato del DTO.

### Cambios en el controlador

Reemplazar el método `index` por:

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

        // Estado consolidado:
        //   - "rechazado" si al menos un pago fue rechazado
        //   - "verificado" sólo si TODOS los pagos están verificados
        //   - "pendiente" en cualquier otro caso
        $estados = $tickets->pluck('pago.estado')->filter();
        $estadoPago = match (true) {
            $estados->contains('rechazado')                                  => 'rechazado',
            $estados->isNotEmpty() && $estados->every(fn ($e) => $e === 'verificado') => 'verificado',
            default                                                          => 'pendiente',
        };

        return (object) [
            'sorteo'          => $first->sorteo,
            'fecha_compra'    => $tickets->max('created_at'), // compra más reciente
            'cantidad'        => $tickets->count(),
            'precio_unitario' => $first->sorteo->precio_ticket,
            'total'           => $totalMonto,
            'estado_pago'     => $estadoPago,
            'tickets'         => $tickets, // colección completa para los pills (numero + link)
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

Imports requeridos: `App\Models\Ticket`, `App\Models\Sorteo`, `Illuminate\Http\Request`.

### Contrato del DTO `$compra` (lo que la vista espera)

| Campo               | Tipo                       | Notas                                                  |
|---------------------|----------------------------|--------------------------------------------------------|
| `sorteo`            | `App\Models\Sorteo`        | Con `imagen`, `nombre`, `fecha_sorteo`, `valor_premio`, `precio_ticket`. |
| `fecha_compra`      | `Carbon`                   | Fecha de la compra más reciente.                       |
| `cantidad`          | `int`                      | Total de tickets del usuario en ese sorteo.            |
| `precio_unitario`   | `decimal`                  | `sorteo->precio_ticket`.                               |
| `total`             | `decimal`                  | Suma de `pago->monto` de todos los tickets.            |
| `estado_pago`       | `'verificado'\|'pendiente'\|'rechazado'` | Estado consolidado.                       |
| `tickets`           | `Collection<Ticket>`       | Para los pills.                                        |

### Criterios de aceptación

1. Cada sorteo del usuario aparece en **una única card**, sin importar cuántas compras (`grupo_compra`) tenga sobre él.
2. Todos los números del usuario en ese sorteo aparecen como pills dentro de esa card. **Ningún número se renderiza como fila individual.**
3. Si todos los pagos están verificados → badge "Pago verificado". Si alguno está rechazado → "Pago rechazado". En cualquier otro caso → "Pago pendiente".
4. La fecha de la compra mostrada es la más reciente entre todas las compras del usuario sobre ese sorteo.
5. La cantidad y el total son la suma agregada a través de las compras.
6. El buscador filtra por `q` (nombre de sorteo) sin perder paginación (`withQueryString`).
7. No hay N+1 queries: una sola query carga todos los tickets de los sorteos paginados con `with(['sorteo','pago'])`.

---

## Ajuste 2 · Layout cliente con navbar al estilo Figma

**Figma:** node `25:2378` (header) y nodes equivalentes en otras pantallas del rol cliente.

**Estado actual:** las vistas del cliente (`cliente/tickets/index.blade.php`, `cliente/dashboard.blade.php`, `cliente/historial.blade.php`, `cliente/tickets/show.blade.php`) usan `<x-app-layout>`, que carga el navbar Breeze por defecto (5 links + dropdown de perfil). El Figma muestra un header minimalista: logo a la izquierda y a la derecha un "pill" bordeado con ícono de usuario y nombre.

**Resultado esperado:** un layout dedicado para el rol cliente que respete el header del Figma, sin afectar a admin ni comercial.

### Archivos
- **Crear** `resources/views/components/cliente-layout.blade.php`.
- **Crear** `resources/views/components/cliente-navbar.blade.php` (o inlineado en el layout, a tu criterio).
- **Migrar** `resources/views/cliente/tickets/index.blade.php` para usar `<x-cliente-layout>` en lugar de `<x-app-layout>`.
- **Migrar** `resources/views/cliente/dashboard.blade.php` ídem.
- **Migrar** `resources/views/cliente/historial.blade.php` ídem.
- **Migrar** `resources/views/cliente/tickets/show.blade.php` ídem (si existe).

### Spec del header del cliente

```
┌─ <header> bg-white border-b border-[#e8ebff] sticky top-0 z-40 ─────────────┐
│   max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20                                │
│                                                                              │
│   [Logo TV, 147x58, link a /cliente/dashboard]   ……    [Pill usuario]        │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

**Pill del usuario** (clickable, abre dropdown con "Perfil" y "Cerrar sesión"):

```html
<div class="border-2 border-[#e8ebff] rounded-lg flex items-center px-2">
    <button class="flex items-center gap-2.5 px-2.5 py-2.5">
        {{-- Ícono circle-user (Heroicons o lucide), 16x16, text-tv-blue-dark --}}
        <svg class="w-4 h-4 text-tv-blue-dark" ... ></svg>
        <span class="font-montserrat font-medium text-base text-black whitespace-nowrap">
            {{ Auth::user()->name }}
        </span>
    </button>
</div>
```

El dropdown puede usar Alpine (`x-data="{ open: false }"`) y el component `<x-dropdown>` ya existente de Breeze, conservando los items "Perfil" → `route('profile.edit')` y "Cerrar Sesión" (form POST a `route('logout')`).

### Layout

`resources/views/components/cliente-layout.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Transformando Vidas') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=urbanist:400,500,600,700,800|montserrat:400,500,600,700|fira-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="font-urbanist antialiased bg-[#f9fafb] min-h-screen">
    <x-cliente-navbar />

    @isset($header)
        <header class="bg-white">
            <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <main>
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
```

### Migración de las vistas

Reemplazar en cada vista del cliente:

```diff
- <x-app-layout>
+ <x-cliente-layout>
      ...
- </x-app-layout>
+ </x-cliente-layout>
```

### Criterios de aceptación

1. Las vistas del rol cliente muestran el header del Figma (logo + pill de usuario), no el navbar Breeze.
2. El pill abre un dropdown con "Perfil" y "Cerrar sesión" funcionales.
3. Las vistas de admin y comercial **no se ven afectadas** (siguen con `<x-app-layout>`).
4. La rama `@auth → !user.isCliente` no se rompe; cualquier vista compartida sigue resolviéndose por su layout original.
5. El logo del header lleva a `route('cliente.dashboard')`.
6. El pill respeta el ancho del contenido (no se estira) y se mantiene a la derecha del header tanto en mobile como en desktop.

---

## Ajuste 3 · Welcome — sufijo de premio configurable (opcional)

**Figma:** node `6:1956` (premio "$100.000 + Camioneta").

**Estado actual:** la card de un solo sorteo activo en `welcome.blade.php` muestra `Premio $X.XXX` puro. El modelo `Sorteo` no tiene un campo para el sufijo descriptivo (la "+ Camioneta" del prototipo).

**Resultado esperado:** poder configurar desde el admin un texto opcional que se concatena al monto del premio, p. ej. "+ Camioneta", "+ TV 55\"", o vacío.

> Solo aplicar si producto/diseño confirma que se quiere ese sufijo configurable. Si no, dejar este ajuste fuera del alcance.

### Archivos
- `database/migrations/{timestamp}_add_premio_extra_to_sorteos_table.php` (nueva).
- `app/Models/Sorteo.php` — agregar al `$fillable`.
- `app/Http/Requests/StoreSorteoRequest.php` y `UpdateSorteoRequest.php` (si existen) — agregar validación.
- `resources/views/admin/sorteos/create.blade.php` — agregar input.
- `resources/views/admin/sorteos/edit.blade.php` — agregar input.
- `resources/views/welcome.blade.php` — render condicional.

### Migración

```php
Schema::table('sorteos', function (Blueprint $table) {
    $table->string('premio_extra', 80)->nullable()->after('valor_premio');
});
```

### Modelo (`app/Models/Sorteo.php`)

Agregar `'premio_extra'` al array `$fillable`.

### Form admin

Input opcional debajo de `valor_premio`:

```blade
<x-input-label for="premio_extra" value="Premio extra (opcional)" />
<x-text-input id="premio_extra"
              name="premio_extra"
              type="text"
              maxlength="80"
              placeholder='Ej: "+ Camioneta"'
              :value="old('premio_extra', $sorteo->premio_extra ?? '')"
              class="block w-full mt-1" />
<p class="text-xs text-gray-500 mt-1">
    Texto corto que aparecerá junto al monto del premio en la home (p. ej. "+ Camioneta").
</p>
<x-input-error :messages="$errors->get('premio_extra')" class="mt-2" />
```

Validación en los Form Requests:

```php
'premio_extra' => ['nullable', 'string', 'max:80'],
```

### Render en `welcome.blade.php`

Dentro de la rama "1 sorteo activo", reemplazar el banner de premio por:

```blade
<div class="bg-[#f0f2ff] rounded-3xl flex items-center justify-center gap-2.5 py-4 px-4">
    <span class="font-urbanist text-[#2f2f2f] text-sm">Premio</span>
    <span class="font-urbanist font-bold text-tv-pink text-2xl leading-none">
        ${{ number_format($sorteo->valor_premio ?? 0, 0, ',', '.') }}@if(!empty($sorteo->premio_extra)) {{ $sorteo->premio_extra }}@endif
    </span>
</div>
```

(El sufijo se concatena con un espacio, dentro del mismo `<span>` rosa, exactamente como en el Figma.)

Aplicar el mismo render en la rama de "más de un sorteo" si se quiere mantener consistencia.

### Criterios de aceptación

1. La columna `premio_extra` existe en `sorteos` y permite null.
2. El admin puede setear y editar el texto opcional desde los formularios de crear/editar sorteo.
3. Si `premio_extra` es null o vacío, el banner muestra solo el monto.
4. Si tiene valor, se concatena exactamente como en el Figma (un espacio + el texto + sin saltos de línea).
5. El monto sigue formateado con separador de miles `.` y sin decimales.

---

## Orden recomendado para entregar a Copilot

1. **Ajuste 1** primero — es el más importante porque corrige la lógica de datos.
2. **Ajuste 2** después — puramente visual; ya con los datos correctos, queda bien presentado.
3. **Ajuste 3** al final, **solo si producto confirma** que el sufijo se quiere configurable. Si no, omitir.

Cada ajuste es independiente: se puede pausar entre ellos para verificar en QA antes de continuar.
