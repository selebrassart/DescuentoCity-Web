Descuento City - Sistema de Gestion de Promociones

Descuento City es una plataforma web dinamica diseñada para la administracion y difusion de beneficios, promociones y novedades dentro de un centro comercial. El sistema optimiza el flujo de publicacion de descuentos para los comercios, mejora la visibilidad de los locales frente a los clientes y ofrece una experiencia segmentada para los usuarios.

El proyecto fue desarrollado bajo una arquitectura estructurada empleando PHP para la logica del lado del servidor, y tecnologias de frontend estructuradas con Bootstrap y CSS para garantizar una interfaz adaptable y accesible.

---

# Modulos y Roles de Usuario

El sistema cuenta con control de accesos y permisos diferenciados segun el tipo de usuario:

*   Administrador: Responsable del alta, baja y modificacion de los locales comerciales. Valida el registro de los dueños de locales, modera (aprueba o rechaza) las propuestas de promociones, gestiona el modulo de novedades institucionales y accede a la generacion de reportes estadisticos.
*   Dueño de Local: Dispone de un panel de control para la creacion y eliminacion de promociones vigentes en su comercio, el control de la recepcion de solicitudes de clientes y la visualizacion de metricas sobre el alcance de sus descuentos.
*   Cliente Registrado: Accede a beneficios segmentados segun su categoria asignada (Inicial, Medium o Premium) y cuenta con la posibilidad de registrar transacciones para validar y canjear cupones de descuento.
*   Usuario No Registrado: Perfil publico con permisos de lectura para visualizar el catalogo de promociones y las novedades generales del shopping, sin capacidad para aplicar canjes.

---

# Tecnologias Utilizadas

*   Backend: PHP 
*   Frontend: HTML5, CSS3 (Estructuracion responsive mediante Flexbox) y Bootstrap
*   Base de Datos: MySQL
*   Control de Versiones: Git

---

# Analisis y Planificacion del Sistema

La implementacion de esta plataforma web se baso en un proceso formal de relevamiento y modelado que incluye:
*   Especificacion de Requerimientos: Definicion detallada de las necesidades de informacion de cada tipo de audiencia.
*   Arquitectura de Informacion: Estructuracion del mapa del sitio funcional y elaboracion de diagramas de flujo transaccionales (como el inicio de sesion y la solicitud de promociones).
*   Diseño de Experiencia de Usuario (UX/UI): Diseños preliminares y bocetos adaptados para dispositivos de escritorio y moviles.
*   Modelo de Datos: Diseño fisico de la base de datos relacional para dar soporte a la consistencia de locales, usuarios, promociones y reportes.

---

# Para Despliegue Local:

Para ejecutar el proyecto en un entorno de desarrollo local, se requieren herramientas de servidor local de PHP y MySQL (como XAMPP, Laragon o WampServer).

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/selebrassart/DescuentoCity-Web.git


# Vista Previa del Sitio

A continuacion ajunto capturas de pantalla del diseño responsive y parte de la interfaz del sistema:

### Vista de Promociones (General)
![Vista de Promociones](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/Administrador-promociones.png)

### Control de Acceso (Inicio de Sesion)
![Inicio de Sesion](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/iniciar-sesion.png)

### Modulo del Administrador: Gestion de Locales y Creacion
![Gestion de Locales](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/Administrador-crear-local.png)

### Modulo del Administrador: Gestion de Locales
![Gestion de Locales](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/pagina-locales.png)

### Modulo del Administrador: Solicitudes de Registro de Dueños
![Solicitudes de Dueños](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/solicitudes-dueños.png)

### Modulo del Administrador: Aprobacion de Promociones
![Solicitudes de Promociones](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/pagina-promociones.png)

### Modulo del Administrador: Estadisticas y Reportes de Uso
![Reportes de Administrador](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/Administrador-reportes.png)

### Formulario de Contacto

![Formulario de Contacto](https://raw.githubusercontent.com/selebrassart/img-vista-previa-descuentocity/main/formulario-contacto.png)

---
