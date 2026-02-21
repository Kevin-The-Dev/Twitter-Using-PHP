# Twitter Clone (X-Style Social Media Platform)

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>

<p align="center">
  <b>A fully functional Twitter/X-like social media web application built with Core PHP and MySQL</b>
</p>

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Technical Architecture](#technical-architecture)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
- [Database Schema](#database-schema)
- [Security Features](#security-features)
- [Screenshots](#screenshots)
- [Future Enhancements](#future-enhancements)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This project is a **complete social media platform** that replicates the core functionality of Twitter/X. Built entirely from scratch using **Core PHP** (no frameworks), it demonstrates fundamental web development concepts including user authentication, database relationships, real-time interactions, and responsive design.

### Purpose

- **Educational Resource**: Learn how social media platforms work under the hood
- **Portfolio Project**: Showcase full-stack PHP development skills
- **Foundation**: Starting point for building more complex social applications

---

## Key Features

### User Authentication & Management
- **Secure Registration** with email/username validation
- **Login System** with password hashing (bcrypt)
- **Session Management** with secure logout functionality
- **Profile Customization**:
  - Upload profile pictures
  - Edit bio/description
  - View follower/following counts

### Content Management
- **Post Tweets**: Create text-based posts (280 character limit)
- **View Timeline**: Chronological feed of all tweets
- **User Profiles**: Dedicated profile pages for each user
- **Profile Pictures**: Image upload with validation

### Social Interactions
- **Like/Unlike System**: Real-time AJAX-powered likes with instant count updates
- **Commenting**: Add comments to any tweet
- **Follow/Unfollow**: Build your social network
- **User Discovery**: View other users' profiles and their tweets

### User Interface
- **Responsive Design**: Mobile-first approach with breakpoints
- **Modern UI**: Twitter/X-inspired design with clean aesthetics
- **Interactive Elements**: AJAX for seamless user experience
- **Font Awesome Icons**: Professional iconography throughout

### Security Features
- **Prepared Statements**: SQL injection prevention
- **Password Hashing**: Secure credential storage
- **Input Sanitization**: XSS protection with `htmlspecialchars()`
- **Session Validation**: Protected routes for authenticated users

---

## Technical Architecture

### Technology Stack

| Layer | Technology | Purpose |
|-------|------------|---------|
| **Backend** | PHP 7.4+ | Server-side logic, routing, database interaction |
| **Database** | MySQL | Relational data storage |
| **Frontend** | HTML5, CSS3, JavaScript | UI structure, styling, client-side interactivity |
| **Icons** | Font Awesome 6 | Professional iconography |
| **Server** | Apache/Nginx | Web server (via XAMPP/WAMP/LAMP) |

### Architecture Pattern

```
┌─────────────────────────────────────────────────────────────┐
│                        Client Layer                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Browser   │  │  JavaScript │  │  CSS Stylesheets    │  │
│  │   (HTML)    │  │   (AJAX)    │  │   (Responsive)      │  │
│  └──────┬──────┘  └──────┬──────┘  └──────────┬──────────┘  │
└─────────┼────────────────┼────────────────────┼─────────────┘
          │                │                    │
          └────────────────┴────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                      Server Layer                           │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              Apache/Nginx Web Server                │    │
│  └────────────────────────┬────────────────────────────┘    │
│                           │                                 │
│  ┌────────────────────────▼────────────────────────────┐    │
│  │                   PHP Engine                        │    │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────────────┐   │    │
│  │  │  index   │  │  login   │  │  post_tweet      │   │    │
│  │  │  home    │  │ register │  │  like_tweet      │   │    │
│  │  │ profile  │  │  logout  │  │  comment_tweet   │   │    │
│  │  └──────────┘  └──────────┘  └──────────────────┘   │    │
│  └────────────────────────┬────────────────────────────┘    │
└───────────────────────────┼─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│                    Database Layer                           │
│  ┌─────────────────────────────────────────────────────┐    │
│  │              MySQL Server                           │    │
│  │  ┌─────────┐ ┌─────────┐ ┌──────────┐ ┌──────────┐  │    │
│  │  │  users  │ │ tweets  │ │  likes   │ │ comments │  │    │
│  │  │followers│ │         │ │          │ │          │  │    │
│  │  └─────────┘ └─────────┘ └──────────┘ └──────────┘  │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

---

## Project Structure

```
Twitter-Using-PHP/
│
├── Core Application Files
│   ├── index.php              # Main feed (all tweets)
│   ├── home.php               # Alternative home page
│   ├── login.php              # User authentication
│   ├── register.php           # User registration
│   ├── logout.php             # Session termination
│   ├── profile.php            # User's own profile
│   └── view_profile.php       # Other users' profiles
│
├── Action Handlers
│   ├── post_tweet.php         # Create new tweet
│   ├── like_tweet.php         # Like/unlike tweets (AJAX)
│   ├── comment_tweet.php      # Add comments
│   └── follow_toggle.php      # Follow/unfollow users
│
├── Configuration
│   ├── config.php             # Database connection
│   └── sql.txt                # Database schema
│
├── Assets
│   └── css/
│       └── tweet_form.css     # Tweet form styling
│
├── Uploads
│   └── uploads/               # User profile pictures
│
└── Documentation
    └── README.md              # This file
```

---

## Installation & Setup

### Prerequisites

Ensure you have the following installed:

- **PHP** 7.4 or higher
- **MySQL** 5.7+ or **MariaDB** 10.3+
- **Apache** Web Server
- **XAMPP/WAMP/LAMP** (recommended for local development)

### Step-by-Step Installation

#### 1. Clone the Repository

```bash
git clone https://github.com/Kevin-The-Dev/Twitter-Using-PHP.git
cd Twitter-Using-PHP
```

#### 2. Move to Web Server Directory

**For XAMPP:**
```bash
mv Twitter-Using-PHP /Applications/XAMPP/htdocs/
```

**For WAMP:**
```bash
mv Twitter-Using-PHP C:\wamp64\www\
```

**For LAMP:**
```bash
sudo mv Twitter-Using-PHP /var/www/html/
```

#### 3. Create the Database

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Create a new database named `twitter_clone`
3. Import the `sql.txt` file:
   - Click on the `twitter_clone` database
   - Go to **Import** tab
   - Select `sql.txt` file
   - Click **Go**

#### 4. Configure Database Connection

Edit `config.php` with your database credentials:

```php
<?php
// Database configuration
$host = "localhost";      // Your database host
$user = "root";           // Your database username
$pass = "";               // Your database password
$db   = "twitter_clone";  // Your database name

// Define upload directory
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
```

#### 5. Set Upload Permissions

Ensure the `uploads` directory is writable:

```bash
chmod 755 uploads/
```

On Windows, ensure the folder has write permissions for the web server.

#### 6. Access the Application

Open your browser and navigate to:

```
http://localhost/Twitter-Using-PHP/
```

---

## Configuration

### Database Configuration (`config.php`)

| Variable | Description | Default |
|----------|-------------|---------|
| `$host` | Database server address | `localhost` |
| `$user` | Database username | `root` |
| `$pass` | Database password | (empty) |
| `$db` | Database name | `twitter_clone` |

### Environment Variables (Optional)

For production deployment, consider using environment variables:

```php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'twitter_clone';
```

---

## Usage Guide

### Getting Started

1. **Register an Account**
   - Navigate to the registration page
   - Enter username, email, and password
   - Click "Sign Up"

2. **Login**
   - Use your email/username and password
   - You'll be redirected to the main feed

3. **Post a Tweet**
   - Type your message in the text area
   - Click "Post Tweet"
   - Your tweet appears in the feed

4. **Interact with Tweets**
   - **Like**: Click the heart icon (toggles like/unlike)
   - **Comment**: Click the comment icon and add your thoughts
   - **View Profile**: Click on any username to see their profile

5. **Manage Your Profile**
   - Click "Profile" in the sidebar
   - Update your bio
   - Upload a profile picture
   - View your tweets and follower stats

6. **Follow Users**
   - Visit any user's profile
   - Click "Follow" to see their tweets in your network
   - Click "Unfollow" to stop following

### User Flow Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Landing   │────▶│   Register  │────▶│    Login    │
│    Page     │     │    Page     │     │    Page     │
└─────────────┘     └─────────────┘     └──────┬──────┘
                                               │
                                               ▼
                                        ┌─────────────┐
                                        │  Main Feed  │
                                        │  (index.php)│
                                        └──────┬──────┘
                                               │
                    ┌──────────────────────────┼──────────────────────────┐
                    │                          │                          │
                    ▼                          ▼                          ▼
            ┌─────────────┐           ┌─────────────┐           ┌─────────────┐
            │ Post Tweet  │           │ View Profile│           │   Logout    │
            │             │           │             │           │             │
            └─────────────┘           └──────┬──────┘           └─────────────┘
                                             │
                    ┌────────────────────────┼────────────────────────┐
                    │                        │                        │
                    ▼                        ▼                        ▼
            ┌─────────────┐          ┌─────────────┐          ┌─────────────┐
            │  Edit Bio   │          │   Upload    │          │  View Own   │
            │             │          │   Avatar    │          │   Tweets    │
            └─────────────┘          └─────────────┘          └─────────────┘
```

---

## Database Schema

### Entity Relationship Diagram

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│      users      │       │     tweets      │       │     likes       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ PK id           │◄──────┤ PK id           │◄──────┤ PK id           │
│    username     │       │ FK user_id      │       │ FK user_id      │
│    email        │       │    text         │       │ FK tweet_id     │
│    password     │       │    created_at   │       │    created_at   │
│    bio          │       └─────────────────┘       └─────────────────┘
│    profile_pic  │                │
│    created_at   │                │
└─────────────────┘                │
         │                         │
         │                ┌────────┴────────┐
         │                │                 │
         │                ▼                 ▼
         │       ┌─────────────────┐  ┌─────────────────┐
         │       │    comments     │  │    followers    │
         │       ├─────────────────┤  ├─────────────────┤
         │       │ PK id           │  │ PK id           │
         │       │ FK user_id      │  │ FK follower_id  │──┐
         │       │ FK tweet_id     │  │ FK followed_id  │◄─┘
         └──────►│    comment_text │  └─────────────────┘
                 │    created_at   │
                 └─────────────────┘
```

### Table Definitions

#### `users` Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique user identifier |
| `username` | VARCHAR(50) | Unique username |
| `email` | VARCHAR(100) | Unique email address |
| `password` | VARCHAR(255) | Hashed password |
| `bio` | TEXT | User biography |
| `profile_picture` | VARCHAR(255) | Profile image filename |
| `created_at` | TIMESTAMP | Account creation date |

#### `tweets` Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique tweet identifier |
| `user_id` | INT (FK) | Author's user ID |
| `text` | VARCHAR(280) | Tweet content |
| `created_at` | TIMESTAMP | Post date |

#### `likes` Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique like identifier |
| `user_id` | INT (FK) | User who liked |
| `tweet_id` | INT (FK) | Liked tweet ID |
| `created_at` | TIMESTAMP | Like date |

#### `comments` Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique comment identifier |
| `user_id` | INT (FK) | Comment author's ID |
| `tweet_id` | INT (FK) | Parent tweet ID |
| `comment_text` | TEXT | Comment content |
| `created_at` | TIMESTAMP | Comment date |

#### `followers` Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique relationship ID |
| `follower_id` | INT (FK) | User who follows |
| `followed_id` | INT (FK) | User being followed |

---

## Security Features

### Implemented Security Measures

1. **SQL Injection Prevention**
   - All database queries use prepared statements
   - User input is never directly concatenated into queries

2. **XSS Protection**
   - Output encoding with `htmlspecialchars()`
   - Prevents script injection in tweets, comments, and profiles

3. **Password Security**
   - Bcrypt hashing with `password_hash()`
   - Secure verification with `password_verify()`

4. **Session Management**
   - Secure session initialization
   - Proper session destruction on logout
   - Session validation on protected pages

5. **File Upload Security**
   - Image type validation with `getimagesize()`
   - Unique filename generation with timestamps
   - Server-side file size and type checks

---

## Responsive Design

The application is fully responsive and optimized for:

- **Desktop**: Full sidebar navigation, expanded layouts
- **Tablet**: Adaptive sidebar, optimized spacing
- **Mobile**: Bottom navigation bar, touch-friendly buttons

### Breakpoints

| Device | Width | Layout Changes |
|--------|-------|----------------|
| Mobile | < 768px | Bottom nav, stacked layout |
| Tablet | 768px - 1024px | Condensed sidebar |
| Desktop | > 1024px | Full sidebar, max-width content |

---

## Future Enhancements

### Planned Features

- [ ] **AJAX Comments**: Real-time comment posting without page reload
- [ ] **Search Functionality**: Search for users and tweets
- [ ] **Hashtags**: Tag-based content discovery
- [ ] **Direct Messaging**: Private user-to-user chat
- [ ] **Notifications**: Real-time alerts for likes, comments, follows
- [ ] **Image Uploads**: Attach images to tweets
- [ ] **Retweets**: Share others' tweets
- [ ] **User Mentions**: @username tagging in tweets
- [ ] **Trending Topics**: Popular hashtags and discussions
- [ ] **Dark Mode**: Toggle between light and dark themes
- [ ] **MVC Architecture**: Refactor to Model-View-Controller pattern
- [ ] **API Development**: RESTful API for mobile apps

### Technical Improvements

- [ ] **Composer Integration**: Dependency management
- [ ] **Autoloading**: PSR-4 autoloading for classes
- [ ] **Unit Testing**: PHPUnit test coverage
- [ ] **Code Linting**: PHP_CodeSniffer integration
- [ ] **Docker Support**: Containerized development environment

---

## Contributing

Contributions are welcome! Here's how you can help:

### How to Contribute

1. **Fork the Repository**
   ```bash
   git clone https://github.com/Kevin-The-Dev/Twitter-Using-PHP.git
   ```

2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make Your Changes**
   - Follow existing code style
   - Add comments where necessary
   - Test your changes thoroughly

4. **Commit Your Changes**
   ```bash
   git add .
   git commit -m "Add: Description of your changes"
   ```

5. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Submit a Pull Request**
   - Provide clear description of changes
   - Reference any related issues

### Contribution Guidelines

- Follow PSR coding standards
- Write clear, concise commit messages
- Update documentation for new features
- Ensure backward compatibility
- Test on multiple browsers/devices


---

## Acknowledgments

- **Font Awesome** for the beautiful icons
- **XAMPP/WAMP/LAMP** communities for development tools
- **Twitter/X** for UI/UX inspiration

---

## Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/Kevin-The-Dev/Twitter-Using-PHP/issues) page
2. Create a new issue with detailed description
3. Include steps to reproduce the problem

---

<p align="center">
  <a href="https://github.com/Kevin-The-Dev/Twitter-Using-PHP">Star this repository</a> if you found it helpful!
</p>
