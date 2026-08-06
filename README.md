## Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/tu-usuario/hikey.git
cd hikey
```

2. Copia el archivo de entorno:

```bash
cp .env.example .env
```

3. Levanta los contenedores con Docker Compose:

```bash
docker compose up -d
```

4. Instala las dependencias de PHP:

```bash
docker compose exec php composer install
```

5. Genera la clave de la aplicación:

```bash
docker compose exec php php artisan key:generate
```

6. Corre las migraciones y seeders:

```bash
docker compose exec php php artisan migrate --seed
```

7. Instala las dependencias de Node y compila los assets:

```bash
npm install
npm run dev
```
