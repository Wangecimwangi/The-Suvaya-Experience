# 📧 Email Setup Guide - The Suvaya Experience

Complete guide to configure email sending for order receipts and reservation confirmations.

---

## 🚀 Quick Start (3 Options)

### **Option 1: Gmail SMTP (Recommended for Testing)**

1. **Create Gmail App Password:**
   - Go to https://myaccount.google.com/apppasswords
   - Sign in to your Google Account
   - Click "Select app" → Choose "Mail"
   - Click "Select device" → Choose "Other" → Enter "Suvaya"
   - Click "Generate"
   - Copy the 16-character password

2. **Configure Email:**
   Edit `backend/config/email.php`:
   ```php
   'smtp' => [
       'enabled' => true,
       'host' => 'smtp.gmail.com',
       'port' => 587,
       'username' => 'youremail@gmail.com',  // Your Gmail
       'password' => 'xxxx xxxx xxxx xxxx',   // Your App Password
       'use_tls' => true,
   ],
   ```

3. **Test It:**
   - Place an order or make a reservation
   - Check recipient email inbox
   - If not in inbox, check spam folder

### **Option 2: Mailtrap (Best for Development)**

Perfect for testing without sending real emails!

1. **Create Free Account:**
   - Go to https://mailtrap.io/
   - Sign up for free account
   - Go to "Email Testing" → "Inboxes"
   - Click on your inbox
   - Copy SMTP credentials

2. **Configure Email:**
   Edit `backend/config/email.php`:
   ```php
   'smtp' => [
       'enabled' => true,
       'host' => 'smtp.mailtrap.io',
       'port' => 2525,
       'username' => 'your_mailtrap_username',
       'password' => 'your_mailtrap_password',
       'use_tls' => true,
   ],
   ```

3. **Test It:**
   - Place an order or make a reservation
   - Check your Mailtrap inbox
   - Emails appear there (not sent to real addresses)

### **Option 3: File Logging (No Configuration Needed)**

Emails are logged to files instead of being sent.

**Already Working!** If SMTP is not configured, emails automatically save to:
```
backend/logs/emails/email_2025-12-04.log
```

---

## 📋 Configuration Options

### Gmail Setup (Detailed)

**Step 1: Enable 2-Step Verification**
1. Go to https://myaccount.google.com/security
2. Click "2-Step Verification"
3. Follow steps to enable

**Step 2: Create App Password**
1. Go to https://myaccount.google.com/apppasswords
2. Select app: Mail
3. Select device: Other (Suvaya)
4. Click Generate
5. Copy the 16-character password (format: xxxx xxxx xxxx xxxx)

**Step 3: Update Config**
```php
// backend/config/email.php
'smtp' => [
    'enabled' => true,
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your-email@gmail.com',
    'password' => 'abcd efgh ijkl mnop', // App password
    'use_tls' => true,
],
```

**Troubleshooting Gmail:**
- ❌ "Invalid credentials" → Check App Password, not regular password
- ❌ "Authentication failed" → Enable 2-Step Verification first
- ❌ Emails in spam → Add "noreply@suvaya.com" to contacts
- ❌ "Less secure app" → Use App Password, not regular password

### SendGrid Setup (Production Recommended)

**Step 1: Create Account**
1. Go to https://sendgrid.com/
2. Sign up (Free: 100 emails/day)
3. Verify your email

**Step 2: Create API Key**
1. Go to Settings → API Keys
2. Click "Create API Key"
3. Name: "Suvaya Experience"
4. Permissions: Full Access (or Mail Send)
5. Copy the API Key

**Step 3: Update Config**
```php
// backend/config/email.php
'smtp' => [
    'enabled' => true,
    'host' => 'smtp.sendgrid.net',
    'port' => 587,
    'username' => 'apikey',  // Literally "apikey"
    'password' => 'SG.xxxxxxxxxxxx', // Your API Key
    'use_tls' => true,
],
```

### Mailgun Setup (Alternative)

1. Sign up at https://www.mailgun.com/
2. Verify your domain or use sandbox
3. Get SMTP credentials from dashboard
4. Update config:

```php
'smtp' => [
    'enabled' => true,
    'host' => 'smtp.mailgun.org',
    'port' => 587,
    'username' => 'postmaster@your-domain.mailgun.org',
    'password' => 'your-mailgun-password',
    'use_tls' => true,
],
```

---

## 🔍 Testing Emails

### Test Order Receipt Email

1. Add items to cart
2. Go to checkout
3. Fill in details with YOUR email address
4. Select any payment method
5. Complete checkout
6. Check your email inbox (or Mailtrap, or log file)

### Test Reservation Confirmation Email

1. Go to Reservations page
2. Fill in form with YOUR email address
3. Select a future date
4. Submit reservation
5. Check your email inbox

### Check Email Logs

View sent emails in log files:
```bash
# View today's emails
cat backend/logs/emails/email_$(date +%Y-%m-%d).log

# View all email logs
ls -la backend/logs/emails/

# Tail live email log
tail -f backend/logs/emails/email_$(date +%Y-%m-%d).log
```

---

## 📊 How It Works

### Email Flow

```
1. Customer places order/reservation
2. Backend saves to database
3. EmailService called
4. Checks SMTP configuration
5a. IF SMTP configured → Sends via SMTP
5b. IF NOT configured → Logs to file
6. Customer receives email (or admin checks log)
```

### File Structure

```
backend/
├── config/
│   └── email.php           # Email configuration
├── lib/
│   └── SimpleMailer.php    # SMTP mailer class
├── utils/
│   └── email.php           # Email service
├── emails/
│   └── templates/
│       ├── order-receipt.php              # Order email template
│       └── reservation-confirmation.php    # Reservation email template
└── logs/
    └── emails/
        └── email_YYYY-MM-DD.log   # Email logs
```

---

## 🎨 Customizing Emails

### Update From Address

Edit `backend/config/email.php`:
```php
'from' => [
    'email' => 'hello@yourdomain.com',  // Your email
    'name' => 'Your Company Name'        // Your name
],
```

### Modify Email Templates

Templates are located in `backend/emails/templates/`:

**Order Receipt:** `order-receipt.php`
- Beautiful HTML design
- Shows all order details
- Mobile responsive

**Reservation Confirmation:** `reservation-confirmation.php`
- Confirmation badge
- Date/time prominent display
- Step-by-step instructions

### Add Your Logo

Edit email templates and add:
```html
<img src="https://yourdomain.com/logo.png" alt="Logo" style="width: 150px;">
```

---

## 🔒 Security Best Practices

### Don't Commit Credentials
```bash
# Add to .gitignore
backend/config/email.php
```

### Use Environment Variables (Production)
```php
// backend/config/email.php
'smtp' => [
    'username' => getenv('SMTP_USERNAME'),
    'password' => getenv('SMTP_PASSWORD'),
],
```

### Rotate Credentials Regularly
- Change passwords every 90 days
- Revoke unused API keys
- Monitor email logs

---

## ⚙️ Advanced Configuration

### Multiple Email Providers (Fallback)

Edit `backend/lib/SimpleMailer.php` to add fallback logic.

### Email Queue System

For high volume, implement queue:
1. Save emails to database
2. Process in batches
3. Use cron job to send

### Email Templates per Language

Create language-specific templates:
```
emails/templates/en/order-receipt.php
emails/templates/sw/order-receipt.php
```

---

## 🐛 Troubleshooting

### Problem: Emails Not Sending

**Check 1: Verify SMTP Credentials**
```php
// Test in backend/test-email.php
$config = require 'config/email.php';
print_r($config['smtp']);
```

**Check 2: View Error Logs**
```bash
tail -f backend/logs/error.log
```

**Check 3: Test SMTP Connection**
```bash
telnet smtp.gmail.com 587
```

### Problem: Emails in Spam

**Solutions:**
1. Add SPF record to your domain
2. Set up DKIM signing
3. Use verified sending domain
4. Ask recipients to whitelist your email

### Problem: "Authentication Failed"

**Gmail:**
- Use App Password, not regular password
- Enable 2-Step Verification
- Check username is full email address

**Other Providers:**
- Verify API key is correct
- Check username format
- Ensure account is active

---

## 📱 Email Delivery Status

### Success Indicators

✅ **Email Sent Successfully:**
- Check recipient inbox
- No errors in logs
- Log shows "Email sent successfully"

✅ **Email Logged (SMTP not configured):**
- File created in `backend/logs/emails/`
- Email content saved
- Ready for manual review

### Monitoring

**Check Email Logs:**
```bash
# View recent emails
tail -20 backend/logs/emails/email_$(date +%Y-%m-%d).log

# Search for specific email
grep "customer@email.com" backend/logs/emails/*.log

# Count emails sent today
grep -c "Email sent successfully" backend/logs/emails/email_$(date +%Y-%m-%d).log
```

---

## 🎯 Production Checklist

Before going live:

- [ ] SMTP credentials configured
- [ ] Test emails sending correctly
- [ ] Emails not going to spam
- [ ] From address is professional
- [ ] Logo/branding added to templates
- [ ] Unsubscribe link added (if required)
- [ ] Email logs monitored
- [ ] Backup email provider configured
- [ ] Error handling tested
- [ ] Privacy policy updated

---

## 📞 Support Resources

### Official Documentation
- [Gmail SMTP](https://support.google.com/mail/answer/7126229)
- [SendGrid Docs](https://docs.sendgrid.com/)
- [Mailgun Docs](https://documentation.mailgun.com/)
- [Mailtrap Docs](https://help.mailtrap.io/)

### Common Issues
- SMTP authentication: Check credentials
- Port blocked: Try ports 465, 587, 2525
- TLS errors: Verify SSL/TLS settings

---

## ✅ Current Status

Your email system is **configured and ready**!

**What works now:**
✅ Email templates created (beautiful HTML)
✅ Email service integrated
✅ Order receipts send automatically
✅ Reservation confirmations send automatically
✅ File logging as fallback
✅ Error handling in place

**What you need to do:**
1. Choose an email provider (Gmail, Mailtrap, etc.)
2. Get credentials
3. Update `backend/config/email.php`
4. Test it!

**If you don't configure SMTP:**
- Emails will be logged to files
- Check `backend/logs/emails/` directory
- Still fully functional, just manual

---

**Good luck with your email setup! 📧✨**
