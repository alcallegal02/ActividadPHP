# Entorno LAMP Dockerizado con phpMyAdmin

Este proyecto proporciona un entorno LAMP (Linux, Apache, MySQL, PHP) completamente dockerizado, junto con phpMyAdmin, para facilitar el desarrollo y despliegue de aplicaciones PHP.

---

## Requisitos previos

Antes de empezar, asegúrate de tener instalados los siguientes programas en tu sistema:

- **Docker**
- **Docker Compose**

---

## Instrucciones de instalación

Sigue los pasos a continuación para configurar y ejecutar el entorno:

### 1. Clonar el repositorio

Clona el repositorio del proyecto en tu máquina local utilizando el siguiente comando:

```bash
git clone https://github.com/alcallegal02/ActividadPHP.git
```


### 2. Iniciar los contenedores

Para levantar los servicios definidos en el archivo docker-compose.yml, ejecuta el siguiente comando:

```bash
docker-compose up -d
```

Esto configurará y ejecutará los siguientes servicios:

- Servidor Apache: Disponible en http://localhost:8080
- Base de datos MySQL: Accesible en el puerto 3306
- phpMyAdmin: Disponible en http://localhost:8081


### 3. Verificar la instalación

Abre http://localhost:8080 en tu navegador para verificar que el servidor Apache está funcionando correctamente.

Accede a phpMyAdmin en http://localhost:8081 usando las siguientes credenciales:

- Servidor: db (nombre del contenedor de la base de datos)
- Usuario: root
- Contraseña: (vacía)


### 4. detener los contenedores

Para detener los servicios en ejecución, usa el comando:

```bash
docker-compose down
```

---

## Estructura del proyecto

### Servidor Apache

- Nombre del contenedor: apache_server
- Puerto expuesto: 8080 (mapeado al puerto 80 del contenedor)
- Volúmenes: ./src mapeado a /var/www/html dentro del contenedor.

### Base de Datos MySQL

- Nombre del contenedor: mysql_db
- Imagen: mysql:8.0
- Puerto expuesto: 3306 (puerto por defecto de MySQL)
- Variables de entorno:
    - MYSQL_ALLOW_EMPTY_PASSWORD: yes (permite usar contraseña vacía para el usuario root).
    - MYSQL_DATABASE: actphp (crea automáticamente una base de datos llamada actphp).
- Volúmenes:
    - db_data: Para almacenar los datos de manera persistente.
    - ./init.sql: Archivo para inicializar la base de datos.


### phpMyAdmin

- Nombre del contenedor: phpmyadmin
- Imagen: phpmyadmin/phpmyadmin
- Puerto expuesto: 8081 (mapeado al puerto 80 del contenedor)
- Variables de entorno:
    - PMA_HOST: db (indica que se debe conectar al contenedor MySQL).


### Redes

- app_network: Red personalizada para permitir la comunicación entre los contenedores.


### Volúmenes

- db_data: Volumen persistente para los datos de MySQL.

---

## Notas importantes

- Asegúrate de que los puertos 8080 y 8081 estén libres en tu máquina. Si están ocupados, puedes cambiar los puertos en el archivo docker-compose.yml.
- Modifica el archivo docker-compose.yml si necesitas agregar configuraciones adicionales.