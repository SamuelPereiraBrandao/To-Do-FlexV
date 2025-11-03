To‑Do FlexV (Vue 3 + Vuetify 3 + Tailwind v4 + Laravel 12)

Aplicação de tarefas (To‑Do) com tema claro/escuro, layout com sidebar expansível e integração completa com API Laravel (autenticação com Sanctum, perfil/avatar e CRUD de tarefas).

Visão Geral
- SPA em Vue 3 (rodando dentro do Laravel via Vite)
- UI com Vuetify 3 + utilitários do Tailwind CSS v4 (sem preflight)
- Tema dark/light sincronizado (Tailwind 'dark:' + Vuetify theme)
- Autenticação com Laravel Sanctum (tokens pessoais)
- Endpoints REST: login, register, perfil, avatar, tarefas (CRUD)
- Sidebar “rail” que expande no hover; conteúdo ocupa 100% a partir da barra

Tecnologias
- Frontend
  - Vue 3 + Vite
  - Vuetify 3 (vite-plugin-vuetify)
  - Tailwind CSS v4 (apenas 'theme' e 'utilities')
  - Axios, @mdi/font
- Backend
  - Laravel 12
  - Laravel Sanctum (tokens)
  - Fortify (presente no boilerplate)
  - SQLite por padrão (ou outro driver conforme seu '.env')

Estrutura Importante
- SPA (Vue):
  - resources/js/spa/main.ts (entry)
  - resources/js/spa/App.vue
  - resources/js/spa/components/* (Login, AuthLayout, Sidebar, Dashboard, TodoThematic, etc.)
  - CSS: resources/css/style.css (Tailwind v4 + tema “flexv”)
  - Blade SPA host: resources/views/app.blade.php
- API (Laravel):
  - Rotas: routes/api.php
  - Login/Registro/Logout/Perfil: app/Http/Controllers/Api/AuthController.php
  - Tarefas (CRUD): app/Http/Controllers/Api/TodoController.php (se aplicável)
  - Modelos: app/Models/User.php (com Sanctum), app/Models/Todo.php
  - Migrações:
    - database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php
    - Outras: criação de 'todos', 'avatar_path' em 'users', etc.

Pré‑requisitos
- Node.js ≥ 18
- PHP ≥ 8.2
- Composer
- SQLite ou outro banco configurado no '.env'

Configuração & Execução (Desenvolvimento)
1) Instale dependências PHP e JS
```
composer install
npm install
```
2) Configure o ambiente Laravel
```
cp .env.example .env   # se necessário
php artisan key:generate
php artisan migrate
php artisan storage:link
```
3) Rode os servidores (dois terminais)
```
# Terminal A (Vite)
npm run dev

# Terminal B (Laravel)
php artisan serve   # http://127.0.0.1:8000
```
4) Acesse o app em http://127.0.0.1:8000

Opcional (host da API no front)
- O SPA usa VITE_API_URL (padrão http://127.0.0.1:8000). Para alterar:
```
# .env do Vite (ou .env na raiz conforme setup)
VITE_API_URL=http://localhost:8000
```

Build de Produção
```
npm run build
# Sirva via Laravel (manifest gerado em public/build)
```
Se ver “Unable to locate file in Vite manifest…”, rode 'npm run build' (ou 'npm run dev') e limpe caches:
```
php artisan optimize:clear
```

Autenticação (Sanctum)
- Login: POST /api/login → { token, user }
- Registro: POST /api/register → { token, user }
- Logout: POST /api/logout (Bearer)
- Me: GET /api/me (Bearer)

Exemplos (curl)
```
# Registro
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Samuel","email":"samuel@example.com","password":"123456","device_name":"web"}'

# Login
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"samuel@example.com","password":"123456","device_name":"web"}'

# Me
curl -H "Authorization: Bearer <TOKEN>" http://127.0.0.1:8000/api/me
```

Tarefas (To‑Do)
- Listar: GET /api/todos (Bearer)
- Criar: POST /api/todos (JSON ou multipart se houver arquivo)
- Detalhar: GET /api/todos/{id}
- Atualizar: PUT /api/todos/{id} (JSON ou multipart)
- Remover: DELETE /api/todos/{id}

Payload de criação (JSON)
```
{
  "title": "Comprar café",
  "theme_id": "personal",
  "done": false
}
```

Tema Dark/Light
- Classe html.dark é aplicada antes do Vue montar (script no Blade), lida pelo Tailwind v4 via '@custom-variant dark'.
- O componente DarkModeToggle sincroniza documentElement.classList e o tema global do Vuetify.
- Preferência é persistida em localStorage ('darkMode').

Dicas & Troubleshooting
- 404/rota API não encontrada: verifique bootstrap/app.php (com 'api: __DIR__/../routes/api.php') e reinicie 'php artisan serve'.
- CORS: habilitado globalmente (HandleCors).
- “These credentials do not match our records”: senha precisa estar hasheada (use o endpoint de '/api/register' ou crie usuário com Hash::make).
- “Vite manifest not found”: rode 'npm run dev' ou 'npm run build' e 'php artisan optimize:clear'.

Scripts úteis
- npm run dev — Vite em modo dev
- npm run build — build de produção
- php artisan serve — servidor Laravel
- php artisan migrate — migrações do banco

Licença
Projeto de exemplo para estudo/uso interno.
