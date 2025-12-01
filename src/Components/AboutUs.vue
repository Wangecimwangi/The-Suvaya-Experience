<template>
    <v-container class="py-8 py-md-12">
        <!-- Our Story Section -->
        <v-row class="mb-8">
            <v-col cols="12" md="6" order="2" order-md="1" class="d-flex align-center">
                <div class="story-content">
                    <h1 class="about-title mb-4">Our Story</h1>
                    <p class="about-lead mb-6">
                        At Suvaya Bakery we bake from the heart. Our recipes blend traditional techniques with modern flavors to make memorable treats for every occasion.
                    </p>

                    <h3 class="section-subtitle mb-3">Our Mission</h3>
                    <p class="about-text mb-5">
                        To bring people together through hand-crafted baked goods made with high-quality ingredients, creativity, and care.
                    </p>

                    <h3 class="section-subtitle mb-3">What We Value</h3>
                    <ul class="values-list mb-6">
                        <li>
                            <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-check-circle</v-icon>
                            Freshness & Quality
                        </li>
                        <li>
                            <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-check-circle</v-icon>
                            Community & Celebration
                        </li>
                        <li>
                            <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-check-circle</v-icon>
                            Creativity & Learning (we run classes!)
                        </li>
                    </ul>

                    <v-btn to="/contactus" color="amber-darken-2" size="large">Contact Us</v-btn>
                </div>
            </v-col>

            <v-col cols="12" md="6" order="1" order-md="2" class="mb-4 mb-md-0">
                <v-img
                    src="public/Menu/alexandra-gornago-YjadnXdoa6s-unsplash.jpg"
                    class="about-image"
                    height="400"
                    cover
                ></v-img>
            </v-col>
        </v-row>

        <!-- Team Section -->
        <v-row class="mt-8 mt-md-12">
            <v-col cols="12" class="text-center mb-6">
                <h2 class="section-title mb-3">Meet the Team</h2>
                <p class="section-lead">A small, passionate crew who love baking and teaching.</p>
            </v-col>

            <v-col
                cols="12"
                sm="6"
                md="4"
                v-for="member in team"
                :key="member.name"
            >
                <v-card class="team-card pa-5" elevation="3" height="100%">
                    <div class="text-center mb-4">
                        <v-avatar size="80" class="team-avatar">
                            <v-img :src="member.image"></v-img>
                        </v-avatar>
                    </div>

                    <div class="text-center">
                        <h3 class="team-name mb-1">{{ member.name }}</h3>
                        <p class="team-role mb-3">{{ member.role }}</p>
                    </div>

                    <v-divider class="my-3"></v-divider>

                    <p class="team-bio text-center">{{ member.bio }}</p>
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
.about-title {
    color: #b28704;
    font-size: 1.8rem;
    font-weight: 700;
}

@media (min-width: 600px) {
    .about-title {
        font-size: 2.5rem;
    }
}

.about-lead {
    font-size: 1rem;
    line-height: 1.6;
    color: #5a5a5a;
}

@media (min-width: 600px) {
    .about-lead {
        font-size: 1.125rem;
    }
}

.section-subtitle {
    color: #b28704;
    font-size: 1.2rem;
    font-weight: 600;
}

@media (min-width: 600px) {
    .section-subtitle {
        font-size: 1.4rem;
    }
}

.about-text {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #5a5a5a;
}

@media (min-width: 600px) {
    .about-text {
        font-size: 1rem;
    }
}

.values-list {
    list-style: none;
    padding: 0;
}

.values-list li {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    font-size: 0.95rem;
    color: #5a5a5a;
}

@media (min-width: 600px) {
    .values-list li {
        font-size: 1rem;
    }
}

.about-image {
    border-radius: 16px;
    overflow: hidden;
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

.section-lead {
    font-size: 1rem;
    color: #7a7a7a;
}

@media (min-width: 600px) {
    .section-lead {
        font-size: 1.125rem;
    }
}

.team-card {
    border-radius: 16px;
    background: #fffbe6;
    transition: all 0.3s ease;
}

.team-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(178, 135, 4, 0.25);
}

.team-avatar {
    border: 3px solid #b28704;
}

.team-name {
    color: #b28704;
    font-size: 1.15rem;
    font-weight: 700;
}

.team-role {
    color: #7a7a7a;
    font-size: 0.9rem;
    font-weight: 500;
}

.team-bio {
    color: #5a5a5a;
    font-size: 0.9rem;
    line-height: 1.5;
}

.story-content {
    width: 100%;
}
</style>