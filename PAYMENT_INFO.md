# 💳 Payment Methods - The Suvaya Experience

This document explains the payment methods available in the application.

---

## 🎯 Available Payment Methods

### 1. **Card Payment (Simulated)**
- **Type**: Credit/Debit Card
- **Supported**: Visa, Mastercard, Amex
- **Status**: Test Mode (Simulation)
- **Processing**: 2-second simulated processing
- **Implementation**:
  - Modal dialog for card entry
  - Simulated payment gateway
  - Always succeeds in test mode

**For Production:**
- Integrate with payment gateway like:
  - Stripe
  - PayPal
  - Flutterwave
  - Paystack

### 2. **M-Pesa (Manual Payment)**
- **Type**: Mobile Money
- **Method**: Manual Till Number Payment
- **Till Number**: `5858585` (Update this with your actual till number)
- **Process**:
  1. Customer places order
  2. System shows M-Pesa payment instructions
  3. Customer pays manually to till number
  4. Admin verifies payment
  5. Admin confirms order

**To Update Till Number:**
Edit `src/Components/Checkout.vue` line 355:
```vue
Enter Till Number: <strong class="text-h6 text-green">5858585</strong>
```

### 3. **Cash on Delivery/Pickup**
- **Type**: Cash Payment
- **When**: On delivery or at pickup
- **Deposit**: 50% required (can be cash or other methods)
- **Balance**: Paid when order is delivered/picked up

---

## 🔧 Configuration

### Update M-Pesa Till Number

1. Open `src/Components/Checkout.vue`
2. Find line 355
3. Replace `5858585` with your actual M-Pesa Till Number
4. Save the file

```vue
<!-- Before -->
<strong class="text-h6 text-green">5858585</strong>

<!-- After -->
<strong class="text-h6 text-green">YOUR_TILL_NUMBER</strong>
```

### Card Payment - Production Integration

To integrate real card payment:

1. **Choose a Payment Gateway**
   - [Stripe](https://stripe.com/) - International
   - [Flutterwave](https://flutterwave.com/) - African markets
   - [Paystack](https://paystack.com/) - African markets

2. **Install SDK**
   ```bash
   npm install @stripe/stripe-js
   # or
   npm install flutterwave-vue-v3
   ```

3. **Update `processCardPayment()` function** in `src/Components/Checkout.vue`:
   ```javascript
   async function processCardPayment() {
     processingPayment.value = true

     try {
       // Replace with actual payment gateway integration
       const response = await stripe.createPaymentMethod({
         type: 'card',
         card: {
           number: cardNumber.value,
           exp_month: cardExpiry.value.split('/')[0],
           exp_year: '20' + cardExpiry.value.split('/')[1],
           cvc: cardCVV.value
         }
       })

       if (response.error) {
         throw new Error(response.error.message)
       }

       // Proceed with order
       await placeOrder()
     } catch (error) {
       alert('Payment failed: ' + error.message)
     } finally {
       processingPayment.value = false
     }
   }
   ```

---

## 📊 Payment Flow

### Card Payment Flow
```
1. Customer adds items to cart
2. Goes to checkout
3. Selects "Card Payment"
4. Fills order details
5. Clicks "Place Order"
6. Card dialog appears
7. Enters card details
8. Clicks "Pay"
9. 2-second simulation (Production: Real processing)
10. Order created
11. Redirected to success page
```

### M-Pesa Manual Flow
```
1. Customer adds items to cart
2. Goes to checkout
3. Selects "M-Pesa (Manual Payment)"
4. Sees M-Pesa instructions with Till Number
5. Fills order details
6. Clicks "Place Order"
7. Order created with "pending payment" status
8. Customer pays manually to Till Number
9. Admin verifies payment in M-Pesa
10. Admin updates order status to "confirmed"
```

### Cash on Delivery Flow
```
1. Customer adds items to cart
2. Goes to checkout
3. Selects "Cash on Delivery/Pickup"
4. Fills order details
5. Clicks "Place Order"
6. Order created with "pending" status
7. Admin prepares order
8. Customer pays cash on delivery/pickup
9. Admin confirms payment and completes order
```

---

## 🎨 User Experience

### Payment Method Display
- **Card Payment**: Blue icon, "Recommended" tag
- **M-Pesa Manual**: Green icon, shows till number instructions
- **Cash**: Amber icon, simple description

### Deposit System
- All orders require 50% deposit
- Deposit amount clearly displayed
- Balance due on delivery/pickup
- Shown in order summary

### Test Mode Notice
- Card payment shows "Test Mode" alert
- Users know it's a simulation
- Instructions for production integration

---

## 🔒 Security Notes

### Current Implementation (Test Mode)
- ⚠️ Card details are NOT stored
- ⚠️ No real payment processing
- ⚠️ For demonstration only

### For Production
- ✅ Use PCI-compliant payment gateway
- ✅ Never store card details directly
- ✅ Use HTTPS for all transactions
- ✅ Implement 3D Secure authentication
- ✅ Add fraud detection
- ✅ Log all transactions

---

## 📱 Mobile Responsiveness

All payment methods are fully responsive:
- Card dialog adapts to screen size
- M-Pesa instructions readable on mobile
- Touch-friendly buttons and inputs
- Optimal layout for small screens

---

## 🚀 Quick Start

### Testing Card Payment
1. Add items to cart
2. Go to checkout
3. Select "Card Payment"
4. Use any card details (test mode):
   - Card: 4111 1111 1111 1111
   - Name: Any name
   - Expiry: Any future date (MM/YY)
   - CVV: Any 3 digits
5. Click "Pay"
6. Wait 2 seconds
7. Order placed successfully!

### Testing M-Pesa Manual
1. Add items to cart
2. Go to checkout
3. Select "M-Pesa (Manual Payment)"
4. Note the Till Number shown
5. Fill order details
6. Click "Place Order"
7. In production: Pay to till number manually
8. Admin verifies and confirms

### Testing Cash on Delivery
1. Add items to cart
2. Go to checkout
3. Select "Cash on Delivery/Pickup"
4. Fill order details
5. Click "Place Order"
6. Pay when order is delivered/picked up

---

## 💡 Tips

1. **Always display deposit amount clearly**
2. **Send email confirmation with payment details**
3. **For M-Pesa: Include transaction reference in order**
4. **For Cash: Remind customer on delivery day**
5. **Test all payment flows thoroughly before launch**

---

## 📞 Support

For payment integration support:
- Card Payment: Check payment gateway documentation
- M-Pesa: Contact Safaricom Business support
- General: Refer to this document

---

**Last Updated:** December 2025
