<template>
  <v-footer class="footer-container" color="amber-lighten-3">
    <v-container>
      <v-row class="py-6">
        <!-- About Section -->
        <v-col cols="12" md="4" class="mb-4 mb-md-0">
          <h3 class="footer-heading mb-3">
            <v-icon color="amber-darken-4" class="mr-2">mdi-cake-variant</v-icon>
            The Suvaya Experience
          </h3>
          <p class="footer-text">
            Where flavor meets creativity. We craft delicious cakes, savory meals, and food experiences that turn simple moments into something special.
          </p>
        </v-col>

        <!-- Quick Links -->
        <v-col cols="12" sm="6" md="2" class="mb-4 mb-md-0">
          <h4 class="footer-heading mb-3">Quick Links</h4>
          <ul class="footer-links">
            <li><router-link to="/">Home</router-link></li>
            <li><router-link to="/menu">Menu</router-link></li>
            <li><router-link to="/reservation">Reservations</router-link></li>
            <li><router-link to="/events">Events</router-link></li>
          </ul>
        </v-col>

        <!-- About Links -->
        <v-col cols="12" sm="6" md="2" class="mb-4 mb-md-0">
          <h4 class="footer-heading mb-3">About</h4>
          <ul class="footer-links">
            <li><router-link to="/aboutus">Our Story</router-link></li>
            <li><router-link to="/contactus">Contact Us</router-link></li>
            <li><router-link to="/orders">My Orders</router-link></li>
          </ul>
        </v-col>

        <!-- Contact Info -->
        <v-col cols="12" md="4">
          <h4 class="footer-heading mb-3">Contact Us</h4>
          <div class="footer-contact">
            <p class="mb-2">
              <v-icon size="18" class="mr-2">mdi-map-marker</v-icon>
              123 Bakery Street, Nairobi
            </p>
            <p class="mb-2">
              <v-icon size="18" class="mr-2">mdi-phone</v-icon>
              +254 712 345 678
            </p>
            <p class="mb-3">
              <v-icon size="18" class="mr-2">mdi-email</v-icon>
              info@suvaya.com
            </p>

            <div class="social-links">
              <v-btn
                icon
                size="small"
                variant="text"
                href="#"
                aria-label="Facebook"
              >
                <v-icon>mdi-facebook</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                href="#"
                aria-label="Instagram"
              >
                <v-icon>mdi-instagram</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                href="#"
                aria-label="Twitter"
              >
                <v-icon>mdi-twitter</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                href="#"
                aria-label="WhatsApp"
              >
                <v-icon>mdi-whatsapp</v-icon>
              </v-btn>
            </div>
          </div>
        </v-col>
      </v-row>

      <v-divider class="my-4"></v-divider>

      <v-row>
        <v-col cols="12" class="text-center">
          <p class="footer-copyright">
            {{ new Date().getFullYear() }} &copy; <strong>The Suvaya Experience</strong>. All rights reserved.
          </p>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script setup>
// No logic needed for static footer
</script>

<style scoped>
.footer-container {
  background: #fffbe6 !important;
  color: #5a5a5a;
  border-top: 2px solid #b28704;
}

.footer-heading {
  color: #b28704;
  font-size: 1.1rem;
  font-weight: 700;
}

.footer-text {
  font-size: 0.9rem;
  line-height: 1.6;
  color: #5a5a5a;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 8px;
}

.footer-links a {
  color: #5a5a5a;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #b28704;
}

.footer-contact {
  font-size: 0.9rem;
  color: #5a5a5a;
}

.footer-contact p {
  display: flex;
  align-items: center;
}

.footer-contact .v-icon {
  color: #b28704;
}

.social-links {
  display: flex;
  gap: 8px;
  margin-top: 8px;
}

.social-links .v-btn {
  color: #b28704 !important;
}

.social-links .v-btn:hover {
  background-color: #fff8dc;
}

.footer-copyright {
  font-size: 0.875rem;
  color: #7a7a7a;
  margin: 0;
}

@media (max-width: 960px) {
  .footer-heading {
    font-size: 1rem;
  }

  .footer-text,
  .footer-links a,
  .footer-contact {
    font-size: 0.85rem;
  }
}
</style>
