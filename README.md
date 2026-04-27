# 🩸 LifeFlow — Online Blood Bank Management System

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)

> A full-stack web application for managing blood bank operations — connecting donors, recipients, and administrators seamlessly.

---

## 📌 Features

### 👤 Donor Side
- Donor Registration & Login
- Personal Dashboard with donation history
- Blood Request Form
- Downloadable Donation Certificate

### 🛡️ Admin Panel
- Secure Admin Login
- Dashboard with overview stats
- Manage Donors
- Manage Blood Requests
- Track Donations
- Monitor Blood Stock levels

### 🌗 UI/UX
- Light / Dark Mode Toggle
- Responsive Design
- Clean and intuitive interface

---

## 🛠️ Tech Stack

| Layer      | Technology          |
|------------|---------------------|
| Frontend   | HTML, CSS, JavaScript |
| Backend    | PHP                 |
| Database   | MySQL               |
| Server     | Apache (XAMPP)      |

---

## 📁 Project Structure

```
bloodbank/
├── index.php               # Home page
├── login.php               # Donor login
├── register.php            # Donor registration
├── logout.php              # Logout
├── donor_dashboard.php     # Donor dashboard
├── request.php             # Blood request form
├── certificate.php         # Donation certificate
├── css/
│   └── theme.css           # Light/Dark theme styles
├── includes/
│   ├── db.php              # Database connection
│   └── theme_toggle.php    # Theme toggle logic
├── admin/
│   ├── login.php           # Admin login
│   ├── dashboard.php       # Admin dashboard
│   ├── donors.php          # Manage donors
│   ├── requests.php        # Manage requests
│   ├── donations.php       # Manage donations
│   ├── stock.php           # Blood stock
│   └── logout.php          # Admin logout
└── database/
    └── bloodbank.sql       # Database schema & seed data
```

---

## ⚙️ Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed on your system

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/VedangDhamal/LifeFlow-BloodBank.git
   ```

2. **Move to XAMPP's htdocs folder**
   ```
   C:\xampp\htdocs\bloodbank
   ```

3. **Import the database**
   - Start **Apache** and **MySQL** from XAMPP Control Panel
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a new database named `bloodbank`
   - Click **Import** → select `database/bloodbank.sql` → click **Go**

4. **Configure database connection**
   - Open `includes/db.php`
   - Update credentials if needed (default: `root` with no password)

5. **Run the project**
   - Open your browser and go to:
     ```
     http://localhost/bloodbank
     ```

---

## 🔐 Default Admin Credentials

| Field    | Value         |
|----------|---------------|
| Username | `admin`       |
| Password | `admin123`    |

> ⚠️ Change these credentials after first login for security.

---

## 📸 Screenshots

> *(Add screenshots of your project here)*

---

## 👨‍💻 Developer

**Vedang Dhamal**
T.Y.B.Sc. Computer Science — S.N.B.P. College of Science & Commerce, Pune
Savitribai Phule Pune University

---

## 📄 License

This project is for educational purposes.
