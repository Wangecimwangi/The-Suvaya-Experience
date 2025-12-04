<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const step = ref(1)
const totalSteps = 5

// Form data
const eventType = ref('')
const guestCount = ref(null)
const budget = ref(null)
const cakePreference = ref('')
const dietaryRestrictions = ref([])
const additionalNotes = ref('')

const eventTypes = [
  { value: 'birthday', title: 'Birthday Party', icon: 'mdi-cake-variant', color: 'pink' },
  { value: 'wedding', title: 'Wedding', icon: 'mdi-ring', color: 'purple' },
  { value: 'anniversary', title: 'Anniversary', icon: 'mdi-heart', color: 'red' },
  { value: 'corporate', title: 'Corporate Event', icon: 'mdi-briefcase', color: 'blue' },
  { value: 'graduation', title: 'Graduation', icon: 'mdi-school', color: 'green' },
  { value: 'baby_shower', title: 'Baby Shower', icon: 'mdi-baby-carriage', color: 'cyan' },
  { value: 'other', title: 'Other Celebration', icon: 'mdi-party-popper', color: 'amber' }
]

const budgetRanges = [
  { value: 'budget', title: 'Budget Friendly', range: 'Under KES 10,000', min: 0, max: 10000 },
  { value: 'moderate', title: 'Moderate', range: 'KES 10,000 - 30,000', min: 10000, max: 30000 },
  { value: 'premium', title: 'Premium', range: 'KES 30,000 - 60,000', min: 30000, max: 60000 },
  { value: 'luxury', title: 'Luxury', range: 'Above KES 60,000', min: 60000, max: 999999 }
]

const cakeTypes = [
  { value: 'chocolate', title: 'Chocolate', icon: 'mdi-cake-variant' },
  { value: 'vanilla', title: 'Vanilla', icon: 'mdi-cake' },
  { value: 'red_velvet', title: 'Red Velvet', icon: 'mdi-cake-layered' },
  { value: 'fruit', title: 'Fruit Cake', icon: 'mdi-fruit-cherries' },
  { value: 'custom', title: 'Custom Design', icon: 'mdi-palette' },
  { value: 'no_preference', title: 'No Preference', icon: 'mdi-help-circle' }
]

const dietaryOptions = [
  'Gluten-free',
  'Dairy-free',
  'Vegan',
  'Nut-free',
  'Sugar-free',
  'Halal'
]

const progressPercent = computed(() => {
  return (step.value / totalSteps) * 100
})

function nextStep() {
  if (step.value < totalSteps) {
    step.value++
  }
}

function prevStep() {
  if (step.value > 1) {
    step.value--
  }
}

function canProceed() {
  switch(step.value) {
    case 1:
      return eventType.value !== ''
    case 2:
      return guestCount.value && guestCount.value > 0
    case 3:
      return budget.value !== null
    case 4:
      return cakePreference.value !== ''
    case 5:
      return true
    default:
      return false
  }
}

function getRecommendations() {
  // Save form data to pass to results page
  const recommendationData = {
    eventType: eventType.value,
    guestCount: guestCount.value,
    budget: budget.value,
    cakePreference: cakePreference.value,
    dietaryRestrictions: dietaryRestrictions.value,
    additionalNotes: additionalNotes.value
  }

  localStorage.setItem('recommendationData', JSON.stringify(recommendationData))
  router.push('/recommendation-results')
}

function reset() {
  step.value = 1
  eventType.value = ''
  guestCount.value = null
  budget.value = null
  cakePreference.value = ''
  dietaryRestrictions.value = []
  additionalNotes.value = ''
}
</script>

<template>
  <v-container class="recommendation-container py-6 py-md-8">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <!-- Header -->
        <div class="text-center mb-6">
          <h1 class="page-title mb-2">
            <v-icon color="amber-darken-2" size="50" class="mr-2">mdi-lightbulb-on</v-icon>
            Package Recommendation Wizard
          </h1>
          <p class="page-subtitle">
            Answer a few questions and we'll suggest the perfect package for your event
          </p>
        </div>

        <!-- Progress Bar -->
        <v-card class="progress-card mb-6" elevation="2">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between mb-2">
              <span class="text-body-2">Step {{ step }} of {{ totalSteps }}</span>
              <span class="text-body-2 font-weight-bold">{{ Math.round(progressPercent) }}%</span>
            </div>
            <v-progress-linear
              :model-value="progressPercent"
              color="amber-darken-2"
              height="8"
              rounded
            ></v-progress-linear>
          </v-card-text>
        </v-card>

        <!-- Wizard Steps -->
        <v-card class="wizard-card" elevation="4">
          <v-card-text class="pa-6 pa-md-8">
            <!-- Step 1: Event Type -->
            <div v-if="step === 1" class="step-content">
              <h2 class="step-title mb-4">
                <v-icon color="amber-darken-2" class="mr-2">mdi-party-popper</v-icon>
                What type of event are you planning?
              </h2>

              <v-row>
                <v-col
                  v-for="type in eventTypes"
                  :key="type.value"
                  cols="6"
                  sm="4"
                  md="3"
                >
                  <v-card
                    :class="['event-type-card', { 'selected': eventType === type.value }]"
                    @click="eventType = type.value"
                    :color="eventType === type.value ? 'amber-lighten-5' : ''"
                    elevation="2"
                  >
                    <v-card-text class="text-center pa-4">
                      <v-icon
                        :color="type.color"
                        size="48"
                        class="mb-2"
                      >{{ type.icon }}</v-icon>
                      <div class="event-type-title">{{ type.title }}</div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>

            <!-- Step 2: Guest Count -->
            <div v-if="step === 2" class="step-content">
              <h2 class="step-title mb-4">
                <v-icon color="amber-darken-2" class="mr-2">mdi-account-group</v-icon>
                How many guests are you expecting?
              </h2>

              <v-row justify="center">
                <v-col cols="12" sm="8" md="6">
                  <v-text-field
                    v-model.number="guestCount"
                    label="Number of Guests"
                    type="number"
                    variant="outlined"
                    color="amber-darken-2"
                    prepend-inner-icon="mdi-account-group"
                    min="1"
                    placeholder="Enter number of guests"
                    hint="This helps us recommend the right portion sizes"
                    persistent-hint
                  ></v-text-field>

                  <!-- Quick selection chips -->
                  <div class="mt-4">
                    <p class="text-caption text-grey mb-2">Quick select:</p>
                    <v-chip-group>
                      <v-chip
                        v-for="count in [10, 25, 50, 100, 200]"
                        :key="count"
                        @click="guestCount = count"
                        color="amber-darken-2"
                        variant="outlined"
                      >
                        {{ count }} guests
                      </v-chip>
                    </v-chip-group>
                  </div>
                </v-col>
              </v-row>
            </div>

            <!-- Step 3: Budget -->
            <div v-if="step === 3" class="step-content">
              <h2 class="step-title mb-4">
                <v-icon color="amber-darken-2" class="mr-2">mdi-cash-multiple</v-icon>
                What's your budget range?
              </h2>

              <v-row>
                <v-col
                  v-for="budgetOption in budgetRanges"
                  :key="budgetOption.value"
                  cols="12"
                  sm="6"
                >
                  <v-card
                    :class="['budget-card', { 'selected': budget === budgetOption.value }]"
                    @click="budget = budgetOption.value"
                    :color="budget === budgetOption.value ? 'amber-lighten-5' : ''"
                    elevation="2"
                  >
                    <v-card-text class="pa-4">
                      <div class="d-flex align-center">
                        <v-icon
                          :color="budget === budgetOption.value ? 'amber-darken-2' : 'grey'"
                          size="36"
                          class="mr-3"
                        >mdi-wallet</v-icon>
                        <div>
                          <div class="budget-title">{{ budgetOption.title }}</div>
                          <div class="budget-range">{{ budgetOption.range }}</div>
                        </div>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>

            <!-- Step 4: Cake Preference -->
            <div v-if="step === 4" class="step-content">
              <h2 class="step-title mb-4">
                <v-icon color="amber-darken-2" class="mr-2">mdi-cake-variant</v-icon>
                What type of cake would you prefer?
              </h2>

              <v-row>
                <v-col
                  v-for="cake in cakeTypes"
                  :key="cake.value"
                  cols="6"
                  sm="4"
                >
                  <v-card
                    :class="['cake-card', { 'selected': cakePreference === cake.value }]"
                    @click="cakePreference = cake.value"
                    :color="cakePreference === cake.value ? 'amber-lighten-5' : ''"
                    elevation="2"
                  >
                    <v-card-text class="text-center pa-4">
                      <v-icon
                        color="amber-darken-2"
                        size="48"
                        class="mb-2"
                      >{{ cake.icon }}</v-icon>
                      <div class="cake-title">{{ cake.title }}</div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>

            <!-- Step 5: Additional Details -->
            <div v-if="step === 5" class="step-content">
              <h2 class="step-title mb-4">
                <v-icon color="amber-darken-2" class="mr-2">mdi-clipboard-text</v-icon>
                Any dietary restrictions or special requests?
              </h2>

              <v-row justify="center">
                <v-col cols="12" md="10">
                  <div class="mb-4">
                    <p class="text-subtitle-1 font-weight-bold mb-2">Dietary Restrictions:</p>
                    <v-chip-group
                      v-model="dietaryRestrictions"
                      multiple
                      column
                    >
                      <v-chip
                        v-for="option in dietaryOptions"
                        :key="option"
                        :value="option"
                        color="amber-darken-2"
                        variant="outlined"
                        filter
                      >
                        {{ option }}
                      </v-chip>
                    </v-chip-group>
                  </div>

                  <v-textarea
                    v-model="additionalNotes"
                    label="Additional Notes (Optional)"
                    variant="outlined"
                    color="amber-darken-2"
                    rows="4"
                    placeholder="Any special themes, colors, or specific requirements..."
                  ></v-textarea>
                </v-col>
              </v-row>
            </div>
          </v-card-text>

          <!-- Navigation Buttons -->
          <v-card-actions class="pa-6 pt-0">
            <v-btn
              v-if="step > 1"
              color="grey"
              variant="outlined"
              prepend-icon="mdi-arrow-left"
              @click="prevStep"
            >
              Back
            </v-btn>

            <v-spacer></v-spacer>

            <v-btn
              color="grey"
              variant="text"
              @click="reset"
            >
              Reset
            </v-btn>

            <v-btn
              v-if="step < totalSteps"
              color="amber-darken-2"
              :disabled="!canProceed()"
              append-icon="mdi-arrow-right"
              @click="nextStep"
            >
              Next
            </v-btn>

            <v-btn
              v-else
              color="amber-darken-2"
              prepend-icon="mdi-lightbulb"
              @click="getRecommendations"
            >
              Get Recommendations
            </v-btn>
          </v-card-actions>
        </v-card>

        <!-- Summary Card (shown on last step) -->
        <v-card v-if="step === totalSteps" class="summary-card mt-4" elevation="2">
          <v-card-title class="summary-title">
            <v-icon color="amber-darken-2" class="mr-2">mdi-clipboard-check</v-icon>
            Your Selections
          </v-card-title>
          <v-card-text>
            <v-list density="compact">
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="amber-darken-2">mdi-party-popper</v-icon>
                </template>
                <v-list-item-title>Event Type</v-list-item-title>
                <v-list-item-subtitle class="text-capitalize">{{ eventTypes.find(t => t.value === eventType)?.title }}</v-list-item-subtitle>
              </v-list-item>

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="amber-darken-2">mdi-account-group</v-icon>
                </template>
                <v-list-item-title>Guest Count</v-list-item-title>
                <v-list-item-subtitle>{{ guestCount }} guests</v-list-item-subtitle>
              </v-list-item>

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="amber-darken-2">mdi-cash-multiple</v-icon>
                </template>
                <v-list-item-title>Budget</v-list-item-title>
                <v-list-item-subtitle>{{ budgetRanges.find(b => b.value === budget)?.title }}</v-list-item-subtitle>
              </v-list-item>

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="amber-darken-2">mdi-cake-variant</v-icon>
                </template>
                <v-list-item-title>Cake Preference</v-list-item-title>
                <v-list-item-subtitle>{{ cakeTypes.find(c => c.value === cakePreference)?.title }}</v-list-item-subtitle>
              </v-list-item>

              <v-list-item v-if="dietaryRestrictions.length > 0">
                <template v-slot:prepend>
                  <v-icon color="amber-darken-2">mdi-food-apple</v-icon>
                </template>
                <v-list-item-title>Dietary Restrictions</v-list-item-title>
                <v-list-item-subtitle>{{ dietaryRestrictions.join(', ') }}</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.recommendation-container {
  background: #f5f5f5;
  min-height: calc(100vh - 64px);
}

.page-title {
  color: #b28704;
  font-size: 2rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (min-width: 960px) {
  .page-title {
    font-size: 2.5rem;
  }
}

.page-subtitle {
  color: #7a7a7a;
  font-size: 1rem;
}

@media (min-width: 960px) {
  .page-subtitle {
    font-size: 1.1rem;
  }
}

.progress-card {
  background: #fffbe6;
  border-radius: 12px;
}

.wizard-card {
  background: #fffbe6;
  border-radius: 20px;
}

.step-content {
  min-height: 400px;
}

.step-title {
  color: #b28704;
  font-size: 1.3rem;
  font-weight: 600;
  display: flex;
  align-items: center;
}

@media (min-width: 960px) {
  .step-title {
    font-size: 1.5rem;
  }
}

.event-type-card,
.budget-card,
.cake-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.event-type-card:hover,
.budget-card:hover,
.cake-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(178, 135, 4, 0.2) !important;
}

.event-type-card.selected,
.budget-card.selected,
.cake-card.selected {
  border-color: #b28704;
  box-shadow: 0 4px 12px rgba(178, 135, 4, 0.3) !important;
}

.event-type-title,
.cake-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #333;
}

.budget-title {
  font-size: 1rem;
  font-weight: 700;
  color: #b28704;
}

.budget-range {
  font-size: 0.875rem;
  color: #7a7a7a;
}

.summary-card {
  background: #fff8dc;
  border-radius: 16px;
}

.summary-title {
  color: #b28704;
  font-size: 1.2rem;
  font-weight: 700;
}

@media (max-width: 600px) {
  .page-title {
    font-size: 1.5rem;
  }

  .step-title {
    font-size: 1.1rem;
  }

  .event-type-title,
  .cake-title {
    font-size: 0.75rem;
  }
}
</style>
