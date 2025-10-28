<script setup>

</script>

<template>
    <!-- Carousel -->
    <v-container>
        <v-row>
            <v-carousel>
                <v-carousel-item src="public/Homepage/olivia-claire-qwzhT20ohOM-unsplash.jpg"></v-carousel-item>
                <v-carousel-item src="public/Homepage/othman-alghanmi-QhkpKufhqYg-unsplash.jpg"></v-carousel-item>
                <v-carousel-item src="public/Homepage/levi-meir-clancy-JVCnRnkCzIg-unsplash.jpg"></v-carousel-item>
                <v-carousel-item src="public/Homepage/olga-kononenko-KVgz8J5SjOg-unsplash.jpg"></v-carousel-item>
            </v-carousel>

        </v-row>
    </v-container>
    <!-- About -->
    <v-container>
        <v-row>
            <v-card align="center">
                <p class="text-h4">Welcome to The Suvaya Experience</p>
                <p class="text-h6">Suvaya is a creative bakery and food hub that brings people together through good taste and beautiful experiences. We specialize in crafting custom cakes, savory bites, and meal plans that fit every occasion. Whether you’re celebrating, learning, or simply enjoying a treat, Suvaya adds a personal touch to every order.</p>
                <p class="text-h6">We make planning easier for you — our website lets you book your date, view our menu, and even pay in installments. You can see available slots on our calendar, explore our packages, and know exactly what’s included in each plan before you order. Everything is designed to be simple, clear, and made just for you.</p>
                <p class="text-h6">At Suvaya, we also love to teach. Through our baking sessions, we help others learn the art of creating delicious, beautiful cakes from scratch. From fluffy sponges to signature frosting designs, every class is about sharing our passion and creativity with you. Suvaya isn’t just about food — it’s about experience, learning, and joy.</p>
            </v-card>
        </v-row>
    </v-container>
    <!-- News -->
    <v-container>
        <v-row>
            <v-col md="4">
                <v-card color="amber-lighten-1">
                    <v-img 
                    src="public/Homepage/magic-mary-nEOzmjguwO0-unsplash.jpg" 
                    class="align-end text-white" 
                    height="200" 
                    cover>
                </v-img>
                <v-card-title>Sample Text</v-card-title>
                <v-card-subtitle> another sample text</v-card-subtitle>
                <v-card-actions>
                    <v-btn> Read More</v-btn>
                </v-card-actions>
                </v-card>
            </v-col>
               <v-col md="4">
                <v-card color="amber-lighten-1">
                    <v-img 
                    src="public/Homepage/brad-9MTp2Z1ejQo-unsplash.jpg" 
                    class="align-end text-white" 
                    height="200" 
                    cover>
                </v-img>
                <v-card-title>Sample Text</v-card-title>
                <v-card-subtitle> another sample text</v-card-subtitle>
                <v-card-actions>
                    <v-btn> Read More</v-btn>
                </v-card-actions>
                </v-card>
            </v-col>   
              <v-col md="4">
                <v-card color="amber-lighten-1">
                    <v-img 
                    src="public/Homepage/hailey-tong-uI2ayKEfgaw-unsplash.jpg" 
                    class="align-end text-white" 
                    height="200" 
                    cover>
                </v-img>
                <v-card-title>Sample Text</v-card-title>
                <v-card-subtitle> another sample text</v-card-subtitle>
                <v-card-actions>
                    <v-btn> Read More</v-btn>
                </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>



