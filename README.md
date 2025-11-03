# 🧠 To-Do-FlexV

Aplicação **Full-Stack** desenvolvida com **Laravel + Vue 3 (Composition API)**.  
O objetivo do projeto é oferecer uma experiência moderna e funcional para gestão de tarefas (To-Do), com autenticação, dashboard e páginas de configuração centralizadas.

---

## 🚀 Tecnologias Utilizadas

### 🔧 Back-End
- **Laravel 12**
- **Sanctum** — autenticação SPA via tokens
- **MySQL** — banco de dados relacional
- **Eloquent ORM**
- **API RESTful** com rotas versionadas

### 🎨 Front-End
- **Vue 3 (Composition API)**
- **Vuetify** — componentes de UI modernos
- **Tailwind CSS** — estilização rápida e responsiva
- **Axios** — comunicação com a API
- **Vite** — build e hot-reload
- **Pinia (opcional)** — gerenciamento de estado

---

## 📁 Estrutura do Projeto

O projeto foi **unificado** (front e back no mesmo repositório) para simplificar o fluxo de desenvolvimento:

/app → Código backend (Laravel)
/bootstrap
/config
/public
/resources → Vue, componentes, views e assets do front-end
/routes → Definições de rotas Laravel
/database

yaml
Copiar código

---

## ⚙️ Funcionalidades

- ✅ CRUD de tarefas (To-Do)
- 🔐 Autenticação completa (login com Laravel Sanctum)
- 🧭 Dashboard interativo com status e resumo
- ⚙️ Páginas de configuração de usuário
- 💅 Interface moderna e responsiva com Vuetify + Tailwind
- 📦 Estrutura única (Laravel + Vue integrados)

---

## 🧰 Como Rodar o Projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/SamuelPereiraBrandao/To-Do-FlexV.git
cd To-Do-FlexV
2. Instalar dependências do Laravel
bash
Copiar código
composer install
cp .env.example .env
php artisan key:generate
3. Configurar o banco de dados
No arquivo .env, defina suas credenciais:

ini
Copiar código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_flexv
DB_USERNAME=root
DB_PASSWORD=senha
Depois rode:

bash
Copiar código
php artisan migrate
4. Instalar dependências do Vue/Vite
bash
Copiar código
npm install
5. Rodar o projeto
Inicie o servidor Laravel e o front-end:

bash
Copiar código
php artisan serve
npm run dev
Acesse:
👉 http://localhost:8000

🧑‍💻 Desenvolvimento
Estrutura limpa e modular

Componentes reutilizáveis com Composition API

API organizada em controllers e resources

Código padronizado conforme Clean Code e SOLID

🧩 Próximos Passos
📱 Implementar design responsivo completo

🗓️ Adicionar filtros e categorias nas tarefas

🔔 Criar sistema de notificações

👥 Permitir múltiplos usuários e permissões

🧾 Licença
Este projeto está sob a licença MIT — sinta-se à vontade para utilizar e contribuir.
Desenvolvido por Samuel Pereira Brandão 🧑‍💻

💬 Contato
📧 Email: samuelpbrandao58@gmail.com
💼 LinkedIn: linkedin.com/in/samuelpereirabrandao
🐙 GitHub: github.com/SamuelPereiraBrandao
