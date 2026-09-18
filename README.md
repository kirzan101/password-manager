# Laravel React18 Template

A modern full-stack boilerplate combining **Laravel 12**, **React 18**, **Inertia.js**, and **MUI7**.  
Built for rapid development with clean architecture, SOLID principles, and the Repository Design Pattern.

---

## 🚀 Features

-   Laravel 12 (PHP 8.2)
-   React 18 + Inertia.js (SPA architecture)
-   MUI7 for beautiful Material Design components
-   Vite for fast frontend builds
-   Repository pattern for clean separation of concerns
-   SOLID principles for maintainable code
-   Modular and scalable project structure

---

## ⚙️ Requirements

Make sure your environment meets these requirements:

-   **PHP**: ^8.2
-   **Composer**
-   **Node.js**: >= 18.15
-   **MariaDB** or **MySQL**
-   PHP Extensions:
    -   `bcmath`
    -   `intl`
    -   `gd`
    -   `xml`
    -   `zip`
    -   `mbstring`
    -   `pdo`
    -   `mysql`
    -   `curl`

---

## 🛠️ Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/kirzan101/laravel-vue.git
cd laravel-vue3-template
```

### 2. Set up environment

```bash
cp .env.example .env
```

### 3. Install PHP dependencies

```bash
composer install
php artisan key:generate

#Optional: Run database migrations and seeders (if applicable):
php artisan migrate:fresh --seed
```

### 4. Install JavaScript dependencies

```bash
npm install
```

### 5. Start development servers
Open **two terminal windows or tabs** (one for the backend and one for the frontend):

```bash
# Terminal 1: Start Laravel backend
php artisan serve

# Terminal 2: Start Vite frontend (Vue + Inertia.js)
npm run dev
```
> 💡 VS Code Tip: Open a new terminal to run this command.
> - "Windows/Linux: Press <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)"
> - "Mac: Press <kbd>Cmd</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)".
>   
> This opens a new terminal tab inside Visual Studio Code.
---

## 🧑‍💻 Development

### 1. 🔧 Generate Service & Interface
This custom command generates a new **Service class**, its corresponding **Interface**, and a **DTO (Data Transfer Object)**. Use the model-style name (PascalCase) when calling the command.

🧾 **Usage**

```bash
php artisan make:service {ModelName}
```

🧪 **Example**

If you run:
```bash
php artisan make:service UserGroup 
```
> 💡 VS Code Tip: Open your third terminal tab in VS Code to run this command.
> - "Windows/Linux: Press <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)"
> - "Mac: Press <kbd>Cmd</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)".

It will generate:
```bash
Interface [app/Interfaces/UserGroupInterface.php] created successfully.
Service [app/Services/UserGroupService.php] created successfully.
DTO [app/DTOs/UserGroupDTO.php] created successfully.
```

🗂️ **Generated files**
- `app/Interfaces/UserGroupInterface.php`
- `app/Services/UserGroupService.php`
- `app/DTOs/UserGroupDTO.php`

### 2. 🔧 Generate Fetch Service & Interface
This custom command generates a new **Fetch Service class** and its corresponding **Fetch Interface**. Use the model-style name (PascalCase) when calling the command.

🧾 **Usage**

```bash
php artisan make:fetch-service {ModelName}
```

🧪 **Example**

If you run:
```bash
php artisan make:fetch-service UserGroup 
```
> 💡 VS Code Tip: Open your third terminal tab in VS Code to run this command.
> - "Windows/Linux: Press <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)"
> - "Mac: Press <kbd>Cmd</kbd> + <kbd>Shift</kbd> + <kbd>`</kbd> (backtick)".

It will generate:
```bash
Interface [app/Interfaces/FetchInterfaces/UserGroupFetchInterface.php] created successfully.
Service [app/Services/FetchServices/UserGroupFetchService.php] created successfully.
```

🗂️ **Generated files**
- `app/Interfaces/FetchInterfaces/UserGroupFetchInterface.php`
- `app/Services/FetchServices/UserGroupFetchService.php`

### 3. 🔐 Generate Module Permissions
This custom Laravel Artisan command helps you generate CRUD permissions for a given module and assign them to all existing user groups in your system. It’s useful for streamlining role-based access control setup in your application.

🧾 **Usage**

```bash
php artisan app:generate-module-permissions {ModuleName} [options]
```

🧪 **Example**

```bash
php artisan app:generate-module-permissions UserGroup --create --view --update

```

This will:
- Generate the following permissions:
    - `create` for `User Group`
    - `view` for `User Group`
    - `update` for `User Group`
- Store them in the `permissions` table (if not already present)
- Assign them to all user groups in the `user_group_permissions` table with default status `is_active = false`.

⚙️ **Options**
```plaintext
| Option     | Description                 |
| ---------- | --------------------------- |
| `--create` | Include "create" permission |
| `--view`   | Include "view" permission   |
| `--update` | Include "update" permission |
| `--delete` | Include "delete" permission |
```
> 💡 **If no option is provided**, the command will automatically add:
> - "`create`"
> - "`view`"
> - "`update`"
>   
> `delete` is excluded by default for safety.

📦 **Output**
```bash
Permissions and user group links generated successfully.
```
And internally:
- Entries are inserted or reused from the `permissions` table.
- Every `user_group` gets each of those permissions inserted into `user_group_permissions` with `is_active = false`.

### 4. 📦 Generate API Controller
This custom Artisan command simplifies the creation of API controllers under the App\Http\Controllers\API namespace with predefined RESTful (API-only) methods.

🧾 **Usage**

```bash
php artisan make:api-controller {ControllerName} {--model=ModelName}
```

🧾 **Arguments & Options**
```plaintext
| Argument / Option   | Description                                                        |
| ------------------- | ------------------------------------------------------------------ |
| `ControllerName`    | **(Required)** The name of the controller (e.g., `UserController`) |
| `--model=ModelName` | **(Optional)** Binds a model to the controller (e.g., `User`)      |
```

📁 **Output**

This command creates a controller at:
```bash
app/Http/Controllers/API/{ControllerName}.php
```

With the following API methods:
- `index()`
- `store()`
- `show($id)`
- `update(Request $request, $id)`
- `destroy($id)`

🧪 **Examples**

➤ **Generate a basic API controller**
```bash
php artisan make:api-controller Product
```
> Creates: `app/Http/Controllers/API/ProductApiController.php`

➤ **Generate an API controller with model binding**
```bash
php artisan make:api-controller Product --model=Product
```
> Binds `App\Models\Product` to the controller resource routes

---

## 📁 Project Structure Highlights

```plaintext 
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Interfaces/
│   │   └── FetchInterfaces/        # Fetch interfaces
│   └── Services/                   # Business logic layer
│       └── FetchServices/          # Fetch services (index & show)
│
├── resources/
│   └── js/                         # React 18 + Inertia frontend
│       ├── Components/
│       │   ├── Customs/            # Customized MUI7 components
│       │   ├── Errors/             # Error display components
│       │   ├── Pages/              # React module pages
│       │   └── Utilities/          # Reusable utility components
│       ├── Layouts/                # Application layout (e.g., blank, main)
│       └── Pages/                  # Laravel + Inertia page components
│
├── routes/
│   └── web.php                     # Inertia-based web routes
│
├── database/
│   ├── migrations/                 # DB schema definitions
│   └── seeders/                    # DB seed data
```

---

## 🧩 Technology Stack

### Backend
* Laravel ^12.0
* Inertia.js Laravel Adapter ^2.0
* PHP ^8.2

### Frontend
* "@emotion/react": "^11.14.0"
* "@emotion/styled": "^11.14.1"
* "@fontsource/roboto": "^5.2.10"
* "@inertiajs/react": "^2.0.8"
* "@mui/icons-material": "^7.3.11"
* "@mui/lab": "^7.0.0"
* "@mui/material": "^7.3.11"
* "react": "^18.3.1"
* "react-dom": "^18.3.1"

## 📦 Production Build
```bash
npm run build
```

---

## 📝 License
This project is open-source under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙌 Contributing
Contributions are welcome! Feel free to open issues, fork the repo, or submit pull requests.

---

## 📫 Contact
For questions, feedback, or support, contact:
ckimescamilla@gmail.com or open an issue in the repository.

