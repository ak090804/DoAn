# Email Verification Registration System

## Overview
This document describes the newly implemented email verification system for customer registration.

## Features
- **Three-step registration process:**
  1. Customer enters email and receives a verification code via email
  2. Customer enters the 6-digit verification code sent to their email
  3. Customer fills in personal details (name, password, phone, address) and completes registration
- **Security measures:**
  - 6-digit numeric verification code
  - Email-based verification (unique per email)
  - 15-minute expiration time for verification codes
  - 5-attempt limit to prevent brute force attacks
  - Automatic code deletion after successful verification or expiration

## Database Schema
### `email_verification_codes` Table
```sql
CREATE TABLE email_verification_codes (
    id BIGINT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    code VARCHAR(255) NOT NULL,
    attempts INT DEFAULT 0,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

## File Structure

### Controllers
- **`app/Http/Controllers/EmailVerificationController.php`**
  - `sendCode(Request $request)` - Generates and sends verification code via email
  - `verifyCode(Request $request)` - Validates the verification code
  - `register(Request $request)` - Creates user account after verification
  - `showRegister()` - Shows registration form

### Models
- **`app/Models/EmailVerificationCode.php`**
  - Handles email verification codes in database
  - Fillable fields: `email`, `code`, `attempts`, `expires_at`
  - Casts `expires_at` as datetime

### Views
- **`resources/views/auth/register.blade.php`**
  - Updated with multi-step form
  - Step 1: Email input with "Send Code" button
  - Step 2: Verification code input with validation
  - Step 3: Personal information form
  - JavaScript handles form state transitions

### Routes
```php
// GET: Show registration form
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// POST: Send verification code to email
Route::post('/register/send-code', [EmailVerificationController::class, 'sendCode'])->name('register.send-code');

// POST: Verify the code entered by user
Route::post('/register/verify-code', [EmailVerificationController::class, 'verifyCode'])->name('register.verify-code');

// POST: Complete registration with personal details
Route::post('/register', [EmailVerificationController::class, 'register'])->name('register.post');
```

## How It Works

### Step 1: Send Verification Code
1. Customer enters email on registration page
2. Frontend sends POST request to `/register/send-code`
3. Backend validates email is not already in use
4. Backend generates 6-digit random code
5. Backend saves code to `email_verification_codes` table with 15-minute expiration
6. Backend sends code to customer's email using Laravel Mail
7. Frontend displays Step 2 form

### Step 2: Verify Code
1. Customer receives email with verification code
2. Customer enters 6-digit code on register page
3. Frontend sends POST request to `/register/verify-code`
4. Backend validates:
   - Code matches the one sent
   - Code hasn't expired (15 minutes)
   - Less than 5 failed attempts
5. If valid: code is deleted from database, Step 3 form is displayed
6. If invalid: attempt count incremented, error message shown

### Step 3: Complete Registration
1. Customer fills in name, password, phone, address
2. Frontend sends POST request to `/register`
3. Backend validates all fields
4. Backend creates User record (with hashed password)
5. Backend creates Customer record linked to User
6. User is automatically logged in via session
7. Redirected to home page with success message

## Mail Configuration

The system uses Laravel's Mail facade with configuration from `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit          # Local mail testing tool
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Laravel"
```

For production, update `.env` with real SMTP settings (Gmail, SendGrid, etc.)

### Testing Mail Locally
When using `mailpit`, verification codes are captured in a local mail interface. No external email service needed for development/testing.

## Error Handling

### Email Errors
- "Email đã tồn tại" (Email already exists)
- "Lỗi gửi email" (Email sending error)

### Code Verification Errors
- "Email chưa được yêu cầu xác thực" (Email not requested for verification)
- "Mã xác thực đã hết hạn" (Code expired - request new code)
- "Bạn đã nhập sai quá nhiều lần" (Too many failed attempts - request new code)
- "Mã xác thực không đúng" (Incorrect code - try again)

### Registration Errors
- "Email chưa được xác thực" (Email not verified)
- "Email đã tồn tại" (Email already in system)
- Validation errors for name, password, phone fields

## Frontend JavaScript
The register.blade.php contains JavaScript that:
1. Prevents form submission, uses AJAX instead
2. Shows/hides form steps dynamically
3. Handles loading states on buttons
4. Displays error/success messages
5. Validates code format (6 digits only)
6. Allows navigation back between steps

## Security Features
1. **Email Verification:** Only verified emails can create accounts
2. **Rate Limiting:** Max 5 failed attempts per verification code
3. **Expiration:** Codes expire after 15 minutes
4. **CSRF Protection:** All forms use @csrf token
5. **Password Hashing:** Uses Laravel Hash::make()
6. **Unique Email Constraint:** Database enforces unique emails
7. **Attempt Tracking:** Failed attempts are counted and limited

## Testing the Feature

### Manual Test Flow
1. Go to `/register`
2. Enter email address (not already in database)
3. Click "Gửi mã xác thực"
4. Check mailpit interface (if local) or email for the verification code
5. Enter the 6-digit code on Step 2
6. Fill in name, password, phone on Step 3
7. Click "Đăng ký"
8. Should be logged in and redirected to home page

### Edge Cases to Test
- Using email that already exists (should fail at step 1)
- Entering wrong code (should show error, allow retry up to 5 times)
- Waiting >15 minutes to verify code (should fail with expiration error)
- Not filling required fields on Step 3 (browser validation prevents submission)

## Future Enhancements
- Add email resend code button (currently requires new email)
- Add countdown timer showing code expiration time
- Add email confirmation message after successful registration
- Add email template/styling instead of plain text
- Add two-factor authentication for login
- Add spam prevention (rate limiting per IP)

## API Response Format

### sendCode Response
```json
{
  "success": true,
  "message": "Mã xác thực đã được gửi đến email của bạn"
}
```

### verifyCode Response (Success)
```json
{
  "success": true,
  "message": "Xác thực email thành công!"
}
```

### verifyCode Response (Error)
```json
{
  "success": false,
  "message": "Mã xác thực không đúng. Vui lòng thử lại."
}
```

## References
- Migration: `database/migrations/2025_11_26_142424_create_email_verification_codes_table.php`
- Controller: `app/Http/Controllers/EmailVerificationController.php`
- Model: `app/Models/EmailVerificationCode.php`
- View: `resources/views/auth/register.blade.php`
- Routes: `routes/web.php` (lines with register.send-code and register.verify-code)
