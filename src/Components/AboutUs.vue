<template>

    <v-container class="py-12">
        <v-row>
            <v-col cols="12" md="6" class="d-flex align-center">
                <div>
                    <h1 class="about-title">Our Story</h1>
                    <p class="about-lead">At Suvaya Bakery we bake from the heart. Our recipes blend traditional techniques with modern flavors to make memorable treats for every occasion.</p>

                    <h3>Our Mission</h3>
                    <p>To bring people together through hand-crafted baked goods made with high-quality ingredients, creativity, and care.</p>

                    <h3>What We Value</h3>
                    <ul>
                        <li>Freshness & Quality</li>
                        <li>Community & Celebration</li>
                        <li>Creativity & Learning (we run classes!)</li>
                    </ul>

                    <v-btn color="amber darken-2" class="mt-4" href="/contact">Contact Us</v-btn>
                </div>
            </v-col>

            <v-col cols="12" md="6">
                <v-img src="public/Menu/alexandra-gornago-YjadnXdoa6s-unsplash.jpg" height="380" cover></v-img>
            </v-col>
        </v-row>

        <v-row class="mt-10">
            <v-col cols="12" class="text-center mb-6">
                <h2 class="section-title">Meet the Team</h2>
                <p class="section-lead">A small, passionate crew who love baking and teaching.</p>
            </v-col>

            <v-col cols="12" sm="6" md="4" v-for="member in team" :key="member.name">
                <v-card class="pa-4 team-card">
                    <v-row>
                        <v-col cols="4">
                            <v-avatar size="64">
                                <v-img :src="member.image"></v-img>
                            </v-avatar>
                        </v-col>
                        <v-col cols="8">
                            <div class="font-weight-bold">{{ member.name }}</div>
                            <div class="text-caption">{{ member.role }}</div>
                        </v-col>
                    </v-row>
                    <v-card-text class="pt-2">{{ member.bio }}</v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script >
export default {
    data() {
        return {
            team: [
                { name: 'Aisha',
                  role: 'Head Baker', 
                  bio: 'Pastry chef with 10+ years experience creating custom cakes.',
                  image: "/About/emediong-umoh-fY9ZV9-475o-unsplash.jpg" },
                { 
                    name: 'Daniel',
                    role: 'Bread & Savory', 
                    bio: 'Love crafting artisan bread and savory bakes for events.',
                    image: "/About/rc-cf-FMh5o5m5N9E-unsplash.jpg" },
                { 
                    name: 'Maya',
                    role: 'Instructor', 
                    bio: 'Runs our baking classes and workshops.', 
                    image: "/About/steward-masweneng-oNcs1L6VJtM-unsplash.jpg" }
            ]
        }
    }
}
</script>


<style scoped>
.about-title { color: #b28704; font-size: 2rem; margin-bottom: 0.5rem }
.about-lead { font-size: 1.05rem; margin-bottom: 1rem }
.section-title { color: #b28704; font-size: 1.6rem }
.team-card { border-radius: 12px; background: #fffaf0 }
</style>