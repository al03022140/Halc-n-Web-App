# Halcón WebApp

Pequeña descripción

Halcón WebApp es una aplicación interna para gestionar pedidos, inventario y el flujo entre Ventas, Almacén, Rutas y Compras. Permite crear órdenes, registrar faltantes desde Almacén que generen solicitudes de compra, y seguir el estado de cada pedido desde preparación hasta entrega (la entrega final la registra el equipo de Ruta).

Objetivo de este README

- Proveer una descripción breve del proyecto.
- Incluir credenciales de prueba para validar comportamientos de roles (excluyendo la cuenta de josue).
- Indicar comandos útiles para levantar la aplicación localmente y crear cuentas si faltan.

Credenciales de prueba (no incluyas la cuenta de josue)

- Admin
  - Email: `admin@halcon.com`
  - Contraseña: `admin123`
  - Rol: `Admin`

- Sales (ejemplo de cuenta prueba)
  - Email: `sales@halcon.com`
  - Contraseña: `sales123`
  - Rol: `Sales`

- Purchasing
  - Email: `purchasing@halcon.com`
  - Contraseña: `purchasing123`
  - Rol: `Purchasing`

- Warehouse
  - Email: `warehouse@halcon.com`
  - Contraseña: `warehouse123`
  - Rol: `Warehouse`

- Route
  - Email: `route@halcon.com`
  - Contraseña: `route123`
  - Rol: `Route`

Notas importantes

- El `UserSeeder` incluido crea por defecto la cuenta `admin@halcon.com` con contraseña `admin123`.
- Si alguna de las cuentas anteriores no existe en tu base de datos, puedes crearla manualmente (instrucciones abajo) o mediante un seeder/tinker.
- No incluyo la cuenta de `josue` por petición.

Comandos útiles (PowerShell / Windows)

- Ejecutar migraciones y seeders básicos:

```powershell
php artisan migrate --seed
```

- Ejecutar un seeder específico (por ejemplo, si tienes un `RouteUserSeeder`):

```powershell
php artisan db:seed --class=RouteUserSeeder
```

- Crear un usuario rápido desde Tinker (ejemplo):

```powershell
php artisan tinker
>>> use App\\Models\\User; use Illuminate\\Support\\Facades\\Hash; use App\\Models\\Role; $r = Role::where('name','Sales')->first(); User::create(['name'=>'Usuario Sales','email'=>'sales@halcon.com','password'=>Hash::make('sales123'),'role_id'=>$r->id]);
```

- Arrancar servidor de desarrollo:

```powershell
php artisan serve
```

Recomendaciones de seguridad y pruebas

- Cambia las contraseñas de las cuentas de prueba al desplegar en cualquier entorno sensible.
- Para pruebas de roles, inicia sesión con cada cuenta y valida las vistas/acciones permitidas (p. ej. `Warehouse` no debe marcar `Delivered`).

Contacto y notas finales

Si quieres, puedo añadir también: un seeder que cree todas estas cuentas automáticamente (excepto la de josue), o un script SQL listo para ejecutar. ¿Lo quieres ahora?
