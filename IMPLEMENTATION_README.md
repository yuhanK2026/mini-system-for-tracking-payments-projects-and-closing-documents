# Payment Tracking Dashboard System

A comprehensive payment tracking dashboard system for digital agencies to manage projects, payments, and closing documents.

## Features Implemented

### 1. **Dashboard Summary**
- Total payments amount: 1,665,000 RUB
- Projects count: 7
- Payments count: 17
- Closed acts amount: 320,000 RUB
- Unclosed acts amount: 1,345,000 RUB
- Payments without sent acts: 4
- Payments with sent but unsigned acts: 7
- Payments requiring attention: 3

### 2. **Projects Table**
- Project name and status
- Client information
- Total payments per project
- Payments count
- Closed vs unclosed acts count
- Project status indicators

### 3. **Payments Table**
- Payment date and amount
- Project and client details
- Payment purpose and service stage
- Act status with visual indicators
- Action buttons for updating act status

### 4. **Filters System**
- Filter by project
- Filter by client
- Filter by act status (Not Sent, Awaiting Signature, Closed, Attention Required)
- Date range filtering
- Search by payment purpose or client name
- Reset filters functionality

### 5. **Act Management**
- Mark acts as sent
- Mark acts as signed
- Add manager comments
- Automatic status calculation based on business logic

## Business Logic Implementation

### Act Status Logic
1. **Not Sent**: Act not sent and not signed
2. **Awaiting Signature**: Act sent but not signed (within 30 days)
3. **Closed**: Act sent and signed
4. **Attention Required**: 
   - Act sent more than 30 days ago but not signed
   - Payment older than 60 days with no act sent

### Data Structure
- **Clients**: Legal entities with INN/OGRN
- **Projects**: Associated with clients
- **Payments**: Linked to projects and clients
- **Acts**: Closing documents for payments

## Technical Implementation

### Backend (Laravel)
- **Models**: Client, Project, Payment, Act with proper relationships
- **Controllers**: DashboardController with filtering logic, ActController for CRUD
- **API Endpoints**:
  - `GET /api/dashboard` - Main dashboard with filters
  - `PATCH /api/acts/{act}` - Update act status
  - `POST /api/acts` - Create new act
- **Database**: SQLite with seeded sample data
- **Migrations**: Proper schema with foreign key constraints

### Frontend (Vue.js)
- **Dashboard.vue**: Complete dashboard component with filters, tables, and modals
- **Responsive Design**: Grid layouts for summary cards
- **Interactive Features**: 
  - Real-time filtering
  - Status updates
  - Comment management
  - Visual status indicators
- **Styling**: Custom CSS with status-based colors

## Sample Data

The system includes seeded data with:
- 5 Clients (legal entities)
- 7 Projects
- 17 Payments with various amounts and dates
- 17 Acts with different statuses for testing

## How to Run

1. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Set up database**:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Build frontend**:
   ```bash
   npm run build
   ```

4. **Start servers**:
   ```bash
   # Terminal 1: Laravel server
   php artisan serve
   
   # Terminal 2: Vite dev server (optional)
   npm run dev
   ```

5. **Access dashboard**: `http://localhost:8000`

## Business Value

This system addresses the core business problem of tracking payments and closing documents in a digital agency:

1. **Visibility**: Clear overview of all payments and their document status
2. **Control**: Ability to track which payments require attention
3. **Efficiency**: Automated status calculations and filtering
4. **Scalability**: Structured data model that can integrate with CRM/banking systems

## Key Design Decisions

1. **Status Logic**: Implemented business rules for "attention required" based on time thresholds
2. **Filtering**: Server-side filtering to handle large datasets efficiently
3. **Relationships**: Proper Eloquent relationships for efficient data loading
4. **User Experience**: Interactive dashboard with visual feedback
5. **Maintainability**: Clean separation between business logic, data layer, and presentation

## Extensibility

The system is designed to be extended:
- Add user authentication
- Integrate with banking APIs
- Add reporting and analytics
- Implement document generation
- Add notifications for overdue acts
- Export functionality

## Testing

The implementation includes:
- Comprehensive sample data covering all scenarios
- Error handling in API endpoints
- Responsive frontend design
- Cross-browser compatibility