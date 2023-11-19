# Sistema de Proyecto - Barbería

Esta aplicación web facilita la gestión de reservas para una barbería. A continuación, se describen las principales páginas y funcionalidades del sistema:

1. **`index.html`**: Menú principal de la barbería. Desde aquí, los usuarios pueden acceder a la página de reservas para clientes y a la sección de información sobre los barberos.

2. **`horareser.php`**: Página donde el usuario puede seleccionar un barbero, hacer clic en una hora disponible, completar sus datos y elegir el servicio que desea reservar.

3. **Acceso a Barbero**: Lleva al formulario de acceso (`login.html`), donde se ingresan las credenciales. El script `authenticate.php` verifica la autenticación y redirige a `perfil.php` si es exitosa.

4. **`perfil.php`**: Proporciona información del barbero y, si tiene permisos de administrador, permite el acceso al tablero de administrador (pendiente por implementar).

5. **Horas Disponibles (`horadisp.php`)**: Desde el perfil del barbero, se puede acceder a esta página para agregar o eliminar las horas disponibles para citas.

6. **`logout.php`**: Permite cerrar la sesión del perfil del barbero.

> **Nota**: La funcionalidad del tablero de administrador está pendiente de implementación. Se recomienda revisar regularmente las actualizaciones del sistema para obtener nuevas características y mejoras.
