# Proyecto Sistemas - Gestión de Horas para Barbería

## Descripción

Esta aplicación web está diseñada para mejorar la gestión de reservas en barberías. Facilita tanto a clientes como a barberos la organización de citas. A continuación, se detallan las principales páginas y funcionalidades:

### Páginas y Funcionalidades

1. *Index (index.html)*: 
   - Menú principal de la barbería.
   - Acceso a la página de reservas para clientes.
   - Información sobre los barberos.

2. *Reservas (horareser.php)*: 
   - Selección de barbero.
   - Elección de hora disponible.
   - Completar datos del cliente y seleccionar el servicio a reservar.

3. *Acceso Barbero*:
   - Formulario de acceso (`login.html`).
   - Autenticación a través de `authenticate.php`.
   - Redirección a `perfil.php` si la autenticación es exitosa.
   - Importante: Desde PHP 5.5.0 se utiliza `password_verify` para la seguridad de las credenciales.

    php
    password_verify($_POST['password'], $password)
    

4. *Perfil Barbero (perfil.php)*: 
   - Información detallada del barbero.
   - Acceso al panel de administración (`datos.php`).
   - Gestión de horas disponibles (`horadisp.php`).
   - Visualización de reservas realizadas (`horarealizada.php`).

5. *Horas Disponibles (horadisp.php)*:
   - Adición o eliminación de horas disponibles para citas.

6. *Gestión de Reservas (horarealizada.php)*:
   - Visualización de clientes con horas reservadas.
   - Confirmación de asistencia a las citas.

7. *Datos de la Barbería (datos.php)*:
   - Visualización de clientes atendidos.

8. *Cerrar Sesión (logout.php)*:
   - Permite al barbero cerrar su sesión.

> *Nota*: Falta implementar un sistema de alertas de reservas y eventos vía correo electrónico. Esta configuración se realiza durante la instalación del código en el servidor. Se recomienda revisar regularmente las actualizaciones del sistema para obtener nuevas características y mejoras.

    php
    mail(
        string $to,
        string $subject,
        string $message,
        string $additional_headers = ?,
        string $additional_parameters = ?
    ): bool
    

## Instalación

1. Ejecutar `ProyectoSchema.sql` en phpMyAdmin del servidor.
2. En los diferentes archivos editar los datos para dar acceso a la base de datos.
3. Colocar todos los archivos en la carpeta 'Public' del servidor.
