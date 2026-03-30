# 🎓 Kristu Jayanti Queue Management System (QMS)

![Status](https://img.shields.io/badge/Status-Active-success) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue) ![MySQL](https://img.shields.io/badge/Database-MySQL-orange) ![UI](https://img.shields.io/badge/UI-Tailwind%20CSS-38B2AC)

Developed by **Tayvie**, the **Kristu Jayanti QMS** is a digital-first solution designed to modernize campus administrative workflows. By replacing manual paper ticketing with an automated, real-time environment, this system reduces congestion in office areas and provides a seamless service experience for both students and staff.

---

## 📑 Table of Contents
* [Key Features](#-key-features)
* [System Architecture](#-system-architecture)
* [Tech Stack](#-tech-stack)
* [Installation & Setup](#-installation--setup)
* [Usage Guide](#-usage-guide)
* [Screenshots](#-screenshots)

---

## 🌟 Key Features

### 🏢 For Administrators & Staff
* **Multi-Counter Support:** Assign staff to specific counters (e.g., Admissions, Finance, Scholarship).
* **Live Dashboard:** Real-time "Call," "Complete," or "Cancel" actions for queue numbers.
* **Performance Analytics:** Track average wait times and daily throughput via an automated algorithm.
* **Status Monitoring:** Toggle counter availability (Online/Offline) instantly.

### 🎓 For Students
* **Automated Token Generation:** Instant queue numbers with service-specific prefixes (e.g., ADM-001, FIN-002).
* **Interactive Public Display:** A dedicated screen view featuring professional animations and a scrolling marquee.
* **Audio Notifications:** Voice-enabled alerts to notify students when their turn arrives.
* **Real-Time Ticker:** Live updates on the current number being served and those in the "Waiting" list.

---

## 🏗️ System Architecture
The system is built on a **Producer-Consumer model** using modern web technologies to ensure zero page refreshes during the queuing process.

* **Asynchronous Updates:** Uses the **JavaScript Fetch API** to poll the server every 5 seconds (Display) and 10 seconds (Admin).
* **Security:** Implements **PDO (PHP Data Objects)** for secure, injection-resistant database interactions.
* **Responsiveness:** Styled with **Tailwind CSS** to ensure the dashboard works on desktops, tablets, and kiosks.

---

## 🛠️ Tech Stack
| Layer | Technology |
| :--- | :--- |
| **Backend** | PHP 7.4+ |
| **Database** | MySQL |
| **Frontend** | Tailwind CSS, JavaScript (ES6+), HTML5 |
| **Icons** | Font Awesome |
| **Data Format** | JSON (via Fetch API) |

---

## 🚦 Installation & Setup

### Prerequisites
* **XAMPP** or **WAMP** installed.
* PHP 7.4 or higher.

### Step-by-Step Installation
1.  **Clone the Repository**
    ```bash
    git clone https://github.com/Tayvie/kristu-jayanti-qms.git
    ```
2.  **Database Configuration**
    * Open **phpMyAdmin**.
    * Create a new database named `queuing_system`.
    * Import the `queuing_system.sql` file located in the `/database` folder.
3.  **Project Deployment**
    * Move the project folder to your `htdocs` (XAMPP) or `www` (WAMP) directory.
    * Update `config.php` (if applicable) with your local database credentials.
4.  **Access the App**
    * Public: `http://localhost/kristu-jayanti-qms/`

---

## 📖 Usage Guide
1.  **Generate Token:** Students select their required service on the kiosk/entry page to receive a ticket.
2.  **Call Student:** Staff members log into the Admin Dashboard and click **"Call Next"** to trigger the audio and visual alert.
3.  **Public View:** The main hall monitor (Display Page) will flash the number and announce it via audio.
4.  **Complete Transaction:** Once the service is finished, staff marks the token as **"Completed"** to update analytics.

---

## 🤝 Contribution & License
This project was developed to enhance the institutional efficiency of **Kristu Jayanti**. 

**Maintained by:** [Tayvie](https://github.com/Tayvie)  
*For support or custom deployments, please open an issue in this repository.*

---
