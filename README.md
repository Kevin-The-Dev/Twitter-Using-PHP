# Twitter-Using-PHP 🐦

A simple **Twitter-like social media web application** built using **Core PHP and MySQL**.  
This project demonstrates how popular social media features work behind the scenes without using any PHP framework.

---

## 🚀 Features

- User Registration & Login
- Post Tweets
- Like & Unlike Tweets
- Comment on Tweets
- Follow / Unfollow Users
- View Own Profile & Other Users’ Profiles
- Dynamic Home Feed
- Secure Session Handling
- SQL file included for easy database setup

---

## 🧱 Tech Stack

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP (Core PHP)  
- **Database:** MySQL  
- **Server:** Apache (XAMPP / WAMP / LAMP)

---

## 📂 Project Structure

Twitter-Using-PHP/
│
├── css/
├── uploads/
│
├── comment_tweet.php
├── config.php
├── follow_toggle.php
├── home.php
├── index.php
├── like_tweet.php
├── login.php
├── logout.php
├── post_tweet.php
├── profile.php
├── register.php
├── view_profile.php
├── temp.php
├── sql.txt
│
└── README.md

yaml
Copy code

---

## ⚙️ Requirements

Make sure you have the following installed:

- PHP 7.4 or higher
- MySQL / MariaDB
- Apache Server
- XAMPP / WAMP / LAMP (recommended)

---

## 🛠️ Installation Steps

### 1️⃣ Clone the Repository
```bash
git clone https://github.com/Kevin-The-Dev/Twitter-Using-PHP.git
2️⃣ Move Project to Server Directory
XAMPP → htdocs

WAMP → www

3️⃣ Create Database
Open phpMyAdmin

Create a database (example: twitter_clone)

Import the sql.txt file

4️⃣ Configure Database
Edit config.php:

php
Copy code
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'twitter_clone');
5️⃣ Run the Project
Open your browser and visit:

arduino
Copy code
http://localhost/Twitter-Using-PHP/
📄 Pages Overview
File Name	Description
index.php	Login page
register.php	User registration
home.php	Twitter feed
post_tweet.php	Post new tweet
profile.php	User profile
view_profile.php	View other users
like_tweet.php	Like/unlike tweet
comment_tweet.php	Comment on tweet
follow_toggle.php	Follow/unfollow

🧠 Learning Purpose
This project is ideal for:

PHP beginners

College mini / major projects

Understanding social media backend logic

Learning MySQL relationships (users, tweets, followers)

🔮 Future Improvements
AJAX for realtime likes & comments

Search & hashtags

Direct messaging

Password hashing improvement

MVC architecture

🤝 Contribution
Contributions are welcome!
Feel free to fork the repo and submit a pull request.
