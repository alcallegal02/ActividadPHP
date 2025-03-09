# Robo cookies con servidor

El proyecto tiene dos partes principales:

Por un lado la web de la víctima, con un contenedor para la propia web, otro para la base de datos mysql y otro phpmyadmin para visualizar la base de datos.
Por otro lado tenemos un servidor python para el atacante en un contenedor y a la vez que el clúster de la víctima tenemos dos contenedores para mysql y otro phpmyadmin para ver los datos. 
En la base de datos del atacante se guardarán las cookies robadas de los usuarios gracias a que el servidor recibe un post proveniente de la página web desplegada de la víctima.
Esta web tiene un script en el archivo product_list.php que roba la cookie de sesión cuando el usuario se loguea en ella.

Vamos a ver un caso práctico para ver como funciona:

![image](https://github.com/user-attachments/assets/345f87e3-d652-4c89-8305-7537c754714a)

El usuario se loguea sin tener cookie aún

Cuando el usuario se loguea se genera una cookie, pero en el momento en el que se carga la página, un div oculto hace un post al servidor con la cookie robada:

![image](https://github.com/user-attachments/assets/1197ddf7-fb17-49a4-b5d6-7171b12942aa)

El servidor lo recibe y con los datos que recibe de la cookie hace una inserción a su base de datos para guardarla:

![image](https://github.com/user-attachments/assets/a337049d-df02-4991-897e-f7387a4d0a4b)

![image](https://github.com/user-attachments/assets/98d1b2a1-de5b-42df-ae4d-771ad1f2fcab)



# Pasos Explotación de vulnerabilidad Reflected XSS

Iniciamos sesión con usuario prueba que es la víctima:

![image](https://github.com/user-attachments/assets/60f41d0d-9fb3-45c1-886c-2903baee3f69)

Ejecutamos el script en la barra de búsquedas para que mi servidor local capture la cookie de sesión para suplantar la identidad:

![image](https://github.com/user-attachments/assets/0d5081cf-430c-4271-9166-8151e9bced0e)

![image](https://github.com/user-attachments/assets/0e8090b3-5ff3-43fb-82e0-26f4e94578ec)


Iniciamos sesión con el usuario que va a suplantar la identidad, en mi caso pepe:

![image](https://github.com/user-attachments/assets/90276ba9-9ab0-4db3-8573-8f9edc73ad64)

Nos crea una cookie de sesión correspondiente al usuario pepe:

![image](https://github.com/user-attachments/assets/13974670-449c-42df-81d8-a060f85e06f7)

Modificamos la cookie de pepe por la cookie de prueba anterior:

![image](https://github.com/user-attachments/assets/8bcc4e75-85b4-4cc5-acfb-b00f2d6ef811)

Y como vemos, nos habría cambiado el usuario al de prueba:

![image](https://github.com/user-attachments/assets/a171ecc5-fd0e-4d1c-b7cb-f4d254b11f5c)

De esta forma podremos suplantar la identidad de prueba en la aplicación.






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
