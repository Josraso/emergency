# 🛡️ PrestaShop Emergency Admin Tool

> **Herramienta profesional de administración de emergencia para PrestaShop**

Una solución completa y profesional para resolver problemas críticos en tiendas PrestaShop cuando el acceso normal al panel de administración no está disponible o cuando necesitas realizar tareas de mantenimiento urgentes.

## 📋 Características Principales

### 🔐 **Sistema de Autenticación Seguro**
- Login protegido por contraseña configurable
- Sesiones con timeout automático (1 hora por defecto)
- Interfaz moderna y responsive

### 👥 **Gestión de Administradores**
- Lista completa de todos los administradores registrados
- Cambio de contraseñas con modal profesional
- Información detallada: estado, último acceso, etc.
- Fechas en formato español (dd/mm/yyyy HH:mm:ss)

### 🗑️ **Gestión Avanzada de Cache**
- Limpieza selectiva (Smarty, CacheFS, Var Cache)
- Análisis del tamaño y número de archivos
- Limpieza completa con un clic
- Limpieza de cache de base de datos

### 🐛 **Configuración Completa de Debug**
- Activación/desactivación del modo desarrollo
- Control del profiler de debug
- Desactivación de overrides para debugging
- Forzar compilación de templates Smarty

### 🔧 **Modo Mantenimiento**
- Activación/desactivación con indicadores visuales
- Gestión de IPs permitidas
- Control visual del estado actual

### 📁 **Gestión de Permisos**
- Verificación automática de permisos de directorios
- Reparación automática de permisos incorrectos
- Tabla detallada con estado visual
- Reparación individual por directorio

### 📂 **Detección y Gestión de Carpeta Admin**
- Detección automática inteligente de la carpeta admin
- Múltiples métodos de detección (constantes, archivos específicos, patrones)
- Renombrado seguro de la carpeta admin
- Acceso directo al panel de administración

### 💾 **Análisis de Espacio en Disco**
- Análisis completo del uso de disco
- Gráficos visuales con barras de progreso
- Top 5 directorios más pesados
- Distribución porcentual del espacio
- Contador de archivos por directorio

### ℹ️ **Información Completa del Sistema**
- Información detallada de PrestaShop (versión, configuración)
- Estado de PHP (versión, memoria, límites)
- Información de base de datos (servidor, tamaño, versión MySQL)
- Información del servidor (OS, software, IPs)
- Verificación de extensiones PHP críticas

### 🚀 **Funcionalidades Adicionales**
- **Dashboard centralizado** con estado del sistema
- **Acciones rápidas** desde la página principal
- **Auto-eliminación segura** del archivo
- **Diseño responsive** para dispositivos móviles
- **Indicadores visuales** de estado en tiempo real

## 🚀 Instalación y Uso

### 1. **Configuración Inicial**
```php
// Editar la línea 11 del archivo
$CONFIG = [
    'password' => 'TuPasswordSeguro123!', // ⚠️ CAMBIAR OBLIGATORIAMENTE
    'session_timeout' => 3600,             // 1 hora
    'app_name' => 'PS Emergency Admin',
    'version' => '2.0'
];
```

### 2. **Instalación**
1. Descarga el archivo `emergency.php`
2. Configura una contraseña segura en la línea 11
3. Sube el archivo a la **raíz** de tu instalación de PrestaShop
4. Accede via: `https://tudominio.com/emergency.php`

### 3. **Uso**
1. Introduce tu contraseña de emergencia
2. Navega por las diferentes secciones usando el menú superior
3. Realiza las tareas necesarias
4. **¡ELIMINA el archivo** después del uso usando el botón de auto-eliminación

## 🔧 Casos de Uso Comunes

### 🔒 **Recuperación de Acceso**
- Has olvidado la contraseña del administrador
- La carpeta admin ha sido renombrada y no recuerdas el nombre
- Necesitas crear un nuevo administrador de emergencia

### 🐛 **Debugging y Desarrollo**
- Activar modo debug para ver errores
- Desactivar overrides que causan problemas
- Limpiar cache que está causando comportamiento extraño
- Verificar configuración del sistema

### 🔧 **Mantenimiento de Emergencia**
- Poner la tienda en modo mantenimiento urgentemente
- Reparar permisos de archivos después de un problema
- Limpiar logs y cache para liberar espacio
- Verificar el estado general del sistema

### 💾 **Análisis de Problemas**
- Identificar qué directorios están ocupando más espacio
- Verificar extensiones PHP faltantes
- Analizar configuración de memoria y límites
- Revisar información de base de datos

## ⚠️ Consideraciones de Seguridad

### 🔐 **Antes del Uso**
- **SIEMPRE** cambia la contraseña por defecto
- Usa una contraseña fuerte y única
- Solo úsalo cuando sea absolutamente necesario

### 🗑️ **Después del Uso**
- **ELIMINA** inmediatamente el archivo usando el botón de auto-eliminación
- O elimínalo manualmente del servidor
- Nunca dejes este archivo en producción

### 🛡️ **Buenas Prácticas**
- Úsalo solo en emergencias reales
- No compartas la contraseña
- Úsalo solo desde conexiones seguras (HTTPS)
- Mantén logs de cuándo y por qué lo usas

## 📖 Navegación y Funciones

### 📊 **Dashboard**
- Estado general del sistema
- Acciones rápidas más utilizadas
- Botón de auto-eliminación del archivo

### 👥 **Administradores**
- Lista de todos los administradores
- Cambio de contraseñas individual
- Información de último acceso

### 🗑️ **Cache**
- Estado actual de todos los tipos de cache
- Limpieza selectiva o completa
- Información de tamaño y archivos

### 🐛 **Debug**
- Configuración completa del modo desarrollo
- Control de profiler y overrides
- Forzar compilación de templates

### 🔧 **Mantenimiento**
- Control visual del modo mantenimiento
- Gestión de IPs permitidas

### 📁 **Permisos**
- Verificación automática de permisos
- Reparación automática o individual
- Estado visual de cada directorio

### 📂 **Carpeta Admin**
- Detección automática inteligente
- Renombrado seguro
- Acceso directo al panel

### 💾 **Espacio en Disco**
- Análisis visual del uso de espacio
- Gráficos y estadísticas detalladas
- Identificación de directorios pesados

### ℹ️ **Información del Sistema**
- Datos completos de PrestaShop, PHP, MySQL
- Verificación de extensiones
- Información del servidor

## 🔍 Detección Inteligente de Carpeta Admin

El script utiliza múltiples métodos para detectar la carpeta admin:

1. **Constante _PS_ADMIN_DIR_** (cuando está disponible)
2. **Archivos específicos** (`get-file-admin.php`, `ajax-tab.php`, `header.inc.php`)
3. **Patrones de nombre** (`admin[números/letras]`)
4. **Estructura de directorios** (presencia de `index.php`, `controllers/`, `tabs/`)

## 🎨 Diseño y UX

- **Interfaz moderna** con gradientes y animaciones
- **Responsive design** que funciona en móviles
- **Indicadores visuales** de estado (activo/inactivo/warning)
- **Navegación intuitiva** por pestañas
- **Colores semánticos** (verde=OK, rojo=error, amarillo=warning)
- **Confirmaciones** para acciones destructivas

## 💡 Características Técnicas

- **PHP 7.0+** compatible
- **No requiere librerías externas**
- **Autocontenido** en un solo archivo
- **Detección automática** de versión de PrestaShop
- **Gestión de errores** robusta
- **Código limpio** y bien documentado

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Si encuentras un bug o tienes una mejora:

1. Abre un **Issue** describiendo el problema o mejora
2. Haz un **Fork** del repositorio
3. Crea una **rama** con tu mejora
4. Envía un **Pull Request**

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Puedes usarlo libremente en proyectos comerciales y personales.

## ⚠️ Descargo de Responsabilidad

Esta herramienta es para uso de emergencia únicamente. El autor no se hace responsable de ningún daño causado por el mal uso de esta herramienta. Úsala bajo tu propia responsabilidad y siempre haz backups antes de realizar cambios importantes.

---

**🚨 Recuerda: Esta es una herramienta de EMERGENCIA. Úsala solo cuando sea necesario y elimínala inmediatamente después del uso.**