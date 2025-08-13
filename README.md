OxoAwards
=========

  Version:  2.0a
  Date:     2015/05/05

Requirements
============

- Crontab
    
    No logueamos como super usuario

    - root@develop:# crontab -e
        
    ```
    [...]
    */1 * * * * /usr/sbin/iotop -b -n 1 -o > /tmp/iotop.log
    */20 * * * * /usr/bin/ifstat -i eth1 -t 120 10 >> /tmp/ifstat.log
    [...]
    ```
        
     - Guardamos y cerramos. [Ctrl + O y Ctrl + X]

SHIT TO FIX
===========

- Votaciones
    - [ ] No mostrar campos con valores vacíos
	- [ ] Chequear la carga inicial con el scroll
	- [x] ó Arrancar con las categorías colapsadas
	- [ ] Filtrar entries asignables a un jurado por categorías de la sesión
	- [ ] Contadores de votos en jurado
	- [ ] Contadores de metales en resultados
	- [x] Metalero debe votar en la lista
	- [ ] Chequear voto si/no desde la lista
	- [x] Thumbnail en la lista
	- [ ] Filtrar entries por voto dentro de categoría
	- [ ] Arreglar asignación de entries a jurado con entry por cateogíra
	- [ ] Árbol de Categorías a la izquierda

- Técnico
	- [x] Crear fileversions de formatos faltantes
	- [x] Reencodear archivo (sin rotar)
	- [x] Crear thumbnail
	- [ ] Mostrar usuario del file

- Entries
	- [x] Filtro de status no funciona en vista por categorías
	- [x] Las subcategorías se ven en la vista en por categorías
	- [ ] Filtro de entries pagados tira error
	- [ ] Subir archivos al usuario
	- [ ] Reemplazar archivo
	- [ ] Nombre del entry en el modal se ve Sin título

- Player
	[ ] No reproduce los videos en safari/iOS
	
- Billing
	[ ] Cambiar método de pago
	[ ] Borrar billing para el admin
	[ ] Check soft delete (entrycategory)
	
Wishlist
========
  - [X] Configuración de Formularios de entries por Categoría
  - [ ] Votación
  - [X] Billing
  - [ ] Extender inscripción a un usuario
  - [ ] Invitar a un tercero a subir un file
  - [ ] Users Online Status
  - [X] Agregar un link o external embed como file
  - [X] Mostrar fecha de creación en historial del entry
  - [x] Entries por usuario
  - [ ] Descarga de files en tecnico 
  - [ ] Descarga de docs en inscripciones con icono 
  
TODO
====
  - [ ] Administrador de sistema
    - [ ] Configuración de relación entre entries y Categorías
    - [ ] Voting sessions results
    - [ ] Gráficos de inscripciones y entries

  - [ ] Billing
    - [ ] Pago de múltiples entries
    - [ ] Estado de pago y acciones desde fomulario del entry
    - [ ] Alertar al quitar un entry de una categoría ya pagada
    - [ ] Detalles del entry y las categorías de un pago
    - [ ] Listado de mis pagos para el inscriptor
    
  - [ ] Terminos y condiciones (Contenido)  
  
  - [ ] Restringir acceso php
    - [ ] vista entry y entry/id
    - [ ] vista tech
    - [ ] vista files
  - [ ] mulitple con columnas en formulario de registro
  - [X] mulitple con columnas con campos de texto
  - [ ] Mails
    - [x] inscripcion Ok
    - [x] entry con error
    - [ ] entry aprobado
    - [ ] invitacion inscriptor
    - [ ] invitacion juez
    - [ ] invitacion colaborador
    - [ ] media con error
    
    
  
  - [ ] Entries
    - [x] 1 entry por categoria
    - [x] Borrar entry
    - [ ] Listado de piezas
    - [x] Visualización de Tamizador
    - [ ] Billing
    - [ ] Crear entries sólo de usuarios que sean Inscriptor, Colaborator y Owner
    - [ ] Arreglar filtros de entries y sort
    - [ ] Link a entries filtrados por estado
    
  - [x] Idioma
    - [x] Cambio de idioma de sistema (Laravel)
    - [x] Metadata con idioma
    - [x] Categorías con idioma
    - [x] InscriptionTypes con idioma
    
  - [ ] Metadata
    - [ ] Campos privados en entries
    - [ ] Campos de texto con caracteres máximos y mínimos
    - [ ] Campo de país
    - [ ] Campo de fecha con mínima y máxima
    - [ ] Campo de checkboxes con máximo y mínimo
    - [ ] Campo de archivo con filtros por tipo
    - [ ] Campo de URL
  
  - [ ] Piezas
    - [x] Player
    - [ ] Técnico
    - [ ] Subir piezas al usuario inscriptor del entry
    - [ ] Reemplazar archivo
    - [ ] 
    
  - [ ] Voting Sessions
    - [ ] Votador
    - [ ] Sistemas de votación
    
  - [ ] Chequear seguridad
    - [ ] HTML con controllers y routes filtrados
    - [ ] Acceso a views/controllers
    - [ ] API
    
Topics
======
  - Contests
  - Categories
  - Entries
  - Metadata
  - Files
  - Usuarios/Roles
  - Administrador
  - Inscripción
  - Fechas
  - Formatos/Encoder
  - Idioma
  - Deploy
  - Billing
  - Export data,files,ranking
  - Make reels
  - Tipos de votos
  - Mailing/Alertas/Telegram
  - Autocomplete
  - Oauth
  - Funciones para users
  - Técnico
  - Responsive/iPad
  - Offline
  - Extender inscripción a un usuario
  - Invitar a un tercero a subir un file
  - Users Online Status
  - Cupos en Cotests
  - Descuentos
  - Log/Historial x Entry
  - Visto
  - Contest total storage/User storage

Known Bugs
==========
  - [x] Campos de multiple choice en entries no guardan el entry
  - [x] Luego de loguearte no se ve el botón de Administrador
  - [x] Luego de loguearte se ven las opciones según la inscripción mal
  - [x] No se actualizan los multiple checkbox con columnas para el super admin (ver tambien owner)
  - [ ] El idioma tiene un hardcode para que no muestre el portuges, langselect.blade (ng-if="key != 'br'")