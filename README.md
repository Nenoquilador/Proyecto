🔐 Matriz de Credenciales y Accesos - Proyecto UJS

Este documento centraliza las identidades de acceso para el Sistema de Bolsa de Trabajo y Servicios Escolares. Su propósito es facilitar las pruebas de integración y la validación de flujos de trabajo por parte del equipo de QA y desarrolladores.

[!WARNING]
POLÍTICA DE PRIVACIDAD: Los datos aquí contenidos son sintéticos. Está estrictamente prohibido el uso de credenciales de producción en este archivo o el commit de datos sensibles reales al repositorio.

🏛️ 1. Cuentas Administrativas (Staff)

El acceso administrativo está segmentado bajo un modelo de Control de Acceso Basado en Roles (RBAC) para garantizar el principio de menor privilegio.

Perfil de Usuario

Identificador (Email)

Contraseña

Alcance del Rol

Admin. Vinculación

admin@ujsierra.com.mx

password

Gestión de convenios, vacantes y convenios empresariales.

Servicios Escolares

escolares@ujsierra.com.mx

password

Auditoría de expedientes, kárdex y validación de alumnos.

🏢 2. Acceso para Entidades Externas (Empresas)

Perfiles destinados a la publicación de ofertas laborales y seguimiento de candidatos.

Empresa

Cuenta de Usuario

Contraseña

Tech Solutions Innova

techsolutions.innova@ejemplo.com

123456

🎓 3. Acceso para Usuarios Finales (Alumnos)

Cuentas de prueba para validar la experiencia de usuario, carga de CV y postulaciones.

Nombre del Alumno

Correo Institucional

Contraseña

Ernesto Gómez

ernestogomez@ujsierra.com.mx

neto0427

🛠️ Stack Tecnológico de Seguridad

La arquitectura de autenticación se basa en los siguientes estándares de la industria:

Motor de Backend: PHP 8.x (Arquitectura MVC).

Capa de Datos: MySQL/MariaDB con abstracción PDO y sentencias preparadas contra inyección SQL.

Protocolo de Hash: Implementación de password_hash() utilizando el algoritmo BCRYPT (Costo 10).

Gestión de Sesiones: Tokens de sesión seguros con regeneración de ID en login.

📋 Directrices de Implementación

Encriptación en DB: Este archivo muestra las contraseñas en texto plano para facilitar el desarrollo, pero el script de migración SQL debe insertar los hashes correspondientes.

Exclusión de Repositorio: Asegúrese de que este archivo esté referenciado en su .gitignore antes de realizar el despliegue a entornos de pre-producción.

Vigencia: Estas credenciales se mantienen activas para el ciclo de desarrollo actual.

UJS - Universidad Justo Sierra Coordinación de Tecnologías de la Información Última actualización: 10 de mayo de 2026
