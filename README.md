# Payment Tracking Dashboard System

A comprehensive payment tracking dashboard for digital agencies to manage projects, payments, and closing documents.

## Overview

This system helps digital agencies track payments from clients, monitor project progress, and manage closing documents (acts). It provides a clear overview of the relationship between projects, legal entities, payments, work stages, and document statuses.

## Features

### 📊 Dashboard Summary
- Total payments amount with currency formatting
- Project and payment counts
- Closed vs unclosed acts financial overview
- Status alerts for payments requiring attention

### 🏢 Projects Management
- Project listing with client information
- Payment statistics per project
- Closed vs unclosed acts tracking
- Project status indicators

### 💰 Payments Tracking
- Payment details with dates and amounts
- Project and client associations
- Payment purpose and service stage tracking
- Act status management with visual indicators

### 📋 Act Status Management
- Automated status calculation:
  - **Not Sent**: Act not sent and not signed
  - **Awaiting Signature**: Act sent but not signed (within 30 days)
  - **Closed**: Act sent and signed
  - **Attention Required**: Sent >30 days ago but not signed, or payment >60 days old with no act
- Interactive status updates
- Manager comment system

### 🔍 Advanced Filtering
- Filter by project
- Filter by client/legal entity
- Filter by act status
- Date range filtering
- Search by payment purpose or client name
- Reset filters functionality

## Technology Stack

### Backend
- **Framework**: Laravel 11
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **API**: RESTful endpoints

### Frontend
- **Framework**: Vue.js 3
- **Build Tool**: Vite
- **Styling**: Custom CSS with responsive design

### Development Tools
- **Package Manager**: Composer (PHP), npm (JavaScript)
- **Development Servers**: Laravel Artisan Serve + Vite Dev Server

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd payments-dashboard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Set up database**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

## Running the Application

### Development Mode
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite development server (optional, for hot reload)
npm run dev
```

Access the application at: `http://localhost:8000`

### Production Mode
```bash
# Build assets for production
npm run build

# Start Laravel server
php artisan serve
```

## API Endpoints

### Dashboard
- `GET /api/dashboard` - Get dashboard data with optional filtering
  - Query parameters: `project_id`, `client_id`, `act_status`, `start_date`, `end_date`, `search`

### Act Management
- `PATCH /api/acts/{act}` - Update act status
- `POST /api/acts` - Create new act

## Database Schema

### Entities
1. **Clients** - Legal entities with INN/OGRN
2. **Projects** - Projects associated with clients
3. **Payments** - Payments linked to projects and clients
4. **Acts** - Closing documents for payments with status tracking

### Relationships
- Client → hasMany → Projects
- Project → belongsTo → Client
- Project → hasMany → Payments
- Payment → belongsTo → Project, Client
- Payment → hasOne → Act
- Act → belongsTo → Payment

## Business Logic

### Status Calculation
The system automatically calculates act status based on:
- Whether the act is sent (`is_sent`)
- Whether the act is signed (`is_signed`)
- Time elapsed since sending (30+ days triggers "Attention Required")
- Payment age (60+ days with no act triggers "Attention Required")

### Summary Calculations
- **Total Amount**: Sum of all payment amounts
- **Closed Acts Amount**: Sum of amounts for payments with sent and signed acts
- **Unclosed Acts Amount**: Sum of amounts for payments without signed acts
- **Attention Required Count**: Payments with status "attention_required"

## Sample Data

The system comes with seeded sample data including:
- 5 different clients/legal entities
- 7 projects across various services (web development, SEO, advertising, design, support, SMM, CRM)
- 17 payments with varying amounts and dates
- 17 acts with different statuses for comprehensive testing

## Project Structure

```
payments-dashboard/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php
│   │       └── ActController.php
│   └── Models/
│       ├── Client.php
│       ├── Project.php
│       ├── Payment.php
│       └── Act.php
├── database/
│   ├── migrations/
│   │   ├── create_clients_table.php
│   │   ├── create_projects_table.php
│   │   ├── create_payments_table.php
│   │   └── create_acts_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── PaymentTrackingSeeder.php
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   ├── bootstrap.js
│   │   └── Dashboard.vue
│   └── views/
│       └── welcome.blade.php
├── routes/
│   ├── web.php
│   └── api.php
└── public/
    └── build/          # Compiled assets
```

## Usage Examples

### Filtering Payments
1. Select a specific project from the dropdown
2. Choose a date range for payments
3. Filter by act status (e.g., "Awaiting Signature")
4. Search for specific payment purposes or clients

### Managing Acts
1. Find a payment without a sent act
2. Click "Mark as Sent" to update the status
3. Once sent, you can click "Mark as Signed" when received
4. Add manager comments for additional context

### Monitoring Status
- **Yellow rows**: Payments without sent acts
- **Blue rows**: Payments with sent but unsigned acts
- **Status colors**: Red (Not Sent), Orange (Awaiting), Green (Closed), Purple (Attention)

## Development

### Adding New Features
1. **New Database Fields**: Create migration files
2. **Business Logic**: Add to models or services
3. **API Endpoints**: Create controllers and add routes
4. **Frontend Components**: Create Vue components
5. **Testing**: Write tests for new functionality

### Code Style
- Follow Laravel and Vue.js best practices
- Use meaningful variable and function names
- Add comments for complex logic
- Keep components focused and reusable

## Testing

### Sample Test Scenarios
1. **Filter Testing**: Verify filters return correct data subsets
2. **Status Calculation**: Test act status logic with various date scenarios
3. **API Validation**: Test endpoint validation and error handling
4. **UI Interaction**: Test all interactive elements work correctly

### Running Tests
```bash
php artisan test
```

## Deployment Considerations

### Production Setup
1. Switch to MySQL or PostgreSQL database
2. Configure proper environment variables
3. Set up proper error logging
4. Configure SSL/TLS for secure connections
5. Set up backup procedures

### Performance Optimization
1. Implement database indexing for frequently queried fields
2. Add caching for dashboard data
3. Optimize asset delivery with CDN
4. Implement pagination for large datasets

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-source software licensed under the MIT license.

## Support

For issues or questions:
1. Check the existing documentation
2. Review the code structure
3. Create an issue in the repository

---

**Built with ❤️ for digital agencies managing complex payment and document workflows**
