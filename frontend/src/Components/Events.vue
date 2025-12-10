<script setup>
import { ref, onMounted, computed } from 'vue'

const events = ref([])
const loading = ref(false)

const upcomingEvents = computed(() => {
  return events.value.filter(event => {
    const eventDate = new Date(event.date)
    const now = new Date()
    return eventDate >= now && event.event_type !== 'special'
  })
})

const specialEvents = computed(() => {
  return events.value.filter(event => event.event_type === 'special')
})

async function loadEvents() {
  loading.value = true
  try {
    const response = await fetch('http://localhost:8000/api/events/get.php')
    const result = await response.json()

    if (result.success) {
      events.value = result.data || []
    }
  } catch (error) {
    console.error('Failed to load events:', error)
  } finally {
    loading.value = false
  }
}

const bookEvent = (event) => {
  alert(`Booking for ${event.title}. Redirecting to reservation page...`)
  // In a real app, this would redirect to reservation page with event details
}

onMounted(() => {
  loadEvents()
})
</script>

<template>
  <v-container class="py-8 py-md-12">
    <!-- Header -->
    <v-row>
      <v-col cols="12" class="text-center mb-6">
        <h1 class="events-title">Upcoming Events & Classes</h1>
        <p class="events-subtitle">Join us for hands-on baking classes, workshops, and special events</p>
      </v-col>
    </v-row>

    <!-- Loading State -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular
          indeterminate
          color="amber-darken-2"
          size="64"
        ></v-progress-circular>
        <p class="mt-4">Loading events...</p>
      </v-col>
    </v-row>

    <!-- Upcoming Events -->
    <v-row v-else>
      <v-col cols="12" class="mb-4">
        <h2 class="section-title">Baking Classes</h2>
      </v-col>

      <v-col
        v-for="event in upcomingEvents"
        :key="event.id"
        cols="12"
        md="6"
        lg="4"
      >
        <v-card class="event-card" elevation="4" height="100%">
          <v-img
            v-if="event.image"
            :src="`http://localhost:8000${event.image}`"
            height="200"
            cover
          ></v-img>
          <div v-else class="no-image-placeholder">
            <v-icon size="80" color="grey-lighten-1">mdi-image-off-outline</v-icon>
          </div>

          <v-card-title class="text-h6 pb-2">{{ event.title }}</v-card-title>

          <v-card-text>
            <div class="event-details mb-3">
              <div class="detail-item mb-2">
                <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-calendar</v-icon>
                <span class="text-body-2">{{ event.date }}</span>
              </div>

              <div class="detail-item mb-2">
                <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-clock-outline</v-icon>
                <span class="text-body-2">{{ event.time }}</span>
              </div>

              <div class="detail-item mb-2">
                <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-map-marker</v-icon>
                <span class="text-body-2">{{ event.location }}</span>
              </div>

              <div class="detail-item mb-3">
                <v-icon color="amber-darken-2" size="20" class="mr-2">mdi-account-group</v-icon>
                <span class="text-body-2">{{ event.spots_available }} spots available</span>
              </div>
            </div>

            <p class="text-body-2 mb-3">{{ event.description }}</p>

            <div class="price-section mb-3">
              <span class="text-h6 font-weight-bold" style="color: #b28704;">
                Ksh {{ event.price.toLocaleString() }}
              </span>
              <span class="text-caption ml-2">per person</span>
            </div>
          </v-card-text>

          <v-card-actions class="pt-0 px-4 pb-4">
            <v-btn
              color="amber-darken-2"
              variant="flat"
              block
              @click="bookEvent(event)"
            >
              Book Now
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Special Events -->
    <v-row class="mt-8">
      <v-col cols="12" class="mb-4">
        <h2 class="section-title">Special Events</h2>
        <p class="text-body-1 text-muted">Custom events tailored to your needs</p>
      </v-col>

      <v-col
        v-for="event in specialEvents"
        :key="event.id"
        cols="12"
        md="6"
      >
        <v-card class="special-event-card pa-6" elevation="3">
          <div class="d-flex align-start">
            <v-icon color="amber-darken-2" size="32" class="mr-4">mdi-star-circle</v-icon>
            <div class="flex-grow-1">
              <h3 class="text-h6 mb-2" style="color: #b28704;">{{ event.title }}</h3>

              <v-chip size="small" color="amber-lighten-4" class="mb-3">
                {{ event.event_type }}
              </v-chip>

              <div class="event-details mb-3">
                <div class="detail-item mb-2">
                  <v-icon color="amber-darken-2" size="18" class="mr-2">mdi-calendar</v-icon>
                  <span class="text-body-2">{{ event.date }}</span>
                </div>

                <div class="detail-item mb-2">
                  <v-icon color="amber-darken-2" size="18" class="mr-2">mdi-clock-outline</v-icon>
                  <span class="text-body-2">{{ event.time }}</span>
                </div>

                <div class="detail-item mb-2">
                  <v-icon color="amber-darken-2" size="18" class="mr-2">mdi-map-marker</v-icon>
                  <span class="text-body-2">{{ event.location }}</span>
                </div>
              </div>

              <p class="text-body-2 mb-4">{{ event.description }}</p>

              <v-btn
                color="amber-darken-2"
                variant="outlined"
                @click="bookEvent(event)"
              >
                Contact Us
              </v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Call to Action -->
    <v-row class="mt-10">
      <v-col cols="12">
        <v-card class="cta-card pa-8 text-center" elevation="0">
          <h2 class="text-h5 mb-3" style="color: #b28704;">Want to Host a Private Event?</h2>
          <p class="text-body-1 mb-4">We can create custom baking experiences for birthdays, team building, or any special occasion.</p>
          <v-btn to="/contactus" color="amber-darken-2" size="large">
            Get In Touch
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.events-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
}

@media (min-width: 600px) {
  .events-title {
    font-size: 2.5rem;
  }
}

.events-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
}

@media (min-width: 600px) {
  .events-subtitle {
    font-size: 1.125rem;
  }
}

.section-title {
  color: #b28704;
  font-size: 1.6rem;
  font-weight: 700;
}

.event-card {
  background: #fffbe6;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
}

.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(178, 135, 4, 0.25);
}

.event-card .v-card-title {
  color: #b28704;
}

.detail-item {
  display: flex;
  align-items: center;
}

.special-event-card {
  background: #fffbe6;
  border-radius: 16px;
  transition: all 0.3s ease;
}

.special-event-card:hover {
  box-shadow: 0 8px 24px rgba(178, 135, 4, 0.25);
}

.cta-card {
  background: linear-gradient(135deg, #fffbe6 0%, #fff8dc 100%);
  border-radius: 16px;
  border: 2px solid #b28704;
}

.no-image-placeholder {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
}
</style>