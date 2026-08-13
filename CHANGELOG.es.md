# Historial de Cambios (Changelog)

Todos los cambios notables de este proyecto se documentarán en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto se adhiere al [Versionamiento Semántico](https://semver.org/spec/v2.0.0.html).

## [Sin Publicar]
### Agregado
- **Notificaciones Push FCM:** Acumulación dinámica de notificaciones en Android, reemplazo por `tag`/`id`, agrupación tipo WhatsApp por `group`/`thread_id` y soporte nativo para **Respuesta Directa (Direct Reply)** con la clave `"reply": true`.
- **Navegación Fluida:** Soporte para `SingleTop` en intents de notificación, evitando reinicios de la app y recargas de splash al tocar avisos.
- Pipeline de GitHub Actions para compilaciones automáticas.

## [v1.0.0] - 2026-07-22
### Agregado
- **CLI de Phphone**: Herramienta de consola global e inteligente para la gestión de proyectos (`create`, `run`, `build`, `setup`, `config`, etc).
- **Soporte Android**: Integración total con JNI, embebiendo un motor ligero de PHP 8.4 corriendo en C++.
- **Soporte iOS**: Integración total con XcodeGen y puentes Swift para ejecutar PHP 8.4 de forma nativa.
- **Hot Reload**: Recarga en vivo automática cuando cambian archivos PHP/JS/CSS (`phphone run`).
- **Comando Setup**: Generación automática de Íconos de App y Splash Screens (`phphone setup`).
- **Comando Config**: Manejo de configuración nativa (Bloqueo de orientación, prevención de pellizco para zoom).
- **Guías de Diseño**: Mejores prácticas de UI/UX documentadas (Márgenes de Safe Area, prevención de Overscroll).

### Modificado
- Refactorización del motor base para interceptar todo el tráfico de red localmente, eliminando problemas de CORS.
- Migración de scripts de Gradle a Kotlin DSL (`build.gradle.kts`) para un manejo de dependencias moderno.

