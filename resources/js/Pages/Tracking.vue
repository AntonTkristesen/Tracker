<template>
  <div>
    <div class="card mb-4">
      <div class="card-header">Time Tracking</div>
      <div class="card-body">
        <p>Time: {{ displayTime }}s</p>
        <Button @click="toggleTimer" class="mb-2">
          {{ isRunning ? 'Stop' : 'Start' }} Timer
        </Button>
        <p v-if="hasSavedTime">Last saved time: {{ savedTime }}s</p>

        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <input 
            id="description" 
            class="form-control" 
            v-model="description" 
            placeholder="What are you tracking?" 
          />
        </div>

        <Button @click="submit" class="btn btn-primary">Register Time Track</Button>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Previous Time Trackings</div>
      <div class="card-body">
        <table v-if="trackings.length" class="table table-striped">
          <thead>
            <tr>
              <th>Date</th>
              <th>Time (s)</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in trackings" :key="t.id">
              <td>{{ formatDate(t.created_at) }}</td>
              <td>{{ t.time }}s</td>
              <td>{{ t.description || 'No description' }}</td>
            </tr>
          </tbody>
        </table>
        <p v-else>No time trackings yet.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'

const { props } = usePage()
const trackings = ref(props.trackings || [])

const elapsedTime = ref(0)
const savedTime = ref(null)
const description = ref('')
const isRunning = ref(false)
let interval = null

const displayTime = computed(() => savedTime.value ?? elapsedTime.value)
const hasSavedTime = computed(() => savedTime.value !== null)

const toggleTimer = () => {
  if (isRunning.value) {
    clearInterval(interval)
    savedTime.value = elapsedTime.value
  } else {
    interval = setInterval(() => elapsedTime.value++, 1000)
  }
  isRunning.value = !isRunning.value
}

const submit = async () => {
  if (isRunning.value) toggleTimer()

  const timeToSave = displayTime.value
  if (timeToSave <= 0) return

  try {
    const { data } = await axios.post('/tracking', {
      time: timeToSave,
      description: description.value
    })

    if (data.tracking) {
      trackings.value.unshift(data.tracking)
    }

    // Reset
    elapsedTime.value = 0
    savedTime.value = null
    description.value = ''
  } catch (err) {
    console.error('Failed to submit time:', err)
  }
}

onMounted(async () => {
  try {
    const { data } = await axios.get('/user-trackings')
    trackings.value = data.trackings
  } catch (err) {
    console.error('Failed to load trackings:', err)
  }
})

onUnmounted(() => clearInterval(interval))

const formatDate = (str) => new Date(str).toLocaleString()
</script>
