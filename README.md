# Suvaya - Bakery Management System

A full-stack web application for managing a bakery business with features including menu management, online ordering, reservations, event management, and M-Pesa payment integration.

## Project Structure

```
The-Suvaya-Experience/
├── frontend/           # Vue.js frontend application
├── backend/           # PHP backend API
├── IMAGE_REFERENCE.txt # Image reference guide for menu items
└── README.md          # This file
```

## Features

- **Menu Management**: Browse cakes, pastries, desserts, drinks, and snacks
- **Online Ordering**: Add items to cart and place orders
- **Package Recommendations**: AI-powered package suggestions based on event type, guest count, and budget
- **Reservations**: Book dates and times for events
- **Events Management**: View and register for baking classes and special events
- **M-Pesa Integration**: Pay deposits via M-Pesa STK Push
- **Admin Dashboard**: Manage menu items, orders, reservations, events, and messages
- **User Authentication**: Secure login and signup system
- **Email Notifications**: Automated emails for orders and reservations
- **Print Receipts**: Print order confirmations
- **Toast Notifications**: User-friendly feedback messages

## Tech Stack

### Frontend
- **Framework**: Vue 3 (Composition API)
- **UI Library**: Vuetify 3 (Material Design)
- **State Management**: Pinia
- **Routing**: Vue Router
- **Build Tool**: Vite
- **Notifications**: vue-toastification

### Backend
- **Language**: PHP
- **Database**: MySQL
- **Payment Gateway**: M-Pesa Daraja API
- **Email**: PHPMailer

## Getting Started

### Prerequisites
- Node.js (v16 or higher)
- PHP (v7.4 or higher)
- MySQL
- Composer (for PHP dependencies)

### Installation

#### 1. Clone the repository
```bash
git clone <repository-url>
cd The-Suvaya-Experience
```

#### 2. Frontend Setup
```bash
cd frontend
npm install
```

#### 3. Backend Setup
```bash
cd ../backend
composer install  # If using Composer for dependencies
```

#### 4. Database Setup
- Create a MySQL database named `suvaya_db`
- Import the schema:
```bash
mysql -u root -p suvaya_db < backend/database/schema.sql
```

#### 5. Configuration
- Update database credentials in `backend/config/database.php`
- Configure M-Pesa credentials in `backend/config/mpesa.php`
- Update email settings in `backend/utils/email.php`

### Running the Application

#### Start Frontend (Development)
```bash
cd frontend
npm run dev
```
Frontend will run on `http://localhost:5173`

#### Start Backend
- **Windows**: Run `START_BACKEND.bat` from root directory
- **Mac/Linux**:
```bash
cd backend
php -S localhost:8000
```
Backend API will run on `http://localhost:8000`

### Building for Production

#### Frontend
```bash
cd frontend
npm run build
```
Built files will be in `frontend/dist/`

## Admin Access

- **URL**: http://localhost:5173/admin
- **Email**: admin@suvaya.com
- **Password**: admin123

## Key Directories

### Frontend
```
frontend/
├── src/
│   ├── Components/        # Vue components
│   ├── stores/           # Pinia stores
│   ├── services/         # API services
│   ├── assets/           # Images, styles
│   └── router/           # Vue Router configuration
├── public/               # Static assets
│   ├── Menu/            # Menu item images
│   ├── Cakes/           # Cake images
│   ├── Homepage/        # Homepage images
│   └── About/           # About page images
└── package.json
```

### Backend
```
backend/
├── api/                  # API endpoints
│   ├── auth/            # Authentication
│   ├── menu/            # Menu management
│   ├── orders/          # Order processing
│   ├── reservations/    # Reservation management
│   ├── events/          # Event management
│   ├── mpesa/           # M-Pesa integration
│   └── contact/         # Contact messages
├── config/              # Configuration files
├── database/            # Database schemas
└── utils/               # Helper functions
```

## API Endpoints

### Authentication
- `POST /api/auth/signup` - Register new user
- `POST /api/auth/login` - User login
- `GET /api/auth/profile` - Get user profile

### Menu
- `GET /api/menu/get.php` - Get all menu items
- `POST /api/menu/create.php` - Create menu item (Admin)

### Orders
- `GET /api/orders/get.php` - Get orders
- `POST /api/orders/create.php` - Create order

### Reservations
- `GET /api/reservations/get.php` - Get reservations
- `POST /api/reservations/create.php` - Create reservation

### M-Pesa
- `POST /api/mpesa/stk-push.php` - Initiate M-Pesa payment
- `POST /api/mpesa/callback.php` - M-Pesa callback handler

## Adding Menu Items with Images

1. Login to admin panel
2. Navigate to Menu tab
3. Click "Add Item"
4. Fill in details:
   - Name, Description, Category
   - Price, Weight (for cakes)
   - Image path (see IMAGE_REFERENCE.txt for available images)
5. Available image categories:
   - Cakes: `/Cakes/[filename].jpg`
   - Menu items: `/Menu/[filename].jpg`

## Environment Variables

Create a `.env` file in the backend directory (if needed):
```
DB_HOST=localhost
DB_NAME=suvaya_db
DB_USER=root
DB_PASS=

MPESA_CONSUMER_KEY=your_key
MPESA_CONSUMER_SECRET=your_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey

SMTP_HOST=smtp.gmail.com
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password
```

## Features Checklist

- ✅ Menu browsing with categories
- ✅ Add to cart functionality
- ✅ Product images in menu and checkout
- ✅ Package recommendation system
- ✅ Responsive design
- ✅ Print receipts
- ✅ Email notifications
- ✅ Toast notifications
- ✅ M-Pesa payment integration
- ✅ Admin dashboard
- ✅ User authentication
- ✅ Order management
- ✅ Reservation system
- ✅ Events management

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a pull request

## License

This project is proprietary and confidential.

## Support

For support or inquiries, contact: info@suvaya.com

---

Built for Suvaya Bakery
