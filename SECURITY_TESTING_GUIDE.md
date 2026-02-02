# Security Implementation Testing Guide

## Overview
This document provides comprehensive testing instructions for the security improvements implemented in the Sportlarity application.

## Prerequisites
- Application should be running (php artisan serve or configured web server)
- Database migrations have been run
- Test users have been seeded

## Setup Test Environment

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Test Users
```bash
php artisan db:seed --class=SecurityTestSeeder --force
```

This creates the following test accounts:
- **superadmin@test.com** / sportlarity (password change required)
- **coach@test.com** / sportlarity (password change required)
- **professional@test.com** / sportlarity (password change required)
- **wellness@test.com** / sportlarity (password change required)
- **normal@test.com** / NewSecurePassword123 (no password change required)

---

## Test Cases

### ✅ TEST 1: Mandatory Password Change Flow

**Objective:** Verify that users with `password_change_required = true` are forced to change their password.

**Steps:**
1. Navigate to `/admin` (or your Filament admin URL)
2. Login with: **superadmin@test.com** / **sportlarity**
3. **Expected:** You should be immediately redirected to `/admin/auth/change-password`
4. **Expected:** You cannot access any other admin pages
5. Try to navigate to `/admin` or `/admin/users`
6. **Expected:** You are redirected back to the change password page

**Test Password Change Form:**
1. On the change password page, fill in:
   - Current Password: `sportlarity`
   - New Password: `sportlarity`
   - Confirm Password: `sportlarity`
2. Submit the form
3. **Expected:** Error message: "New password must be different from 'sportlarity'"

4. Fill in:
   - Current Password: `sportlarity`
   - New Password: `weak`
   - Confirm Password: `weak`
5. Submit
6. **Expected:** Validation errors (too short, missing requirements)

7. Fill in:
   - Current Password: `sportlarity`
   - New Password: `SecurePassword123`
   - Confirm Password: `SecurePassword123`
8. Submit
9. **Expected:** Success! Redirected to dashboard
10. **Expected:** Can now access all admin pages normally

**Verify Password Change Persisted:**
1. Logout
2. Try to login with old password: **superadmin@test.com** / **sportlarity**
3. **Expected:** Login fails
4. Login with new password: **superadmin@test.com** / **SecurePassword123**
5. **Expected:** Login succeeds and you go straight to dashboard (no redirect to change password)

---

### ✅ TEST 2: Users Without Password Change Requirement

**Objective:** Verify normal users can login without being forced to change password.

**Steps:**
1. Logout if logged in
2. Login with: **normal@test.com** / **NewSecurePassword123**
3. **Expected:** Login succeeds
4. **Expected:** Redirected directly to dashboard (NOT to change password page)
5. **Expected:** Can access all pages normally

---

### ✅ TEST 3: Avatar Upload Validation

**Objective:** Verify strict avatar validation (file type, size, dimensions).

**Prerequisites:** 
- Prepare test files:
  - Valid JPEG/PNG/WebP image (< 2MB)
  - Image larger than 2000x2000px
  - Image larger than 2MB
  - Non-image file renamed to .jpg (e.g., rename document.pdf to fake.jpg)
  - Different file types: .gif, .bmp, .svg

**Steps:**
1. Login as any user
2. Navigate to Users → Edit any user
3. Try to upload a **valid JPEG/PNG/WebP** (< 2MB, < 2000x2000px)
4. **Expected:** Upload succeeds
5. **Expected:** File is stored in `storage/app/private/avatars/`
6. **Expected:** Database `users.image` column contains path like `avatars/[timestamp]-[random].jpg`

7. Try to upload an image **larger than 2000x2000px**
8. **Expected:** Image is automatically resized to 400x400px (check file dimensions after upload)

9. Try to upload an image **larger than 2MB**
10. **Expected:** Validation error: "The image field must not be greater than 2048 kilobytes."

11. Try to upload a **renamed PDF as .jpg** (fake image)
12. **Expected:** Validation error from ValidFileContent rule

13. Try to upload **.gif, .bmp, .svg** files
14. **Expected:** Validation errors (only JPEG, PNG, WebP allowed)

**Verify Private Storage:**
1. Upload a valid avatar
2. Copy the filename from the image preview
3. Try to access directly in browser: `http://your-domain/storage/avatars/[filename].jpg`
4. **Expected:** 404 Not Found (file is not in public storage)
5. Access via secure route: `http://your-domain/secure/avatar/[filename].jpg`
6. **Expected (if logged in):** Image displays
7. **Expected (if NOT logged in):** Redirect to login

---

### ✅ TEST 4: Document Upload Validation

**Objective:** Verify document file validation and ZIP/RAR are blocked.

**Prerequisites:**
- Prepare test files:
  - Valid PDF, DOCX, XLSX files (< 5MB)
  - File larger than 5MB
  - ZIP file
  - RAR file
  - Executable file renamed to .pdf (e.g., program.exe → fake.pdf)
  - Valid document image (JPEG/PNG/WebP < 5MB)

**Steps:**
1. Login as user with permission to create documents
2. Navigate to Documents → Create
3. Try to upload a **valid PDF** (< 5MB)
4. **Expected:** Upload succeeds
5. **Expected:** File stored in `storage/app/private/documents/`
6. **Expected:** Database path like `documents/doc-[timestamp]-[random].pdf`

7. Try to upload a **ZIP file**
8. **Expected:** Validation error (ZIP not in allowed types)

9. Try to upload a **RAR file**
10. **Expected:** Validation error (RAR not in allowed types)

11. Try to upload a **renamed executable as .pdf**
12. **Expected:** ValidFileContent rule rejects it (magic bytes don't match PDF)

13. Try to upload a file **larger than 5MB**
14. **Expected:** Validation error: "must not be greater than 5120 kilobytes"

15. Upload a **valid document image** (JPEG/PNG/WebP)
16. **Expected:** Succeeds, stored in private/documents/

**Verify File Access Control:**
1. Upload a document as **wellness user**
2. Note the document ID
3. Logout and login as a **different wellness user**
4. Try to access: `http://your-domain/secure/document/[document-id]`
5. **Expected:** Access denied (403) - wellness users can only access their own documents
6. Login as **coach/professional/super_admin**
7. Access the same URL
8. **Expected:** File downloads successfully

---

### ✅ TEST 5: Feedback File Upload Validation

**Objective:** Verify feedback files are validated and only PDF/DOC/DOCX allowed.

**Prerequisites:**
- Prepare test files:
  - Valid PDF, DOC, DOCX (< 5MB)
  - Excel file (.xlsx)
  - Text file (.txt)
  - Renamed executable as .pdf

**Steps:**
1. Login as user
2. Navigate to Feedback → Create
3. Try to upload a **valid PDF** (< 5MB)
4. **Expected:** Upload succeeds
5. **Expected:** File stored in `storage/app/private/feedback/`
6. **Expected:** Database path like `feedback/feedback-[timestamp]-[random].pdf`

7. Try to upload an **Excel file (.xlsx)**
8. **Expected:** Validation error (only PDF, DOC, DOCX allowed)

9. Try to upload a **text file (.txt)**
10. **Expected:** Validation error

11. Try to upload a **renamed executable as .pdf**
12. **Expected:** ValidFileContent rule rejects it

**Verify Access Control:**
1. Upload feedback file
2. Note the feedback ID
3. Try to access: `http://your-domain/secure/feedback/[feedback-id]` while logged in
4. **Expected:** File downloads
5. Logout and try to access the same URL
6. **Expected:** Redirect to login

---

### ✅ TEST 6: File Content Validation (Magic Bytes)

**Objective:** Verify ValidFileContent rule checks actual file content.

**Create Test Files:**

**Test 6.1 - Fake PDF:**
1. Create a text file with content: "This is not a PDF"
2. Rename it to `fake.pdf`
3. Try to upload to Documents
4. **Expected:** Validation error "The file content does not match the declared file type"

**Test 6.2 - Fake Image:**
1. Create a text file with content: "Not an image"
2. Rename it to `fake.jpg`
3. Try to upload as avatar or document image
4. **Expected:** Validation error from ValidFileContent

**Test 6.3 - Real PDF:**
1. Use an actual PDF file
2. Upload to Documents
3. **Expected:** Upload succeeds

**Test 6.4 - Real JPEG:**
1. Use an actual JPEG image
2. Upload as avatar
3. **Expected:** Upload succeeds

---

### ✅ TEST 7: Existing File Migration

**Objective:** Test migration of existing files from public to private storage.

**Setup:**
1. Manually place test files in public storage:
```bash
# Create test avatar
mkdir -p storage/app/public/avatars
cp /path/to/test-image.jpg storage/app/public/avatars/test-avatar.jpg

# Create test document
mkdir -p storage/app/public/documents
cp /path/to/test-doc.pdf storage/app/public/documents/test-doc.pdf
```

2. Update database to reference these files:
```sql
UPDATE users SET image = 'avatars/test-avatar.jpg' WHERE email = 'wellness@test.com';
UPDATE documents SET file = 'documents/test-doc.pdf' WHERE id = 1;
```

**Test Dry Run:**
```bash
php artisan files:migrate-to-private --dry-run
```
**Expected:**
- Shows files that would be migrated
- No files are actually moved
- No database changes

**Test Real Migration:**
```bash
php artisan files:migrate-to-private --force
```
**Expected:**
- Files are copied from `storage/app/public/*` to `storage/app/private/*`
- Database paths are updated
- Original files are deleted from public storage
- Summary shows migrated/skipped/error counts

**Verify:**
1. Check files exist in private storage:
```bash
ls -la storage/app/private/avatars/
ls -la storage/app/private/documents/
```

2. Check files removed from public storage:
```bash
ls -la storage/app/public/avatars/  # Should be empty or not exist
```

3. Login and verify files are accessible via secure routes

---

### ✅ TEST 8: New User Creation Flow

**Objective:** Verify new users are created with password_change_required = true.

**Steps:**
1. Login as super_admin
2. Navigate to Users → Create
3. Fill in user details:
   - Name: Test New User
   - Email: newuser@test.com
   - Password: (leave blank, should auto-generate 'sportlarity')
   - Role: wellness
4. Save
5. **Expected:** User created successfully

**Verify in Database:**
```bash
php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'newuser@test.com')->first();
echo 'Password change required: ' . (\$user->password_change_required ? 'YES' : 'NO') . PHP_EOL;
"
```
**Expected:** Output should be "YES"

**Test Login:**
1. Logout
2. Login with: **newuser@test.com** / **sportlarity**
3. **Expected:** Immediately redirected to change password page
4. Change password to something secure
5. **Expected:** Can now access dashboard

---

### ✅ TEST 9: Security Headers and Direct File Access

**Objective:** Verify files cannot be accessed directly without authentication.

**Steps:**
1. Upload an avatar, document, and feedback file while logged in
2. Note the filenames
3. **Logout completely** (or use incognito/private browser window)
4. Try to access files directly:
   - `http://your-domain/storage/avatars/[filename]`
   - `http://your-domain/storage/documents/[filename]`
   - `http://your-domain/storage/feedback/[filename]`
5. **Expected:** 404 Not Found (files are not in public storage)

6. Try to access via secure routes without logging in:
   - `http://your-domain/secure/avatar/[filename]`
   - `http://your-domain/secure/document/[id]`
   - `http://your-domain/secure/feedback/[id]`
7. **Expected:** Redirect to login page (401/403)

8. Login and try the same secure URLs
9. **Expected:** Files are served correctly

---

## Automated Test Checklist

Use this checklist to track your testing progress:

- [ ] **TEST 1:** Mandatory password change works for new users
- [ ] **TEST 1:** Cannot access other pages until password is changed
- [ ] **TEST 1:** Cannot reuse 'sportlarity' as new password
- [ ] **TEST 1:** Password complexity requirements enforced
- [ ] **TEST 1:** After changing password, can access dashboard
- [ ] **TEST 1:** Old password no longer works after change
- [ ] **TEST 2:** Normal users (password_change_required = false) login normally
- [ ] **TEST 3:** Valid avatars upload successfully
- [ ] **TEST 3:** Avatars stored in private storage
- [ ] **TEST 3:** Large images auto-resized
- [ ] **TEST 3:** Oversized files rejected (> 2MB)
- [ ] **TEST 3:** Fake images rejected (ValidFileContent)
- [ ] **TEST 3:** Invalid formats rejected (GIF, BMP, SVG)
- [ ] **TEST 3:** Avatar URLs use secure route
- [ ] **TEST 4:** Valid documents upload successfully
- [ ] **TEST 4:** ZIP files rejected
- [ ] **TEST 4:** RAR files rejected
- [ ] **TEST 4:** Fake PDFs rejected (ValidFileContent)
- [ ] **TEST 4:** Wellness users cannot access other users' documents
- [ ] **TEST 4:** Admins can access all documents
- [ ] **TEST 5:** Valid feedback files upload successfully
- [ ] **TEST 5:** Non-PDF/DOC/DOCX files rejected
- [ ] **TEST 5:** Fake files rejected (ValidFileContent)
- [ ] **TEST 6:** Magic bytes validation working for all file types
- [ ] **TEST 7:** File migration dry-run works correctly
- [ ] **TEST 7:** File migration moves files and updates database
- [ ] **TEST 8:** New users created with password_change_required = true
- [ ] **TEST 8:** New users forced to change password on first login
- [ ] **TEST 9:** Direct file access returns 404
- [ ] **TEST 9:** Secure routes require authentication
- [ ] **TEST 9:** Logged-in users can access files via secure routes

---

## Troubleshooting

### Issue: "password_change_required" column not found
**Solution:** Run migrations: `php artisan migrate`

### Issue: "There is no role named 'X'"
**Solution:** Run seeder: `php artisan db:seed --class=SecurityTestSeeder --force`

### Issue: Files not uploading
**Solution:** Check directory permissions:
```bash
chmod -R 775 storage/app/private
chown -R www-data:www-data storage/app/private  # Linux/Apache
chown -R _www:_www storage/app/private  # macOS
```

### Issue: 404 on secure routes
**Solution:** Clear route cache:
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: LSP errors in IDE
**Solution:** These are normal - run `composer dump-autoload` if needed, but LSP errors don't affect runtime.

---

## Production Deployment Checklist

Before deploying to production:

1. **✅ Run all tests above** and verify they pass
2. **✅ Backup database** before migration
3. **✅ Run file migration:**
   ```bash
   php artisan files:migrate-to-private --dry-run  # Review
   php artisan files:migrate-to-private --force    # Execute
   ```
4. **✅ Update all existing users:**
   ```sql
   -- Set password_change_required for users still using default password
   UPDATE users 
   SET password_change_required = 1 
   WHERE password = '$2y$12$[hash-of-sportlarity]';
   ```
5. **✅ Clear all caches:**
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   php artisan optimize
   ```
6. **✅ Verify file permissions:** Ensure web server can read private storage
7. **✅ Test in staging** environment first
8. **✅ Monitor logs** after deployment for any file access errors

---

## Security Audit Log

Keep track of your security testing:

| Date | Tester | Test # | Status | Notes |
|------|--------|--------|--------|-------|
| YYYY-MM-DD | Name | TEST 1 | ✅ PASS | |
| YYYY-MM-DD | Name | TEST 2 | ✅ PASS | |
| ... | ... | ... | ... | |

---

## Additional Security Recommendations (Future)

Consider implementing these additional security measures:

1. **Two-Factor Authentication (2FA)** for admin users
2. **Rate limiting** on file uploads (prevent DoS)
3. **Virus scanning** integration (ClamAV)
4. **Audit logging** for file access/downloads
5. **Session timeout** configuration
6. **Password expiration** policy (force change every 90 days)
7. **Failed login attempt** tracking and account lockout
8. **Email notifications** when password is changed
9. **File upload size quotas** per user role
10. **Content Security Policy (CSP)** headers
