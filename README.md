# Corretor Prime - Site Imobiliario em Laravel

Projeto completo de site para corretor de imoveis, inspirado nos prints compartilhados:

- Home com hero, busca e grid de imoveis
- Pagina de listagem com filtros
- Pagina de detalhes com galeria, mapa e formulario de lead
- Painel admin com login, dashboard, CRUD de imoveis e leitura de leads

## Stack

- Laravel 13
- Blade + Vite
- MySQL (configuravel)

## Setup rapido

1. Instale dependencias:

```bash
composer install
npm install
```

2. Configure ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

3. Ajuste o banco no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=corretor_imoveis
DB_USERNAME=root
DB_PASSWORD=
```

4. Rode migrations + seed:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

5. Suba o projeto:

```bash
php artisan serve
npm run dev
```

## Credenciais admin (seed)

- Email: `admin@corretorprime.com.br`
- Senha: `Admin@123456`

## Rotas principais

- Site: `/`
- Listagem: `/imoveis`
- Detalhe: `/imoveis/{slug}`
- Login admin: `/admin/login`
- Dashboard admin: `/admin`

## Estrutura principal

- `app/Models/Property.php` model de imoveis, filtros e accessors
- `app/Http/Controllers/PropertyController.php` listagem e detalhes
- `app/Http/Controllers/LeadController.php` captura de leads
- `app/Http/Controllers/Admin/*` painel e CRUD
- `resources/views/*` frontend publico e admin
- `database/seeders/PropertySeeder.php` dados iniciais de imoveis
