# 🎓 Quizhub - Online Quiz Portal

**Quizhub** is a robust, web-based examination and quiz portal designed to streamline the process of conducting online assessments. Built with PHP and MySQL, it offers a seamless experience for administrators, teachers, and students.

---

## 🚀 Key Features

### 🛠️ Admin Dashboard
- **Subject Management**: Create, update, and organize various academic subjects.
- **User Monitoring**: Manage teacher and student accounts to ensure a secure environment.
- **System Settings**: Configure global portal parameters.

### 👨‍🏫 Teacher Module
- **Quiz Creation**: Design quizzes for specific subjects with ease.
- **Question Bank**: Add, edit, and categorize questions (multiple choice).
- **Result Analytics**: Track student performance and generate automated reports.
- **Media Support**: Upload images for questions and profiles.

### 🎓 Student Module
- **Self-Registration**: Easy signup and profile management.
- **Take Quizzes**: Attempt subject-specific quizzes with real-time feedback.
- **Performance History**: View past scores and detailed result breakdowns.
- **Interactive Blog**: Access educational blogs and resources.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 8.x
- **Database**: MySQL
- **Tooling**: XAMPP / WAMP / LAMP

---

## ⚙️ Installation & Setup

### 1. Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (Apache & MySQL)
- Git (optional)

### 2. Local Setup
1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/sijanKc/Quizhub.git
    ```
2.  **Move to Web Root**:
    Copy the `online_quiz_portal` folder to your XAMPP `htdocs` directory:
    `C:\xampp\htdocs\online_quiz_portal`
3.  **Start Services**:
    Open the XAMPP Control Panel and start **Apache** and **MySQL**.
4.  **Database Configuration**:
    - Open [phpMyAdmin](http://localhost/phpmyadmin/).
    - Create a new database named `quizhub`.
    - (Optional) Run the `fix_database.php` script via browser to initialize tables:
      `http://localhost/online_quiz_portal/fix_database.php`
5.  **Access the Portals**:
    - **Main Index**: `http://localhost/online_quiz_portal/`
    - **Admin Login**: `http://localhost/online_quiz_portal/admin-login/`
    - **Teacher Login**: `http://localhost/online_quiz_portal/teacher-login/`
    - **Student Login**: `http://localhost/online_quiz_portal/studentlogin/`

---

## 📁 Project Structure
```text
├── admin-login/         # Admin authentication
├── admindashboard/      # Admin control panel
├── teacher-login/       # Teacher authentication
├── teacherdashboard/    # Teacher control panel
├── studentlogin/        # Student authentication
├── studentdashboard/    # Student control panel
├── index/               # Landing page and Blogs
├── db_connect.php       # Database connection logic
└── fix_database.php     # Helper script for DB setup
```

---

## 🤝 Contributing
Contributions are welcome! If you'd like to improve Quizhub, feel free to fork the repo and submit a Pull Request.

---

## 📄 License
This project is for educational purposes. Feel free to use and modify it.

---

**Developed with ❤️ by [Sijan](https://github.com/sijanKc)**
