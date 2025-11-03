# ✅ To-Do FlexV  
*(Vue 3 + Vuetify 3 + Tailwind v4 + Laravel 12)*

Aplicação **To-Do** moderna com **tema claro/escuro**, **sidebar expansível** e **autenticação via Laravel Sanctum**.  
Front-end e back-end integrados em um único projeto.

---

## ⚙️ Stack

**Frontend**
- 🧩 Vue 3 + Vite  
- 🎨 Vuetify 3  
- 💨 Tailwind CSS v4  
- 🔗 Axios  

**Backend**
- 🧱 Laravel 12  
- 🔐 Sanctum (tokens pessoais)  
- 🗄️ SQLite ou MySQL  

---

## 🚀 Recursos

- CRUD de tarefas  
- Login / Registro / Logout (Sanctum)  
- Perfil e avatar de usuário  
- Tema dark/light sincronizado  
- Layout com sidebar expansível  
- Dashboard e tela de configurações  

---

## 🧰 Instalação

```bash
# Clonar o projeto
git clone https://github.com/SamuelPereiraBrandao/To-Do-FlexV.git
cd To-Do-FlexV

# Dependências
composer install
npm install

# Configuração Laravel
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
