<script setup>

</script>

<template>
    <!-- Hero Carousel -->
    <v-container fluid class="pa-0">
        <v-carousel height="400" cycle hide-delimiters show-arrows="hover">
            <v-carousel-item src="public/Homepage/olivia-claire-qwzhT20ohOM-unsplash.jpg" cover></v-carousel-item>
            <v-carousel-item src="public/Homepage/othman-alghanmi-QhkpKufhqYg-unsplash.jpg" cover></v-carousel-item>
            <v-carousel-item src="public/Homepage/levi-meir-clancy-JVCnRnkCzIg-unsplash.jpg" cover></v-carousel-item>
            <v-carousel-item src="public/Homepage/olga-kononenko-KVgz8J5SjOg-unsplash.jpg" cover></v-carousel-item>
        </v-carousel>
    </v-container>

    <!-- Welcome Section -->
    <v-container class="py-8 py-md-12">
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card class="welcome-card pa-6 pa-md-8" elevation="0">
                    <h1 class="welcome-title text-center mb-4">Welcome to The Suvaya Experience</h1>

                    <p class="welcome-text mb-4">
                        Suvaya is a creative bakery and food hub that brings people together through good taste and beautiful experiences. We specialize in crafting custom cakes, savory bites, and meal plans that fit every occasion. Whether you're celebrating, learning, or simply enjoying a treat, Suvaya adds a personal touch to every order.
                    </p>

                    <p class="welcome-text mb-4">
                        We make planning easier for you — our website lets you book your date, view our menu, and even pay in installments. You can see available slots on our calendar, explore our packages, and know exactly what's included in each plan before you order. Everything is designed to be simple, clear, and made just for you.
                    </p>

                    <p class="welcome-text mb-4">
                        At Suvaya, we also love to teach. Through our baking sessions, we help others learn the art of creating delicious, beautiful cakes from scratch. From fluffy sponges to signature frosting designs, every class is about sharing our passion and creativity with you. Suvaya isn't just about food — it's about experience, learning, and joy.
                    </p>

                    <div class="text-center mt-6">
                        <v-btn to="/menu" color="amber-darken-2" size="large" class="mr-2 mb-2">Explore Menu</v-btn>
                        <v-btn to="/reservation" variant="outlined" color="amber-darken-2" size="large" class="mb-2">Book Now</v-btn>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

    <!-- Package Recommendation CTA -->
    <v-container class="py-6 py-md-10">
        <v-row justify="center">
            <v-col cols="12" md="10" lg="8">
                <v-card class="recommendation-cta-card pa-6 pa-md-8" elevation="4">
                    <v-row align="center">
                        <v-col cols="12" md="8">
                            <div class="d-flex align-center mb-3">
                                <v-icon color="amber-darken-2" size="60" class="mr-3">mdi-lightbulb-on</v-icon>
                                <div>
                                    <h2 class="cta-title mb-1">Not Sure What to Order?</h2>
                                    <p class="cta-subtitle mb-0">Let us help you find the perfect package for your event</p>
                                </div>
                            </div>
                            <p class="cta-description">
                                Answer a few simple questions about your event, and our smart recommendation system will suggest the best packages tailored to your needs, budget, and guest count.
                            </p>
                        </v-col>
                        <v-col cols="12" md="4" class="text-center text-md-right">
                            <v-btn
                                to="/package-recommendation"
                                color="amber-darken-2"
                                size="x-large"
                                prepend-icon="mdi-wizard-hat"
                                class="text-wrap"
                                style="height: auto; min-height: 60px;"
                            >
                                <span class="d-none d-sm-inline">Get Recommendations</span>
                                <span class="d-inline d-sm-none">Recommendations</span>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

    <!-- Featured Section -->
    <v-container class="py-8">
        <v-row>
            <v-col cols="12" class="text-center mb-4">
                <h2 class="section-title">What's Happening</h2>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" sm="6" md="4">
                <v-card class="feature-card" elevation="3" hover>
                    <v-img
                        src="public/Homepage/magic-mary-nEOzmjguwO0-unsplash.jpg"
                        height="220"
                        cover
                    ></v-img>
                    <v-card-title class="text-h6">New Seasonal Cakes</v-card-title>
                    <v-card-subtitle>Try our limited edition flavors</v-card-subtitle>
                    <v-card-actions>
                        <v-btn to="/menu" variant="text" color="amber-darken-2">Learn More</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="4">
                <v-card class="feature-card" elevation="3" hover>
                    <v-img
                        src="public/Homepage/brad-9MTp2Z1ejQo-unsplash.jpg"
                        height="220"
                        cover
                    ></v-img>
                    <v-card-title class="text-h6">Baking Classes</v-card-title>
                    <v-card-subtitle>Learn from our expert bakers</v-card-subtitle>
                    <v-card-actions>
                        <v-btn to="/events" variant="text" color="amber-darken-2">View Schedule</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="4">
                <v-card class="feature-card" elevation="3" hover>
                    <v-img
                        src="public/Homepage/hailey-tong-uI2ayKEfgaw-unsplash.jpg"
                        height="220"
                        cover
                    ></v-img>
                    <v-card-title class="text-h6">Custom Orders</v-card-title>
                    <v-card-subtitle>Perfect for your special occasions</v-card-subtitle>
                    <v-card-actions>
                        <v-btn to="/contactus" variant="text" color="amber-darken-2">Get In Touch</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
.welcome-card {
    background: #fffbe6;
    border-radius: 16px;
}

.welcome-title {
    color: #b28704;
    font-size: 2rem;
    font-weight: 700;
}

@media (min-width: 600px) {
    .welcome-title {
        font-size: 2.5rem;
    }
}

.welcome-text {
    color: #5a5a5a;
    font-size: 1rem;
    line-height: 1.6;
    text-align: justify;
}

@media (min-width: 600px) {
    .welcome-text {
        font-size: 1.125rem;
    }
}

.section-title {
    color: #b28704;
    font-size: 1.8rem;
    font-weight: 700;
}

@media (min-width: 600px) {
    .section-title {
        font-size: 2.2rem;
    }
}

.feature-card {
    background: #fffbe6;
    border-radius: 12px;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.feature-card:hover {
    transform: translateY(-4px);
    transition: transform 0.3s ease;
}

.feature-card .v-card-title {
    color: #b28704;
}

.feature-card .v-card-subtitle {
    color: #7a7a7a;
}

.recommendation-cta-card {
    background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
    border-radius: 20px;
    border: 2px solid #b28704;
}

.cta-title {
    color: #b28704;
    font-size: 1.5rem;
    font-weight: 700;
}

@media (min-width: 960px) {
    .cta-title {
        font-size: 1.8rem;
    }
}

.cta-subtitle {
    color: #7a7a7a;
    font-size: 0.95rem;
}

@media (min-width: 960px) {
    .cta-subtitle {
        font-size: 1.05rem;
    }
}

.cta-description {
    color: #5a5a5a;
    font-size: 0.95rem;
    line-height: 1.6;
}

@media (min-width: 960px) {
    .cta-description {
        font-size: 1rem;
    }
}
</style>



