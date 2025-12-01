<script setup>
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const submitForm = () => {
  // Form submission logic would go here
  alert('Thank you for contacting us! We will get back to you soon.')
  // Reset form
  form.value = {
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
  }
}
</script>

<template>
  <v-container class="py-8 py-md-12">
    <v-row justify="center">
      <v-col cols="12" class="text-center mb-6">
        <h1 class="contact-title">Get In Touch</h1>
        <p class="contact-subtitle">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
      </v-col>
    </v-row>

    <v-row justify="center">
      <!-- Contact Form -->
      <v-col cols="12" md="6" lg="5">
        <v-card class="contact-card pa-6 pa-md-8" elevation="3">
          <v-card-title class="text-h5 mb-4 px-0">Send us a Message</v-card-title>

          <v-form @submit.prevent="submitForm">
            <v-text-field
              v-model="form.name"
              label="Your Name"
              variant="outlined"
              color="amber-darken-2"
              required
              class="mb-2"
            ></v-text-field>

            <v-text-field
              v-model="form.email"
              label="Email Address"
              type="email"
              variant="outlined"
              color="amber-darken-2"
              required
              class="mb-2"
            ></v-text-field>

            <v-text-field
              v-model="form.phone"
              label="Phone Number"
              variant="outlined"
              color="amber-darken-2"
              class="mb-2"
            ></v-text-field>

            <v-text-field
              v-model="form.subject"
              label="Subject"
              variant="outlined"
              color="amber-darken-2"
              required
              class="mb-2"
            ></v-text-field>

            <v-textarea
              v-model="form.message"
              label="Your Message"
              variant="outlined"
              color="amber-darken-2"
              rows="5"
              required
              class="mb-4"
            ></v-textarea>

            <v-btn
              type="submit"
              color="amber-darken-2"
              size="large"
              block
            >
              Send Message
            </v-btn>
          </v-form>
        </v-card>
      </v-col>

      <!-- Contact Information -->
      <v-col cols="12" md="6" lg="5">
        <v-card class="contact-card pa-6 pa-md-8" elevation="3">
          <v-card-title class="text-h5 mb-4 px-0">Contact Information</v-card-title>

          <div class="contact-info">
            <div class="info-item mb-5">
              <v-icon color="amber-darken-2" size="28" class="mr-3">mdi-map-marker</v-icon>
              <div>
                <h3 class="text-subtitle-1 font-weight-bold mb-1">Location</h3>
                <p class="text-body-2">123 Bakery Street, Nairobi, Kenya</p>
              </div>
            </div>

            <div class="info-item mb-5">
              <v-icon color="amber-darken-2" size="28" class="mr-3">mdi-phone</v-icon>
              <div>
                <h3 class="text-subtitle-1 font-weight-bold mb-1">Phone</h3>
                <p class="text-body-2">+254 712 345 678</p>
                <p class="text-body-2">+254 733 456 789</p>
              </div>
            </div>

            <div class="info-item mb-5">
              <v-icon color="amber-darken-2" size="28" class="mr-3">mdi-email</v-icon>
              <div>
                <h3 class="text-subtitle-1 font-weight-bold mb-1">Email</h3>
                <p class="text-body-2">info@suvaya.com</p>
                <p class="text-body-2">orders@suvaya.com</p>
              </div>
            </div>

            <div class="info-item mb-5">
              <v-icon color="amber-darken-2" size="28" class="mr-3">mdi-clock-outline</v-icon>
              <div>
                <h3 class="text-subtitle-1 font-weight-bold mb-1">Business Hours</h3>
                <p class="text-body-2">Monday - Friday: 8:00 AM - 6:00 PM</p>
                <p class="text-body-2">Saturday: 9:00 AM - 5:00 PM</p>
                <p class="text-body-2">Sunday: Closed</p>
              </div>
            </div>

            <v-divider class="my-4"></v-divider>

            <div class="text-center">
              <h3 class="text-subtitle-1 font-weight-bold mb-3">Follow Us</h3>
              <v-btn icon variant="text" color="amber-darken-2" href="#" class="mx-1">
                <v-icon>mdi-facebook</v-icon>
              </v-btn>
              <v-btn icon variant="text" color="amber-darken-2" href="#" class="mx-1">
                <v-icon>mdi-instagram</v-icon>
              </v-btn>
              <v-btn icon variant="text" color="amber-darken-2" href="#" class="mx-1">
                <v-icon>mdi-twitter</v-icon>
              </v-btn>
              <v-btn icon variant="text" color="amber-darken-2" href="#" class="mx-1">
                <v-icon>mdi-whatsapp</v-icon>
              </v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.contact-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .contact-title {
    font-size: 2.5rem;
  }
}

.contact-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
}

@media (min-width: 600px) {
  .contact-subtitle {
    font-size: 1.125rem;
  }
}

.contact-card {
  background: #fffbe6;
  border-radius: 16px;
  height: 100%;
}

.contact-card .v-card-title {
  color: #b28704;
}

.info-item {
  display: flex;
  align-items: flex-start;
}

.info-item h3 {
  color: #b28704;
}

.info-item p {
  color: #5a5a5a;
}
</style>
