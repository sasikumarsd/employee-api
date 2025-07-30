# Employee Management API - Laravel Project

## Overview

This Laravel project provides a RESTful API for managing employees, their departments, contacts, and addresses. It includes features such as CRUD operations, eager loading, searching, pagination, and input validation.

---

## Requirements

- PHP >= 8.0
- Composer
- Laravel >= 10
- MySQL

---

## Setup Instructions

1. **Clone the repository**:

```bash
   git clone https://github.com/sasikumarsd/employee-api.git
   cd employee-api
```

2. **Install dependencies**:

```bash
   composer install
```

3. **Create and configure **``:

```bash
   cp .env.example .env
   php artisan key:generate
```

4. **Update database credentials** in `.env`:

```
   DB_DATABASE=your_db
   DB_USERNAME=root
   DB_PASSWORD=
```


5. **Start the development server**:

```bash
   php artisan serve
```

---

## API Endpoints

### Departments

- `GET /api/departments` - List all departments
- `POST /api/departments` - Create department
- `GET /api/departments/{id}` - Show department
- `PUT /api/departments/{id}` - Update department
- `DELETE /api/departments/{id}` - Delete department

### Employees

- `GET /api/employees` - List all employees (with pagination)
- `GET /api/employees/search?search={keyword}` - Search by name or email
- `GET /api/employees/{id}` - Show employee with relations
- `POST /api/employees` - Create employee with contacts and addresses
- `PUT /api/employees/{id}` - Update employee data and nested relations
- `DELETE /api/employees/{id}` - Delete employee and associated data

---

## Sample Request: Create Employee

```json
POST /api/employees
{
  "name": "John Doe",
  "email": "john@example.com",
  "department_id": 1,
  "contacts": ["9876543210", "9123456780"],
  "addresses": [
    {
      "address_line1": "123 Street",
      "address_line2": "Apt 101",
      "city": "Chennai",
      "state": "TN",
      "postal_code": "600001"
    },
    {
      "address_line1": "2nd Avenue",
      "address_line2": "Flat B2",
      "city": "Bangalore",
      "state": "KA",
      "postal_code": "560001"
    }
  ]
}
```

---

## Pagination Example

- `GET /api/employees?per_page=1&page=1`
- Default per page: 10
- Supports metadata like `total`, `current_page`, `last_page` in response.

---

## Search Example

- `GET /api/employees/search?search=john`
- Case-insensitive search through name and email

---

## Validation & Security

- Laravel Form Requests used for employee and department creation and updates.
- Proper 404 and 422 responses for invalid data.
- JSON structure standardized for errors and success.
- Input validation for all request fields
- Sanitized search inputs
- API routes protected using default Laravel CSRF & headers (you may extend this with Sanctum or Passport for token-based auth)

---

## Notes

- Eager loading is implemented to optimize queries.
- Cascading delete handled for contacts and addresses.

---

## Author

Developed by: Sasi Kumar
Contact: sasikrs18@gmail.com

---

## License

This project is open-source and free to use under the MIT License.

