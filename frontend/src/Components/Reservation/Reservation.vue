<script setup>
import ReservationForm from './ReservationForm.vue'
</script>

<template>
    <v-container class="py-8 py-md-12">
        <!-- Header -->
        <v-row>
            <v-col cols="12" class="text-center mb-6">
                <h1 class="reservation-title">Make a Reservation</h1>
                <p class="reservation-subtitle">Book your date for events, baking classes, or custom orders. Easy booking with flexible payment options.</p>
            </v-col>
        </v-row>

        <!-- Main Content -->
        <v-row justify="center">
            <!-- Form Section (Full Width) -->
            <v-col cols="12" lg="8">
                <ReservationForm />
            </v-col>
        </v-row>

        <!-- Info Section -->
        <v-row class="mt-8">
            <v-col cols="12">
                <v-card class="info-card pa-6" elevation="0">
                    <h3 class="text-h6 mb-4 text-center" style="color: #b28704;">Booking Information</h3>

                    <v-row>
                        <v-col cols="12" sm="6" md="3">
                            <div class="info-item text-center">
                                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-calendar-check</v-icon>
                                <h4 class="text-subtitle-2 mb-1">Easy Booking</h4>
                                <p class="text-caption">Select your date and fill in your details</p>
                            </div>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <div class="info-item text-center">
                                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-cash-multiple</v-icon>
                                <h4 class="text-subtitle-2 mb-1">50% Deposit</h4>
                                <p class="text-caption">Pay half now, half on delivery</p>
                            </div>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <div class="info-item text-center">
                                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-calendar-clock</v-icon>
                                <h4 class="text-subtitle-2 mb-1">Real-time Availability</h4>
                                <p class="text-caption">See available dates instantly</p>
                            </div>
                        </v-col>

                        <v-col cols="12" sm="6" md="3">
                            <div class="info-item text-center">
                                <v-icon color="amber-darken-2" size="36" class="mb-2">mdi-email-check</v-icon>
                                <h4 class="text-subtitle-2 mb-1">Confirmation</h4>
                                <p class="text-caption">Receive instant booking confirmation</p>
                            </div>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
.reservation-title {
    color: #b28704;
    font-size: 2rem;
    font-weight: 700;
}

@media (min-width: 600px) {
    .reservation-title {
        font-size: 2.5rem;
    }
}

.reservation-subtitle {
    color: #7a7a7a;
    font-size: 1rem;
    line-height: 1.6;
}

@media (min-width: 600px) {
    .reservation-subtitle {
        font-size: 1.125rem;
    }
}

.info-card {
    background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
    border-radius: 16px;
    border: 2px solid #b28704;
}

.info-item h4 {
    color: #b28704;
    font-weight: 600;
}

.info-item p {
    color: #5a5a5a;
}
</style>